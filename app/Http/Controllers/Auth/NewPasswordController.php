<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    // tampilkan tampilan pengaturan ulang kata sandi
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * tangani permintaan kata sandi baru yang masuk
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // disini kita akan mencoba mengatur ulang kata sandi pengguna. jika berhasil, kita
        // akan memperbarui kata sandi pada model pengguna sebenarnya dan menyimpannya ke
        // basis data. Jika tidak, kita akan menguraikan kesalahan dan mengembalikan respons
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // jika kata sandi berhasil direset, kami akan mengarahkan pengguna kembali ke
        // tampilan beranda aplikasi yang terautentikasi. jika terjadi kesalahan, kami dapat
        // mengarahkan mereka kembali ke tempat asal mereka dengan pesan kesalahan
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
