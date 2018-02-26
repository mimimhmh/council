<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{
    /**
     * @test
     */
    public function it_checks_for_invalid_keywords()
    {

        // Invalid keywords

        // Key held down
        $spam = new Spam();

        $this->assertFalse($spam->detect('slag'));

        $this->expectException('Exception');

        $spam->detect('yahoo customer support');
    }

    /**
     * @test
     */
    public function it_checks_for_any_key_held_down()
    {
        $spam = new Spam();

        $this->expectException('Exception');

        $spam->detect('Hello world aaaaaa');
    }
}
