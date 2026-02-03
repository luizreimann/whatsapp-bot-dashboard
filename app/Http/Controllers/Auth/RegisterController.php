<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $tenant = Tenant::create([
                'name' => $validated['company_name'],
                'slug' => Str::slug($validated['company_name']) . '-' . Str::random(6),
                'status' => 'pending',
            ]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'admin',
                'is_admin' => false,
            ]);

            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'status' => 'pending',
                'amount' => 297.00,
                'currency' => 'BRL',
                'billing_cycle' => 'monthly',
            ]);

            DB::commit();

            // Fazer login automático
            auth()->login($user);

            // Redirecionar para checkout
            return redirect()->route('checkout.index', $subscription)
                ->with('success', 'Conta criada com sucesso! Complete o pagamento para ativar sua assinatura.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar conta. Tente novamente.']);
        }
    }
}
