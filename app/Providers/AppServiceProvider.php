<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configurePasswordResetLinks();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Configure dynamic password reset URLs for Web (Vue) and Mobile (Flutter).
     */
    protected function configurePasswordResetLinks(): void
    {
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            // Use the global request() helper instead of Request::input()
            $client = request('client', 'web');
            $email = urlencode($notifiable->getEmailForPasswordReset());

            if ($client === 'mobile') {
                // Returns your custom mobile deep link scheme for Flutter
                return "http://localhost:31785/#/reset-password?token={$token}&email={$email}";
            }

            // Default fallback URL for your Vue web app
            return "http://localhost:8000/reset-password/{$token}?email={$email}";
        });
    }
}