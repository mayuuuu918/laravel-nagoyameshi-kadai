<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\RegularHoliday;
use Illuminate\Support\Facades\Hash;


class HomeTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインユーザーは管理者側のtopページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_home()
    {
        $response = $this->get(route('admin.restaurants.home'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側のtopページにアクセスできない
    public function test_user_cannot_access_admin_restaurants_home()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.home'));

        $response->assertRedirect(route('admin.login'));
    }

     // ログイン済みの管理者は管理者側のtopページにアクセスできる
    public function test_admin_can_access_admin_restaurants_home()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.home'));

        $response->assertStatus(200);
    }

}
