<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    // 会社概要ページ
    public function index() {
    // companiesテーブルの最初のデータを取得
    $company = Company::first();

    return view('company.index', compact('company'));
    }
}
