<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use Lite\Auth\Auth;
use Lite\Http\Controller;
use Lite\Http\Request;
use Lite\Http\Response;
use Lite\Session\Session;

final class AuthController extends Controller
{
    public function __construct(
        private readonly Auth $auth,
        private readonly Session $session,
    ) {
    }

    public function showLogin(): Response
    {
        return $this->view('auth.login', [
            'title' => 'Log in',
        ]);
    }

    public function login(Request $request): Response
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! $this->auth->attempt((string) $credentials['email'], (string) $credentials['password'])) {
            if ($request->wantsJson()) {
                return $this->json(['message' => 'Invalid credentials.'], 422);
            }

            $this->session->flash('error', 'These credentials do not match our records.');
            $this->session->flash('old', $request->only(['email']));

            return $this->redirect('/login');
        }

        if ($request->wantsJson()) {
            return $this->json(['user' => $this->auth->user()], 200);
        }

        $intended = (string) $this->session->get('url.intended', '/');
        $this->session->forget('url.intended');
        $this->session->flash('success', 'Welcome back.');

        return $this->redirect($intended === '' ? '/' : $intended);
    }

    public function showRegister(): Response
    {
        return $this->view('auth.register', [
            'title' => 'Register',
        ]);
    }

    public function register(Request $request): Response
    {
        $data = $this->validate($request, [
            'name' => 'required|min:2|max:80',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::query()->create($data);
        $this->auth->login($user);

        if ($request->wantsJson()) {
            return $this->json(['user' => $this->auth->user()], 201);
        }

        $this->session->flash('success', 'Your account has been created.');

        return $this->redirect('/');
    }

    public function logout(Request $request): Response
    {
        $this->auth->logout();

        if ($request->wantsJson()) {
            return Response::noContent();
        }

        $this->session->flash('success', 'You have been logged out.');

        return $this->redirect('/');
    }
}
