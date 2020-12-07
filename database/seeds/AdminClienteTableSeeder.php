<?php

use Illuminate\Database\Seeder;
use App\Administracion\Admincliente;
use App\Administracion\AdminclienteModulo;

class AdminClienteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admincliente::create([
            'nombreAdmincliente' => 'miguel',
            'user_id' => 1
        ]);

        AdminclienteModulo::create([
            'admincliente_id' => 1,
            'modulo_id' => 1
        ]);

        AdminclienteModulo::create([
            'admincliente_id' => 1,
            'modulo_id' => 2
        ]);
    }
}
