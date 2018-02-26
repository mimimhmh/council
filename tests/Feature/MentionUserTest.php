<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MentionUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function mentioned_users_in_a_reply_are_notified()
    {
        $john = create(User::class, ['name' => 'JohnDoe']);

        $this->signIn($john);

        $jane = create(User::class, ['name' => 'JaneDoe']);

        $thread = create(Thread::class);

        $reply = make(Reply::class, [
            'body' => 'Hey @JaneDoe look at this.']);

        $this->json('post', $thread->path().'/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }

    /**
     * @test
     */
    function it_can_fetch_all_mentioned_users_starting_with_the_given_characters()
    {
        $this->signIn();

        create('App\User', ['name' => 'johndoe']);
        create('App\User', ['name' => 'johndoe2']);
        create('App\User', ['name' => 'janedoe']);

        $results = $this->json('GET', '/api/users', ['name' => 'john']);

        $this->assertCount(2, $results->json());
    }
}
