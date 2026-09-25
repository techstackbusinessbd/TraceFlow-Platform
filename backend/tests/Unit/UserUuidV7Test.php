<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserUuidV7Test extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that User model automatically generates a valid UUID v7 primary key.
     */
    public function test_user_generates_valid_uuid_v7_primary_key(): void
    {
        $user = User::factory()->create([
            'email' => 'test@traceflow.com',
            'name' => 'TraceFlow Admin',
        ]);

        $this->assertNotEmpty($user->id);
        $this->assertTrue(Str::isUuid($user->id));
        $this->assertEquals(7, (int) substr($user->id, 14, 1)); // UUID version 7 check
    }
}
