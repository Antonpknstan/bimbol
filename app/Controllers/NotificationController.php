<?php
namespace App\Controllers;

use App\Models\Notification;
use App\Utils\Session;

class NotificationController extends BaseController
{
    public function fetch()
    {
        if (!Session::has('user')) return $this->json([]);
        
        $notifModel = new Notification();
        $userId = Session::get('user')['id'];
        
        $notifications = $notifModel->findByUser($userId);
        $unreadCount = $notifModel->countUnread($userId);
        
        return $this->json(['notifications' => $notifications, 'unread_count' => $unreadCount]);
    }

    public function markRead()
    {
        if (!Session::has('user')) return $this->json([], 401);
        
        $notifModel = new Notification();
        $notifModel->markAllAsRead(Session::get('user')['id']);
        return $this->json(['status' => 'success']);
    }
}