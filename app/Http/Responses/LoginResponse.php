<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = auth()->user();
        $role = strtolower($user->role ?? '');

        if ($role === 'admin') {
            return redirect('/murid');
        }

        if ($role === 'bendahara') {
            return redirect('/pemasukan/index');
        }

        if ($role === 'wali') {
            return redirect('/pembayaran');
        }

        return redirect('/');
    }
}

