<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    // 1つの店舗は複数のカテゴリを登録できる
    public function categories() {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }
}
