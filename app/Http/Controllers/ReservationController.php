<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    // 予約一覧
    public function index(Reservation $reservation)
    {
        $reservations = Reservation::where('user_id', Auth::id())
                ->orderBy('reserved_datetime', 'desc')
                ->paginate(15);
                return view('reservations.index', compact('reservations'));
    }

    // 予約ページ
    public function create(Restaurant $restaurant)
    {
        return view('reservations.create', compact('restaurant'));
    }

    // 予約機能
    public function store(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'reservation_date' => 'required|date_format:Y-m-d',
            'reservation_time' => 'required|date_format:H:i',
            'number_of_people' => 'required|numeric|between:1,50',
        ]);

        $reservation = new Reservation();
        $reservation->reserved_datetime = $request->input('reservation_date') . ' ' . $request->input('reservation_time');
        $reservation->number_of_people = $request->input('number_of_people');
        $reservation->restaurant_id = $restaurant->id;
        $reservation->user_id = $request->user()->id;
        $reservation->save();

        return redirect()->route('reservations.index', $restaurant)->with('flash_message', '予約が完了しました。');
    }

    // 予約キャンセル機能
    public function destroy(Reservation $reservation, Restaurant $restaurant)
    {
        // 現在ログイン中のユーザーのIDと編集対象のユーザーIDを比較
        if (Auth::id() !== $reservation->user_id) {
            // 不一致の場合、予約一覧ページにリダイレクトし、エラーメッセージをセッションに保存
            return redirect()->route('reservations.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }
        $reservation->delete();

        return redirect()->route('reservations.index', $restaurant)->with('flash_message', '予約をキャンセルしました。');
    }
}
