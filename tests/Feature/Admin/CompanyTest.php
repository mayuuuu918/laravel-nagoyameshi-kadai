<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    // 未ログインのユーザーは管理者側の会社概要ページにアクセスできない
    public function test_guest_cannot_access_admin_company_index()
    {
        $response = $this->get(route('admin.company.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会社概要ページにアクセスできない
    public function test_user_cannot_access_admin_company_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.company.index'));

        $response->assertRedirect(route('admin.login'));
    }

     // ログイン済みの管理者は管理者側の会社概要ページにアクセスできる
    public function test_admin_can_access_admin_company_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.company.index'));

        $response->assertStatus(200);
    }

    // 未ログインのユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_guest_cannot_access_admin_company_edit()
    {
        $company = Company::factory()->create();

        $response = $this->get(route('admin.company.create', $company));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_user_cannot_access_admin_company_edit()
    {
        $user = User::factory()->create();

        $company = Company::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.company.edit', $restaurant));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の会社概要編集ページにアクセスできる
    public function test_admin_can_access_admin_company_edit()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $company = Company::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.company.edit', $company));

        $response->assertStatus(200);
    }

     // 未ログインのユーザーは会社概要を更新できない
    public function test_guest_cannot_access_company_update()
    {
            // レストランのダミーデータを準備する
            $company = Company::factory()->create();

            // 更新しようとするデータ
            $updateData = [
                'name' => 'テスト更新',
                'postal_code' => '0000001',
                'address' => 'テスト',
                'representative' => 'テスト',
                'establishment_date' => 'テスト',
                'capital' => 'テスト',
                'business' => 'テスト',
                'number_of_employees' => 'テスト',
            ];

            // 未ログインのユーザーが更新しようとした場合のレスポンス
            $response = $this->patch(route('admin.company.update', $company), $updateData);

            $this->assertDatabaseMissing('companies', $updateData);

            $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは会社概要を更新できない
    public function test_user_cannot_access_admin_company_update()
    {
            $user = User::factory()->create();
            // レストランのダミーデータを準備する
            $company = Company::factory()->create();

            // 更新しようとするデータ
            $updateData = [
                'name' => 'テスト更新',
                'postal_code' => '0000001',
                'address' => 'テスト',
                'representative' => 'テスト',
                'establishment_date' => 'テスト',
                'capital' => 'テスト',
                'business' => 'テスト',
                'number_of_employees' => 'テスト',
            ];

            $response = $this->actingAs($user)->patch(route('admin.company.update', $company),$updateData);

            $this->assertDatabaseMissing('company', $updateData);

            $response->assertRedirect(route('admin.login'));
            }

     // ログイン済みの管理者は会社概要を更新できる
    public function test_admin_can_access_admin_company_update()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

            // レストランのダミーデータを準備する
            $company = Company::factory()->create();

            // 更新しようとするデータ
            $updateData = [
                'name' => 'テスト更新',
                'postal_code' => '0000001',
                'address' => 'テスト',
                'representative' => 'テスト',
                'establishment_date' => 'テスト',
                'capital' => 'テスト',
                'business' => 'テスト',
                'number_of_employees' => 'テスト',
            ];


            $response = $this->actingAs($admin, 'admin')->patch(route('admin.company.update', $company), $updateData);

            $this->assertDatabaseHas('company', $updateData);

            $company = Company::latest('id')->first();

            $response->assertRedirect(route('admin.company.show', $company));
    }


}
