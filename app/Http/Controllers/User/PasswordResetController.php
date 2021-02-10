<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Notifications\Password\PasswordResetRequest;
use App\Notifications\Password\PasswordResetSuccess;
use App\Models\User;
use App\Models\PasswordReset;
use App\Services\MyServices;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends ApiController
{

    public function __construct()
    {
        //$this->middleware('client.credentials')->except(['find']);
        $this->middleware('auth:api')->only(['resetfirst']);
    }
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function create(Request $request)
    {
        $request->validate([
            'username' => 'required',
        ]);
        $username = MyServices::getEncryptedString(strtoupper($request->username));
        $user = User::where('username', $username)->first();

        if (!$user)
            return $this->errorResponse("We can't find a user with that e-mail address.", 404);

        $passwordReset = PasswordReset::updateOrCreate(
                ['username' => $username],
                [
                    'username' => $username,
                    'token' => Str::random(60)
                ]
            );

            if ($user && $passwordReset)
                $user->notify(
                    new PasswordResetRequest($passwordReset->token)
                );
            return $this->showMessage("We have e-mailed your password reset link!");
    }

    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();

        if (!$passwordReset)
            return $this->errorResponse("This password reset token is invalid.", 404);

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return $this->errorResponse("This password reset token is invalid.", 404);
        }

        return $this->showOne($passwordReset);
    }

    /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|confirmed',
            'pin' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);

        //$username = MyServices::getEncryptedString(strtoupper($request->username));
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['username', $request->username]
        ])->first();
        //dd($passwordReset);
        if (!$passwordReset)
            return $this->errorResponse("This password reset token is invalid.", 404);

        $user = User::where('username', $passwordReset->username)->first();
        if (!$user)
            return $this->errorResponse("We can't find a user with that e-mail address.", 404);

        $user->password = Hash::make($request->password);
        $user->pin = $request->pin;
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess($passwordReset));
        return $this->showOne($user);
    }

    public function resetfirst(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|confirmed',
            'pin' => 'required|string|confirmed',
        ]);
        $user = User::where('username', $request->username)->first();
        if (!$user)
            return $this->errorResponse("We can't find a user with that e-mail address.", 404);

        $user->password = Hash::make($request->password);
        $user->pin = $request->pin;
        $user->is_first = 0;
        $user->save();
        return $this->showOne($user);
    }
}
