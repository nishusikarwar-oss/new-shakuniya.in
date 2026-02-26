<?php

namespace App\Http\Controllers\API;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class NewsletterSubscriberController extends Controller
{
    /**
     * Display a listing of subscribers (admin only).
     */
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query();

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            }
        }

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Date range filter
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->subscribedBetween($request->date_from, $request->date_to);
        }

        // Order by latest first
        $query->orderBy('subscribed_at', 'desc');

        $subscribers = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $subscribers
        ]);
    }

    /**
     * Store a newly created subscriber (public endpoint).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:newsletter_subscribers',
            'name' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['subscribed_at'] = now();

        $subscriber = NewsletterSubscriber::create($data);

        // Send welcome email (optional)
        // $this->sendWelcomeEmail($subscriber);

        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to newsletter!',
            'data' => [
                'subscriber_id' => $subscriber->subscriber_id,
                'email' => $subscriber->email,
                'name' => $subscriber->name,
                'subscribed_at' => $subscriber->subscribed_at
            ]
        ], 201);
    }

    /**
     * Display the specified subscriber.
     */
    public function show($id)
    {
        $subscriber = NewsletterSubscriber::find($id);

        if (!$subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'Subscriber not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $subscriber
        ]);
    }

    /**
     * Get subscriber by email.
     */
    public function findByEmail($email)
    {
        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if (!$subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'Subscriber not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $subscriber
        ]);
    }

    /**
     * Update the specified subscriber.
     */
    public function update(Request $request, $id)
    {
        $subscriber = NewsletterSubscriber::find($id);

        if (!$subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'Subscriber not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required|email|max:255|unique:newsletter_subscribers,email,' . $id . ',subscriber_id',
            'name' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $subscriber->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Subscriber updated successfully',
            'data' => $subscriber
        ]);
    }

    /**
     * Unsubscribe a subscriber (public endpoint).
     */
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:newsletter_subscribers,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $subscriber = NewsletterSubscriber::where('email', $request->email)->first();

        if (!$subscriber->is_active_subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'This email is already unsubscribed.'
            ], 400);
        }

        $subscriber->unsubscribe();

        return response()->json([
            'success' => true,
            'message' => 'Successfully unsubscribed from newsletter.'
        ]);
    }

    /**
     * Unsubscribe by ID (admin or authenticated).
     */
    public function unsubscribeById($id)
    {
        $subscriber = NewsletterSubscriber::find($id);

        if (!$subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'Subscriber not found'
            ], 404);
        }

        if (!$subscriber->is_active_subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'This subscriber is already unsubscribed.'
            ], 400);
        }

        $subscriber->unsubscribe();

        return response()->json([
            'success' => true,
            'message' => 'Subscriber unsubscribed successfully',
            'data' => $subscriber
        ]);
    }

    /**
     * Resubscribe a subscriber.
     */
    public function resubscribe($id)
    {
        $subscriber = NewsletterSubscriber::find($id);

        if (!$subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'Subscriber not found'
            ], 404);
        }

        if ($subscriber->is_active_subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'This subscriber is already active.'
            ], 400);
        }

        $subscriber->resubscribe();

        return response()->json([
            'success' => true,
            'message' => 'Subscriber resubscribed successfully',
            'data' => $subscriber
        ]);
    }

    /**
     * Remove the specified subscriber.
     */
    public function destroy($id)
    {
        $subscriber = NewsletterSubscriber::find($id);

        if (!$subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'Subscriber not found'
            ], 404);
        }

        $subscriber->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscriber deleted successfully'
        ]);
    }

    /**
     * Bulk delete subscribers.
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:newsletter_subscribers,subscriber_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        NewsletterSubscriber::whereIn('subscriber_id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' subscribers deleted successfully'
        ]);
    }

    /**
     * Get subscribers statistics.
     */
    public function stats()
    {
        $total = NewsletterSubscriber::count();
        $active = NewsletterSubscriber::active()->count();
        $inactive = NewsletterSubscriber::inactive()->count();
        
        // Today's subscribers
        $today = NewsletterSubscriber::whereDate('subscribed_at', now()->toDateString())->count();
        
        // This week's subscribers
        $thisWeek = NewsletterSubscriber::whereBetween('subscribed_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();
        
        // This month's subscribers
        $thisMonth = NewsletterSubscriber::whereMonth('subscribed_at', now()->month)
            ->whereYear('subscribed_at', now()->year)
            ->count();

        // Growth rate (compared to last month)
        $lastMonth = NewsletterSubscriber::whereMonth('subscribed_at', now()->subMonth()->month)
            ->whereYear('subscribed_at', now()->subMonth()->year)
            ->count();

        $growthRate = $lastMonth > 0 
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2)
            : ($thisMonth > 0 ? 100 : 0);

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'today' => $today,
                'this_week' => $thisWeek,
                'this_month' => $thisMonth,
                'growth_rate' => $growthRate,
                'active_percentage' => $total > 0 ? round(($active / $total) * 100, 2) : 0
            ]
        ]);
    }

    /**
     * Export subscribers to CSV.
     */
    public function export(Request $request)
    {
        $query = NewsletterSubscriber::query();

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            }
        }

        $subscribers = $query->orderBy('subscribed_at', 'desc')->get();

        $csvData = [];
        $csvData[] = ['ID', 'Email', 'Name', 'Status', 'Subscribed At', 'Unsubscribed At'];

        foreach ($subscribers as $subscriber) {
            $csvData[] = [
                $subscriber->subscriber_id,
                $subscriber->email,
                $subscriber->name ?? 'N/A',
                $subscriber->is_active_subscriber ? 'Active' : 'Inactive',
                $subscriber->subscribed_at?->format('Y-m-d H:i:s'),
                $subscriber->unsubscribed_at?->format('Y-m-d H:i:s') ?? 'N/A'
            ];
        }

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="newsletter_subscribers_' . now()->format('Y-m-d') . '.csv"'
        ]);
    }

    /**
     * Send newsletter to all active subscribers (placeholder).
     */
    public function sendNewsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $subscribers = NewsletterSubscriber::active()->get();
        $count = $subscribers->count();

        // Here you would implement actual email sending logic
        // Mail::to($subscribers)->send(new NewsletterMail($request->subject, $request->content));

        return response()->json([
            'success' => true,
            'message' => "Newsletter will be sent to {$count} subscribers",
            'data' => [
                'total_recipients' => $count,
                'subject' => $request->subject
            ]
        ]);
    }
}