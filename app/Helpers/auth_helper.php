<?php

declare(strict_types=1);

if (! function_exists('verify_auth_identity_password')) {
    /**
     * Verify a plaintext password against an auth_identities row (Shield + legacy formats).
     */
    function verify_auth_identity_password(string $password, ?object $identity): bool
    {
        if ($identity === null) {
            return false;
        }

        $secret  = (string) ($identity->secret ?? '');
        $secret2 = (string) ($identity->secret2 ?? '');

        foreach ([$secret2, $secret] as $candidate) {
            if ($candidate === '') {
                continue;
            }

            $info = password_get_info($candidate);
            if (($info['algo'] ?? 0) !== 0 && password_verify($password, $candidate)) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('sync_auth_password')) {
    /**
     * Store login password in auth_identities (works with Auth::performLoginAttempt).
     * Sets bcrypt hash in both secret and secret2 for compatibility.
     */
    function sync_auth_password(int $userId, string $email, string $plainPassword): bool
    {
        if ($userId <= 0 || $plainPassword === '') {
            return false;
        }

        $db             = \Config\Database::connect();
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        $now            = date('Y-m-d H:i:s');
        $cleanEmail     = str_replace('mailto:', '', trim($email));

        $existing = $db->table('auth_identities')
            ->where('user_id', $userId)
            ->where('type', 'email_password')
            ->get()
            ->getRow();

        $payload = [
            'name'       => $cleanEmail,
            'secret'     => $hashedPassword,
            'secret2'    => $hashedPassword,
            'updated_at' => $now,
        ];

        if ($existing) {
            return $db->table('auth_identities')
                ->where('user_id', $userId)
                ->where('type', 'email_password')
                ->update($payload);
        }

        $payload['user_id']      = $userId;
        $payload['type']         = 'email_password';
        $payload['expires']      = null;
        $payload['extra']        = null;
        $payload['force_reset']  = 0;
        $payload['last_used_at'] = null;
        $payload['created_at']   = $now;

        return $db->table('auth_identities')->insert($payload) !== false;
    }
}

if (! function_exists('ensure_user_in_group')) {
    function ensure_user_in_group(int $userId, string $group): bool
    {
        if ($userId <= 0 || $group === '') {
            return false;
        }

        $db = \Config\Database::connect();

        $exists = $db->table('auth_groups_users')
            ->where('user_id', $userId)
            ->where('group', $group)
            ->countAllResults();

        if ($exists > 0) {
            return true;
        }

        return $db->table('auth_groups_users')->insert([
            'user_id'    => $userId,
            'group'      => $group,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
