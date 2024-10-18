<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        // 検索ボックスに入力されたキーワードを取得する
        $keyword = $request->input('keyword');
        $category_id = $request->input('category_id');
        $price = $request->input('price');

        $sorts = [
        '掲載日が新しい順' => 'created_at desc',
        '価格が安い順' => 'lowest_price asc'
        ];

        $sorted = "created_at desc";
        $sort_query = [];

    if ($request->has('select_sort')) {
        $slices = explode(' ', $request->input('select_sort'));
        $sort_query[$slices[0]] = $slices[1];
        $sorted = $request->input('select_sort');
    }

    $categories = Category::all();

    $restaurants = Restaurant::sortable($sort_query)->orderBy('created_at', 'desc')->paginate(15);

    if($keyword !== null){
        $restaurants = Restaurant::where('address', 'like', "%{$keyword}%")->orWhere('name', 'like', "%{$keyword}%")->orWhereHas('categories', function ($query) use ($keyword) {
                $query->where('categories.name', 'like', "%{$keyword}%"); })->sortable($sort_query)->orderBy('created_at', 'desc')->paginate(15);


    } elseif ($category_id !== null) {
        $restaurants = Restaurant::whereHas('categories', function ($query) use ($category_id) {
            $query->where('categories.id', '=', "{$category_id}");
        })->sortable($sort_query)->orderBy('created_at', 'desc')->paginate(15);
        $total = $restaurants->total();
    } elseif($price !== null){
        $restaurants = Restaurant::where('lowest_price', '<', "{$price}")->sortable($sort_query)->orderBy('created_at', 'desc')->paginate(15);
        $total = $restaurants->total();
    } else {
            $restaurants = Restaurant::sortable($sort_query)->orderBy('created_at', 'desc')->paginate(15);
            $total = $restaurants->total();
        }

    $total = $restaurants->total();

    return view('restaurants.index', compact('keyword', 'category_id', 'price', 'sorts', 'sorted', 'restaurants', 'categories', 'total'));
}

}
