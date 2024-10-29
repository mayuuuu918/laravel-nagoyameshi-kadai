<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Restaurant $restaurant)
    {
        // 有料プランに登録済みの場合
        if (Auth::user()->subscribed('premium_plan')) {
            $reviews = Review::where('restaurant_id', $restaurant->id)
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        // 有料プラン未登録
        } else {
            $reviews = Review::where('restaurant_id', $restaurant->id)
            ->orderBy('created_at', 'desc')
            ->take(3)  //3件取得
            ->get();
        }
        return view('reviews.index', compact('restaurant', 'reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Restaurant $restaurant)
    {
        return view('reviews.create', compact('restaurant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'score' => 'required|numeric|between:1,5',
            'content' => 'required'
        ]);

        $review = new Review();
        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->restaurant_id = $restaurant->id;
        $review->user_id = $request->user()->id;
        $review->save();

        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを登録しました。');
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Restaurant $restaurant, Review $review)
    {
        // 現在ログイン中のユーザーのIDと編集対象のユーザーIDを比較
        if (Auth::id() !== $review->user_id) {
        // 不一致の場合、会員情報ページにリダイレクトし、エラーメッセージをセッションに保存
        return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
    }
        return view('reviews.edit', compact('restaurant', 'review'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Restaurant $restaurant, Review $review)
    {
        // 現在ログイン中のユーザーのIDと編集対象のユーザーIDを比較
        if (Auth::id() !== $review->user_id) {
        // 不一致の場合、会員情報ページにリダイレクトし、エラーメッセージをセッションに保存
        return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        $request->validate([
        'score' => ['required', 'numeric', 'between:1,5'],
        'content' => ['required'],
        ]);

        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->save();

        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを編集しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Review $review)
    {
        // 現在ログイン中のユーザーのIDと編集対象のユーザーIDを比較
        if (Auth::id() !== $review->user_id) {
            // 不一致の場合、会員情報ページにリダイレクトし、エラーメッセージをセッションに保存
            return redirect()->route('restaurants.review.index', $restaurant)->with('error_message', '不正なアクセスです。');
            }

        $review->delete();

        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを削除しました。');
    }
}
