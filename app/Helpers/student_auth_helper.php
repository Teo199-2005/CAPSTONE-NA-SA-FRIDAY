<?php

declare(strict_types=1);

if (! function_exists('resolve_student_account_email')) {
    /**
     * Resolve the login email for a student user (students row + Shield user).
     */
    function resolve_student_account_email(array $student, ?object $user = null): ?string
    {
        if (isset($student['email']) && $student['email'] !== '' && filter_var($student['email'], FILTER_VALIDATE_EMAIL)) {
            return (string) $student['email'];
        }

        if ($user !== null && isset($user->email) && $user->email !== '' && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return (string) $user->email;
        }

        return null;
    }
}

if (! function_exists('sync_student_auth_password')) {
    /** @see sync_auth_password() */
    function sync_student_auth_password(int $userId, string $email, string $plainPassword): bool
    {
        helper('auth');

        return sync_auth_password($userId, $email, $plainPassword);
    }
}
