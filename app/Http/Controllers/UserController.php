<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ChangeEmailRequest;
use App\Http\Requests\User\ChangeEmailSubmitRequest;
use App\Services\UserService;

class UserController extends Controller
{
    public function changeEmail(ChangeEmailRequest $request)
    {
        return UserService::changeEmail($request);
    }

    public function changeEmailSubmit(ChangeEmailSubmitRequest $request)
    {
        return UserService::changeEmailSubmit($request);
    }
}
