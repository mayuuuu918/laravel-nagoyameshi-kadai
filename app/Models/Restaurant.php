<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Restaurant extends Model
{
    use HasFactory, Sortable;

    // 1つの店舗は複数のカテゴリを登録できる
    public function categories() {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    // 1つの店舗は複数の定休日を登録できる
    public function regular_holidays() {
        return $this->belongsToMany(RegularHoliday::class)->withTimestamps();
    }

    // 1つの店舗は複数のレビューを登録できる
    public function reviews() {
        return $this->hasMany(Review::class);
    }

    // 評価を並び替える
    public function ratingSortable($query, $direction) {
        return $query->withAvg('reviews', 'score')->orderBy('reviews_avg_score', $direction);
    }

    // 1つの店舗は複数の予約を登録できる
    public function reservations() {
        return $this->hasMany(Reservation::class);
    }

    // 予約を並び替える
    public function popularSortable($query, $direction) {
        return $query->withCount('reservations')->orderBy('reservations_count', $direction);
    }
}
