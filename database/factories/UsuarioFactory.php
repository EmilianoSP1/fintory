<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Usuario;

class UsuarioFactory extends Factory
{
    /**
     * El modelo que esta fábrica corresponde.
     *
     * @var string
     */
    protected $model = Usuario::class;

    /**
     * Definición de los atributos por defecto.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'     => $this->faker->name(),
            'email'    => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'rol'      => 'usuario',
        ];
    }
}
