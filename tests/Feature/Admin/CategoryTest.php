<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    // 1.未ログインのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_categories_index()
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_user_cannot_access_admin_categories_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.categories.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者は管理者側のカテゴリ一覧ページにアクセスできる
    public function test_admin_can_access_admin_categories_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.categories.index'));

        $response->assertStatus(200);
    }


    // 4.未ログインのユーザーはカテゴリを登録できない
    public function test_guest_cannot_access_admin_categories_store()
    {

        $categories_data = ['name' => 'テスト登録'];

        $response = $this->post(route('admin.categories.store'), $categories_data);

        $this->assertDatabaseMissing('categories', $categories_data);
        $response->assertRedirect(route('admin.login'));
    }

    // 5.ログイン済みの一般ユーザーはカテゴリを登録できない
    public function test_user_cannot_access_admin_categories_store()
    {
        $user = User::factory()->create();

        $categories_data = ['name' => 'テスト登録'];

        $response = $this->actingAs($user)->post(route('admin.categories.store'), $categories_data);

        $this->assertDatabaseMissing('categories', $categories_data);
        $response->assertRedirect(route('admin.login'));
    }

    // 6.ログイン済みの管理者はカテゴリを登録できる
    public function test_admin_can_access_admin_categories_store()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $categories_data = ['name' => 'テスト登録'];

        $response = $this->actingAs($admin, 'admin')->post(route('admin.categories.store'), $categories_data);

        $this->assertDatabaseHas('categories', $categories_data);
        $response->assertRedirect(route('admin.categories.index'));
    }

    // 7.未ログインのユーザーは店カテゴリを更新できない
    public function test_guest_cannot_access_admin_categories_update()
    {

        $categories_data = Category::factory()->create();

        $new_categories_data = ['name' => 'テスト更新'];

        $response = $this->patch(route('admin.categories.update', $categories_data), $new_categories_data);

        $this->assertDatabaseMissing('categories', $new_categories_data);
        $response->assertRedirect(route('admin.login'));
    }

    // 8.ログイン済みの一般ユーザーはカテゴリを更新できない
    public function test_user_cannot_access_admin_categories_update()
    {
        // ミドルウェアを無効にする（CSRF保護を無効化）
        $this->withoutMiddleware();

        $user = User::factory()->create();

        $categories_data = Category::factory()->create();

        $new_categories_data = ['name' => 'テスト更新'];

        $response = $this->actingAs($user)->patch(route('admin.categories.update', $categories_data), $new_categories_data);

        $this->assertDatabaseMissing('categories', $new_categories_data);
        $response->assertRedirect(route('admin.login'));
    }

    // 9.ログイン済みの管理者はカテゴリを更新できる
    public function test_admin_can_access_admin_categories_update()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $categories_data = Category::factory()->create();

        $new_categories_data = ['name' => 'テスト更新'];

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.categories.update', $categories_data), $new_categories_data);

        $this->assertDatabaseHas('categories', $new_categories_data);
        $response->assertRedirect(route('admin.categories.index', $categories_data));
    }

    // 未ログインのユーザーはカテゴリを削除できない
    public function test_guest_cannot_access_admin_categories_destroy()
    {
        // ミドルウェアを無効にする（CSRF保護を無効化）
        $this->withoutMiddleware();

        $categories = Category::factory()->create();

        $response = $this->delete(route('admin.categories.destroy', $categories));

        $this->assertDatabaseHas('categories', ['id' => $categories->id]);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーはカテゴリを削除できない
    public function test_user_cannot_access_admin_categories_destroy()
    {
        // ミドルウェアを無効にする（CSRF保護を無効化）
        $this->withoutMiddleware();

        $user = User::factory()->create();

        $categories = Category::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.categories.destroy', $categories));

        $this->assertDatabaseHas('categories', ['id' => $categories->id]);
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者はカテゴリを削除できる
    public function test_admin_can_access_admin_categories_destroy()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $categories = Category::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.categories.destroy', $categories));

        $this->assertDatabaseMissing('categories', ['id' => $categories->id]);
        $response->assertRedirect(route('admin.categories.index'));
    }

}
