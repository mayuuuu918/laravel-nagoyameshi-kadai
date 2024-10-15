<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegularHoliday extends Model
{
    use HasFactory;

    // 1つの定休日は複数の店舗を登録できる
    public function restaurants() {
        return $this->belongsToMany(Restaurant::class)->withTimestamps();
    }
}
