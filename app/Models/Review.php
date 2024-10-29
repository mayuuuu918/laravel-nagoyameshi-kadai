<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // 1つのレビューは一人のユーザーに属する
    public function user() {
        return $this->belongsTo(User::class);
    }

    // 1つのレビューは一つの店舗に属する
    public function restaurant() {
        return $this->belongsTo(Restaurant::class);
    }
}
