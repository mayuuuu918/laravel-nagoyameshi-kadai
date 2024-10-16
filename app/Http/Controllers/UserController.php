<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    // 会員情報ページ
    public function index() {
        // 現在ログインしているユーザーのデータを取得
        $user = Auth::user();

        return view('user.index', compact('user'));
    }


     // 編集ページ
    public function edit(User $user) {


        // 現在ログイン中のユーザーのIDと編集対象のユーザーIDを比較
        if (Auth::id() !== $user->id) {
            // 不一致の場合、会員情報ページにリダイレクトし、エラーメッセージをセッションに保存
            return redirect()->route('user.index')->with('error_message', '不正なアクセスです。');
        }

        return view('user.edit', compact('user'));
    }

    // 更新ページ
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kana' => 'required|string|regex:/\A[ァ-ヴー]+\z/u|max:255',
            'email' => [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users')->ignore($user->id),
        ],
            'postal_code' => 'required|numeric|digits:7',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|numeric|min_digits:10|max_digits:11',
            'birthday' => 'nullable|numeric|digits:8',
            'occupation' => 'nullable|string|max:255',
        ]);


        // 現在ログイン中のユーザーのIDと編集対象のユーザーIDを比較
        if (Auth::id() !== $user->id) {
            // 不一致の場合、会員情報ページにリダイレクトし、エラーメッセージをセッションに保存
            return redirect()->route('user.profile')->with('error_message', '不正なアクセスです。');
        }

        $user->name = $request->input('name');
        $user->kana = $request->input('kana');
        $user->email = $request->input('email');
        $user->postal_code = $request->input('postal_code');
        $user->address = $request->input('address');
        $user->phone_number = $request->input('phone_number');
        $user->birthday = $request->input('birthday');
        $user->occupation = $request->input('occupation');

        $user->save();

        return redirect()->route('user.index', $user)->with('flash_message', '会員情報を編集しました。');
    }

}
