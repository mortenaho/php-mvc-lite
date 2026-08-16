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
                'title' => 'معماری MVC سبک، بدون اضافات',
                'excerpt' => 'هسته فریم‌ورک فقط همان چیزی را دارد که برای ساخت یک اپلیکیشن واقعی لازم است: مسیر، کنترلر، مدل و ویو.',
                'body' => "Lite یک هسته PHP با تزریق وابستگی، پایپ‌لاین میدل‌ویر و Front Controller است.\n\nکنترلرها نازک می‌مانند، مدل‌ها با Eloquent کار می‌کنند و قالب‌ها در Blade کامپایل می‌شوند تا XSS به‌صورت پیش‌فرض بسته باشد.",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Eloquent برای داده، Blade برای نمایش',
                'excerpt' => 'به‌جای ORM دست‌نویس، از کتابخانه‌های بالغ استفاده شده تا کوئری، رابطه و قالب‌ها قابل اعتماد باشند.',
                'body' => "لایه داده Illuminate Database است؛ همان Eloquent لاراول، بدون خود فریم‌ورک.\n\nلایه ویو Blade است: وراثت لایه‌ها، @csrf، auto-escape و کش قالب کامپایل‌شده.",
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
