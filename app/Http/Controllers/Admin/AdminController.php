<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $suspendedTenants = Tenant::where('status', 'suspended')->count();
        $pendingTenants = Tenant::where('status', 'pending')->count();

        $activeSubscriptions = Subscription::active()->count();
        $mrr = Subscription::active()->sum('amount');

        $recentPayments = Payment::with(['tenant', 'subscription'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentTenants = Tenant::with(['subscription', 'users'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $expiringSoon = Subscription::active()
            ->where('current_period_end', '<=', now()->addDays(7))
            ->where('current_period_end', '>=', now())
            ->with('tenant')
            ->get();

        return view('admin.index', compact(
            'totalTenants',
            'activeTenants',
            'suspendedTenants',
            'pendingTenants',
            'activeSubscriptions',
            'mrr',
            'recentPayments',
            'recentTenants',
            'expiringSoon'
        ));
    }
}
