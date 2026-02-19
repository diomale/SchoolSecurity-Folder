<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\OutsideUser;

class OutsideUserController extends Controller
{
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
