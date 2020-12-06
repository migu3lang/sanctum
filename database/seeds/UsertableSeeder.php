<?php

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Role;

class UsertableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user= new User();
        $user->name="miguel";
        $user->email="miguelegion@gmail.com";
        $user->password=bcrypt('12345678');
        $user->save();
        $role=Role::create(['name' => 'admin','display_name' =>'admin','guard_name'=>'sanctum']);
        $role->givePermissionTo([1,2,3,4]);
        $user->assignRole($role);

        //$adminRole = Role::create(['name' => 'Admin','display_name'=>'Admin']);
        //$superRole->givePermissionTo('Ver SuperUsuario','Crear SuperUsuario','Editar SuperUsuario','Eliminar SuperUsuario');
    }
}
