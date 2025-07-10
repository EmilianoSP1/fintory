<?php

// database/seeders/AdministradorSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class AdministradorSeeder extends Seeder
{
    public function run()
    {
        Usuario::updateOrCreate(
            ['email' => 'gedent_24@hotmail.com'],
            [
                'name'     => 'Administrador Fintory',
                'password' => Hash::make('gera12345'),
                'rol'      => 'admin',
            ]
        );
    }
}
