<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
        public function run(): void
    {
        $names = ['Work', 'Personal', 'Study'];

        User::all()->each(function ($user) use ($names) {
            foreach ($names as $name) {
                $user->categories()->firstOrCreate([
                    'name' => $name,
                ]);
            }
        });
    }
}
