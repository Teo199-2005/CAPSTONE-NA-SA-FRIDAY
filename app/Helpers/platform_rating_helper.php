<?php

declare(strict_types=1);

if (! function_exists('platform_rating_responder_role')) {
    function platform_rating_responder_role(?object $user): ?string
    {
        if ($user === null) {
            return null;
        }
        if ($user->inGroup('student')) {
            return 'student';
        }
        if ($user->inGroup('teacher')) {
            return 'teacher';
        }

        return null;
    }
}

if (! function_exists('platform_rating_user_must_submit')) {
    /**
     * Students and teachers must submit one rating per school year + term.
     */
    function platform_rating_user_must_submit(?object $user): bool
    {
        return platform_rating_responder_role($user) !== null;
    }
}

if (! function_exists('platform_rating_term_label')) {
    function platform_rating_term_label(int $term): string
    {
        return 'Term ' . $term;
    }
}
