<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Instrument;
use App\Models\Queue;
use App\Models\User;
use App\Services\EmailService;
use App\Services\QueueService;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AjaxController extends Controller
{
    public function bootstrap(Request $request): JsonResponse
    {
        $user = $this->sessionUser($request);
        $locations = Instrument::query()
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->distinct()
            ->orderBy('location')
            ->pluck('location')
            ->values();

        $notificationCount = 0;
        if ($user) {
            $notificationCount = $this->notificationItems($user->email, $request)->where('read', false)->count();
        }

        return $this->ok([
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ] : null,
            'counts' => [
                'bag' => count($request->session()->get('web_bag', [])),
                'favorites' => count($request->session()->get('web_favorites', [])),
                'notifications' => $notificationCount,
            ],
            'locations' => $locations,
        ]);
    }

    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        if ($query === '') {
            return $this->ok(['suggestions' => []]);
        }

        $suggestions = collect();

        $names = Instrument::query()
            ->where('name', 'like', '%' . $query . '%')
            ->orderBy('name')
            ->limit(8)
            ->pluck('name');

        $categories = Instrument::query()
            ->where('category', 'like', '%' . $query . '%')
            ->orderBy('category')
            ->limit(6)
            ->pluck('category');

        $locations = Instrument::query()
            ->where('location', 'like', '%' . $query . '%')
            ->orderBy('location')
            ->limit(6)
            ->pluck('location');

        $suggestions = $suggestions
            ->merge($names)
            ->merge($categories)
            ->merge($locations)
            ->filter(fn ($value) => trim((string) $value) !== '')
            ->unique()
            ->take(12)
            ->values();

        return $this->ok(['suggestions' => $suggestions]);
    }

    public function authLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return $this->fail('Invalid credentials.', null, 401);
        }

        if (! $user->email_verified) {
            return $this->fail('Email not verified. Please complete OTP verification.', [
                'email' => $user->email,
                'otpRequired' => true,
            ], 403);
        }

        $user->api_token = Str::random(60);
        $user->save();

        $request->session()->put('web_user_id', $user->id);
        $request->session()->put('web_user_name', $user->name);
        $request->session()->put('web_user_email', $user->email);

        return $this->ok([
            'token' => $user->api_token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ], 'Login successful.');
    }

    public function authSignup(Request $request): JsonResponse
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

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();
        $request->session()->put('pending_otp_email', $user->email);
        EmailService::sendOTP($user->email, $otp);

        return $this->ok([
            'email' => $user->email,
            'otpRequired' => true,
        ], 'Signup successful. OTP sent to email.');
    }

    public function authLogout(Request $request): JsonResponse
    {
        $request->session()->forget([
            'web_user_id',
            'web_user_name',
            'web_user_email',
        ]);

        return $this->ok(null, 'Logged out successfully.');
    }

    public function authMe(Request $request): JsonResponse
    {
        $user = $this->sessionUser($request);
        if (! $user) {
            return $this->fail('Unauthenticated.', null, 401);
        }

        return $this->ok([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ]);
    }

    public function instruments(Request $request): JsonResponse
    {
        $query = Instrument::query();

        $search = trim((string) $request->query('search', ''));
        $location = trim((string) $request->query('location', ''));
        $availability = trim((string) $request->query('availability', 'all'));
        $status = trim((string) $request->query('status', 'all'));
        $sort = trim((string) $request->query('sort', 'name_asc'));
        $minPrice = trim((string) $request->query('min_price', ''));
        $maxPrice = trim((string) $request->query('max_price', ''));
        $categoryInput = $request->query('category', []);

        if (! is_array($categoryInput)) {
            $categoryInput = trim((string) $categoryInput) !== '' ? [trim((string) $categoryInput)] : [];
        }
        $categories = array_values(array_filter(array_map('trim', $categoryInput), fn ($value) => $value !== ''));

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

        if (count($categories) > 0) {
            $query->whereIn('category', $categories);
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
                $q->where('status', 'approved')->whereDate('end_date', '>=', now());
            });
        } elseif ($availability === 'booked') {
            $query->whereHas('bookings', function ($q) {
                $q->where('status', 'approved')->whereDate('end_date', '>=', now());
            });
        }

        if ($sort === 'price_asc') {
            $query->orderByRaw("CAST(COALESCE(NULLIF(usage_cost, ''), 0) AS DECIMAL(10,2)) ASC");
        } elseif ($sort === 'price_desc') {
            $query->orderByRaw("CAST(COALESCE(NULLIF(usage_cost, ''), 0) AS DECIMAL(10,2)) DESC");
        } elseif ($sort === 'newest') {
            $query->orderByDesc('created_at');
        } else {
            $query->orderBy('name');
        }

        $perPage = max(1, min(24, (int) $request->query('per_page', 9)));
        $list = $query->paginate($perPage);

        $items = $list->getCollection()->map(function ($instrument) {
            return [
                'id' => $instrument->id,
                'name' => $instrument->name,
                'description' => $instrument->description,
                'category' => $instrument->category,
                'location' => $instrument->location,
                'status' => $instrument->status,
                'usage_cost' => $instrument->usage_cost,
                'image_url' => $instrument->image_url,
                'is_available' => $instrument->is_available,
            ];
        });

        return $this->ok([
            'items' => $items,
            'pagination' => [
                'current_page' => $list->currentPage(),
                'last_page' => $list->lastPage(),
                'per_page' => $list->perPage(),
                'total' => $list->total(),
            ],
        ]);
    }

    public function instrument(string $id): JsonResponse
    {
        $instrument = Instrument::with(['bookings', 'queues'])->findOrFail($id);
        $comments = Booking::query()
            ->where('instrument_id', $id)
            ->whereNotNull('admin_comment')
            ->where('admin_comment', '!=', '')
            ->orderByDesc('created_at')
            ->limit(5)
            ->pluck('admin_comment');

        return $this->ok([
            'id' => $instrument->id,
            'name' => $instrument->name,
            'description' => $instrument->description,
            'category' => $instrument->category,
            'location' => $instrument->location,
            'status' => $instrument->status,
            'usage_cost' => $instrument->usage_cost,
            'image_url' => $instrument->image_url,
            'is_available' => $instrument->is_available,
            'admin_comments' => $comments,
        ]);
    }

    public function relatedInstruments(string $id): JsonResponse
    {
        $instrument = Instrument::findOrFail($id);
        $related = Instrument::query()
            ->where('id', '!=', $id)
            ->where('category', $instrument->category)
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'category', 'location', 'usage_cost', 'status', 'image']);

        return $this->ok($related);
    }

    public function instrumentAvailability(Request $request, string $id): JsonResponse
    {
        $instrument = Instrument::findOrFail($id);
        $ranges = Booking::query()
            ->where('instrument_id', $id)
            ->where('status', 'approved')
            ->orderBy('start_date')
            ->get(['start_date', 'end_date']);

        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $availability = null;
        if (! empty($validated['start_date']) && ! empty($validated['end_date'])) {
            $hasConflict = SlotService::hasConflict($instrument->id, $validated['start_date'], $validated['end_date']);
            $availability = ! $hasConflict;
        }

        return $this->ok([
            'unavailable_ranges' => $ranges,
            'available' => $availability,
        ]);
    }

    public function bagList(Request $request): JsonResponse
    {
        $bag = array_values($request->session()->get('web_bag', []));

        return $this->ok([
            'items' => $bag,
            'count' => count($bag),
        ]);
    }

    public function bagAdd(Request $request, string $id): JsonResponse
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

        return $this->ok([
            'count' => count($bag),
            'item' => $bag[$id],
        ], 'Added to bag.');
    }

    public function bagRemove(Request $request, string $id): JsonResponse
    {
        $bag = $request->session()->get('web_bag', []);
        unset($bag[$id]);
        $request->session()->put('web_bag', $bag);

        return $this->ok([
            'count' => count($bag),
        ], 'Removed from bag.');
    }

    public function favoritesList(Request $request): JsonResponse
    {
        $favoriteIds = array_values($request->session()->get('web_favorites', []));
        $items = Instrument::query()
            ->whereIn('id', $favoriteIds)
            ->orderBy('name')
            ->get();

        return $this->ok([
            'items' => $items,
            'count' => count($favoriteIds),
        ]);
    }

    public function favoritesToggle(Request $request, string $id): JsonResponse
    {
        Instrument::findOrFail($id);

        $favorites = $request->session()->get('web_favorites', []);
        $isFavorite = isset($favorites[$id]);

        if ($isFavorite) {
            unset($favorites[$id]);
        } else {
            $favorites[$id] = $id;
        }

        $request->session()->put('web_favorites', $favorites);

        return $this->ok([
            'favorite' => ! $isFavorite,
            'count' => count($favorites),
        ], ! $isFavorite ? 'Added to favorites.' : 'Removed from favorites.');
    }

    public function bookingValidate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'instrument_id' => 'required|string|exists:instruments,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'email' => 'nullable|email',
        ]);

        $conflict = SlotService::hasConflict(
            $validated['instrument_id'],
            $validated['start_date'],
            $validated['end_date'],
            $validated['email'] ?? null
        );

        return $this->ok([
            'available' => ! $conflict,
        ], $conflict ? 'Selected range is unavailable.' : 'Selected range is available.');
    }

    public function bookingSubmit(Request $request): JsonResponse
    {
        $user = $this->sessionUser($request);
        if (! $user) {
            return $this->fail('Please login first.', null, 401);
        }

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
            return $this->fail('Bag is empty.', null, 422);
        }

        $created = [];
        foreach ($bag as $item) {
            if (SlotService::hasConflict($item['instrument_id'], $item['start_date'], $item['end_date'], $validated['email'])) {
                return $this->fail('One or more selected slots are unavailable.', null, 409);
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

        return $this->ok([
            'bookings' => $created,
        ], 'Booking request submitted.');
    }

    public function userBookings(Request $request): JsonResponse
    {
        $user = $this->sessionUser($request);
        if (! $user) {
            return $this->fail('Unauthenticated.', null, 401);
        }

        $bookings = Booking::with('instrument')
            ->where(function ($q) use ($user) {
                $q->where('email', $user->email)->orWhere('user_email', $user->email);
            })
            ->orderByDesc('created_at')
            ->get();

        return $this->ok([
            'items' => $bookings,
        ]);
    }

    public function joinQueue(Request $request, string $id): JsonResponse
    {
        $user = $this->sessionUser($request);
        if (! $user) {
            return $this->fail('Please login first.', null, 401);
        }

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date|after_or_equal:today',
        ]);

        $date = $validated['start_date'];
        $slot = $validated['start_date'] === $validated['end_date']
            ? $validated['start_date']
            : $validated['start_date'] . ' → ' . $validated['end_date'];

        $exists = Queue::where('instrument_id', $id)
            ->where('date', $date)
            ->where('time_slot', $slot)
            ->where(function ($q) use ($user) {
                $q->where('user_id', (string) $user->id)->orWhere('email', $user->email);
            })
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return $this->ok(['alreadyQueued' => true], 'Already in queue for this slot.');
        }

        QueueService::addToQueue($id, $user->name, $user->email, (string) $user->id, $date, $slot);

        return $this->ok(['queued' => true], 'Joined queue successfully.');
    }

    public function queueStatus(Request $request): JsonResponse
    {
        $user = $this->sessionUser($request);
        if (! $user) {
            return $this->fail('Unauthenticated.', null, 401);
        }

        $items = Queue::with('instrument')
            ->where('email', $user->email)
            ->orderByDesc('created_at')
            ->get();

        return $this->ok([
            'items' => $items,
        ]);
    }

    public function notifications(Request $request): JsonResponse
    {
        $user = $this->sessionUser($request);
        if (! $user) {
            return $this->ok([
                'items' => [],
                'unread' => 0,
            ]);
        }

        $items = $this->notificationItems($user->email, $request);

        return $this->ok([
            'items' => $items->values(),
            'unread' => $items->where('read', false)->count(),
        ]);
    }

    public function notificationsMarkRead(Request $request): JsonResponse
    {
        $request->session()->put('web_notifications_seen_at', now()->toDateTimeString());

        return $this->ok(['marked' => true], 'Notifications marked as read.');
    }

    public function adminInstruments(Request $request): JsonResponse
    {
        $perPage = max(1, min(50, (int) $request->query('per_page', 10)));
        $search = trim((string) $request->query('search', ''));

        $query = Instrument::query();
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('category', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        $rows = $query->orderByDesc('created_at')->paginate($perPage);

        return $this->ok([
            'items' => $rows->items(),
            'pagination' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'total' => $rows->total(),
            ],
        ]);
    }

    public function adminBookings(Request $request): JsonResponse
    {
        $perPage = max(1, min(50, (int) $request->query('per_page', 10)));
        $rows = Booking::with('instrument')->orderByDesc('created_at')->paginate($perPage);

        return $this->ok([
            'items' => $rows->items(),
            'pagination' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'total' => $rows->total(),
            ],
        ]);
    }

    public function adminUsers(Request $request): JsonResponse
    {
        $perPage = max(1, min(50, (int) $request->query('per_page', 10)));
        $search = trim((string) $request->query('search', ''));

        $query = User::query();
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $rows = $query->orderByDesc('created_at')->paginate($perPage);

        return $this->ok([
            'items' => $rows->items(),
            'pagination' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'total' => $rows->total(),
            ],
        ]);
    }

    public function adminAnalytics(): JsonResponse
    {
        $statusCounts = Booking::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $months = collect(range(5, 0))->map(function ($offset) {
            $date = Carbon::now()->subMonths($offset);
            return [
                'key' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
            ];
        });

        $monthlyRaw = Booking::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, count(*) as total")
            ->where('created_at', '>=', Carbon::now()->startOfMonth()->subMonths(5))
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $trend = $months->map(fn ($month) => [
            'label' => $month['label'],
            'total' => (int) ($monthlyRaw[$month['key']] ?? 0),
        ]);

        return $this->ok([
            'status_counts' => $statusCounts,
            'monthly_trend' => $trend,
            'users' => User::count(),
            'instruments' => Instrument::count(),
            'bookings' => Booking::count(),
        ]);
    }

    private function notificationItems(string $email, Request $request)
    {
        $seenAt = $request->session()->get('web_notifications_seen_at');
        $seenAtCarbon = $seenAt ? Carbon::parse($seenAt) : null;

        $bookingItems = Booking::query()
            ->where(function ($q) use ($email) {
                $q->where('email', $email)->orWhere('user_email', $email);
            })
            ->whereIn('status', ['approved', 'rejected'])
            ->orderByDesc('updated_at')
            ->limit(15)
            ->get()
            ->map(function ($booking) use ($seenAtCarbon) {
                return [
                    'type' => 'booking',
                    'title' => 'Booking ' . ucfirst($booking->status),
                    'message' => 'Booking #' . $booking->id . ' is now ' . $booking->status . '.',
                    'timestamp' => optional($booking->updated_at)->toDateTimeString(),
                    'read' => $seenAtCarbon ? $booking->updated_at->lte($seenAtCarbon) : false,
                ];
            });

        $queueItems = Queue::query()
            ->where('email', $email)
            ->whereIn('status', ['approved', 'rejected'])
            ->orderByDesc('updated_at')
            ->limit(15)
            ->get()
            ->map(function ($queue) use ($seenAtCarbon) {
                return [
                    'type' => 'queue',
                    'title' => 'Queue ' . ucfirst($queue->status),
                    'message' => 'Queue #' . $queue->id . ' is now ' . $queue->status . '.',
                    'timestamp' => optional($queue->updated_at)->toDateTimeString(),
                    'read' => $seenAtCarbon ? $queue->updated_at->lte($seenAtCarbon) : false,
                ];
            });

        return $bookingItems->merge($queueItems)->sortByDesc('timestamp')->take(20)->values();
    }

    private function sessionUser(Request $request): ?User
    {
        $id = $request->session()->get('web_user_id');
        if (! $id) {
            return null;
        }

        return User::find($id);
    }

    private function ok($data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data ?? (object) [],
            'message' => $message,
        ], $status);
    }

    private function fail(string $message, $data = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => $data ?? (object) [],
            'message' => $message,
        ], $status);
    }
}
