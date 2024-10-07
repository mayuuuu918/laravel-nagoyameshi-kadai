<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーは管理者側の会員一覧ページにアクセスできない
     *
     * @return void
     */
    public function test_guest_cannot_access_admin_users_index()
    {
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_cannot_access_admin_users_index()
    {
        // 一般ユーザーを作成しログインさせる
        $user = factory(User::class)->create();
        $this->actingAs($user);

        // 管理者側の会員一覧ページにアクセスし、403エラーが返されることを確認
        $response = $this->get(route('admin.users.index'));
        $response->assertForbidden();
    }

    public function test_authenticated_admin_can_access_admin_users_index()
    {
        // 管理者ユーザーを作成しログインさせる
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin);

        // 管理者側の会員一覧ページにアクセスし、200ステータスが返されることを確認
        $response = $this->get(route('admin.users.index'));
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_admin_user_show()
    {
        // ユーザーを作成する
        $user = factory(User::class)->create();

        // 未ログイン状態で会員詳細ページにアクセス
        $response = $this->get(route('admin.users.show', $user));

        // ログインページにリダイレクトされることを確認
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_cannot_access_admin_user_show()
    {
        // 一般ユーザーを作成しログインさせる
        $user = factory(User::class)->create();
        $this->actingAs($user);

        // 管理者側の会員詳細ページにアクセスし、403エラーが返されることを確認
        $adminUser = factory(User::class)->create();
        $response = $this->get(route('admin.users.show', $adminUser));
        $response->assertForbidden();
    }

    public function test_authenticated_admin_can_access_admin_user_show()
    {
        // 管理者ユーザーを作成しログインさせる
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin);

        // 管理者側の会員詳細ページにアクセスし、200ステータスが返されることを確認
        $user = factory(User::class)->create();
        $response = $this->get(route('admin.users.show', $user));
        $response->assertStatus(200);
    }
}
