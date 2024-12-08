<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(User::class)]
class UserTest extends TestCase
{
    public function test_user_model_has_the_correct_fillable_properties(): void
    {
        $user = new User();
        $fillable = $user->getFillable();

        static::assertEquals([
            'name',
            'email',
            'password',
        ], $fillable);
    }

    public function test_user_model_has_the_correct_hidden_properties(): void
    {
        $user = new User();
        $hidden = $user->getHidden();

        static::assertEquals([
            'password',
            'remember_token',
        ], $hidden);
    }

    public function test_user_model_has_the_correct_casts(): void
    {
        $user = new User();
        $casts = $user->getCasts();

        static::assertEquals([
            'id' => 'int',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ], $casts);
    }
}
