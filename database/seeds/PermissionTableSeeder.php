<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Administracion\Modulo;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Modulo::create(['nombreModulo'=>'institutions']);
        Permission::create(['name' => 'View Institutions','display_name' =>'View','modulo_id'=>1]);
        Permission::create(['name' => 'Create Institutions','display_name' =>'Create','modulo_id'=>1]);
        Permission::create(['name' => 'Edit Institutions','display_name' =>'Edit','modulo_id'=>1]);
        Permission::create(['name' => 'Delete Institutions','display_name' =>'Delete','modulo_id'=>1]);
    }
}
