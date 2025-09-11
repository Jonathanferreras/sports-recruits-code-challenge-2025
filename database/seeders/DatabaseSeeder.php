<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Load the SQL file data
        $sql = file_get_contents(storage_path('users_pg.sql'));
        \DB::unprepared($sql);
    }
}
