<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\BookingLock;
use App\Models\Instrument;
use App\Models\Queue;
use App\Models\User;
use App\Services\EmailService;
use App\Services\QueueService;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WebController extends Controller
{
    public function home(Request $request): View
    {
        $payload = $this->buildInstrumentListingPayload($request, null, 12);

        return view('web.home', [
            'instruments' => $payload['instruments'],
            'categories' => $payload['categories'],
            'locations' => $payload['locations'],
            'filters' => $payload['filters'],
            'totalInstruments' => $payload['total'],
        ]);
    }

    public function instruments(Request $request): View
    {
        $payload = $this->buildInstrumentListingPayload($request, null, 12);

        return view('web.instruments', [
            'instruments' => $payload['instruments'],
            'categories' => $payload['categories'],
            'locations' => $payload['locations'],
            'filters' => $payload['filters'],
            'totalInstruments' => $payload['total'],
            'currentCategory' => null,
        ]);
    }

    public function category(Request $request, string $name): View
    {
        $payload = $this->buildInstrumentListingPayload($request, $name, 12);

        return view('web.instruments', [
            'instruments' => $payload['instruments'],
            'categories' => $payload['categories'],
            'locations' => $payload['locations'],
            'filters' => $payload['filters'],
            'totalInstruments' => $payload['total'],
            'currentCategory' => $name,
        ]);
    }

    public function instrument(string $id): View
    {
        $instrument = Instrument::findOrFail($id);
        $queue = Queue::where('instrument_id', $id)
            ->orderBy('queue_position')
            ->limit(10)
            ->get();

        $relatedInstruments = Instrument::query()
            ->where('id', '!=', $instrument->id)
            ->where('category', $instrument->category)
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('web.instrument', [
            'instrument' => $instrument,
            'queue' => $queue,
            'relatedInstruments' => $relatedInstruments,
        ]);
    }

    public function instrumentGuidelines(string $id)
    {
        $instrument = Instrument::findOrFail($id);

        $content = implode("\n", [
            'ISTEM Instrument Usage Guidelines',
            '--------------------------------',
            'Instrument: ' . $instrument->name,
            'Category: ' . ($instrument->category ?: 'General'),
            'Location: ' . ($instrument->location ?: 'Main Lab'),
            '',
            '1) Wear proper safety equipment before operation.',
            '2) Follow approved booking slots and handover protocol.',
            '3) Report any issue immediately to lab admin.',
            '4) Leave instrument in clean and standby-ready state.',
        ]);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . Str::slug($instrument->name, '-') . '-usage-guidelines.txt"',
        ]);
    }

    public function addToBag(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date|after_or_equal:today',
        ]);

        $instrument = Instrument::findOrFail($id);

        $bag = $request->session()->get('web_bag', []);
        $bag[$id] = [
            'instrument_id' => $instrument->id,
            'instrument_name' => $instrument->name,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ];

        $request->session()->put('web_bag', $bag);

        return redirect()->route('web.bag')->with('success', 'Instrument added to booking bag.');
    }

    public function bag(Request $request): View
    {
        $bag = array_values($request->session()->get('web_bag', []));

        return view('web.bag', [
            'bag' => $bag,
        ]);
    }

    public function removeFromBag(Request $request, string $id): RedirectResponse
    {
        $bag = $request->session()->get('web_bag', []);
        unset($bag[$id]);
        $request->session()->put('web_bag', $bag);

        return back()->with('success', 'Item removed from bag.');
    }

    public function bookingForm(Request $request): View|RedirectResponse
    {
        $bag = array_values($request->session()->get('web_bag', []));

        if (count($bag) === 0) {
            return redirect()->route('web.home')->with('error', 'Your booking bag is empty.');
        }

        return view('web.booking-form', [
            'bag' => $bag,
            'user' => $this->webUser($request),
        ]);
    }

    public function submitBooking(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'user_type' => 'required|in:student,employee',
            'identifier' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'program_or_school' => 'required|string|max:255',
            'project_title' => 'nullable|string|max:255',
            'confidential_project' => 'nullable|boolean',
        ]);

        $bag = array_values($request->session()->get('web_bag', []));

        if (count($bag) === 0) {
            return redirect()->route('web.home')->with('error', 'Your booking bag is empty.');
        }

        BookingLock::where('expires_at', '<', now())->delete();

        $created = [];
        foreach ($bag as $item) {
            if (SlotService::hasConflict($item['instrument_id'], $item['start_date'], $item['end_date'], $validated['email'])) {
                return back()->withInput()->with('error', 'One or more selected slots are unavailable.');
            }

            $created[] = Booking::create([
                'id' => uniqid('B'),
                'instrument_id' => $item['instrument_id'],
                'name' => $validated['name'],
                'user_email' => $validated['email'],
                'email' => $validated['email'],
                'start_date' => $item['start_date'],
                'end_date' => $item['end_date'],
                'user_type' => $validated['user_type'],
                'identifier' => $validated['identifier'],
                'phone' => $validated['phone'] ?? null,
                'department' => $validated['department'],
                'program_or_school' => $validated['program_or_school'],
                'project_title' => $validated['project_title'] ?? 'N/A',
                'confidential_project' => (bool) ($validated['confidential_project'] ?? false),
                'status' => 'pending',
            ]);
        }

        $request->session()->forget('web_bag');

        return redirect()->route('web.booking.confirmation', ['id' => $created[0]->id])
            ->with('success', 'Booking request submitted successfully.');
    }

    public function bookingConfirmation(string $id): View
    {
        $booking = Booking::with('instrument')->findOrFail($id);

        return view('web.booking-confirmation', [
            'booking' => $booking,
        ]);
    }

    public function loginForm(): View
    {
        return view('web.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return back()->withInput()->with('error', 'Invalid credentials.');
        }

        if (! $user->email_verified) {
            $this->sendOtpForUser($user);
            $request->session()->put('pending_otp_email', $user->email);

            return redirect()->route('web.verify-otp.form')->with('error', 'Email not verified. OTP sent again.');
        }

        $this->setWebUserSession($request, $user);

        return redirect()->route('web.home')->with('success', 'Logged in successfully.');
    }

    public function signupForm(): View
    {
        return view('web.auth.signup');
    }

    public function signup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'email_verified' => false,
            'api_token' => Str::random(60),
        ]);

        if (! $this->sendOtpForUser($user)) {
            return back()->withInput()->with('error', 'Failed to send OTP email.');
        }

        $request->session()->put('pending_otp_email', $user->email);

        return redirect()->route('web.verify-otp.form')->with('success', 'Signup successful. Please verify OTP.');
    }

    public function verifyOtpForm(Request $request): View
    {
        return view('web.auth.verify-otp', [
            'email' => $request->session()->get('pending_otp_email'),
        ]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        if (! $user->otp || ! $user->otp_expires_at || now()->gt($user->otp_expires_at)) {
            return back()->withInput()->with('error', 'OTP expired.');
        }

        if ($user->otp !== $validated['otp']) {
            return back()->withInput()->with('error', 'Invalid OTP.');
        }

        $user->otp = null;
        $user->otp_expires_at = null;
        $user->email_verified = true;
        $user->api_token = Str::random(60);
        $user->save();

        $request->session()->forget('pending_otp_email');
        $this->setWebUserSession($request, $user);

        return redirect()->route('web.home')->with('success', 'OTP verified. You are now logged in.');
    }

    public function forgotPasswordForm(): View
    {
        return view('web.auth.forgot-password');
    }

    public function forgotPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();
        if (! $this->sendOtpForUser($user)) {
            return back()->withInput()->with('error', 'Failed to send OTP email.');
        }

        $request->session()->put('password_reset_email', $user->email);

        return redirect()->route('web.verify-reset-otp.form')->with('success', 'OTP sent for password reset.');
    }

    public function verifyResetOtpForm(Request $request): View
    {
        return view('web.auth.verify-reset-otp', [
            'email' => $request->session()->get('password_reset_email'),
        ]);
    }

    public function verifyResetOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();
        if (! $user->otp || ! $user->otp_expires_at || now()->gt($user->otp_expires_at) || $user->otp !== $validated['otp']) {
            return back()->withInput()->with('error', 'Invalid or expired OTP.');
        }

        $request->session()->put('password_reset_verified_email', $user->email);

        return redirect()->route('web.reset-password.form')->with('success', 'OTP verified. Set your new password.');
    }

    public function resetPasswordForm(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get('password_reset_verified_email');
        if (! $email) {
            return redirect()->route('web.forgot-password.form')->with('error', 'Start password reset first.');
        }

        return view('web.auth.reset-password', [
            'email' => $email,
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $verifiedEmail = $request->session()->get('password_reset_verified_email');
        if ($verifiedEmail !== $validated['email']) {
            return back()->withInput()->with('error', 'Password reset session mismatch.');
        }

        $user = User::where('email', $validated['email'])->firstOrFail();
        $user->password = Hash::make($validated['password']);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        $request->session()->forget(['password_reset_email', 'password_reset_verified_email']);

        return redirect()->route('web.login')->with('success', 'Password reset successful. Please login.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'web_user_id',
            'web_user_name',
            'web_user_email',
            'web_bag',
            'web_favorites',
        ]);

        return redirect()->route('web.home')->with('success', 'Logged out.');
    }

    public function dashboard(Request $request): View
    {
        $user = $this->webUser($request);

        $bookings = Booking::with('instrument')
            ->where(function ($q) use ($user) {
                $q->where('email', $user->email)
                    ->orWhere('user_email', $user->email);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $queueItems = Queue::where('email', $user->email)->orderBy('created_at', 'desc')->get();

        return view('web.dashboard', [
            'bookings' => $bookings,
            'queueItems' => $queueItems,
            'totalBookings' => $bookings->count(),
            'activeBookings' => $bookings->where('status', 'approved')->count(),
            'rejectedBookings' => $bookings->where('status', 'rejected')->count(),
            'queueStatus' => $queueItems->whereIn('status', ['pending', 'approved'])->count(),
        ]);
    }

    public function profile(Request $request): View
    {
        return view('web.profile', [
            'user' => $this->webUser($request),
        ]);
    }

    public function exportMyBookings(Request $request)
    {
        $user = $this->webUser($request);

        $rows = Booking::with('instrument')
            ->where(function ($q) use ($user) {
                $q->where('email', $user->email)
                    ->orWhere('user_email', $user->email);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $lines = [
            'Booking ID,Instrument,Start Date,End Date,Status,Created At',
        ];

        foreach ($rows as $booking) {
            $line = [
                $booking->id,
                '"' . str_replace('"', '""', (string) ($booking->instrument?->name ?: $booking->instrument_id)) . '"',
                $booking->start_date,
                $booking->end_date,
                $booking->status,
                optional($booking->created_at)->format('Y-m-d H:i:s'),
            ];

            $lines[] = implode(',', $line);
        }

        $csv = implode("\n", $lines);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="my-booking-history.csv"',
        ]);
    }

    public function myBookings(Request $request): View
    {
        $user = $this->webUser($request);
        $bookings = Booking::with('instrument')
            ->where(function ($q) use ($user) {
                $q->where('email', $user->email)
                    ->orWhere('user_email', $user->email);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('web.my-bookings', [
            'bookings' => $bookings,
        ]);
    }

    public function favorites(Request $request): View
    {
        $favoriteIds = array_values($request->session()->get('web_favorites', []));
        $query = Instrument::query();

        if (count($favoriteIds)) {
            $query->whereIn('id', $favoriteIds);
        } else {
            $query->whereRaw('1 = 0');
        }

        $instruments = $query->orderBy('name')->paginate(9);

        return view('web.favorites', [
            'favorites' => $instruments,
        ]);
    }

    public function addToFavorites(Request $request, string $id): RedirectResponse
    {
        Instrument::findOrFail($id);

        $favorites = $request->session()->get('web_favorites', []);
        $favorites[$id] = $id;
        $request->session()->put('web_favorites', $favorites);

        return back()->with('success', 'Instrument added to favorites.');
    }

    public function removeFromFavorites(Request $request, string $id): RedirectResponse
    {
        $favorites = $request->session()->get('web_favorites', []);
        unset($favorites[$id]);
        $request->session()->put('web_favorites', $favorites);

        return back()->with('success', 'Instrument removed from favorites.');
    }

    public function queueStatus(Request $request): View
    {
        $user = $this->webUser($request);
        $queueItems = Queue::with('instrument')
            ->where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('web.queue-status', [
            'queueItems' => $queueItems,
        ]);
    }

    public function joinQueue(Request $request, string $instrumentId): RedirectResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date|after_or_equal:today',
        ]);

        $user = $this->webUser($request);

        $date = $validated['start_date'];
        $timeSlot = $validated['start_date'] === $validated['end_date']
            ? $validated['start_date']
            : $validated['start_date'] . ' → ' . $validated['end_date'];

        $exists = Queue::where('instrument_id', $instrumentId)
            ->where('date', $date)
            ->where('time_slot', $timeSlot)
            ->where(function ($q) use ($user) {
                $q->where('user_id', (string) $user->id)
                    ->orWhere('email', $user->email);
            })
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return back()->with('success', 'You are already in queue for this slot.');
        }

        QueueService::addToQueue(
            $instrumentId,
            $user->name,
            $user->email,
            (string) $user->id,
            $date,
            $timeSlot
        );

        return back()->with('success', 'You were added to the queue.');
    }

    public function adminLoginForm(): View
    {
        return view('web.admin.login');
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('username', $validated['username'])->first();
        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
            return back()->withInput()->with('error', 'Invalid admin credentials.');
        }

        $request->session()->put('web_admin_id', $admin->id);
        $request->session()->put('web_admin_username', $admin->username);

        return redirect()->route('web.admin.dashboard')->with('success', 'Admin login successful.');
    }

    public function adminSignupForm(): View
    {
        return view('web.admin.signup', [
            'adminExists' => Admin::count() > 0,
        ]);
    }

    public function adminSignup(Request $request): RedirectResponse
    {
        if (Admin::count() > 0) {
            return redirect()->route('web.admin.login')->with('error', 'Admin already exists.');
        }

        $validated = $request->validate([
            'username' => 'required|string|unique:admins,username',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Admin::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'api_token' => Str::random(60),
        ]);

        $request->session()->put('web_admin_id', $admin->id);
        $request->session()->put('web_admin_username', $admin->username);

        return redirect()->route('web.admin.dashboard')->with('success', 'Admin account created.');
    }

    public function adminLogout(Request $request): RedirectResponse
    {
        $request->session()->forget(['web_admin_id', 'web_admin_username']);

        return redirect()->route('web.admin.login')->with('success', 'Admin logged out.');
    }

    public function adminDashboard(): View
    {
        $users = User::count();
        $instruments = Instrument::count();
        $bookings = Booking::count();
        $pending = Booking::where('status', 'pending')->count();
        $approved = Booking::where('status', 'approved')->count();
        $rejected = Booking::where('status', 'rejected')->count();

        return view('web.admin.dashboard', compact('users', 'instruments', 'bookings', 'pending', 'approved', 'rejected'));
    }

    public function adminInstruments(Request $request): View
    {
        $location = trim((string) $request->query('location', ''));
        $availability = trim((string) $request->query('availability', 'all'));
        $status = trim((string) $request->query('status', 'all'));
        $search = trim((string) $request->query('search', ''));

        $query = Instrument::query();

        if ($location !== '') {
            $query->where('location', $location);
        }

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('category', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        if ($availability === 'available') {
            $query->whereDoesntHave('bookings', function ($q) {
                $q->where('status', 'approved')
                    ->whereDate('end_date', '>=', now());
            });
        } elseif ($availability === 'booked') {
            $query->whereHas('bookings', function ($q) {
                $q->where('status', 'approved')
                    ->whereDate('end_date', '>=', now());
            });
        }

        $instruments = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $locations = Instrument::query()
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        $statuses = ['available', 'active', 'booked', 'blocked', 'limited'];

        return view('web.admin.instruments', [
            'instruments' => $instruments,
            'locations' => $locations,
            'statuses' => $statuses,
            'filters' => [
                'location' => $location,
                'availability' => $availability,
                'status' => $status,
                'search' => $search,
            ],
        ]);
    }

    public function adminInstrumentBulkUpload(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bulk_upload' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $filePath = $validated['bulk_upload']->getRealPath();
        $handle = fopen($filePath, 'r');

        if (! $handle) {
            return back()->with('error', 'Unable to read uploaded CSV file.');
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);

            return back()->with('error', 'CSV file is empty.');
        }

        $keys = array_map(static fn ($value) => strtolower(trim((string) $value)), $header);
        $requiredKeys = ['name', 'category', 'location'];
        foreach ($requiredKeys as $requiredKey) {
            if (! in_array($requiredKey, $keys, true)) {
                fclose($handle);

                return back()->with('error', 'CSV must include headers: name, category, location.');
            }
        }

        $allowedStatuses = ['available', 'active', 'booked', 'blocked', 'limited'];
        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($keys, array_pad($row, count($keys), null));
            $name = trim((string) ($data['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $status = strtolower(trim((string) ($data['status'] ?? 'available')));
            if (! in_array($status, $allowedStatuses, true)) {
                $status = 'available';
            }

            Instrument::create([
                'id' => 'INS' . strtoupper(uniqid()),
                'name' => $name,
                'category' => trim((string) ($data['category'] ?? 'General')),
                'description' => trim((string) ($data['description'] ?? '')) ?: null,
                'location' => trim((string) ($data['location'] ?? '')),
                'usage_cost' => trim((string) ($data['usage_cost'] ?? '')) ?: null,
                'status' => $status,
            ]);

            $imported++;
        }

        fclose($handle);

        if ($imported === 0) {
            return back()->with('error', 'No valid rows were found in the CSV.');
        }

        return back()->with('success', "Imported {$imported} instruments from CSV.");
    }

    public function adminInstrumentStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'usage_cost' => 'nullable|string|max:255',
            'status' => 'nullable|in:available,active,booked,blocked,limited',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['id'] = 'INS' . strtoupper(uniqid());
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('instruments', 'public');
        }

        Instrument::create($validated);

        return back()->with('success', 'Instrument created successfully.');
    }

    public function adminInstrumentUpdate(Request $request, string $id): RedirectResponse
    {
        $instrument = Instrument::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'usage_cost' => 'nullable|string|max:255',
            'status' => 'nullable|in:available,active,booked,blocked,limited',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('instruments', 'public');
        }

        $instrument->update($validated);

        return back()->with('success', 'Instrument updated successfully.');
    }

    public function adminInstrumentDelete(string $id): RedirectResponse
    {
        Instrument::destroy($id);

        return back()->with('success', 'Instrument deleted successfully.');
    }

    public function adminBookings(): View
    {
        $bookings = Booking::with('instrument')->orderBy('created_at', 'desc')->paginate(12);

        return view('web.admin.bookings', [
            'bookings' => $bookings,
        ]);
    }

    public function adminExportBookings()
    {
        $rows = Booking::with('instrument')->orderBy('created_at', 'desc')->get();

        $lines = [
            'Booking ID,User Name,User Email,Instrument,Start Date,End Date,Status,Admin Comment,Rejection Reason',
        ];

        foreach ($rows as $booking) {
            $lines[] = implode(',', [
                $booking->id,
                '"' . str_replace('"', '""', (string) $booking->name) . '"',
                $booking->email,
                '"' . str_replace('"', '""', (string) ($booking->instrument?->name ?: $booking->instrument_id)) . '"',
                $booking->start_date,
                $booking->end_date,
                $booking->status,
                '"' . str_replace('"', '""', (string) ($booking->admin_comment ?? '')) . '"',
                '"' . str_replace('"', '""', (string) ($booking->rejection_reason ?? '')) . '"',
            ]);
        }

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="admin-bookings.csv"',
        ]);
    }

    public function approveBooking(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'admin_comment' => 'nullable|string|max:500',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->status = 'approved';
        $booking->admin_comment = $validated['admin_comment'] ?? null;
        $booking->rejection_reason = null;
        $booking->save();

        QueueService::processQueue($booking->instrument_id);

        return back()->with('success', 'Booking approved successfully.');
    }

    public function rejectBooking(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
            'admin_comment' => 'nullable|string|max:500',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->status = 'rejected';
        $booking->rejection_reason = $validated['rejection_reason'];
        $booking->admin_comment = $validated['admin_comment'] ?? null;
        $booking->save();

        return back()->with('success', 'Booking rejected successfully.');
    }

    public function adminQueue(): View
    {
        $queueItems = Queue::with('instrument')->orderBy('created_at', 'desc')->paginate(12);

        return view('web.admin.queue', [
            'queueItems' => $queueItems,
        ]);
    }

    public function approveQueue(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => 'required|string|exists:queues,id',
        ]);

        $queue = Queue::findOrFail($validated['id']);
        $queue->status = 'approved';
        $queue->save();

        return back()->with('success', 'Queue request approved successfully.');
    }

    public function rejectQueue(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => 'required|string|exists:queues,id',
        ]);

        $queue = Queue::findOrFail($validated['id']);
        $queue->status = 'rejected';
        $queue->save();

        return back()->with('success', 'Queue request rejected successfully.');
    }

    public function adminAnalytics(): View
    {
        $statusCounts = Booking::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $bookingsByStatus = collect(['pending', 'approved', 'rejected', 'completed'])
            ->mapWithKeys(fn ($status) => [$status => (int) ($statusCounts[$status] ?? 0)]);

        $totalBookings = (int) $bookingsByStatus->sum();

        $months = collect(range(5, 0))->map(function ($offset) {
            $date = Carbon::now()->subMonths($offset);

            return [
                'key' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
            ];
        })->values();

        $monthlyRaw = Booking::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, count(*) as total")
            ->where('created_at', '>=', Carbon::now()->startOfMonth()->subMonths(5))
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $monthlyTrend = $months->map(function ($month) use ($monthlyRaw) {
            return [
                'label' => $month['label'],
                'total' => (int) ($monthlyRaw[$month['key']] ?? 0),
            ];
        });

        $trendPeak = max(1, (int) $monthlyTrend->max('total'));

        $bookingsByInstrument = Booking::selectRaw('instrument_id, count(*) as total')
            ->groupBy('instrument_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('instrument:id,name')
            ->get();

        return view('web.admin.analytics', [
            'bookingsByStatus' => $bookingsByStatus,
            'totalBookings' => $totalBookings,
            'monthlyTrend' => $monthlyTrend,
            'trendPeak' => $trendPeak,
            'bookingsByInstrument' => $bookingsByInstrument,
        ]);
    }

    public function adminUsers(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $sort = trim((string) $request->query('sort', 'newest'));

        $query = User::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'name') {
            $query->orderBy('name');
        } elseif ($sort === 'oldest') {
            $query->orderBy('created_at');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(15)->withQueryString();

        return view('web.admin.users', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
            ],
        ]);
    }

    public function adminExportUsers()
    {
        $rows = User::query()->orderBy('created_at', 'desc')->get();

        $lines = [
            'User ID,Name,Email,Phone,Email Verified,Created At',
        ];

        foreach ($rows as $user) {
            $lines[] = implode(',', [
                $user->id,
                '"' . str_replace('"', '""', (string) $user->name) . '"',
                $user->email,
                '"' . str_replace('"', '""', (string) ($user->phone ?? '')) . '"',
                $user->email_verified ? 'Yes' : 'No',
                optional($user->created_at)->format('Y-m-d H:i:s'),
            ]);
        }

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="admin-users.csv"',
        ]);
    }

    private function buildInstrumentListingPayload(Request $request, ?string $category = null, int $perPage = 12): array
    {
        $search = trim((string) $request->query('search', ''));
        $location = trim((string) $request->query('location', ''));
        $availability = trim((string) $request->query('availability', 'all'));
        $status = trim((string) $request->query('status', 'all'));
        $minPrice = trim((string) $request->query('min_price', ''));
        $maxPrice = trim((string) $request->query('max_price', ''));
        $categoryInput = $request->query('category', []);
        if (! is_array($categoryInput)) {
            $categoryInput = trim((string) $categoryInput) !== '' ? [trim((string) $categoryInput)] : [];
        }

        $effectiveCategories = $category ? [$category] : array_values(array_filter(array_map('trim', $categoryInput), fn ($value) => $value !== ''));

        $query = Instrument::query();

        if (count($effectiveCategories) > 0) {
            $query->whereIn('category', $effectiveCategories);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('category', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        if ($location !== '') {
            $query->where('location', $location);
        }

        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($minPrice !== '' && is_numeric($minPrice)) {
            $query->whereRaw("CAST(COALESCE(NULLIF(usage_cost, ''), 0) AS DECIMAL(10,2)) >= ?", [(float) $minPrice]);
        }

        if ($maxPrice !== '' && is_numeric($maxPrice)) {
            $query->whereRaw("CAST(COALESCE(NULLIF(usage_cost, ''), 0) AS DECIMAL(10,2)) <= ?", [(float) $maxPrice]);
        }

        if ($availability === 'available') {
            $query->whereDoesntHave('bookings', function ($q) {
                $q->where('status', 'approved')
                    ->whereDate('end_date', '>=', now());
            });
        } elseif ($availability === 'booked') {
            $query->whereHas('bookings', function ($q) {
                $q->where('status', 'approved')
                    ->whereDate('end_date', '>=', now());
            });
        }

        $instruments = $query->orderBy('name')->paginate($perPage)->withQueryString();

        $categories = Instrument::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $locations = Instrument::query()
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        return [
            'instruments' => $instruments,
            'categories' => $categories,
            'locations' => $locations,
            'total' => $instruments->total(),
            'filters' => [
                'search' => $search,
                'location' => $location,
                'availability' => $availability,
                'status' => $status,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'category' => $effectiveCategories,
            ],
        ];
    }

    private function sendOtpForUser(User $user): bool
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        return EmailService::sendOTP($user->email, $otp);
    }

    private function setWebUserSession(Request $request, User $user): void
    {
        $request->session()->put('web_user_id', $user->id);
        $request->session()->put('web_user_name', $user->name);
        $request->session()->put('web_user_email', $user->email);
    }

    private function webUser(Request $request): User
    {
        return User::findOrFail($request->session()->get('web_user_id'));
    }
}
