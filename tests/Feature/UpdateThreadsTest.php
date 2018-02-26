<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateThreadsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->signIn();
    }

    /** @test */
    function unauthorized_users_may_not_update_threads()
    {
        $thread = create(Thread::class, ['user_id' => create(User::class)->id]);

        $this->patch($thread->path(), [
            'title' => 'changed title',
            'body' => 'changed body',
        ])->assertStatus(403);
    }

    /** @test */
    function a_thread_requires_a_title_and_body_to_be_updated()
    {
        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'title' => 'changed title',
        ])->assertSessionHasErrors('body');

        $this->patch($thread->path(), [
            'body' => 'changed body',
        ])->assertSessionHasErrors('title');

    }

    /** @test */
    function a_thread_can_be_updated_by_its_creator()
    {
        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'title' => 'changed title',
            'body' => 'changed body',
        ]);

        $thread = $thread->fresh();

        $this->assertEquals('changed title', $thread->title);
        $this->assertEquals('changed body', $thread->body);

    }
}
