<?php

namespace Tests\Models\Instagram;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Models\Instagram\InstagramModel;

class InstagramModelTest extends TestCase
{
    private InstagramModel $model;

    protected function setUp(): void
    {
        $this->model = new InstagramModel();
    }

    // =============================================
    // Stories
    // =============================================

    #[Test]
    public function getStoriesReturnsNonEmptyArray(): void
    {
        $stories = $this->model->getStories();
        $this->assertNotEmpty($stories);
        $this->assertCount(12, $stories);
    }

    #[Test]
    public function eachStoryHasRequiredKeys(): void
    {
        $requiredKeys = ['username', 'avatar', 'is_yours', 'profile_url', 'is_unseen'];
        foreach ($this->model->getStories() as $i => $story) {
            foreach ($requiredKeys as $key) {
                $this->assertArrayHasKey($key, $story, "Story #$i manque la clé '$key'");
            }
        }
    }

    #[Test]
    public function firstStoryIsMelina(): void
    {
        $stories = $this->model->getStories();
        $this->assertSame('mel_133', $stories[0]['username']);
    }

    // =============================================
    // User Profiles
    // =============================================

    #[Test]
    public function getAllUserProfilesReturnsAllProfiles(): void
    {
        $profiles = $this->model->getAllUserProfiles();
        $this->assertNotEmpty($profiles);
        $this->assertArrayHasKey('mel_133', $profiles);
        $this->assertArrayHasKey('anna_food', $profiles);
        $this->assertArrayHasKey('avsl_ydbjb', $profiles);
    }

    #[Test]
    public function getUserProfileReturnsCorrectProfile(): void
    {
        $profile = $this->model->getUserProfile('mel_133');
        $this->assertNotNull($profile);
        $this->assertSame('mel_133', $profile['username']);
        $this->assertSame('Melina', $profile['display_name']);
    }

    #[Test]
    public function getUserProfileReturnsNullForUnknown(): void
    {
        $this->assertNull($this->model->getUserProfile('unknown_user_xyz'));
    }

    #[Test]
    #[DataProvider('profileKeysProvider')]
    public function eachProfileHasRequiredKeys(string $username): void
    {
        $profile = $this->model->getUserProfile($username);
        $this->assertNotNull($profile);

        $requiredKeys = ['username', 'display_name', 'avatar', 'posts_count', 'followers_count', 'following_count', 'bio', 'posts'];
        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $profile, "Profile '$username' manque la clé '$key'");
        }
    }

    public static function profileKeysProvider(): array
    {
        return [
            'mel_133'         => ['mel_133'],
            'anna_food'       => ['anna_food'],
            'avsl_ydbjb'      => ['avsl_ydbjb'],
            'brooke_kitchen'  => ['brooke_kitchen'],
            'leo_creative'    => ['leo_creative'],
        ];
    }

    // =============================================
    // Chat Messages
    // =============================================

    #[Test]
    public function getChatMessagesForKnownUser(): void
    {
        $messages = $this->model->getUserChatMessages('anna_food');
        $this->assertNotEmpty($messages);
        $this->assertArrayHasKey('type', $messages[0]);
        $this->assertArrayHasKey('content', $messages[0]);
        $this->assertArrayHasKey('time', $messages[0]);
    }

    #[Test]
    public function getChatMessagesForUnknownUserReturnsDefault(): void
    {
        $messages = $this->model->getUserChatMessages('unknown_user_xyz');
        $this->assertNotEmpty($messages);
        $this->assertCount(3, $messages);
    }

    #[Test]
    public function melinaChatContainsCipherHints(): void
    {
        $messages = $this->model->getUserChatMessages('mel_133');
        $combined = implode(' ', array_column($messages, 'content'));
        $this->assertStringContainsString('César', $combined);
    }

    // =============================================
    // Posts (sans session)
    // =============================================

    #[Test]
    public function getPostsReturnsAtLeast10Posts(): void
    {
        // Sans session unlocked, on a 10 posts de base
        $posts = $this->model->getPosts();
        $this->assertGreaterThanOrEqual(10, count($posts));
    }

    #[Test]
    public function eachPostHasRequiredKeys(): void
    {
        $requiredKeys = ['id', 'username', 'avatar', 'image', 'likes', 'caption', 'time'];
        foreach ($this->model->getPosts() as $i => $post) {
            foreach ($requiredKeys as $key) {
                $this->assertArrayHasKey($key, $post, "Post #$i manque la clé '$key'");
            }
        }
    }
}
