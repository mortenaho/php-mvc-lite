<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Post;
use Lite\Http\Controller;
use Lite\Http\Request;
use Lite\Http\Response;

final class HomeController extends Controller
{
    public function index(): Response
    {
        return $this->view('home.index', [
            'title' => 'Lite MVC',
            'posts' => Post::query()->latest()->limit(3)->get(),
        ]);
    }
}
