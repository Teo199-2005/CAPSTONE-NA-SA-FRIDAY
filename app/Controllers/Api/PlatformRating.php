<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PlatformRatingModel;

class PlatformRating extends BaseController
{
    public function status()
    {
        $auth = auth();
        if (! $auth->loggedIn()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized'])->setStatusCode(401);
        }

        helper(['platform_rating', 'school_year']);
        $user = $auth->user();
        $role = platform_rating_responder_role($user);

        if ($role === null) {
            return $this->response->setJSON([
                'success'     => true,
                'required'    => false,
                'submitted'   => true,
                'school_year' => get_current_school_year(),
                'term'        => get_current_term(),
            ]);
        }

        $model     = new PlatformRatingModel();
        $userId    = (int) $user->id;
        $schoolYear = get_current_school_year();
        $term      = get_current_term();
        $submitted = $model->hasSubmittedForCurrentTerm($userId);

        return $this->response->setJSON([
            'success'     => true,
            'required'    => true,
            'submitted'   => $submitted,
            'school_year' => $schoolYear,
            'term'        => $term,
            'term_label'  => platform_rating_term_label($term),
        ]);
    }

    public function submit()
    {
        $auth = auth();
        if (! $auth->loggedIn()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized'])->setStatusCode(401);
        }

        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid method'])->setStatusCode(405);
        }

        helper('platform_rating');
        $user = $auth->user();
        $role = platform_rating_responder_role($user);

        if ($role === null) {
            return $this->response->setJSON(['success' => false, 'error' => 'Not applicable'])->setStatusCode(403);
        }

        $rules = [
            'rating'  => 'required|integer|greater_than[0]|less_than[6]',
            'comment' => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => implode(' ', $this->validator->getErrors()),
            ])->setStatusCode(422);
        }

        $rating  = (int) $this->request->getPost('rating');
        $comment = $this->request->getPost('comment');
        $model   = new PlatformRatingModel();
        $ok      = $model->saveForCurrentTerm(
            (int) $user->id,
            $role,
            $rating,
            is_string($comment) ? $comment : null
        );

        if (! $ok) {
            return $this->response->setJSON(['success' => false, 'error' => 'Could not save rating'])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success'   => true,
            'message'   => 'Thank you for your feedback!',
            'submitted' => true,
        ]);
    }
}
