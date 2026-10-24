<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TenantContextController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        if ($request->filled('tenant_id')) {
            session(['tenant_context_id' => (int) $request->tenant_id]);
        } else {
            session()->forget('tenant_context_id');
        }

        return back();
    }
}