<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

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