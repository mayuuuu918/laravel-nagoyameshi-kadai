<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1つ目の管理者ユーザを作成、存在する場合は取得
        Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['password' => Hash::make('nagoyameshi')]
        );

        // 2つ目の管理者ユーザを作成、存在する場合は取得
        Admin::firstOrCreate(
            ['email' => 'admin2@example.com'],
            ['password' => Hash::make('password')]
        );
    }
}
