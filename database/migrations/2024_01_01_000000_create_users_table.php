<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(Capsule $db): void
    {
        $db->schema()->create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('user');
            $table->timestamps();
        });

        $db->table('users')->insert([
            'name' => 'Demo User',
            'email' => 'demo@lite.test',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'role' => 'user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down(Capsule $db): void
    {
        $db->schema()->dropIfExists('users');
    }
};
