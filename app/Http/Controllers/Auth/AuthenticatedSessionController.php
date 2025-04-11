<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    // public function store(LoginRequest $request)
    // {
    //     $userdata = $request -> only('mail_address', 'password');
    //     if (Auth::attempt($userdata)) {
    //         return redirect('top');
    //     }else{
    //         return redirect('login')->with('flash_message', 'name or password is incorrect');
    //     }
    // }
    public function store(LoginRequest $request)
{
    // LoginRequestがバリデーションを通過した場合、すでに値が$validatedに格納されている
    $userdata = $request->only('mail_address', 'password'); // mail_addressとpasswordを取り出す

    // 認証処理
    if (Auth::attempt($userdata)) {
        return redirect()->route('top.show'); // topページにリダイレクト
    } else {
        return redirect()->route('login')
            ->withErrors(['email' => 'メールアドレスまたはパスワードが正しくありません。'])
            ->withInput(); // エラーと入力内容を持ってリダイレクト
    }
}


    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('login');
    }
}
