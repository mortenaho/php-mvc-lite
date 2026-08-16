<?php

declare(strict_types=1);

namespace App\Models;

final class Post extends Model
{
    protected $fillable = [
        'title',
        'excerpt',
        'body',
    ];
}
