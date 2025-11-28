<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Lead;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        // Placeholder
        $contactsInitiated = 12;
        $journeysDropped   = 4;
        $leadsCollected    = Lead::where('tenant_id', $tenant->id)->count();

        $whatsappInstance = $tenant->whatsappInstance ?? null;

        return view('dashboard.index', [
            'tenant'            => $tenant,
            'whatsappInstance'  => $whatsappInstance,
            'contactsInitiated' => $contactsInitiated,
            'journeysDropped'   => $journeysDropped,
            'leadsCollected'    => $leadsCollected,
        ]);
    }


    public function logout(Request $request)
    {
        $token = $request->query('token');

        if (! hash_equals(csrf_token(), (string) $token)) {
            abort(419, 'Token inválido.');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
