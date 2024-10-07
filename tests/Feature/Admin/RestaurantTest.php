<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
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

class AdminRestaurantTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーは管理者側の店舗一覧ページにアクセスできない
     *
     * @return void
     */
    public function test_guest_cannot_access_admin_restaurant_index()
    {
        $response = $this->get(route('admin.restaurants.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_user_cannot_access_admin_restaurant_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.index'));

        $response->assertRedirect(route('admin.login'));
    }

     // ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
    public function test_admin_can_access_admin_restaurant_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.index'));

        $response->assertStatus(200);
    }

    // 未ログインのユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurant_show()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('admin.restaurants.show', $restaurant->id));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_user_cannot_access_admin_restaurant_show()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.show', $restaurant));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗詳細ページにアクセスできる
    public function test_admin_can_access_admin_restaurant_show()
    {
        // 管理者ユーザーを作成
        $admin = User::factory()->create([
            'is_admin' => true, // 管理者権限を持つユーザー
        ]);

        // ダミーレストランデータを作成
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.restaurants.show', $restaurant->id));

        $response->assertStatus(200);
    }

    // 未ログインのユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurant_create()
    {
        $user = User::factory()->create();

        $response = $this->get(route('admin.restaurants.create', $user));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_user_cannot_access_admin_restaurant_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.create', $user));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる
    public function test_admin_can_access_admin_restaurant_create()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.create'));

        $response->assertStatus(200);
    }

     // 未ログインのユーザーは店舗を登録できない
    public function test_guest_cannot_access_restaurants_store()
    {
            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->make()->toArray(); // レコード作成ではなく、データ生成のみ

            // 未ログインのユーザーが店舗を登録しようとした場合のレスポンス
            $response = $this->post(route('admin.restaurants.store'), $post);

            // レストランテーブルにデータが登録されていないことを確認
            $this->assertDatabaseMissing('restaurants', $restaurant);
            $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは店舗を登録できない
    public function test_user_cannot_access_admin_restaurant_store()
    {
            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->make()->toArray(); // レコード作成ではなく、データ生成のみ

            $user = User::factory()->create();

            $response = $this->actingAs($user)->get(route('admin.restaurants.store', $restaurant));

            // アクセス拒否 (403 Forbidden) が返されることを期待
            $response->assertStatus(403);
    }

     // ログイン済みの管理者は店舗を登録できる
    public function test_admin_can_access_restaurants_store()
    {
            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->make()->toArray(); // レコード作成ではなく、データ生成のみ

            $admin = User::factory()->create([
                'is_admin' => true, // 管理者フラグをtrueにする
            ]);


            $response = $this->actingAs($admin)->post(route('restaurants.store'), $restaurant);

            $this->assertDatabaseHas('restaurants', ['name' => $restaurant['name']]);
            $response->assertRedirect(route('restaurants.index'));
    }

    // 未ログインのユーザーは管理者側の店舗編集ページにアクセスできない
        public function test_guest_cannot_access_admin_restaurant_edit()
    {
        $user = User::factory()->create();

        $response = $this->get(route('admin.restaurants.create', $user));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_user_cannot_access_admin_restaurant_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.edit', $user));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗編集ページにアクセスできる
    public function test_admin_can_access_admin_restaurant_edit()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.edit'));

        $response->assertStatus(200);
    }

     // 未ログインのユーザーは店舗を更新できない
    public function test_guest_cannot_access_restaurants_update()
    {

            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->create();

            // 更新しようとするデータ
            $updateData = [
                'name' => 'テスト',
                'description' => 'テスト',
                'lowest_price' => 1000,
                'highest_price' => 5000,
                'postal_code' => '0000000',
                'address' => 'テスト',
                'opening_time' => '10:00:00',
                'closing_time' => '20:00:00',
                'seating_capacity' => 50,

            ];

            // 未ログインのユーザーが店舗を登録しようとした場合のレスポンス
            $response = $this->patch(route('admin.restaurants.update', $restaurant), $updateData);

            // レストランテーブルにデータが登録されていないことを確認
            $this->assertDatabaseMissing('restaurants', $updateData);
            $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは店舗を更新できない
    public function test_user_cannot_access_admin_restaurant_update()
    {
            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->create();

            // 更新しようとするデータ
            $updateData = [
                'name' => 'テスト',
                'description' => 'テスト',
                'lowest_price' => 1000,
                'highest_price' => 5000,
                'postal_code' => '0000000',
                'address' => 'テスト',
                'opening_time' => '10:00:00',
                'closing_time' => '20:00:00',
                'seating_capacity' => 50,

            ];

            $user = User::factory()->create();

            $response = $this->actingAs($user)->patch(route('admin.restaurants.update', $restaurant),$updateData);
            $this->assertDatabaseMissing('restaurants', $updateData);
            $response->assertStatus(403);
    }

     // ログイン済みの管理者は店舗を更新できる
    public function test_user_can_access_restaurants_update()
    {
            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->create();

            $admin = User::factory()->create([
                'is_admin' => true, // 管理者フラグをtrueにする
            ]);

            // 更新しようとするデータ
            $updateData = [
                'name' => 'テスト',
                'description' => 'テスト',
                'lowest_price' => 1000,
                'highest_price' => 5000,
                'postal_code' => '0000000',
                'address' => 'テスト',
                'opening_time' => '10:00:00',
                'closing_time' => '20:00:00',
                'seating_capacity' => 50,

            ];


            $response = $this->actingAs($admin)->patch(route('restaurants.update', $restaurant), $updateData);

            $this->assertDatabaseHas('restaurants', [
                'id' => $restaurant->id,
                'name' => $updateData['name'], // 更新された値
                'description' => $updateData['description'],
                'lowest_price' => $updateData['lowest_price'],
                'highest_price' => $updateData['highest_price'],
                'postal_code' => $updateData['postal_code'],
                'address' => $updateData['address'],
                'opening_time' => $updateData['opening_time'],
                'closing_time' => $updateData['closing_time'],
                'seating_capacity' => $updateData['seating_capacity'],
                ]);

            $response->assertRedirect(route('restaurants.index'));
    }

     // 未ログインのユーザーは店舗を削除できない
    public function test_guest_cannot_access_restaurants_destroy()
    {
            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->create(); // レコード作成ではなく、データ生成のみ

            // 未ログインのユーザーが店舗を削除しようとした場合のレスポンス
            $response = $this->delete(route('admin.restaurants.destroy', $restaurant->id));

            // レストランテーブルにデータが削除されていないことを確認
            $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);
            $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは店舗を削除できない
    public function test_user_cannot_access_admin_restaurant_destroy()
    {
            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->create(); // レコード作成ではなく、データ生成のみ

            $user = User::factory()->create();

            $response = $this->actingAs($user)->delete(route('admin.restaurants.destroy', $restaurant));

            // アクセス拒否 (403 Forbidden) が返されることを期待
            $response->assertStatus(403);

            // レストランがデータベースから削除されていないことを確認
            $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);
    }

     // ログイン済みの管理者は店舗を削除できる
    public function test_user_can_access_restaurants_destroy()
    {
            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->create();

            $admin = User::factory()->create([
                'is_admin' => true, // 管理者フラグをtrueにする
            ]);


            $response = $this->actingAs($admin)->delete(route('restaurants.destroy', $restaurant->id));

            $this->assertDatabaseMissing('restaurants', ['id' => $restaurant['id']]);
            $response->assertRedirect(route('restaurants.index'));
    }
}
