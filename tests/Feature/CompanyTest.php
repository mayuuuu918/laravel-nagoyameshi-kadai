<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    // 会社概要ページ(indexアクション)
    // 1.未ログインのユーザーは会員側の会社概要ページにアクセスできる
    public function test_guest_cannot_access_company_index()
    {
        $company = Company::factory()->create();

        $response = $this->get(route('company.index'));

        $response->assertStatus(200);
    }

    // 2.ログイン済みの一般ユーザーは会員側の会社概要ページにアクセスできる
    public function test_user_can_access_company_index()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $response = $this->actingAs($user)->get(route('company.index'));
        $response->assertStatus(200);
    }

    // 3.ログイン済みの管理者は会員側の会社概要ページにアクセスできない
    public function test_adminUser_cannot_access_company_index()
    {
        $adminUser = Admin::factory()->create();
        $company = Company::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('company.index'));
        $response->assertRedirect(route('admin.home'));
    }
}
