<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Models\Term;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TermTest extends TestCase
{
    use RefreshDatabase;

    // 利用規約ページ(indexアクション)
    // 1.未ログインのユーザーは会員側の利用規約ページにアクセスできる
    public function test_guest_cannot_access_company_index()
    {
        $company = Term::factory()->create();

        $response = $this->get(route('terms.index'));

        $response->assertStatus(200);
    }

    // 2.ログイン済みの一般ユーザーは会員側の利用規約ページにアクセスできる
    public function test_user_can_access_company_index()
    {
        $user = User::factory()->create();
        $company = Term::factory()->create();

        $response = $this->actingAs($user)->get(route('terms.index'));
        $response->assertStatus(200);
    }

    // 3.ログイン済みの管理者は会員側の利用規約ページにアクセスできない
    public function test_adminUser_cannot_access_company_index()
    {
        $adminUser = Admin::factory()->create();
        $company = Term::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('terms.index'));
        $response->assertRedirect(route('admin.home'));
    }
}
