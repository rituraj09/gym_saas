<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $query = AppNotification::forUser(auth()->id())->latest();

        if ($request->filled('filter')) {
            match ($request->filter) {
                'unread' => $query->unread(),
                'read' => $query->read(),
                default => null,
            };
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->paginate(20)->withQueryString();
        $unreadCount = auth()->user()->unreadNotificationCount();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(AppNotification $notification)
    {
        $this->notificationService->markAsRead($notification, auth()->id());

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        // If notification has a link, redirect there
        if ($notification->link) {
            return redirect($notification->link);
        }

        return back();
    }

    public function markAllAsRead()
    {
        $count = $this->notificationService->markAllAsRead(auth()->id());

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'count' => $count]);
        }

        return back()->with('success', "Marked {$count} notification(s) as read.");
    }

    public function destroy(AppNotification $notification)
    {
        if ($notification->user_id === auth()->id()) {
            $notification->delete();
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification deleted.');
    }

    /**
     * API endpoint for the bell dropdown (returns JSON).
     */
    public function recent()
    {
        $notifications = AppNotification::forUser(auth()->id())
            ->latest()
            ->limit(5)
            ->get();

        $unreadCount = auth()->user()->unreadNotificationCount();

        return response()->json([
            'notifications' => $notifications->map(fn($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'type_label' => $n->type_label,
                'type_icon' => $n->type_icon,
                'type_color' => $n->type_color,
                'title' => $n->title,
                'message' => $n->message,
                'link' => $n->link,
                'is_read' => $n->is_read,
                'time_ago' => $n->created_at->diffForHumans(),
            ]),
            'unread_count' => $unreadCount,
        ]);
    }
}
