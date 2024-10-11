<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // 1つのカテゴリは複数の店舗を登録できる
    public function restaurants() {
        return $this->belongsToMany(Restaurant::class)->withTimestamps();
    }
}
