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
        Modulo::create(['nombreModulo'=>'clients']);
        Modulo::create(['nombreModulo'=>'institutions']);
        Modulo::create(['nombreModulo'=>'roles']);
        Modulo::create(['nombreModulo'=>'generations']);

        Permission::create(['name' => 'View Institutions','display_name' =>'View','modulo_id'=>1 , 'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Create Institutions','display_name' =>'Create','modulo_id'=>1, 'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Edit Institutions','display_name' =>'Edit','modulo_id'=>1,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Delete Institutions','display_name' =>'Delete','modulo_id'=>1,'guard_name'=>'sanctum']);

        Permission::create(['name' => 'View Clients','display_name' =>'View Clients','modulo_id'=>2,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Create Clients','display_name' =>'Create Clients','modulo_id'=>2,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Edit Clients','display_name' =>'Edit Clients','modulo_id'=>2,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Delete Clients','display_name' =>'Delete Clients','modulo_id'=>2,'guard_name'=>'sanctum']);

        Permission::create(['name' => 'View Roles','display_name' =>'View Roles','modulo_id'=>3,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Create Roles','display_name' =>'Create Roles','modulo_id'=>3,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Edit Roles','display_name' =>'Edit Roles','modulo_id'=>3,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Delete Roles','display_name' =>'Delete Roles','modulo_id'=>3,'guard_name'=>'sanctum']);
    }
}
