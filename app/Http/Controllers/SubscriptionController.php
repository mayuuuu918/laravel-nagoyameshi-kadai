<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\User;


class SubscriptionController extends Controller
{
    // 有料プラン登録ページ
    public function create() {

        $intent = Auth::user()->createSetupIntent();
        return view('subscription.create', compact('intent'));
        }

    // 有料プラン登録機能
    public function store(Request $request) {
        // 現在ログインしているユーザーのデータを取得
            $request->user()->newSubscription(
                'premium_plan', 'price_1QCJDRP6PHEjO4IVj7pjbxvu'
            )->create($request->paymentMethodId);

            return redirect()->route('home')->with('flash_message', '有料プランへの登録が完了しました。');
        }

    // お支払い方法編集ページ
    public function edit(Request $request) {
        // 現在ログインしているユーザーのデータを取得
        $user = Auth::user();
        // 現在ログイン中のユーザーのSetupIntentオブジェクト
        $intent = Auth::user()->createSetupIntent();

        return view('subscription.edit', compact('user', 'intent'));
    }

    // お支払い方法更新機能
    public function update(Request $request)
    {
        $request->user()->updateDefaultPaymentMethod($request->$paymentMethod);

        return redirect()->route('home')->with('flash_message', 'お支払い方法を変更しました。');
    }

    // 有料プラン解約ページ
    public function cancel()
    {
        return view('subscription.cancel');
    }

    // 有料プラン解約機能
    public function destroy(Request $request)
    {
        $request->user()->subscription(
            'premium_plan')->cancelNow();

        return redirect()->route('home')->with('flash_message', '有料プランを解約しました。');
    }

}
