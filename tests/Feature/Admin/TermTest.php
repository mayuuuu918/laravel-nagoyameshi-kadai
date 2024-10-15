<?php

namespace Tests\Feature\Admin;

use App\Models\Term;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TermTest extends TestCase
{
    // 未ログインのユーザーは管理者側の利用規約ページにアクセスできない
    public function test_guest_cannot_access_admin_term_index()
    {
        $response = $this->get(route('admin.term.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の利用規約ページにアクセスできない
    public function test_user_cannot_access_admin_term_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.term.index'));

        $response->assertRedirect(route('admin.login'));
    }

     // ログイン済みの管理者は管理者側の利用規約ページにアクセスできる
    public function test_admin_can_access_admin_term_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.term.index'));

        $response->assertStatus(200);
    }

    // 未ログインのユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_guest_cannot_access_admin_term_edit()
    {
        $term = Term::factory()->create();

        $response = $this->get(route('admin.term.create', $term));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_user_cannot_access_admin_term_edit()
    {
        $user = User::factory()->create();

        $term = Term::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.term.edit', $term));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は管理者側の利用規約編集ページにアクセスできる
    public function test_admin_can_access_admin_company_edit()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $term = Term::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.term.edit', $term));

        $response->assertStatus(200);
    }

     // 未ログインのユーザーは利用規約を更新できない
    public function test_guest_cannot_access_term_update()
    {
            $term = Term::factory()->create();

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
            $response = $this->patch(route('admin.term.update', $term), $updateData);

            $this->assertDatabaseMissing('terms', $updateData);

            $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは利用規約を更新できない
    public function test_user_cannot_access_admin_term_update()
    {
            $user = User::factory()->create();
            $term = Term::factory()->create();

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

            $response = $this->actingAs($user)->patch(route('admin.term.update', $term),$updateData);

            $this->assertDatabaseMissing('term', $updateData);

            $response->assertRedirect(route('admin.login'));
            }

     // ログイン済みの管理者は利用規約を更新できる
    public function test_admin_can_access_admin_term_update()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

            $term = Term::factory()->create();

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


            $response = $this->actingAs($admin, 'admin')->patch(route('admin.term.update', $term), $updateData);

            $this->assertDatabaseHas('term', $updateData);

            $term = Term::latest('id')->first();

            $response->assertRedirect(route('admin.term.show', $term));
    }


}
