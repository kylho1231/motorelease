<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Motorcycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerPortalController extends Controller
{
    /**
     * Public cinematic homepage with 3D/motion design and Yamaha showcase
     */
    public function landing(): View
    {
        return view('portal.landing');
    }

    /**
     * Public tracking landing page
     */
    public function index(Request $request): View|RedirectResponse
    {
        $ref = $request->query('ref');

        // Check if customer already has a valid tracking session
        $sessionRef = session('customer_tracking_ref');
        if ($sessionRef && (!$ref || $sessionRef === $ref)) {
            $motorcycle = Motorcycle::with(['customer', 'branch', 'orcrRecord', 'licensePlateRecord'])
                ->where('tracking_reference', $sessionRef)
                ->first();

            if ($motorcycle) {
                return view('portal.status', compact('motorcycle'));
            }
        }

        return view('portal.index', compact('ref'));
    }

    /**
     * Verify customer tracking access
     * Requires: Tracking Reference + Last 4 digits of registered mobile phone
     */
    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tracking_reference' => ['required', 'string', 'max:50'],
            'pin' => ['required', 'string', 'digits:4'],
        ]);

        $throttleKey = 'portal_track|' . Str::lower($validated['tracking_reference']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'tracking_reference' => "Too many verification attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $motorcycle = Motorcycle::with(['customer', 'branch', 'orcrRecord', 'licensePlateRecord'])
            ->where('tracking_reference', trim($validated['tracking_reference']))
            ->first();

        if (!$motorcycle || !$motorcycle->customer) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'tracking_reference' => 'No active motorcycle record found matching this tracking reference.',
            ])->withInput();
        }

        // Compare pin against stored hash or last 4 digits of registered phone
        $customerPhone = preg_replace('/[^0-9]/', '', $motorcycle->customer->mobile_number);
        $expectedPin = substr($customerPhone, -4);

        $pinValid = false;
        if (!empty($motorcycle->tracking_pin_hash)) {
            $pinValid = Hash::check($validated['pin'], $motorcycle->tracking_pin_hash);
        }
        
        // Fallback to direct phone matching if pin was not hashed
        if (!$pinValid && $validated['pin'] === $expectedPin) {
            $pinValid = true;
            // Upgrade hash
            $motorcycle->update(['tracking_pin_hash' => Hash::make($expectedPin)]);
        }

        if (!$pinValid) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'pin' => 'Verification failed. The security PIN (last 4 digits of registered phone) is incorrect.',
            ])->withInput();
        }

        RateLimiter::clear($throttleKey);

        // Store secure token in session
        session([
            'customer_tracking_ref' => $motorcycle->tracking_reference,
            'customer_verified_at' => now()->timestamp,
        ]);

        AuditLog::log('PORTAL_ACCESS', "Customer checked status for motorcycle {$motorcycle->tracking_reference}");

        return redirect()->route('customer.status');
    }

    /**
     * Show authenticated status view
     */
    public function showStatus(): View|RedirectResponse
    {
        $sessionRef = session('customer_tracking_ref');
        if (!$sessionRef) {
            return redirect()->route('customer.track')->with('warning', 'Please enter your tracking reference to check document status.');
        }

        $motorcycle = Motorcycle::with([
            'customer',
            'branch',
            'orcrRecord.statusHistories',
            'licensePlateRecord.statusHistories',
        ])
        ->where('tracking_reference', $sessionRef)
        ->first();

        if (!$motorcycle) {
            session()->forget('customer_tracking_ref');
            return redirect()->route('customer.track')->withErrors(['tracking_reference' => 'Record not found.']);
        }

        return view('portal.status', compact('motorcycle'));
    }

    /**
     * Customer logout from portal session
     */
    public function logout(): RedirectResponse
    {
        session()->forget(['customer_tracking_ref', 'customer_verified_at']);
        return redirect()->route('customer.track')->with('status', 'Tracking session ended safely.');
    }
}
