<?php

namespace Vientodigital\LaravelForum\Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Models\Discussion;
use Vientodigital\LaravelForum\Models\Discussion\Tag as DiscussionTag;
use Vientodigital\LaravelForum\Models\Tag;
use Vientodigital\LaravelForum\Tests\Fixtures\User;
use Vientodigital\LaravelForum\Tests\TestCase;

class DiscussionTagTest extends TestCase
{
    #[Test]
    public function it_can_create_a_discussion_tag_pivot(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $discussion = Discussion::create([
            'title' => 'Test Discussion',
            'slug' => 'test-discussion',
            'user_id' => $user->id,
        ]);

        $tag = Tag::create([
            'name' => 'PHP',
            'slug' => 'php',
        ]);

        $discussionTag = DiscussionTag::create([
            'discussion_id' => $discussion->id,
            'tag_id' => $tag->id,
        ]);

        $this->assertDatabaseHas('forum_discussion_tag', [
            'discussion_id' => $discussion->id,
            'tag_id' => $tag->id,
        ]);
    }

    #[Test]
    public function it_uses_correct_table_name(): void
    {
        $discussionTag = new DiscussionTag();
        $this->assertEquals('forum_discussion_tag', $discussionTag->getTable());
    }

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $discussionTag = new DiscussionTag();
        $fillable = $discussionTag->getFillable();

        $this->assertContains('discussion_id', $fillable);
        $this->assertContains('tag_id', $fillable);
    }

    #[Test]
    public function it_can_link_multiple_tags_to_discussion(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $discussion = Discussion::create([
            'title' => 'Test Discussion',
            'slug' => 'test-discussion',
            'user_id' => $user->id,
        ]);

        $tag1 = Tag::create(['name' => 'PHP', 'slug' => 'php']);
        $tag2 = Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);

        DiscussionTag::create(['discussion_id' => $discussion->id, 'tag_id' => $tag1->id]);
        DiscussionTag::create(['discussion_id' => $discussion->id, 'tag_id' => $tag2->id]);

        $this->assertCount(2, DiscussionTag::where('discussion_id', $discussion->id)->get());
    }
}
