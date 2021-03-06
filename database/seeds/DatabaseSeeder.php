<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ModulesTableSeeder::class);
        $this->call(PermissionTableSeeder::class);        
        $this->call(UsertableSeeder::class);
        $this->call(AdminClienteTableSeeder::class);
    }
}
