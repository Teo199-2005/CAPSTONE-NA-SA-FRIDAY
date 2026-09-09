<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use CodeIgniter\Shield\Models\UserModel;

class Notifications extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function index()
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $notificationModel = new NotificationModel();

        // Get all sent notifications with user counts
        $sentNotifications = $this->getSentNotifications();

        return view('admin/notifications', [
            'title' => 'Bulk Notifications - CSCS SMS',
            'sentNotifications' => $sentNotifications,
        ]);
    }

    /**
     * Get sent notifications grouped by title and message
     */
    private function getSentNotifications()
    {
        $notificationModel = new NotificationModel();
        $db = \Config\Database::connect();

        // Get grouped notifications with counts
        $query = $db->query("
            SELECT
                MIN(id) as id,
                title,
                message,
                type,
                COUNT(*) as recipient_count,
                COUNT(CASE WHEN is_read = 1 THEN 1 END) as read_count,
                MIN(created_at) as sent_at,
                GROUP_CONCAT(DISTINCT
                    CASE
                        WHEN user_id IN (SELECT user_id FROM auth_groups_users WHERE `group` = 'admin') THEN 'admin'
                        WHEN user_id IN (SELECT user_id FROM auth_groups_users WHERE `group` = 'teacher') THEN 'teacher'
                        WHEN user_id IN (SELECT user_id FROM auth_groups_users WHERE `group` = 'student') THEN 'student'
                        WHEN user_id IN (SELECT user_id FROM auth_groups_users WHERE `group` = 'parent') THEN 'parent'
                        ELSE 'unknown'
                    END
                ) as target_groups
            FROM notifications
            WHERE deleted_at IS NULL
            GROUP BY title, message, type
            ORDER BY sent_at DESC
            LIMIT 50
        ");

        return $query->getResultArray();
    }

    public function send()
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $rules = [
            'target' => 'required|in_list[all,admin,teacher,student,parent]',
            'title' => 'required|max_length[255]',
            'message' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $target = $this->request->getPost('target');
        $title = $this->request->getPost('title');
        $message = $this->request->getPost('message');

        $notificationModel = new NotificationModel();
        $userModel = new UserModel();
        
        // Get target users
        $users = [];
        if ($target === 'all') {
            $users = $userModel->findAll();
        } else {
            $users = $userModel->whereHas('groups', function($query) use ($target) {
                $query->where('group', $target);
            })->findAll();
        }
        
        // Create notification for each user
        foreach ($users as $user) {
            $notificationModel->insert([
                'user_id' => $user->id,
                'type' => 'announcement',
                'title' => $title,
                'message' => $message,
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Notification sent to ' . count($users) . ' ' . $target . ' users.');
    }

    /**
     * Show details of a specific notification group
     */
    public function show($id)
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $notificationModel = new NotificationModel();
        $userModel = new UserModel();

        // Get the notification details
        $notification = $notificationModel->find($id);
        if (!$notification) {
            return redirect()->to(base_url('admin/notifications'))->with('error', 'Notification not found.');
        }

        // Get all notifications with same title and message
        $relatedNotifications = $notificationModel
            ->select('notifications.*, users.email')
            ->join('users', 'users.id = notifications.user_id')
            ->where('notifications.title', $notification['title'])
            ->where('notifications.message', $notification['message'])
            ->orderBy('notifications.created_at', 'DESC')
            ->findAll();

        return view('admin/notifications_show', [
            'title' => 'Notification Details - CSCS SMS',
            'notification' => $notification,
            'relatedNotifications' => $relatedNotifications,
        ]);
    }

    /**
     * Edit a notification (resend with modifications)
     */
    public function edit($id)
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $notificationModel = new NotificationModel();
        $notification = $notificationModel->find($id);

        if (!$notification) {
            return redirect()->to(base_url('admin/notifications'))->with('error', 'Notification not found.');
        }

        return view('admin/notifications_edit', [
            'title' => 'Edit Notification - CSCS SMS',
            'notification' => $notification,
        ]);
    }

    /**
     * Update and resend notification
     */
    public function update($id)
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $rules = [
            'target' => 'required|in_list[all,admin,teacher,student,parent]',
            'title' => 'required|max_length[255]',
            'message' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $target = $this->request->getPost('target');
        $title = $this->request->getPost('title');
        $message = $this->request->getPost('message');

        // Send new notification (same as create)
        $this->sendNotificationToTarget($target, $title, $message);

        return redirect()->to(base_url('admin/notifications'))->with('success', 'Updated notification sent to ' . $target . ' users.');
    }

    /**
     * Delete notification group (mark all related notifications as deleted)
     */
    public function delete($id)
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $notificationModel = new NotificationModel();
        $notification = $notificationModel->find($id);

        if (!$notification) {
            return redirect()->to(base_url('admin/notifications'))->with('error', 'Notification not found.');
        }

        // Delete all notifications with same title and message
        $notificationModel
            ->where('title', $notification['title'])
            ->where('message', $notification['message'])
            ->delete();

        return redirect()->to(base_url('admin/notifications'))->with('success', 'Notification group deleted successfully.');
    }

    /**
     * Helper method to send notifications to target audience
     */
    private function sendNotificationToTarget($target, $title, $message)
    {
        $notificationModel = new NotificationModel();
        $userModel = new UserModel();

        // Get target users
        $users = [];
        if ($target === 'all') {
            $users = $userModel->findAll();
        } else {
            // Get users by group
            $db = \Config\Database::connect();
            $query = $db->query("
                SELECT u.* FROM users u
                JOIN auth_groups_users agu ON u.id = agu.user_id
                WHERE agu.group = ? AND u.deleted_at IS NULL
            ", [$target]);
            $users = $query->getResultArray();
        }

        // Create notification for each user
        foreach ($users as $user) {
            $notificationModel->insert([
                'user_id' => $user['id'],
                'type' => 'announcement',
                'title' => $title,
                'message' => $message,
                'is_read' => false,
            ]);
        }

        return count($users);
    }

    /**
     * Mark notification as read (AJAX)
     */
    public function markAsRead()
    {
        if (!$this->request->isAJAX() || ! is_any_admin()) {
            return $this->response->setStatusCode(403);
        }

        $notificationId = $this->request->getPost('notification_id');
        $notificationModel = new NotificationModel();

        $updated = $notificationModel->update($notificationId, [
            'is_read' => true,
            'read_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => $updated]);
    }

    /**
     * Get notification statistics (AJAX)
     */
    public function getStats()
    {
        if (!$this->request->isAJAX() || ! is_any_admin()) {
            return $this->response->setStatusCode(403);
        }

        $notificationModel = new NotificationModel();

        $stats = [
            'total_sent' => $notificationModel->countAllResults(false),
            'total_read' => $notificationModel->where('is_read', true)->countAllResults(false),
            'total_unread' => $notificationModel->where('is_read', false)->countAllResults(false),
        ];

        return $this->response->setJSON($stats);
    }
}





