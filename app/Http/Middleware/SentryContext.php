<?php
/**
 * Copyright (C) MUNPANEL
 * This file is part of MUNPANEL System.
 *
 * Open-sourced under AGPL v3 License.
 */

namespace App\Http\Middleware;

use Closure;
use Sentry\State\Hub;
use Sentry\State\Scope;

class SentryContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (app()->bound('sentry')) {
            /** @var \Raven_Client $sentry */
            $sentry = app('sentry');

            // Add user context
            $user = $request->user();
            if (null !== $user) {
                $sentry->configureScope(function (Scope $scope) use ($user, $request): void {
                    $scope->setUser(['id' => $user->id, 'username' => $user->name, 'email' => $user->email, 'ip_address' => $request->ip()]);
                }); 
            } else {
                $sentry->configureScope(function (Scope $scope): void {
                    $scope->setUser(['id' => null]);
                }); 
            }

            // Add tags context
            $conferenceID = \App\Reg::currentConferenceID();
            $sentry->configureScope(function (Scope $scope) use ($conferenceID): void {
                $scope->setTag('conference_id', $conferenceID);
            });
        }
    return $next($request);
    }
}
