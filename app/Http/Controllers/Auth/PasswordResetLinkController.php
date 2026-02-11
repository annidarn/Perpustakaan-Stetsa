<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    // menampilkan tampilan permintaan tautan pengaturan ulang kata sandi
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * tangani permintaan tautan pengaturan ulang kata sandi yang masuk
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // kami akan mengirimkan tautan pengaturan ulang kata sandi kepada pengguna ini. setelah kami mencoba
        // mengirimkan tautan tersebut, kami akan memeriksa responsnya lalu melihat pesan yang perlu kami
        // tampilkan kepada pengguna. terakhir, kami akan mengirimkan respons yang tepat..
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
