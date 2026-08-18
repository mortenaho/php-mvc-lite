<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(Capsule $db): void
    {
        $db->schema()->create('posts', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('excerpt', 280);
            $table->text('body');
            $table->timestamps();
        });

        $db->table('posts')->insert([
            [
                'title' => 'A small MVC core, without the extras',
                'excerpt' => 'The framework core only includes what you need to build a real app: routes, controllers, models, and views.',
                'body' => "Lite is a PHP core with dependency injection, a middleware pipeline, and a front controller.\n\nControllers stay thin, models use Eloquent, and templates compile through Blade so XSS is escaped by default.",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Eloquent for data, Blade for display',
                'excerpt' => 'Instead of a hand-rolled ORM, mature libraries handle queries, relationships, and templates.',
                'body' => "The data layer is Illuminate Database — Laravel’s Eloquent, without the rest of the framework.\n\nThe view layer is Blade: layouts, @csrf, auto-escape, and compiled template caching.",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function down(Capsule $db): void
    {
        $db->schema()->dropIfExists('posts');
    }
};
