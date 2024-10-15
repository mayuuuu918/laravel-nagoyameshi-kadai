<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Term;


class TermController extends Controller
{
    // 利用規約ページ
    public function index() {
        // termsテーブルの最初のデータを取得
        $term = Term::first();

        return view('admin.terms.index', compact('term'));
    }


     // 利用規約編集ページ
    public function edit(Term $term) {

        return view('admin.terms.edit', compact('term'));
    }

    // 利用規約更新ページ
    public function update(Request $request, Term $term)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $term->content = $request->input('content');

        $term->save();

        return redirect()->route('admin.terms.index')->with('flash_message', '利用規約を編集しました。');
    }
}
