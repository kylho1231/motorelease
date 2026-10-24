<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\NotificationTemplate;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchAndSettingsController extends Controller
{
    /**
     * Branches management
     */
    public function branchIndex(): View
    {
        $branches = Branch::withCount(['users', 'customers', 'motorcycles'])->get();
        return view('settings.branches', compact('branches'));
    }

    public function branchStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:branches,code'],
            'name' => ['required', 'string', 'max:150'],
            'dealership_brand' => ['required', 'string', 'max:100'],
            'address_line' => ['required', 'string', 'max:255'],
            'municipality' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'contact_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],
            'operating_hours' => ['required', 'string', 'max:150'],
            'pickup_instructions' => ['nullable', 'string'],
        ]);

        $branch = Branch::create(array_merge($validated, ['is_active' => true]));

        AuditLog::log('BRANCH_CREATED', "Created branch {$branch->name} ({$branch->code})", $branch);

        return back()->with('success', "Branch {$branch->name} added successfully.");
    }

    public function branchUpdate(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'dealership_brand' => ['required', 'string', 'max:100'],
            'address_line' => ['required', 'string', 'max:255'],
            'municipality' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'contact_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],
            'operating_hours' => ['required', 'string', 'max:150'],
            'pickup_instructions' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $branch->update(array_merge($validated, [
            'is_active' => $request->boolean('is_active'),
        ]));

        AuditLog::log('BRANCH_UPDATED', "Updated branch {$branch->name}", $branch);

        return back()->with('success', "Branch {$branch->name} updated successfully.");
    }

    /**
     * System Settings & SMS Templates management
     */
    public function settingsIndex(): View
    {
        $templates = NotificationTemplate::all();
        $settings = SystemSetting::all()->keyBy('key');

        return view('settings.index', compact('templates', 'settings'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $inputs = $request->except(['_token', 'templates']);

        foreach ($inputs as $key => $val) {
            SystemSetting::set($key, $val);
        }

        AuditLog::log('SETTINGS_UPDATED', 'Updated system-wide configuration settings.');

        return back()->with('success', 'System settings saved successfully.');
    }

    public function updateTemplate(Request $request, NotificationTemplate $template): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'body_template' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $template->update([
            'name' => $validated['name'],
            'body_template' => $validated['body_template'],
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLog::log('TEMPLATE_UPDATED', "Updated notification template {$template->code}", $template);

        return back()->with('success', "Template {$template->code} updated successfully.");
    }

    /**
     * Audit Logs Explorer
     */
    public function auditLogs(Request $request): View
    {
        $action = $request->input('action');
        $search = $request->input('search');

        $logs = AuditLog::with('user')
            ->when($action, fn($q) => $q->where('action', $action))
            ->when($search, function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $actions = AuditLog::select('action')->distinct()->pluck('action');

        return view('settings.audit_logs', compact('logs', 'actions', 'action', 'search'));
    }
}
