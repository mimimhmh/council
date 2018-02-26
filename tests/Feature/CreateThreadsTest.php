<?php

namespace Tests\Feature;

use App\Activity;
use App\Channel;
use App\Reply;
use App\Rules\Recaptcha;
use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use RefreshDatabase, MockeryPHPUnitIntegration;

    protected function setUp()
    {
        parent::setUp();

        app()->singleton(Recaptcha::class, function () {
            return \Mockery::mock(Recaptcha::class, function ($m) {
                $m->shouldReceive('passes')->andReturn(true);
            });
        });
    }

    /**
     * @test
     */
    public function guests_may_not_create_threads()
    {
        $this->withExceptionHandling();

        $this->get('/threads/create')->assertRedirect(route('login'));

        $this->get('/threads/create')->assertRedirect(route('login'));
    }

    /**
     * @test
     */
    public function new_users_must_first_confirm_their_email_address_before_creating_threads()
    {
        $user = factory('App\User')->states('unconfirmed')->create();

        $this->signIn($user);

        $thread = make('App\Thread');

        $this->post(route('threads'), $thread->toArray())->assertRedirect(route('threads'))
            ->assertSessionHas('flash', 'You must first confirm your email address.');
    }

    /**
     * @test
     */
    public function a_user_can_create_new_forum_threads()
    {

        $this->signIn();

        $thread = make(Thread::class);

        $response = $this->publishThread(['title' => 'Some Title', 'body' => 'Some body.']);

        $path = $response->headers->get('location');

        $this->get($path)->assertSee('Some Title')->assertSee('Some body.');
    }

    /**
     * @test
     */
    public function a_thread_requires_a_title()
    {

        $this->publishThread(['title' => null])->assertSessionHasErrors('title');
    }

    /**
     * @test
     */
    public function a_thread_requires_a_unique_slug()
    {
        $this->signIn();

        $thread = create(Thread::class, ['title' => 'Foo Title']);

        $this->assertEquals($thread->slug, 'foo-title');

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();

        $this->assertEquals("foo-title-{$thread['id']}", $thread['slug']);
    }

    /**
     * @test
     */
    public function a_thread_with_a_title_that_ends_in_a_number_should_generate_the_proper_slug()
    {
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'Some Title 24']);

        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();

        $this->assertEquals("some-title-24-{$thread['id']}", $thread['slug']);
    }

    /**
     * @test
     */
    public function a_thread_requires_a_body()
    {

        $this->publishThread(['body' => null])->assertSessionHasErrors('body');
    }

    /** @test */
    function a_thread_requires_recaptcha_verification()
    {
        unset(app()[Recaptcha::class]);

        $this->publishThread(['g-recaptcha-response' => 'test'])
            ->assertSessionHasErrors('g-recaptcha-response');

    }

    /**
     * @test
     */
    public function a_thread_requires_a_valid_channel()
    {

        factory(Channel::class, 2)->create();

        $this->publishThread(['channel_id' => null])->assertSessionHasErrors('channel_id');

        //a channel that doesn't exist.
        $this->publishThread(['channel_id' => 999])->assertSessionHasErrors('channel_id');
    }

    /**
     * @test
     */
    public function unauthorized_users_may_not_delete_threads()
    {
        $this->withExceptionHandling();

        $thread = create(Thread::class);

        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();

        $this->delete($thread->path())->assertStatus(403);
    }

    /**
     * @test
     */
    public function authorized_users_can_delete_threads()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $reply = create(Reply::class, ['thread_id' => $thread->id]);

        $this->json('DELETE', $thread->path());

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);

        $this->assertEquals(0, Activity::count());
    }

    /**
     * @param array $overrides
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();

        $thread = make(Thread::class, $overrides);

        return $this->post(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token']);
    }
}

