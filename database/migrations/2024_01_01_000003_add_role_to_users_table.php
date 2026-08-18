<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(Capsule $db): void
    {
        if (! $db->schema()->hasColumn('users', 'role')) {
            $db->schema()->table('users', function (Blueprint $table): void {
                $table->string('role')->default('user');
            });
        }

        $db->table('users')->where('role', '')->update(['role' => 'user']);

        if ($db->table('users')->where('email', 'admin@lite.test')->doesntExist()) {
            $db->table('users')->insert([
                'name' => 'Admin',
                'email' => 'admin@lite.test',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down(Capsule $db): void
    {
        $db->table('users')->where('email', 'admin@lite.test')->delete();

        if ($db->schema()->hasColumn('users', 'role')) {
            $db->schema()->table('users', function (Blueprint $table): void {
                $table->dropColumn('role');
            });
        }
    }
};
