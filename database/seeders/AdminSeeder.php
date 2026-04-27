<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@virtual.brin.go.id'],
            [
                'name' => 'Admin Studio',
                'password' => Hash::make('brin@2026!'),
            ]
        );
    }
}
