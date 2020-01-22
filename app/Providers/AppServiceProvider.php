<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

use Validator;
use App\Helper\RouteChecker;
use App\Company;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Fix for SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long; max key length is 767 bytes
         * 
         * @link https://laravel-news.com/laravel-5-4-key-too-long-error
         */
        Schema::defaultStringLength(191); 


        /**
         * Custom Validators
         */
        Validator::extend('alpha_space', 'App\Validators\CustomValidator@validateAlphaSpaces');

        


        /**
         * Set in global variables
         */
        View::composer('*', function ($view) {
            $words = explode(" ", config('app.name'));
            $acronym = "";

            foreach ($words as $w) {
              $acronym .= $w[0];
            }

            View::share('shortTitle', $acronym);

            /* Add in the needed vars if logged in */
            if(\Auth::check()) {

                $user = \Auth::user();

                View::share('self', $user);

                /* Add in notification counts */
                View::share('notifCount', $user->unreadNotifications()->count());
                View::share('subordinateCount', $user->getSubordinateCount());
                View::share('approvalCount', $user->getRequestApprovalCount());
                View::share('updateApprovalCount', $user->getRequestUpdateApprovalCount());
                View::share('idpCount', $user->getIDPApprovalCount());
                View::share('eventCount', $user->getEventApprovalCount());
                View::share('ticketCount', $user->getTicketApprovalCount());

                View::share('isUserTechnician', in_array($user->id, Company::getTechnicianIds()));
            }

            /* Add in the public vars */
            $route = \Request::route();

            if($route) {
                View::share('route', $route->getName());
                View::share('checker', new RouteChecker($route));
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
