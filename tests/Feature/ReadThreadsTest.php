<?php

namespace Tests\Feature;

use App\Channel;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    use RefreshDatabase;

    private $thread;

    public function setUp()
    {

        parent::setUp();

        $this->thread = factory(Thread::class)->create();
    }

    /**
     * @test
     */
    public function a_user_can_view_all_threads()
    {

        $response = $this->get('/threads');

        $response->assertSee($this->thread->title);
    }

    /**
     * @test
     */
    public function a_user_can_view_a_single_thread()
    {

        $response = $this->get($this->thread->path());

        $response->assertSee($this->thread->title);
    }

    /**
     * @test
     */
    public function a_user_can_filter_threads_according_to_a_channel()
    {

        $channel = create(Channel::class);

        $threadInChannel = create(Thread::class, ['channel_id' => $channel->id]);

        $threadNotInChannel = create(Thread::class);

        $response = $this->get('/threads/'.$channel->slug);

        $response->assertSee($threadInChannel->title);

        $response->assertDontSee($threadNotInChannel->title);
    }

    /**
     * @test
     */
    public function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create(User::class, ['name' => 'JohnDoe']));

        $threadByJohn = create(Thread::class, ['user_id' => auth()->id()]);

        $threadNotByJohn = create(Thread::class);

        $response = $this->get('/threads?by=JohnDoe');

        $response->assertSee($threadByJohn->title);

        $response->assertDontSee($threadNotByJohn->title);
    }

    /**
     * @test
     */
    public function a_user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = create(Thread::class);
        create(Reply::class, ['thread_id' => $threadWithTwoReplies->id], 2);

        $threadWithThreeReplies = create(Thread::class);
        create(Reply::class, ['thread_id' => $threadWithThreeReplies->id], 3);

        $threadWithZeroReply = $this->thread;

        $response = $this->getJson('threads?popular=1')->json();

        //dd(array_column($response['data'], 'replies_count'));

        $this->assertEquals([3, 2, 0], array_column($response['data'], 'replies_count'));
    }

    /**
     * @test
     */
    public function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $thread = create(Thread::class);

        create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1, $response['data']);
    }

    /**
     * @test
     */
    public function a_user_can_request_all_replies_for_a_given_thread()
    {
        $thread = create('App\Thread');
        create(Reply::class, ['thread_id' => $thread->id]);

        $response = $this->getJson($thread->path().'/replies')->json();

        $this->assertCount(1, $response['data']);
    }

    /**
     * @test
     */
    public function we_record_a_new_visit_each_time_the_thread_is_read()
    {
        $thread = create('App\Thread');

        $this->assertSame(0, $thread->visits);

        $this->call('GET', $thread->path());

        $this->assertEquals(1, $thread->fresh()->visits);
    }
}
