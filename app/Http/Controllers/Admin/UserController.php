<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
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

    public function show(User $user)
    {

        return view('admin.users.show', compact('user'));
    }
}
