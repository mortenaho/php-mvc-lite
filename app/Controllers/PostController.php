<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Post;
use Lite\Exceptions\HttpException;
use Lite\Http\Controller;
use Lite\Http\Request;
use Lite\Http\Response;
use Lite\Session\Session;

final class PostController extends Controller
{
    public function __construct(private readonly Session $session)
    {
    }

    public function index(): Response
    {
        return $this->view('posts.index', [
            'title' => 'نوشته‌ها',
            'posts' => Post::query()->latest()->get(),
        ]);
    }

    public function show(string $id): Response
    {
        $post = Post::query()->find($id);

        if ($post === null) {
            throw HttpException::notFound('نوشته پیدا نشد.');
        }

        return $this->view('posts.show', [
            'title' => $post->title,
            'post' => $post,
        ]);
    }

    public function create(): Response
    {
        return $this->view('posts.create', [
            'title' => 'نوشته جدید',
        ]);
    }

    public function store(Request $request): Response
    {
        $data = $this->validate($request, [
            'title' => 'required|min:3|max:180',
            'excerpt' => 'required|min:10|max:280',
            'body' => 'required|min:20',
        ]);

        $post = Post::query()->create($data);
        $this->session->flash('success', 'نوشته با موفقیت ذخیره شد.');

        return $this->redirect('/posts/' . $post->id);
    }

    public function edit(string $id): Response
    {
        $post = Post::query()->find($id);

        if ($post === null) {
            throw HttpException::notFound('نوشته پیدا نشد.');
        }

        return $this->view('posts.edit', [
            'title' => 'ویرایش نوشته',
            'post' => $post,
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $post = Post::query()->find($id);

        if ($post === null) {
            throw HttpException::notFound('نوشته پیدا نشد.');
        }

        $data = $this->validate($request, [
            'title' => 'required|min:3|max:180',
            'excerpt' => 'required|min:10|max:280',
            'body' => 'required|min:20',
        ]);

        $post->update($data);
        $this->session->flash('success', 'نوشته به‌روزرسانی شد.');

        return $this->redirect('/posts/' . $post->id);
    }

    public function destroy(string $id): Response
    {
        $post = Post::query()->find($id);

        if ($post === null) {
            throw HttpException::notFound('نوشته پیدا نشد.');
        }

        $post->delete();
        $this->session->flash('success', 'نوشته حذف شد.');

        return $this->redirect('/posts');
    }
}
