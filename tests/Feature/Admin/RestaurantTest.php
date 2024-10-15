<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\RegularHoliday;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーは管理者側の店舗一覧ページにアクセスできない
     *
     * @return void
     */
    public function test_guest_cannot_access_admin_restaurants_index()
    {
        $response = $this->get(route('admin.restaurants.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_user_cannot_access_admin_restaurants_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.index'));

        $response->assertRedirect(route('admin.login'));
    }

     // ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.index'));

        $response->assertStatus(200);
    }

    // 未ログインのユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_show()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('admin.restaurants.show', $restaurant));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_user_cannot_access_admin_restaurants_show()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.show', $restaurant));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗詳細ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_show()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        // ダミーレストランデータを作成
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.show', $restaurant));

        $response->assertStatus(200);
    }

    // 未ログインのユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_create()
    {
        $response = $this->get(route('admin.restaurants.create'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_user_cannot_access_admin_restaurants_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.create'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_create()
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
        $categories = Category::factory()->count(3)->create();
        $category_ids = $categories->pluck('id')->toArray();

        $regular_Holidays = RegularHoliday::factory()->count(3)->create();
        $regularHoliday_Ids = $regularHolidays->pluck('id')->toArray();


            // レストランのダミーデータを準備する
            $restaurant_data = [
                'name' => 'テスト',
                'description' => 'テスト',
                'lowest_price' => 1000,
                'highest_price' => 5000,
                'postal_code' => '0000000',
                'address' => 'テスト',
                'opening_time' => '10:00:00',
                'closing_time' => '20:00:00',
                'seating_capacity' => 50,
                'category_ids' => $category_ids,
                'regular_holiday_ids' => $regularHoliday_Ids,
            ];

            // 未ログインのユーザーが店舗を登録しようとした場合のレスポンス
            $response = $this->post(route('admin.restaurants.store'), $restaurant_data);

        unset($restaurant_data['category_ids'], $restaurant_data['regular_holiday_ids']);
        // データベースに更新内容が反映されていないことを確認
        $this->assertDatabaseMissing('restaurants', $restaurant_data);

        foreach ($category_ids as $category_id) {
            $this->assertDatabaseMissing('category_restaurant', ['category_id' => $category_id]);
        }

        foreach ($regular_holiday_ids as $regular_holiday_id) {
            $this->assertDatabaseMissing('regular_holiday_restaurant', ['regular_holiday_id' => $regular_holiday_id]);
        }

            $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは店舗を登録できない
    public function test_user_cannot_access_admin_restaurants_store()
    {
            $user = User::factory()->create();

            $categories = Category::factory()->count(3)->create();
            $category_ids = $categories->pluck('id')->toArray();

            $regular_holidays = RegularHoliday::factory()->count(3)->create();
            $regular_holiday_ids = $regular_holidays->pluck('id')->toArray();


            $restaurant_data = [
                'name' => 'テスト',
                'description' => 'テスト',
                'lowest_price' => 1000,
                'highest_price' => 5000,
                'postal_code' => '0000000',
                'address' => 'テスト',
                'opening_time' => '10:00:00',
                'closing_time' => '20:00:00',
                'seating_capacity' => 50,
                'category_ids' => $category_ids,
                'regular_holiday_ids' => $regular_Holiday_Ids,
            ];

            $response = $this->actingAs($user)->post(route('admin.restaurants.store', $restaurant_data));

            unset($restaurant_data['category_ids'], $restaurant_data['regular_holiday_ids']);
            $this->assertDatabaseMissing('restaurants', $restaurant_data);

            foreach ($category_ids as $category_id) {
                $this->assertDatabaseMissing('category_restaurant', ['category_id' => $category_id]);
            }

            foreach ($regular_holiday_ids as $regular_holiday_id) {
                $this->assertDatabaseMissing('regular_holiday_restaurant', ['regular_holiday_id' => $regular_holiday_id]);
            }

            $response->assertRedirect(route('admin.login'));
        }

     // ログイン済みの管理者は店舗を登録できる
    public function test_admin_can_access_restaurants_store()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $categories = Category::factory()->count(3)->create();
        $category_ids = $categories->pluck('id')->toArray();

        $regular_holidays = RegularHoliday::factory()->count(3)->create();
        $regular_holiday_ids = $regular_holidays->pluck('id')->toArray();


        $restaurant_data = [
            'name' => 'テスト',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
            'category_ids' => $category_ids,
            'regular_holiday_ids' => $regular_Holiday_Ids,
        ];

        $response = $this->actingAs($admin, 'admin')->post(route('admin.restaurants.store'), $restaurant_data);

        unset($restaurant_data['category_ids'], $restaurant_data['regular_holiday_ids']);
        $this->assertDatabaseHas('restaurants', $restaurant_data);

        $restaurant = Restaurant::latest('id')->first();

        foreach ($category_ids as $category_id) {
            $this->assertDatabaseHas('category_restaurant', ['restaurant_id' => $restaurant->id, 'category_id' => $category_id]);
        }

        foreach ($regular_holiday_ids as $regular_holiday_id) {
            $this->assertDatabaseHas('regular_holiday_restaurant', ['restaurant_id' => $restaurant->id, 'regular_holiday_id' => $regular_holiday_id]);
        }
            $response->assertRedirect(route('admin.restaurants.index'));

    }

    // 未ログインのユーザーは管理者側の店舗編集ページにアクセスできない
        public function test_guest_cannot_access_admin_restaurants_edit()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('admin.restaurants.create', $restaurant));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_user_cannot_access_admin_restaurants_edit()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.edit', $restaurant));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の店舗編集ページにアクセスできる
    public function test_admin_can_access_admin_restaurants_edit()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.edit', $restaurant));

        $response->assertStatus(200);
    }

     // 未ログインのユーザーは店舗を更新できない
    public function test_guest_cannot_access_restaurants_update()
    {
            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->create();

            $categories = Category::factory()->count(3)->create();
            $category_ids = $categories->pluck('id')->toArray();

            $regular_holidays = RegularHoliday::factory()->count(3)->create();
            $regular_holiday_ids = $regular_holidays->pluck('id')->toArray();


            // 更新しようとするデータ
            $updateData = [
                'name' => 'テスト更新',
                'description' => 'テスト更新',
                'lowest_price' => 1000,
                'highest_price' => 5000,
                'postal_code' => '0000000',
                'address' => 'テスト',
                'opening_time' => '10:00:00',
                'closing_time' => '20:00:00',
                'seating_capacity' => 50,
                'category_ids' => $category_ids,
                'regular_holiday_ids' => $regular_Holiday_Ids,

            ];

            // 未ログインのユーザーが店舗を登録しようとした場合のレスポンス
            $response = $this->patch(route('admin.restaurants.update', $restaurant), $updateData);

            unset($new_restaurant_data['category_ids'], $new_restaurant_data['regular_holiday_ids']);
            $this->assertDatabaseMissing('restaurants', $new_restaurant_data);

            foreach ($category_ids as $category_id) {
                $this->assertDatabaseMissing('category_restaurant', ['category_id' => $category_id]);
            }

            foreach ($regular_holiday_ids as $regular_holiday_id) {
                $this->assertDatabaseMissing('regular_holiday_restaurant', ['regular_holiday_id' => $regular_holiday_id]);
            }
            $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは店舗を更新できない
    public function test_user_cannot_access_admin_restaurant_update()
    {
            $user = User::factory()->create();
            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->create();

            $categories = Category::factory()->count(3)->create();
            $category_ids = $categories->pluck('id')->toArray();

            $regular_holidays = RegularHoliday::factory()->count(3)->create();
            $regular_holiday_ids = $regular_holidays->pluck('id')->toArray();

            // 更新しようとするデータ
            $updateData = [
                'name' => 'テスト更新',
                'description' => 'テスト',
                'lowest_price' => 1000,
                'highest_price' => 5000,
                'postal_code' => '0000000',
                'address' => 'テスト',
                'opening_time' => '10:00:00',
                'closing_time' => '20:00:00',
                'seating_capacity' => 50,
                'category_ids' => $category_ids,
                'regular_holiday_ids' => $regular_Holiday_Ids,

            ];

            $response = $this->actingAs($user)->patch(route('admin.restaurants.update', $restaurant),$updateData);

            unset($new_restaurant_data['category_ids'], $new_restaurant_data['regular_holiday_ids']);
            $this->assertDatabaseMissing('restaurants', $new_restaurant_data);

            foreach ($category_ids as $category_id) {
                $this->assertDatabaseMissing('category_restaurant', ['category_id' => $category_id]);
            }

            foreach ($regular_holiday_ids as $regular_holiday_id) {
                $this->assertDatabaseMissing('regular_holiday_restaurant', ['regular_holiday_id' => $regular_holiday_id]);
            }
            $response->assertRedirect(route('admin.login'));
            }

     // ログイン済みの管理者は店舗を更新できる
    public function test_admin_can_access_admin_restaurants_update()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->create();

            $categories = Category::factory()->count(3)->create();
            $category_ids = $categories->pluck('id')->toArray();

            $regular_holidays = RegularHoliday::factory()->count(3)->create();
            $regular_holiday_ids = $regular_holidays->pluck('id')->toArray();


            // 更新しようとするデータ
            $updateData = [
                'name' => 'テスト更新',
                'description' => 'テスト',
                'lowest_price' => 1000,
                'highest_price' => 5000,
                'postal_code' => '0000000',
                'address' => 'テスト',
                'opening_time' => '10:00:00',
                'closing_time' => '20:00:00',
                'seating_capacity' => 50,
                'category_ids' => $category_ids,
                'regular_holiday_ids' => $regular_Holiday_Ids,
            ];


            $response = $this->actingAs($admin, 'admin')->patch(route('admin.restaurants.update', $restaurant), $updateData);

            unset($new_restaurant_data['category_ids'], $new_restaurant_data['regular_holiday_ids']);
            $this->assertDatabaseHas('restaurants', $new_restaurant_data);

            $restaurant = Restaurant::latest('id')->first();

            foreach ($category_ids as $category_id) {
                $this->assertDatabaseHas('category_restaurant', ['restaurant_id' => $restaurant->id, 'category_id' => $category_id]);
            }

            foreach ($regular_holiday_ids as $regular_holiday_id) {
                $this->assertDatabaseHas('regular_holiday_restaurant', ['restaurant_id' => $restaurant->id, 'regular_holiday_id' => $regular_holiday_id]);
            }

            $response->assertRedirect(route('admin.restaurants.show', $restaurant));
    }

     // 未ログインのユーザーは店舗を削除できない
    public function test_guest_cannot_access_restaurants_destroy()
    {
            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->create(); // レコード作成ではなく、データ生成のみ

            // 未ログインのユーザーが店舗を削除しようとした場合のレスポンス
            $response = $this->delete(route('admin.restaurants.destroy', $restaurant));

            // レストランテーブルにデータが削除されていないことを確認
            $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);
            $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは店舗を削除できない
    public function test_user_cannot_access_admin_restaurants_destroy()
    {
            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->create(); // レコード作成ではなく、データ生成のみ

            $user = User::factory()->create();

            $response = $this->actingAs($user)->delete(route('admin.restaurants.destroy', $restaurant));

            // レストランがデータベースから削除されていないことを確認
            $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);
            $response->assertRedirect(route('admin.login'));
        }

     // ログイン済みの管理者は店舗を削除できる
    public function test_admin_can_access_admin_restaurants_destroy()
    {
            $admin = new Admin();
            $admin->email = 'admin@example.com';
            $admin->password = Hash::make('nagoyameshi');
            $admin->save();
            // レストランのダミーデータを準備する
            $restaurant = Restaurant::factory()->create();

            $response = $this->actingAs($admin, 'admin')->delete(route('restaurants.destroy', $restaurant));

            $this->assertDatabaseMissing('restaurants', ['id' => $restaurant['id']]);
            $response->assertRedirect(route('admin.restaurants.index'));
    }
}
