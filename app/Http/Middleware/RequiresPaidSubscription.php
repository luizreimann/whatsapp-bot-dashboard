<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiresPaidSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $tenant = $user->tenant;
        $subscription = $tenant?->subscription;

        // Permite acesso à página de checkout
        if ($request->routeIs('checkout.*')) {
            return $next($request);
        }

        // Bloqueia se não tiver assinatura
        if (!$subscription) {
            return redirect()->route('checkout.index', $subscription ?? 0)
                ->with('error', 'Você precisa ter uma assinatura ativa para acessar a plataforma.');
        }

        // Bloqueia se assinatura não estiver ativa (pending, suspended, expired, canceled)
        if ($subscription->status !== 'active') {
            return redirect()->route('checkout.index', $subscription)
                ->with('warning', 'Sua assinatura está ' . $subscription->status . '. Complete o pagamento para continuar.');
        }

        return $next($request);
    }
}
