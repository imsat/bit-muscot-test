<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\VerificationCodeMail;
use App\Services\LoginService;
use App\Services\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    protected $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    /**
     * Show the application's register form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function showRegisterForm()
    {
        return view('pages.auth.register');
    }

    public function register(RegisterRequest $request)
    {
        try {
            $isRegister = $this->registerService->register($request);
            if ($isRegister) {
                return redirect()->route('email-verification');
            }
            return redirect()->back()->with('error', 'Something went wrong');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage() ?? 'Something went wrong!');
        }
    }

    public function checkEmailUnique(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'success' => false], 400);
        }
        return response()->json(['message' => 'Everything is ok.', 'success' => true]);
    }
}
