<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;


class SiteController extends Controller
{
    /**
     * SiteController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth')->except([
            'showLoginPage',
            'login'
        ]);

        $this->middleware('guest')->only([
            'showLoginPage',
            'login'
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginPage()
    {
        return view('login');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make(['username' => $request->input('username')],
            ['username' => 'required|string|max:25']);

        if ($validator->fails()) {
            return $this->sendFailedLoginResponse($request, $validator->errors());
        }

        $this->loginUser($request);

        return redirect()->to('/chat');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

    /**
     * @param Request $request
     */
    protected function loginUser(Request $request)
    {
        $username = $request->input('username');

        Auth::login(new User([
            'id' => $request->session()->getId(),
            'username' => $username,
        ]));

        session()->put([
            'username' => $username,
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request, $errors)
    {
        return redirect()->back()
            ->withInput($request->only('username'))
            ->withErrors($errors);
    }
}
