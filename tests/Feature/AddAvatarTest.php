<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddAvatarTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function only_members_can_add_avatars()
    {
        $this->withExceptionHandling();

        $this->json('post', 'api/users/{user}/avatar')->assertStatus(401);
    }

    /**
     * @test
     */
    public function a_valid_avatar_must_be_provided()
    {
        $this->signIn();

        Storage::fake('public');

        $this->json('post', 'api/users/'.auth()->id().'/avatar', [
                'avatar' => $file = UploadedFile::fake()->image('avatar.jpg'),
            ]);

        $this->assertEquals(asset('storage/avatars/'.$file->hashName()), auth()->user()->avatar_path);

        Storage::disk('public')->assertExists('avatars/'. $file->hashName());
    }
}
