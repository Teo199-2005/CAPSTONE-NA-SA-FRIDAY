<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class PlatformRatingModel extends Model
{
    protected $table            = 'platform_ratings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'school_year',
        'term',
        'responder_role',
        'rating',
        'comment',
    ];

    protected bool $allowEmptyInserts = false;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'user_id'        => 'required|is_natural_no_zero',
        'school_year'    => 'required|max_length[9]',
        'term'           => 'required|integer|greater_than[0]|less_than[4]',
        'responder_role' => 'required|in_list[student,teacher]',
        'rating'         => 'required|integer|greater_than[0]|less_than[6]',
        'comment'        => 'permit_empty|max_length[500]',
    ];

    public function findByUserId(int $userId): ?array
    {
        helper('school_year');

        return $this->findForUserTerm($userId, get_current_school_year(), get_current_term());
    }

    public function findForUserTerm(int $userId, string $schoolYear, int $term): ?array
    {
        $row = $this->where('user_id', $userId)
            ->where('school_year', $schoolYear)
            ->where('term', $term)
            ->first();

        return $row ?: null;
    }

    public function hasSubmittedForCurrentTerm(int $userId): bool
    {
        helper('school_year');

        return $this->findForUserTerm($userId, get_current_school_year(), get_current_term()) !== null;
    }

    /**
     * Save rating for the current school year and term (one row per user per term).
     */
    public function saveForCurrentTerm(int $userId, string $role, int $rating, ?string $comment): bool
    {
        helper('school_year');

        return $this->saveForTerm($userId, get_current_school_year(), get_current_term(), $role, $rating, $comment);
    }

    public function saveForTerm(int $userId, string $schoolYear, int $term, string $role, int $rating, ?string $comment): bool
    {
        if (! in_array($role, ['student', 'teacher'], true)) {
            return false;
        }

        if ($term < 1 || $term > 3) {
            return false;
        }

        $comment = $comment !== null && $comment !== '' ? trim($comment) : null;
        if ($comment === '') {
            $comment = null;
        }

        $existing = $this->findForUserTerm($userId, $schoolYear, $term);
        $data     = [
            'user_id'        => $userId,
            'school_year'    => $schoolYear,
            'term'           => $term,
            'responder_role' => $role,
            'rating'         => $rating,
            'comment'        => $comment,
        ];

        if ($existing === null) {
            return $this->insert($data) !== false;
        }

        return $this->update((int) $existing['id'], $data);
    }

    /**
     * @deprecated Use saveForCurrentTerm() — kept for backward compatibility.
     */
    public function saveOrUpdateForUser(int $userId, string $role, int $rating, ?string $comment): bool
    {
        return $this->saveForCurrentTerm($userId, $role, $rating, $comment);
    }
}
