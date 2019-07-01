<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Transformers\UserTransformer;

class UsersController extends Controller
{
    public function show(User $user)
    {
        return $this->response->item($user, new UserTransformer('simple'));
    }
}
