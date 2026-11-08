<?php

namespace Database\Seeders;

use App\Models\Master;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        foreach (json_decode(File::get(database_path('seeders/data/masters.json')), true, 512, JSON_THROW_ON_ERROR) as $master) {
            Master::firstOrCreate(['slug' => $master['slug']], $master);
        }
    }
}
