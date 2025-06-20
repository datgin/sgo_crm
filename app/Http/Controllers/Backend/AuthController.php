<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;


class AuthController extends Controller
{
    public function login()
    {
        return view('backend.auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
            // |regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
        ], __('request.messages'), [
            'email' => 'Email',
            'password' => 'mật khẩu'
        ]);

        return transaction(function () use ($request) {
            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');

            if (Auth::guard('admin')->attempt($credentials, $remember)) {
                Auth::guard('admin')->user();

                return successResponse("Đăng nhập thành công.", ['redirect' => '/']);
            }

            return errorResponse("Mật khẩu không chính xác!", 400, true);
        });
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->flush();
        return redirect()->route('login');
    }
}
