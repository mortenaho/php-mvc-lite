<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(Capsule $db): void
    {
        $db->schema()->table('posts', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->nullable();
        });

        $demoId = $db->table('users')->where('email', 'demo@lite.test')->value('id');

        if ($demoId !== null) {
            $db->table('posts')->whereNull('user_id')->update(['user_id' => $demoId]);
        }
    }

    public function down(Capsule $db): void
    {
        $db->schema()->table('posts', function (Blueprint $table): void {
            $table->dropColumn('user_id');
        });
    }
};
