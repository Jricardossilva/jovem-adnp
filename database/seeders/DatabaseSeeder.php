<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Organizador inicial. ALTERE a senha após o primeiro acesso.
        User::firstOrCreate(
            ['email' => 'organizador@igreja.local'],
            ['name' => 'Organizador', 'password' => Hash::make('mudar123')]
        );

        $this->call([
            VersiculoSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
