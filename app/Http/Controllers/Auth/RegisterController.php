<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Subscription;
use App\Rules\CpfRule;
use App\Rules\CnpjRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Etapa 1 — Exibe formulário de dados pessoais
     */
    public function showStep1()
    {
        return view('auth.register.step1');
    }

    /**
     * Etapa 1 — Processa dados pessoais e salva na session
     */
    public function processStep1(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
            'document' => ['nullable', 'string', 'max:20', new CpfRule],
        ]);

        // Limpar máscaras antes de guardar
        if (!empty($validated['phone'])) {
            $validated['phone'] = preg_replace('/\D/', '', $validated['phone']);
        }
        if (!empty($validated['document'])) {
            $validated['document'] = preg_replace('/\D/', '', $validated['document']);
        }

        // Guardar hash da senha na session (nunca texto puro)
        $validated['password'] = Hash::make($validated['password']);

        $request->session()->put('onboarding', $validated);

        return redirect()->route('register.company');
    }

    /**
     * Etapa 2 — Exibe formulário de dados da empresa (opcional)
     */
    public function showStep2(Request $request)
    {
        if (!$request->session()->has('onboarding')) {
            return redirect()->route('register');
        }

        return view('auth.register.step2');
    }

    /**
     * Etapa 2 — Processa dados da empresa, cria tudo no banco
     */
    public function processStep2(Request $request)
    {
        $onboarding = $request->session()->get('onboarding');

        if (!$onboarding) {
            return redirect()->route('register');
        }

        $validated = $request->validate([
            'company_name'  => 'nullable|string|max:255',
            'cnpj'          => ['nullable', 'string', 'max:20', new CnpjRule],
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'segment'       => 'nullable|string|max:100',
            'zip'           => 'nullable|string|max:10',
            'street'        => 'nullable|string|max:255',
            'number'        => 'nullable|string|max:20',
            'complement'    => 'nullable|string|max:255',
            'neighborhood'  => 'nullable|string|max:255',
            'city'          => 'nullable|string|max:255',
            'state'         => 'nullable|string|max:2',
        ]);

        // Limpar máscaras
        if (!empty($validated['cnpj'])) {
            $validated['cnpj'] = preg_replace('/\D/', '', $validated['cnpj']);
        }
        if (!empty($validated['company_phone'])) {
            $validated['company_phone'] = preg_replace('/\D/', '', $validated['company_phone']);
        }
        if (!empty($validated['zip'])) {
            $validated['zip'] = preg_replace('/\D/', '', $validated['zip']);
        }

        try {
            DB::beginTransaction();

            // Tenant — nome vem do usuário (etapa 1)
            $tenant = Tenant::create([
                'name'   => $onboarding['name'],
                'slug'   => Str::slug($onboarding['name']) . '-' . Str::random(6),
                'status' => 'pending',
            ]);

            // User
            $user = User::create([
                'tenant_id'     => $tenant->id,
                'name'          => $onboarding['name'],
                'email'         => $onboarding['email'],
                'password'      => $onboarding['password'], // já é hash
                'phone'         => $onboarding['phone'] ?? null,
                'document'      => $onboarding['document'] ?? null,
                'document_type' => !empty($onboarding['document']) ? 'cpf' : null,
                'role'          => 'admin',
                'is_admin'      => false,
            ]);

            // Company (somente se algum dado foi preenchido)
            $hasCompanyData = !empty($validated['company_name'])
                || !empty($validated['cnpj'])
                || !empty($validated['company_phone'])
                || !empty($validated['company_email'])
                || !empty($validated['segment']);

            if ($hasCompanyData) {
                $address = null;
                if (!empty($validated['zip'])) {
                    $address = array_filter([
                        'zip'          => $validated['zip'],
                        'street'       => $validated['street'] ?? null,
                        'number'       => $validated['number'] ?? null,
                        'complement'   => $validated['complement'] ?? null,
                        'neighborhood' => $validated['neighborhood'] ?? null,
                        'city'         => $validated['city'] ?? null,
                        'state'        => $validated['state'] ?? null,
                    ]);
                }

                Company::create([
                    'tenant_id'     => $tenant->id,
                    'name'          => $validated['company_name'] ?? $onboarding['name'],
                    'document'      => $validated['cnpj'] ?? null,
                    'document_type' => 'cnpj',
                    'phone'         => $validated['company_phone'] ?? null,
                    'email'         => $validated['company_email'] ?? null,
                    'segment'       => $validated['segment'] ?? null,
                    'address'       => $address,
                ]);
            }

            // Subscription
            $subscription = Subscription::create([
                'tenant_id'     => $tenant->id,
                'status'        => 'pending',
                'amount'        => 297.00,
                'currency'      => 'BRL',
                'billing_cycle' => 'monthly',
            ]);

            DB::commit();

            // Limpar session de onboarding
            $request->session()->forget('onboarding');

            // Login automático
            auth()->login($user);

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
