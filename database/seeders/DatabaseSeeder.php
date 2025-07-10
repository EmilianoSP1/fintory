<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Fábrica de usuarios normales
        Usuario::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
            'rol'   => 'usuario',
        ]);

        // Seeder de administrador
        $this->call(AdministradorSeeder::class);
    }
}
