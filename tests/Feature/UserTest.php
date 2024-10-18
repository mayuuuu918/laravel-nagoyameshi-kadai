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
    use RefreshDatabase;
    /**
     * 未ログインユーザーは会員側の会員情報ページにアクセスできない
     *
     * @return void
     */
    public function test_guest_cannot_access_admin_user_index()
    {
        $response = $this->get(route('user.index'));

        $response->assertRedirect(route('login'));
    }

      // ログイン済みの一般ユーザーは会員側の会員情報ページにアクセスできる
    public function test_user_can_access_user_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.index'));

        $response->assertStatus(200);
    }

     // ログイン済みの管理者は会員側の会員情報ページにアクセスできない
    public function test_admin_cannot_user_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('user.index'));

        $response->assertRedirect(route('admin.home'));
    }

    /*
     * 未ログインユーザーは会員側の会員情報編集ページにアクセスできない
     */
    public function test_guest_cannot_access_user_edit()
    {
        $user = User::factory()->create();

        $response = $this->get(route('user.edit', $user->id));

        $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは会員側の他人の会員情報編集ページにアクセスできない
    public function test_user_cannot_access_another_user_edit()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create(); // 他のユーザー

         // $userでログイン
        $this->actingAs($user);

        // 他人の編集ページにアクセス
        $response = $this->get(route('user.edit', $anotherUser->id));

        // 会員情報ページにリダイレクトされることを確認（user.index リダイレクト）
        $response->assertRedirect(route('user.index'));

        // 不正なアクセスのフラッシュメッセージが設定されていることを確認
        $response->assertSessionHas('error_message', '不正なアクセスです。');
    }

      // ログイン済みの一般ユーザーは会員側の自身の会員情報編集ページにアクセスできる
    public function test_user_can_access_user_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.edit'));

        $response->assertStatus(200);
    }

     // ログイン済みの管理者は会員側の会員情報編集ページにアクセスできない
    public function test_admin_cannot_user_edit()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('user.edit'));

        $response->assertRedirect(route('admin.home'));
    }

    /**
    * 未ログインのユーザーは会員情報を更新できない
    */
public function test_guest_cannot_update_user()
{
    // テスト用のユーザーを作成
    $user = User::factory()->create();

    // 会員情報の更新リクエスト（未ログインの状態）
    $response = $this->patch(route('user.update', $user->id), [
        'name' => 'New Name',
        'email' => 'newemail@example.com'
    ]);

    // ログインページにリダイレクトされることを確認
    $response->assertRedirect(route('login'));
}

/**
 * ログイン済みの一般ユーザーは他人の会員情報を更新できない
 */
public function test_logged_in_user_cannot_update_another_users()
{
    // テスト用のユーザーを2人作成
    $user = User::factory()->create(); // ログイン中のユーザー
    $anotherUser = User::factory()->create(); // 他のユーザー

    // $userでログイン
    $this->actingAs($user);

    // 他人の会員情報を更新しようとする
    $response = $this->patch(route('user.update', $anotherUser->id), [
        'name' => 'New Name',
        'email' => 'newemail@example.com'
    ]);

    // 自分の会員情報ページにリダイレクトされることを確認
    $response->assertRedirect(route('user.index'));

    // 不正なアクセスのフラッシュメッセージを確認
    $response->assertSessionHas('error_message', '不正なアクセスです。');
}

/**
 * ログイン済みの一般ユーザーは自身の会員情報を更新できる
 */
public function test_logged_in_user_can_update_own_info()
{
    // テスト用のユーザーを作成
    $user = User::factory()->create();

    // $userでログイン
    $this->actingAs($user);

    // 自分の会員情報を更新
    $response = $this->patch(route('user.update', $user->id), [
        'name' => 'Updated Name',
        'email' => 'updatedemail@example.com'
    ]);

    // データベースが更新されたことを確認
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updatedemail@example.com'
    ]);

    // リダイレクト先
    $response->assertRedirect(route('user.index'));
}

/**
 * ログイン済みの管理者は会員情報を更新できない
 */
public function test_admin_cannot_update_user_info()
{
    // テスト用の管理者とユーザーを作成
    $admin = Admin::factory()->create(); // 管理者
    $user = User::factory()->create(); // 一般ユーザー

    // $adminでログイン（管理者ガードを利用）
    $this->actingAs($admin, 'admin');

    // 一般ユーザーの会員情報を更新しようとする
    $response = $this->patch(route('user.update', $user->id), [
        'name' => 'Admin Updated Name',
        'email' => 'adminupdatedemail@example.com'
    ]);

    // 一般ユーザーの編集ページにアクセスできないため、リダイレクト確認
    $response->assertRedirect(route('admin.dashboard'));

    // エラーメッセージの確認（必要に応じてフラッシュメッセージを設定する場合）
    $response->assertSessionHas('error_message', '管理者は会員情報を更新できません。');
}




}
