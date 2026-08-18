<?php

declare(strict_types=1);

namespace App\Controllers;

use Lite\Auth\Auth;
use Lite\Http\Controller;
use Lite\Http\Response;

final class AccountController extends Controller
{
    public function __construct(private readonly Auth $auth)
    {
    }

    public function show(): Response
    {
        return $this->view('account.show', [
            'title' => 'Account',
            'account' => $this->auth->user(),
        ]);
    }
}
