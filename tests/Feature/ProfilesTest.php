<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfilesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_user_has_a_profile()
    {
        $user = create(User::class);

        $this->get("/profiles/{$user->name}")->assertSee(e($user->name));
    }

    /**
     * @test
     */
    public function profiles_display_all_threads_created_by_associated_user()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->get("/profiles/". auth()->user()->name)
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
