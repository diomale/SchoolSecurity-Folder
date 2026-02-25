<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OutsideUser;

class OutsideUserController extends Controller
{

    public function dashboard()
    {
        return view('OutsideUser.dashboard');
    }

    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1 
        ];

        if (Auth::guard('outsideuser')->attempt($credentials)) {
            $request->session()->regenerate(); 
            return redirect()->intended(route('outsider.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function ShowLogin()
    {
        return view('OutsideUser.login');
    }

    public function showSignup()
    {
        return view('OutsideUser.Signup');
    }

    public function SignupRequest(Request $request)
    {
        $validated = $request->validate([
            'first_name'           => 'required|string|max:150',
            'last_name'            => 'required|string|max:150',
            'email'                => 'required|string|email|max:155|unique:mysql_second.outside_user,email',
            'phone_number'         => 'required|string|max:20',
            'password'             => 'required|string|min:8|confirmed',
            'g-recaptcha-response' => 'required',
        ]);

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        
        if ($response->failed() || !$response->json('success')) {
            return back()
                ->withErrors(['captcha' => 'Captcha verification failed. Please try again.'])
                ->withInput();
        }

        OutsideUser::create([
            'first_name'   => $validated['first_name'],
            'last_name'    => $validated['last_name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password'     => $validated['password'],
            'status'       => OutsideUser::STATUS_PENDING,
        ]);

        return redirect()->route('welcome')
            ->with('success', 'Account created! Please wait for approval.');
    }
}
