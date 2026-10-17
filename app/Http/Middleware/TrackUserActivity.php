<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if ($user) {
            $this->updateUserActivity($user, $request);
        }

        $response = $next($request);

        // Track when user is authenticated during request execution (e.g. login)
        if (! $user) {
            $user = $request->user();

            if (! $user && $response instanceof JsonResponse) {
                $data = $response->getData(true);
                if (isset($data['user']['id'])) {
                    $user = User::find($data['user']['id']);
                }
            }

            if ($user) {
                $this->updateUserActivity($user, $request);
            }
        }

        return $response;
    }

    protected function updateUserActivity(User $user, Request $request): void
    {
        $userAgent = $request->userAgent() ?? '';

        $isMobile = str_contains($userAgent, 'TrackMobileApp')
            || str_contains(strtolower($userAgent), 'dart')
            || str_contains(strtolower($userAgent), 'flutter')
            || $request->is('api/*')
            || $request->bearerToken() !== null;

        $user->forceFill([
            'last_client_used' => $isMobile
                ? 'flutter-mobile'
                : 'vue-web',
            'last_login_at' => now(),
        ])->save();
    }
}

