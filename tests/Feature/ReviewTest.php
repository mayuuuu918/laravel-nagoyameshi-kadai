<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Review;
use App\Models\Restaurant;


class ReviewTest extends TestCase
{
    //  未ログインユーザーは会員側のレビュー一覧ページにアクセスできない
    public function test_unauthenticated_users_cannot_access_member_side_review_list_page()
    {
        $response = $this->get('review.index');
        $response->assertRedirect('login');
    }


    // ログイン済みの無料会員は会員側のレビュー一覧ページにアクセスできる
    public function test_logged_in_free_members_can_access_member_side_review_list_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('review.index'));
        $response->assertStatus(200);
    }

    //  ログイン済みの有料会員は会員側のレビュー一覧ページにアクセスできる
    public function test_logged_in_paid_members_can_access_member_side_review_list_page()
    {
        $user = User::factory()->create();
        $user->newSubscription('default', 'price_1PBCDL2MIT849GWt3ambAejQ')->create('pm_card_visa');

        $response = $this->actingAs($user)->get('review.index');
        $response->assertStatus(200);
    }

    //  ログイン済みの管理者は会員側のレビュー一覧ページにアクセスできない
    public function test_logged_in_administrators_cannot_access_member_side_review_list_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('review.index'));
        $response->assertRedirect(route('admin.home'));
    }

    // create アクション（レビュー投稿ページ）
    // 未ログインのユーザーは会員側のレビュー投稿ページにアクセスできない
    public function test_guest_user_cannot_access_member_side_review_submission_page()
    {
        $response = $this->get('/reviews/create');
        $response->assertRedirect('/login');
    }

    // ログイン済みの無料会員は会員側のレビュー投稿ページにアクセスできない
    public function test_logged_in_free_member_cannot_access_member_side_review_submission_page()
    {
        $user = User::factory()->create(['type' => 'freeMember']);

        $response = $this->actingAs($user)->get('/reviews/create');
        $response->assertStatus(403);
    }

    // ログイン済みの有料会員は会員側のレビュー投稿ページにアクセスできる
    public function test_logged_in_paid_member_can_access_member_side_review_submission_page()
    {
        $user = User::factory()->create(['type' => 'premiumMember']);

        $response = $this->actingAs($user)->get('/reviews/create');
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側のレビュー投稿ページにアクセスできない
    public function test_logged_in_admin_cannot_access_member_side_review_submission_page()
    {
        $admin = User::factory()->create(['type' => 'admin']);

        $response = $this->actingAs($admin)->get('/reviews/create');
        $response->assertStatus(403);
    }

    // レビュー投稿
    public function test_guest_user_cannot_submit_review()
    {
        $response = $this->post('/reviews/store', ['content' => 'Sample Review']);
        $response->assertRedirect('/login');
    }

    public function test_logged_in_free_member_cannot_submit_review()
    {
        $user = User::factory()->create(['type' => 'freeMember']);

        $response = $this->actingAs($user)->post('/reviews/store', ['content' => 'Sample Review']);
        $response->assertStatus(403);
    }

    public function test_logged_in_paid_member_can_submit_review()
    {
        $user = User::factory()->create(['type' => 'premiumMember']);

        $response = $this->actingAs($user)->post('/reviews/store', ['content' => 'Sample Review']);
        $response->assertStatus(302); // リダイレクトを想定
    }

    public function test_logged_in_admin_cannot_submit_review()
    {
        $admin = User::factory()->create(['type' => 'admin']);

        $response = $this->actingAs($admin)->post('/reviews/store', ['content' => 'Sample Review']);
        $response->assertStatus(403);
    }

    // レビュー編集ページ
    public function test_guest_user_cannot_access_member_side_review_edit_page()
    {
        $response = $this->get('/reviews/edit/1');
        $response->assertRedirect('/login');
    }

    public function test_logged_in_free_member_cannot_access_review_edit_page()
    {
        $user = User::factory()->create(['type' => 'freeMember']);

        $response = $this->actingAs($user)->get('/reviews/edit/1');
        $response->assertStatus(403);
    }

    public function test_logged_in_paid_member_cannot_access_others_review_edit_page()
    {
        $user = User::factory()->create(['type' => 'premiumMember']);
        $anotherUser = User::factory()->create(['type' => 'premiumMember']);

        $response = $this->actingAs($user)->get("/reviews/edit/{$anotherUser->id}");
        $response->assertStatus(403);
    }
    public function test_logged_in_paid_member_can_access_own_review_edit_page()

    {
        $user = User::factory()->create(['type' => 'premiumMember']);

        $response = $this->actingAs($user)->get("/reviews/edit/{$user->id}");
        $response->assertStatus(200);
    }

    public function test_logged_in_admin_cannot_access_member_side_review_edit_page()
    {
        $admin = User::factory()->create(['type' => 'admin']);

        $response = $this->actingAs($admin)->get("/reviews/edit/1");
        $response->assertStatus(403);
    }

    public function test_guest_user_cannot_update_review()
{
    $review = Review::factory()->create();
    $response = $this->patch(route('reviews.update', $review), [
        'score' => 5,
        'content' => 'Updated Content'
    ]);
    $response->assertRedirect(route('login'));
    $this->assertDatabaseMissing('reviews', ['content' => 'Updated Content']);
}

public function test_logged_in_free_member_cannot_update_review()
{
    $user = User::factory()->freeMember()->create();
    $review = Review::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);
    $response = $this->patch(route('reviews.update', $review), [
        'score' => 5,
        'content' => 'Updated Content'
    ]);
    $response->assertStatus(403);
    $this->assertDatabaseMissing('reviews', ['content' => 'Updated Content']);
}

public function test_logged_in_paid_member_cannot_update_others_review()
{
    $user = User::factory()->paidMember()->create();
    $otherUser = User::factory()->create();
    $review = Review::factory()->create(['user_id' => $otherUser->id]);
    $this->actingAs($user);
    $response = $this->patch(route('reviews.update', $review), [
        'score' => 5,
        'content' => 'Updated Content'
    ]);
    $response->assertStatus(403);
    $this->assertDatabaseMissing('reviews', ['content' => 'Updated Content']);
}

public function test_logged_in_paid_member_can_update_own_review()
{
    $user = User::factory()->paidMember()->create();
    $review = Review::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);
    $response = $this->patch(route('reviews.update', $review), [
        'score' => 5,
        'content' => 'Updated Content'
    ]);
    $response->assertStatus(200);
    $this->assertDatabaseHas('reviews', ['content' => 'Updated Content']);
}

public function test_logged_in_admin_cannot_update_review()
{
    $admin = User::factory()->admin()->create();
    $review = Review::factory()->create();
    $this->actingAs($admin);
    $response = $this->patch(route('reviews.update', $review), [
        'score' => 5,
        'content' => 'Updated Content'
    ]);
    $response->assertStatus(403);
    $this->assertDatabaseMissing('reviews', ['content' => 'Updated Content']);
}


public function test_guest_user_cannot_delete_review()
{
    $review = Review::factory()->create();
    $response = $this->delete(route('reviews.destroy', $review));
    $response->assertRedirect(route('login'));
    $this->assertDatabaseHas('reviews', ['id' => $review->id]);
}

public function test_logged_in_free_member_cannot_delete_review()
{
    $user = User::factory()->freeMember()->create();
    $review = Review::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);
    $response = $this->delete(route('reviews.destroy', $review));
    $response->assertStatus(403);
    $this->assertDatabaseHas('reviews', ['id' => $review->id]);
}

public function test_logged_in_paid_member_cannot_delete_others_review()
{
    $user = User::factory()->paidMember()->create();
    $otherUser = User::factory()->create();
    $review = Review::factory()->create(['user_id' => $otherUser->id]);
    $this->actingAs($user);
    $response = $this->delete(route('reviews.destroy', $review));
    $response->assertStatus(403);
    $this->assertDatabaseHas('reviews', ['id' => $review->id]);
}


public function test_logged_in_paid_member_can_delete_own_review()
{
    $user = User::factory()->paidMember()->create();
    $review = Review::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);
    $response = $this->delete(route('reviews.destroy', $review));
    $response->assertStatus(200);
    $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
}

public function test_logged_in_admin_cannot_delete_review()
{
    $admin = User::factory()->admin()->create();
    $review = Review::factory()->create();
    $this->actingAs($admin);
    $response = $this->delete(route('reviews.destroy', $review));
    $response->assertStatus(403);
    $this->assertDatabaseHas('reviews', ['id' => $review->id]);
}

}
