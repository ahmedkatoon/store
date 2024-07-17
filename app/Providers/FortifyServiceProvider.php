<?php

namespace App\Providers;

use App\Actions\Fortify\AuthenticateUSer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Contracts\LogoutResponse;
use App\Actions\Fortify\UpdateUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $request = request();
        if ($request->is("admin/*")) {
            Config::set('fortify.guard', "admin");
            Config::set('fortify.passwords', "admins");
            Config::set('fortify.prefix', "admin");
            // config::set("fortify.home","admin/dashboard");
        }
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse
        {
            public function toResponse($request)
            {
                if ($request->user("admin")) {

                    return redirect()->intended("admin/dashboard");
                }
                return redirect()->intended('/');
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
        if (config::get("fortify.guard") == "admin") {
            Fortify::authenticateUsing([new AuthenticateUSer, "authenticate"]);
            Fortify::viewPrefix("auth.");
        } else {
            Fortify::viewPrefix("front.auth.");
        }
        // Fortify::viewPrefix("auth.");

        //     Fortify::loginView("auth.login");
        //     // Fortify::forgotPasswordView("auth.forgot-password");
        //     Fortify::registerView("auth.register");
    }
}
