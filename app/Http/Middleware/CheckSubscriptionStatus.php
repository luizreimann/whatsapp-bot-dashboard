<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $tenant = $user->tenant;
        $subscription = $tenant->subscription;

        if (!$subscription || !$subscription->isActive()) {
            return redirect()->route('subscription.expired')
                ->with('error', 'Sua assinatura está inativa. Por favor, regularize seu pagamento.');
        }

        return $next($request);
    }
}
