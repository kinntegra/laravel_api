<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        // User::created(function($user) use ($request){
        //     // $user->created_by = $request->auth()->id;
        //     // $user->updated_by = $request->auth()->id;
        //     // $user->update();
        // });

        // User::created(function($user) use ($request){
        //     $user->updated_by = $request->auth()->id;
        //     $user->update();
        // });
    }
}
