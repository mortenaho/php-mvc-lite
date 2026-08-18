<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Lite\Auth\HasRoles;

final class User extends Model
{
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function setPasswordAttribute(string $value): void
    {
        $info = password_get_info($value);

        $this->attributes['password'] = ($info['algo'] ?? 0) !== 0
            ? $value
            : password_hash($value, PASSWORD_DEFAULT);
    }
}
