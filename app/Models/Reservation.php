<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // 1つの予約は一人のユーザーに属する
    public function user() {
        return $this->belongsTo(User::class);
    }

    // 1つの予約は一つの店舗に属する
    public function restaurant() {
        return $this->belongsTo(Restaurant::class);
    }
}
