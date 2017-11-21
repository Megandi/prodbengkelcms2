<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        \Validator::extend('email_valid', function($attribute, $value, $parameters, $validator) {
            
            // $messages = [
            //     'email_valid' => 'The :attribute field is not valid.',
            // ];

            // $validator = Validator::make(['email'=>$value], $rules, $messages);
            
            if (!filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                if(!preg_match('/[^a-zA-Z0-9._@]/i', $value)){
              //echo("$email is a valid email address");
                     return true;
                }else{
                    return false;
                }
            } else {
                return false;
              // echo("$email is not a valid email address");
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
