<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BotController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;
        $instance = $tenant->whatsappInstance;

        return view('dashboard.bot.index', [
            'tenant'   => $tenant,
            'instance' => $instance,
        ]);
    }
}