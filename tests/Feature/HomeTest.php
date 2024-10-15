<?php

namespace Tests\Feature;


use App\Models\Restaurants;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーは会員側のトップページにアクセスできる
     *
     * @return void
     */
    public function test_guest_can_access_home_index()
    {
        $response = $this->get(route('home'));

        $response = $this->actingAs()->get(route('home'));
        $response->assertStatus(200);
    }

     // ログイン済みの一般ユーザーは会員側のトップページにアクセスできる
    public function test_user_can_access_admin_home_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側のトップページにアクセスできない
    public function test_admin_cannot_access_home_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('home'));

        $response->assertRedirect(route('admin.login'));
    }
}
