<?php

use Illuminate\Database\Seeder;
use App\Administracion\Modulo;

class ModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Modulo::create(['nombreModulo'=>'clients']);
        Modulo::create(['nombreModulo'=>'institutions']);
        Modulo::create(['nombreModulo'=>'roles']);
        Modulo::create(['nombreModulo'=>'generations']);
    }
}
