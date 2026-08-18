<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Post;
use Lite\Auth\Auth;
use Lite\Exceptions\HttpException;
use Lite\Http\Controller;
use Lite\Http\Request;
use Lite\Http\Response;
use Lite\Session\Session;

final class PostController extends Controller
{
    public function __construct(
        private readonly Session $session,
        private readonly Auth $auth,
    ) {
    }

    public function index(): Response
    {
        return $this->view('posts.index', [
            'title' => 'Posts',
            'posts' => Post::query()->latest()->get(),
        ]);
    }

    public function show(string $id): Response
    {
        $post = $this->find($id);

        return $this->view('posts.show', [
            'title' => $post->title,
            'post' => $post,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Post::class);

        return $this->view('posts.create', [
            'title' => 'New post',
        ]);
    }

    public function store(Request $request): Response
    {
        $data = $this->validate($request, [
            'title' => 'required|min:3|max:180',
            'excerpt' => 'required|min:10|max:280',
            'body' => 'required|min:20',
        ]);

        $this->authorize('create', Post::class);

        $data['user_id'] = $this->auth->id();
        $post = Post::query()->create($data);
        $this->session->flash('success', 'Post saved.');

        return $this->redirect('/posts/' . $post->id);
    }

    public function edit(string $id): Response
    {
        $post = $this->find($id);
        $this->authorize('update', $post);

        return $this->view('posts.edit', [
            'title' => 'Edit post',
            'post' => $post,
        ]);
    }

    public function update(Request $request, string $id): Response
    {
        $post = $this->find($id);
        $this->authorize('update', $post);

        $data = $this->validate($request, [
            'title' => 'required|min:3|max:180',
            'excerpt' => 'required|min:10|max:280',
            'body' => 'required|min:20',
        ]);

        $post->update($data);
        $this->session->flash('success', 'Post updated.');

        return $this->redirect('/posts/' . $post->id);
    }

    public function destroy(string $id): Response
    {
        $post = $this->find($id);
        $this->authorize('delete', $post);
        $post->delete();
        $this->session->flash('success', 'Post deleted.');

        return $this->redirect('/posts');
    }

    private function find(string $id): Post
    {
        $post = Post::query()->with('user')->find($id);

        if ($post === null) {
            throw HttpException::notFound('Post not found.');
        }

        return $post;
    }
}
