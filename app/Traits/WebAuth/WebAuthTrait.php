<?php

namespace App\Traits\WebAuth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Enums\RoleEnum;
use App\Models\Email;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

trait WebAuthTrait
{
    public function weblogin($request)
    {
        $validator = validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        // Allow debuggers only
        $email = Email::where('value', '=', $request->email)
            ->first();
        if (!$email) {
            return response()->json([
                'message' => __('app_translation.email_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        if ($validator->passes()) {
            if (Auth::guard('web')->attempt([
                'email_id' => $email->id,
                'password' => $request->password
            ], $request->get('remember'))) {
                $role =  Auth::user()->role_id;
                if ($role == RoleEnum::debugger->value) {
                    // return view('keygenerator.generatekey',['role'=>$role]);

                    return redirect()->route('master.dashboard')->with($role);
                    // return 

                } else {
                    Auth::guard('web')->logout();
                    return response()->json([
                        'message' => __('app_translation.unauthorized')
                    ], 403, [], JSON_UNESCAPED_UNICODE);
                }
            } else {
                return redirect()->route('web.login')->with(
                    'error',
                    'Email/Password is incorrect'
                );
            }
        } else {
            return redirect()->route('UserLogin')
                ->withErrors($validator)
                ->withInput($request->only('email'));
            // return "error";
        }
    }


    public function traitweblogout($request): RedirectResponse
    {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
