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
        Modulo::create(['nombreModulo'=>'users']);
        Modulo::create(['nombreModulo'=>'generations']);

        Permission::create(['name' => 'View Institutions','display_name' =>'View','modulo_id'=>1 , 'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Create Institutions','display_name' =>'Create','modulo_id'=>1, 'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Edit Institutions','display_name' =>'Edit','modulo_id'=>1,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Delete Institutions','display_name' =>'Delete','modulo_id'=>1,'guard_name'=>'sanctum']);

        Permission::create(['name' => 'View Clients','display_name' =>'View Clients','modulo_id'=>2,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Create Clients','display_name' =>'Create Clients','modulo_id'=>2,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Edit Clients','display_name' =>'Edit Clients','modulo_id'=>2,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Delete Clients','display_name' =>'Delete Clients','modulo_id'=>2,'guard_name'=>'sanctum']);

        Permission::create(['name' => 'View Users','display_name' =>'View Users','modulo_id'=>3,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Create Users','display_name' =>'Create Users','modulo_id'=>3,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Edit Users','display_name' =>'Edit Users','modulo_id'=>3,'guard_name'=>'sanctum']);
        Permission::create(['name' => 'Delete Users','display_name' =>'Delete Users','modulo_id'=>3,'guard_name'=>'sanctum']);
    }
}
