<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


class UserController extends Controller
{
    public function index(Request $request) {
    /*
        $keyword = $request->keyword;

        $users = User::all();
        $total_all = User::count();

        if ($keyword !== null) {
            $users = User::when($keyword, function($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                        ->orWhere('kana', 'like', "%{$keyword}%");
            })->paginate(15);
            $total = $users->count();
        } else {
            $users = User::paginate(15);
            $total = "";
        }


        return view('admin.users.index', compact('users', 'total_all', 'keyword', 'total'));
    }
    */

    // 検索ボックスに入力されたキーワードを取得する
    $keyword = $request->input('keyword');

    // キーワードが存在すれば氏名またはフリガナで部分一致検索を行い、そうでなければ全件取得する
    if ($keyword) {
        $users = User::where('name', 'like', "%{$keyword}%")->orWhere('kana', 'like', "%{$keyword}%")->paginate(15);
    } else {
        $users = User::paginate(15);
    }

    $total = $users->total();

    return view('admin.users.index', compact('users', 'keyword', 'total'));
}



    public function show(User $user)
    {

        return view('admin.users.show', compact('user'));
    }
}
