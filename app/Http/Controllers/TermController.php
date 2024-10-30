<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Term;

class TermController extends Controller
{
    // 利用規約ページ
    public function index() {
        // termsテーブルの最初のデータを取得
        $term = Term::first();

        return view('terms.index', compact('term'));
    }

}
