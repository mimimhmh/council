<?php

namespace Tests\Unit;

use App\Channel;
use App\Notifications\ThreadWasUpdated;
use App\Thread;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ThreadsTest extends TestCase
{
    use RefreshDatabase;

    protected $thread;

    protected function setUp()
    {
        parent::setUp();

        $this->thread = create(Thread::class);
    }

    /**
     * @test
     */
    public function a_thread_has_a_path()
    {
        $thread = create(Thread::class);

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->slug}", $thread->path());
    }

    /**
     * @test
     */
    public function a_thread_has_replies()
    {

        $this->assertInstanceOf(Collection::class, $this->thread->replies);
    }

    /**
     * @test
     */
    public function a_thread_has_a_creator()
    {

        $response = $this->get($this->thread->path());

        $response->assertSee($this->thread->creator->name);
        $this->assertInstanceOf(User::class, $this->thread->creator);
    }

    /**
     * @test
     */
    public function a_thread_can_add_a_reply()
    {

        $this->thread->addReply([
            'user_id' => 1,
            'body' => 'Foobar',
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /**
     * @test
     */
    public function a_thread_belongs_to_a_channel()
    {
        $thread = create(Thread::class);

        $this->assertInstanceOf(Channel::class, $thread->channel);
    }

    /**
     * @test
     */
    public function a_thread_can_be_subscribed_to()
    {
        //given we have a thread
        $thread = create(Thread::class);

        //And an authenticated user

        //When a user subscribe a thread
        $thread->subscribe($userId = 1);

        //Then we should be able to fetch all threads that the user has subscribed to.
        $this->assertEquals(1, $thread->subscriptions()->where('user_id', $userId)->count());
    }

    /**
     * @test
     */
    public function a_thread_can_be_unsubscribed_from()
    {
        //given we have a thread
        $thread = create(Thread::class);

        //When a user subscribe a thread
        $thread->subscribe($userId = 1);

        //a user unsubscribe a thread
        $thread->unsubscribe($userId);

        $this->assertCount(0, $thread->subscriptions);
    }

    /**
     * @test
     */
    public function it_knows_if_the_authenticated_user_is_subscribed_to_it()
    {
        $thread = create(Thread::class);

        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /**
     * @test
     */
    public function a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake();

        $this->signIn();

        $this->thread->subscribe()->addReply([
                'body' => 'Foobar',
                'user_id' => 999,
            ]);

        Notification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    /**
     * @test
     */
    public function a_thread_can_check_if_the_authenticated_user_has_read_all_replies()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $this->assertTrue($thread->hasUpdatesFor(auth()->user()));

        auth()->user()->read($thread);

        $this->assertFalse($thread->hasUpdatesFor(auth()->user()));
    }

    /** @test */
    function a_threads_body_is_sanitized_automatically()
    {
        $thread = make('App\Thread', ['body' => '<script>alert("bad")</script><p>This is okay.</p>']);

        $this->assertEquals("<p>This is okay.</p>", $thread->body);
    }
}
