<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ButtonSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('button_setting')->insert([
            [
                'id' => 1,
                'title' => 'LIVE DOORPRIZE',
                'url' => 'https://www.facebook.com/lotto21groupofficiall/videos/7339985162718717',
                'created_at' => NULL,
                'updated_at' => '2024-01-16 09:05:49',
            ],
        ]);
    }
}
