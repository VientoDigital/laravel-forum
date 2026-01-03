<?php

namespace Vientodigital\LaravelForum\Tests\Feature\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Vientodigital\LaravelForum\Models\Setting;
use Vientodigital\LaravelForum\Tests\Fixtures\User;
use Vientodigital\LaravelForum\Tests\TestCase;

class SettingControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoutes();
    }

    #[Test]
    public function it_can_store_a_setting(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->post(route('forum.settings.store'), [
                'key' => 'new_setting',
                'value' => 'setting_value',
            ]);

        $this->assertDatabaseHas('forum_settings', [
            'key' => 'new_setting',
            'value' => 'setting_value',
        ]);
    }

    #[Test]
    public function it_validates_key_when_storing(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->post(route('forum.settings.store'), [
                'key' => '',
                'value' => 'some_value',
            ])
            ->assertSessionHasErrors('key');
    }

    #[Test]
    public function it_validates_value_when_storing(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->post(route('forum.settings.store'), [
                'key' => 'test_key',
                'value' => '',
            ])
            ->assertSessionHasErrors('value');
    }

    #[Test]
    public function it_validates_unique_key(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        Setting::create([
            'key' => 'existing_key',
            'value' => 'existing_value',
        ]);

        $this->actingAs($user)
            ->post(route('forum.settings.store'), [
                'key' => 'existing_key',
                'value' => 'new_value',
            ])
            ->assertSessionHasErrors('key');
    }

    #[Test]
    public function it_can_update_a_setting(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $setting = Setting::create([
            'key' => 'forum_name',
            'value' => 'Test Forum',
        ]);

        $this->actingAs($user)
            ->put(route('forum.settings.update', ['setting' => $setting]), [
                'key' => 'forum_name',
                'value' => 'Updated Forum Name',
            ]);

        $this->assertDatabaseHas('forum_settings', [
            'id' => $setting->id,
            'value' => 'Updated Forum Name',
        ]);
    }

    #[Test]
    public function it_can_update_setting_key(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $setting = Setting::create([
            'key' => 'old_key',
            'value' => 'some_value',
        ]);

        $this->actingAs($user)
            ->put(route('forum.settings.update', ['setting' => $setting]), [
                'key' => 'new_key',
                'value' => 'some_value',
            ]);

        $this->assertDatabaseHas('forum_settings', [
            'id' => $setting->id,
            'key' => 'new_key',
        ]);
    }

    #[Test]
    public function it_can_delete_a_setting(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $setting = Setting::create([
            'key' => 'forum_name',
            'value' => 'Test Forum',
        ]);

        $this->actingAs($user)
            ->delete(route('forum.settings.destroy', ['setting' => $setting]));

        $this->assertDatabaseMissing('forum_settings', [
            'id' => $setting->id,
        ]);
    }
}
