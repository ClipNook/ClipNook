<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the main settings page with tabs.
     */
    public function index(Request $request): View
    {
        $user = $request->user()->load(['streamerProfile', 'cutterProfile']);

        return view('settings.index', [
            'user'      => $user,
            'activeTab' => $request->get('tab', 'profile'),
        ]);
    }
}
