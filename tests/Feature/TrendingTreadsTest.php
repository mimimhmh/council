<?php

namespace Tests\Feature;

use App\Thread;
use App\Trending;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrendingTreadsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();

        $this->trending = new Trending();

        $this->trending->reset();
    }

    /**
     * @test
     */
    public function it_increments_a_threads_score_each_time_it_is_read()
    {
        $this->signIn();

        $this->assertEmpty($this->trending->get());

        $thread = create(Thread::class);

        $this->call('GET', $thread->path());

        $this->assertCount(1, $trending = $this->trending->get());

        $this->assertEquals($thread->title, $trending[0]->title);
    }
}
