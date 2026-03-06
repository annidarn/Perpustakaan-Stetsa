<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Member;
use App\Models\ClassModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MemberImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            // 1. Determine Type
            $typeInput = strtolower($row['tipe_siswagurustaff'] ?? 'siswa');
            if (str_contains($typeInput, 'siswa')) $type = 'student';
            elseif (str_contains($typeInput, 'guru')) $type = 'teacher';
            elseif (str_contains($typeInput, 'staff')) $type = 'staff';
            else $type = 'student';

            // 2. NIS/NIP handling
            $nis = $row['nis'] ?? null;
            $nip = $row['nip'] ?? null;

            // NIS auto-gen for student if empty
            if ($type === 'student' && empty($nis)) {
                $nis = $this->generateNIS();
            }
            
            // NIP auto-gen for teacher/staff if empty
            if (in_array($type, ['teacher', 'staff']) && empty($nip)) {
                $nip = $this->generateNIP($type);
            }

            // 3. Create User
            $email = $this->generateEmail($nis, $nip, $type);
            
            // Avoid duplicate users if importing again
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $row['nama'],
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]);
            }

            // 4. Class Handling (Only for students)
            $classId = null;
            if ($type === 'student' && !empty($row['grade_kelas']) && !empty($row['nama_kelas'])) {
                $class = ClassModel::where('grade', $row['grade_kelas'])
                    ->where('class_name', $row['nama_kelas'])
                    ->first();
                if ($class) {
                    $classId = $class->id;
                }
            }

            // 5. Gender handling
            $gender = strtoupper($row['jenis_kelamin'] ?? 'L');
            if (str_contains($gender, 'PEREMPUAN') || $gender === 'P') $gender = 'P';
            else $gender = 'L';

            // 6. Create/Update Member
            return Member::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nis' => ($type === 'student') ? $nis : null,
                    'nip' => (in_array($type, ['teacher', 'staff'])) ? $nip : null,
                    'class_id' => $classId,
                    'enrollment_year' => $row['tahun_masuk'] ?? ($type === 'student' ? date('Y') : null),
                    'phone' => $row['no_telp'] ?? null,
                    'gender' => $gender,
                    'type' => $type,
                    'status' => 'active',
                ]
            );
        });
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'tipe_siswagurustaff' => 'required',
            'nis' => 'nullable|unique:members,nis',
            'nip' => 'nullable|unique:members,nip',
            'grade_kelas' => 'nullable',
            'nama_kelas' => 'nullable',
            'jenis_kelamin' => 'nullable',
            'no_telp' => 'nullable',
            'tahun_masuk' => 'nullable|integer',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Kolom :attribute harus diisi.',
            'nis.unique' => ':attribute :input sudah terdaftar.',
            'nip.unique' => ':attribute :input sudah terdaftar.',
            'tipe_siswagurustaff.required' => 'Kolom :attribute harus diisi.',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'nama' => 'Nama',
            'tipe_siswagurustaff' => 'Tipe (siswa/guru/staff)',
            'nis' => 'NIS',
            'nip' => 'NIP',
            'grade_kelas' => 'Grade Kelas',
            'nama_kelas' => 'Nama Kelas',
            'jenis_kelamin' => 'Jenis Kelamin',
            'no_telp' => 'No. Telp',
            'tahun_masuk' => 'Tahun Masuk',
        ];
    }

    private function generateEmail($nis, $nip, $type)
    {
        $prefix = '';
        if ($type === 'student' && $nis) {
            $prefix = $nis;
        } elseif (in_array($type, ['teacher', 'staff']) && $nip) {
            $prefix = $nip;
        } else {
            $prefix = strtolower($type) . '.' . time() . Str::random(4);
        }
        return $prefix . '@perpustakaan.sch.id';
    }

    private function generateNIS()
    {
        $lastMember = Member::whereNotNull('nis')
            ->where('type', 'student')
            ->orderBy('nis', 'desc')
            ->first();
        
        if ($lastMember && is_numeric($lastMember->nis)) {
            return str_pad((int)$lastMember->nis + 1, 5, '0', STR_PAD_LEFT);
        }
        return '10001';
    }

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
}
