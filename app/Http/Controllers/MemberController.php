<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::with(['user', 'class']);
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by class (for students only)
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }
        
        $members = $query->orderBy('created_at', 'desc')->paginate(15);
        $classes = ClassModel::orderBy('grade')->orderBy('class_name')->get();
        
        return view('members.index', compact('members', 'classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = ClassModel::orderBy('grade')->orderBy('class_name')->get();
        return view('members.create', compact('classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'nullable|string|max:20|unique:members',
            'nip' => 'nullable|string|max:20|unique:members',
            'class_id' => 'nullable|exists:classes,id',
            'enrollment_year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'phone' => 'nullable|string|max:15',
            'gender' => 'nullable|in:L,P',
            'type' => 'required|in:student,teacher,staff',
            'status' => 'required|in:active,inactive,graduated',
        ]);

        // 1. Create User account
        $user = User::create([
            'name' => $request->name,
            'email' => $this->generateEmail($request->nis, $request->nip, $request->type),
            'password' => Hash::make('password123'),
        ]);

        // 2. Auto-generate NIS if empty and type is student
        $nis = $request->nis;
        if (empty($nis) && $request->type === 'student') {
            $nis = $this->generateNIS();
        }
        
        // PERBAIKAN: Reset NIS jika bukan student
        if ($request->type !== 'student') {
            $nis = null;
        }

        // 3. Auto-generate NIP if empty and type is teacher/staff
        $nip = $request->nip;
        if (empty($nip) && in_array($request->type, ['teacher', 'staff'])) {
            $nip = $this->generateNIP($request->type);
        }
        
        // PERBAIKAN: Reset NIP jika student
        if ($request->type === 'student') {
            $nip = null;
        }

        // 4. Auto-set enrollment year for new students
        $enrollmentYear = $request->enrollment_year;
        if (empty($enrollmentYear) && $request->type === 'student') {
            $enrollmentYear = date('Y');
        }
        
        // PERBAIKAN: Reset enrollment year jika bukan student
        if ($request->type !== 'student') {
            $enrollmentYear = null;
        }

        // 5. Create Member
        $member = Member::create([
            'user_id' => $user->id,
            'nis' => $nis,
            'nip' => $nip,
            'class_id' => $request->type === 'student' ? $request->class_id : null,
            'enrollment_year' => $enrollmentYear,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'type' => $request->type,
            'status' => $request->status,
        ]);

        return redirect()->route('members.index')
            ->with('success', "Anggota {$request->name} berhasil ditambahkan.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        $member->load(['user', 'class', 'borrows.bookCopy.book']);
        return view('members.show', compact('member'));
    }

    /**
     * Show promote form (naik kelas massal)
     */
    public function showPromoteForm()
    {
        // Get all classes
        $classes = ClassModel::orderBy('grade')->orderBy('class_name')->get();
        
        // Get promotion statistics
        $stats = [
            'grade_10' => Member::whereHas('class', function($q) {
                $q->where('grade', '10');
            })->where('type', 'student')->where('status', 'active')->count(),
            
            'grade_11' => Member::whereHas('class', function($q) {
                $q->where('grade', '11');
            })->where('type', 'student')->where('status', 'active')->count(),
            
            'grade_12' => Member::whereHas('class', function($q) {
                $q->where('grade', '12');
            })->where('type', 'student')->where('status', 'active')->count(),
        ];
        
        return view('members.promote', compact('classes', 'stats'));
    }

    /**
     * Process mass promotion
     */
    public function processPromotion(Request $request)
    {
        if ($request->has('preview_only')) {
            $previewData = $this->generatePreview($promoteMap);
            return response()->json([
                'success' => true,
                'data' => $previewData
            ]);
        }
        $request->validate([
            'promote_map' => 'required|array',
            'promote_map.*' => 'nullable|string',
            'update_enrollment_year' => 'nullable|boolean',
            'create_backup' => 'nullable|boolean',
            'notify_students' => 'nullable|boolean',
        ]);
        
        $promoteMap = $request->promote_map;
        $updateEnrollmentYear = $request->boolean('update_enrollment_year');
        $createBackup = $request->boolean('create_backup');
        $notifyStudents = $request->boolean('notify_students');
        
        DB::beginTransaction();
        
        try {
            $results = [
                'promoted' => 0,
                'graduated' => 0,
                'skipped' => 0,
                'errors' => []
            ];
            
            // Create backup if requested
            if ($createBackup) {
                $this->createPromotionBackup();
            }
            
            // Process each class mapping
            foreach ($promoteMap as $oldClassId => $newTarget) {
                if (empty($newTarget)) {
                    $results['skipped']++;
                    continue;
                }
                
                if ($newTarget === 'graduated') {
                    // Graduate all students in this class
                    $count = $this->graduateClass($oldClassId, $notifyStudents);
                    $results['graduated'] += $count;
                } else {
                    // Promote to new class
                    $count = $this->promoteClass($oldClassId, $newTarget, $updateEnrollmentYear, $notifyStudents);
                    $results['promoted'] += $count;
                }
            }
            
            DB::commit();
            
            // Success message with summary
            $message = "Naik kelas berhasil diproses!<br>";
            $message .= "• {$results['promoted']} siswa naik kelas<br>";
            $message .= "• {$results['graduated']} siswa lulus<br>";
            if ($results['skipped'] > 0) {
                $message .= "• {$results['skipped']} kelas dilewati (tidak ada mapping)<br>";
            }
            if ($createBackup) {
                $message .= "• Backup data telah dibuat<br>";
            }
            
            return redirect()->route('members.promote.form')
                ->with('success', $message)
                ->with('results', $results);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('members.promote.form')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        $classes = ClassModel::orderBy('grade')->orderBy('class_name')->get();
        $member->load('user');
        return view('members.edit', compact('member', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'nullable|string|max:20|unique:members,nis,' . $member->id,
            'nip' => 'nullable|string|max:20|unique:members,nip,' . $member->id,
            'class_id' => 'nullable|exists:classes,id',
            'enrollment_year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'phone' => 'nullable|string|max:15',
            'gender' => 'nullable|in:L,P',
            'type' => 'required|in:student,teacher,staff',
            'status' => 'required|in:active,inactive,graduated',
        ]);

        // Update User name
        $member->user->update(['name' => $request->name]);

        // === PERBAIKAN: Handle NIS/NIP based on type ===
        $nis = $request->nis;
        $nip = $request->nip;
        
        // Reset yang tidak sesuai type
        if ($request->type !== 'student') {
            $nis = null; // Non-student tidak perlu NIS
        }
        
        if ($request->type === 'student') {
            $nip = null; // Student tidak perlu NIP
        }

        // Update Member
        $member->update([
            'nis' => $nis,
            'nip' => $nip,
            'class_id' => $request->type === 'student' ? $request->class_id : null,
            'enrollment_year' => $request->enrollment_year,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'type' => $request->type,
            'status' => $request->status,
        ]);

        return redirect()->route('members.show', $member)
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Member $member)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,graduated'
        ]);

        $member->update(['status' => $request->status]);

        return redirect()->route('members.show', $member)
            ->with('success', "Status anggota berhasil diubah menjadi " . 
                ($request->status === 'active' ? 'Aktif' : 
                ($request->status === 'inactive' ? 'Non-Aktif' : 'Lulus')));
    }

    // Di MemberController.php
    public function batchUpdate(Request $request)
    {
        $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:members,id',
            'action' => 'required|in:status,class',
            'status' => 'required_if:action,status|in:active,inactive,graduated',
            'class_id' => 'required_if:action,class|exists:classes,id'
        ]);
        
        $memberIds = $request->member_ids;
        
        DB::beginTransaction();
        try {
            if ($request->action === 'status') {
                Member::whereIn('id', $memberIds)
                    ->update(['status' => $request->status]);
                    
                $message = "Status {$request->status} berhasil diterapkan ke " . count($memberIds) . " anggota.";
            } 
            elseif ($request->action === 'class') {
                // Hanya untuk siswa
                Member::whereIn('id', $memberIds)
                    ->where('type', 'student')
                    ->update(['class_id' => $request->class_id]);
                    
                $class = ClassModel::find($request->class_id);
                $message = "Kelas berhasil diupdate ke {$class->grade} {$class->class_name} untuk " . count($memberIds) . " siswa.";
            }
            
            DB::commit();
            return redirect()->route('members.index')->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('members.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function batchDelete(Request $request)
    {
        $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:members,id'
        ]);
        
        $members = Member::whereIn('id', $request->member_ids)
            ->with('borrows')
            ->get();
        
        // Cek apakah ada yang masih punya peminjaman aktif
        $hasActiveBorrows = $members->contains(function($member) {
            return $member->borrows()->whereNull('return_date')->exists();
        });
        
        if ($hasActiveBorrows) {
            return redirect()->route('members.index')
                ->with('error', 'Tidak dapat menghapus anggota yang masih memiliki peminjaman aktif.');
        }
        
        DB::beginTransaction();
        try {
            foreach ($members as $member) {
                $member->user->delete();
                $member->delete();
            }
            
            DB::commit();
            return redirect()->route('members.index')
                ->with('success', count($request->member_ids) . ' anggota berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('members.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        // Cek apakah anggota masih punya peminjaman aktif
        if ($member->borrows()->whereNull('return_date')->exists()) {
            return redirect()->route('members.index')
                ->with('error', 'Tidak dapat menghapus anggota yang masih memiliki peminjaman aktif.');
        }

        // Hapus user (optional, bisa juga di-keep)
        $member->user->delete();
        
        // Hapus member
        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }

    /**
     * Generate email for user account
     */
    private function generateEmail($nis, $nip, $type)
    {
        $prefix = '';
        
        if ($type === 'student' && $nis) {
            $prefix = $nis;
        } elseif (in_array($type, ['teacher', 'staff']) && $nip) {
            $prefix = $nip;
        } else {
            $prefix = strtolower(str_replace(' ', '.', $type)) . '.' . time();
        }
        
        return $prefix . '@perpustakaan.sch.id';
    }

    /**
     * Generate NIS if not provided
     */
    private function generateNIS()
    {
        $lastMember = Member::whereNotNull('nis')
            ->where('type', 'student')
            ->orderBy('nis', 'desc')
            ->first();
        
        if ($lastMember && is_numeric($lastMember->nis)) {
            return str_pad((int)$lastMember->nis + 1, 5, '0', STR_PAD_LEFT);
        }
        
        return '10001'; // Start from 10001
    }

    /**
     * Generate NIP if not provided
     */
    private function generateNIP($type)
    {
        $prefix = $type === 'teacher' ? 'T' : 'S';
        $lastMember = Member::whereNotNull('nip')
            ->where('type', $type)
            ->orderBy('nip', 'desc')
            ->first();
        
        if ($lastMember && preg_match('/^[TS](\d+)$/', $lastMember->nip, $matches)) {
            $number = (int)$matches[1] + 1;
            return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
        }
        
        return $prefix . '0001';
    }

    /**
     * Graduate all active students in a class
     */
    private function graduateClass($classId, $addNote = false)
    {
        $students = Member::where('class_id', $classId)
            ->where('type', 'student')
            ->where('status', 'active')
            ->get();
        
        foreach ($students as $student) {
            $student->update([
                'status' => 'graduated',
                'class_id' => null,
            ]);
            
            if ($addNote) {
                $currentNotes = $student->notes ? $student->notes . "\n" : '';
                $student->update([
                    'notes' => $currentNotes . "[LULUS] " . date('Y-m-d') . " - Kelas " . $classId
                ]);
            }
        }
        
        return $students->count();
    }

    /**
     * Promote students from old class to new class
     */
    private function promoteClass($oldClassId, $newClassId, $updateEnrollmentYear = false, $addNote = false)
    {
        $students = Member::where('class_id', $oldClassId)
            ->where('type', 'student')
            ->where('status', 'active')
            ->get();
        
        foreach ($students as $student) {
            $updates = ['class_id' => $newClassId];
            
            // Update enrollment year if requested
            if ($updateEnrollmentYear) {
                $updates['enrollment_year'] = $student->enrollment_year + 1;
            }
            
            // Add note if requested
            if ($addNote) {
                $currentNotes = $student->notes ? $student->notes . "\n" : '';
                $oldClass = ClassModel::find($oldClassId);
                $newClass = ClassModel::find($newClassId);
                $updates['notes'] = $currentNotes . "[NAIK KELAS] " . date('Y-m-d') . 
                    " - Dari: " . ($oldClass ? $oldClass->grade . ' ' . $oldClass->class_name : '?') .
                    " → Ke: " . ($newClass ? $newClass->grade . ' ' . $newClass->class_name : '?');
            }
            
            $student->update($updates);
        }
        
        return $students->count();
    }

    /**
     * Create backup of current student data
     */
    private function createPromotionBackup()
    {
        try {
            // Create backup table if not exists
            DB::statement("CREATE TABLE IF NOT EXISTS members_backup LIKE members");
            
            // Insert backup data with timestamp
            $backupData = Member::where('type', 'student')
                ->get()
                ->map(function($member) {
                    return [
                        'id' => $member->id,
                        'user_id' => $member->user_id,
                        'nis' => $member->nis,
                        'nip' => $member->nip,
                        'class_id' => $member->class_id,
                        'enrollment_year' => $member->enrollment_year,
                        'phone' => $member->phone,
                        'gender' => $member->gender,
                        'type' => $member->type,
                        'status' => $member->status,
                        'notes' => $member->notes,
                        'created_at' => $member->created_at,
                        'updated_at' => $member->updated_at,
                        'backup_date' => now(),
                        'backup_type' => 'promotion'
                    ];
                })->toArray();
            
            if (!empty($backupData)) {
                DB::table('members_backup')->insert($backupData);
            }
            
            return true;
            
        } catch (\Exception $e) {
            // Log error but don't stop the process
            \Log::error('Backup creation failed: ' . $e->getMessage());
            return false;
        }
    }
}