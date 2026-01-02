<?php

namespace Vientodigital\LaravelForum\Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Models\Setting;
use Vientodigital\LaravelForum\Tests\TestCase;

class SettingTest extends TestCase
{
    #[Test]
    public function it_can_create_a_setting(): void
    {
        $setting = Setting::create([
            'key' => 'forum_name',
            'value' => 'My Forum',
        ]);

        $this->assertDatabaseHas('forum_settings', [
            'key' => 'forum_name',
            'value' => 'My Forum',
        ]);
    }

    #[Test]
    public function it_uses_correct_table_name(): void
    {
        $setting = new Setting();
        $this->assertEquals('forum_settings', $setting->getTable());
    }

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $setting = new Setting();
        $fillable = $setting->getFillable();

        $this->assertContains('key', $fillable);
        $this->assertContains('value', $fillable);
    }

    #[Test]
    public function it_can_update_a_setting(): void
    {
        $setting = Setting::create([
            'key' => 'forum_name',
            'value' => 'My Forum',
        ]);

        $setting->update(['value' => 'Updated Forum']);

        $this->assertDatabaseHas('forum_settings', [
            'key' => 'forum_name',
            'value' => 'Updated Forum',
        ]);
    }
}
