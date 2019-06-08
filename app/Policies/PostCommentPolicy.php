<?php

namespace App\Policies;

use App\Models\PostComment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostCommentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function delete(User $user, PostComment $comment)
    {
        if ($comment->user_id === $user->id || $user->id === $comment->post->user_id) {
            return true;
        } else {
            return false;
        }
    }
}
