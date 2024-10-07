<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)

    {
        // 検索ボックスに入力されたキーワードを取得する
        $keyword = $request->input('keyword');

        // キーワードが存在すれば氏名またはフリガナで部分一致検索を行い、そうでなければ全件取得する
        if ($keyword) {
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}%")->paginate(15);
        } else {
            $restaurants = Restaurant::paginate(15);
        }

        $total = $restaurants->total();

        return view('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.restaurants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'image|max:2048kb',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lt:highest_price',
            'highest_price' => 'required|numeric|min:0|gt:lowest_price',
            'postal_code' => 'required|digits:7',
            'address' => 'required',
            'opening_time' => 'required|date_format:H:i|before:closing_time',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

        $restaurant = new Restaurant();
        $restaurant->name = $request->input('name');
        $restaurant->image = $request->input('image');
        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');

        // アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存し、戻り値（ファイルパス）を変数$imageに代入する
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public.restaurants');

            // ファイルパスからファイル名のみを取得し、Restaurantインスタンスのimage_nameプロパティに代入する
            $image = basename($path);
            $restaurant->image = $image;
        } else {
            // 画像がアップロードされていない場合、空文字を代入
            $restaurant->image = '';
        }


        $restaurant->save();

        return to_route('admin.restaurants.index')->with('flash_message', '店舗を登録しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant)
    {
        return view('admin.restaurants.show', compact('restaurant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    // 編集ページ
    public function edit(Restaurant $restaurant)
    {
        return view('admin.restaurants.edit', compact('restaurant'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lt:highest_price',
            'highest_price' => 'required|numeric|min:0|gt:lowest_price',
            'postal_code' => 'required|digits:7',
            'address' => 'required',
            'opening_time' => 'required|date_format:H:i|before:closing_time',
            'closing_time' => 'required|date_format:H:i|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);


        $restaurant->name = $request->input('name');
        $restaurant->image = basename('image');
        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');

        // アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存し、戻り値（ファイルパス）を変数$imageに代入する
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/restaurants');

            // ファイルパスからファイル名のみを取得し、Restaurantインスタンスのimage_nameプロパティに代入する
            $image = basename($path);
            $restaurant->image = $image;

        } else {
        // アップロードされていない場合、空の文字列を設定
            $restaurant->image = $restaurant->image ?? '';  // デフォルトで空の文字列
        }

        $restaurant->update();

        return to_route('admin.restaurants.show', $restaurant->id)->with('flash_message', '店舗を編集しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を削除しました。');
    }
}
