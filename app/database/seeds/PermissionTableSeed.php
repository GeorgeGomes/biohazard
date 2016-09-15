<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class PermissionTableSeed extends Seeder {

    public function run() {
        DB::table('permission')->delete();

        Permission::create(array('id' => 1, 'name' => 'map'));
        Permission::create(array('id' => 2, 'name' => 'provider'));
        Permission::create(array('id' => 3, 'name' => 'request'));
        Permission::create(array('id' => 4, 'name' => 'user'));
        Permission::create(array('id' => 5, 'name' => 'reviews'));
        Permission::create(array('id' => 6, 'name' => 'info'));
        Permission::create(array('id' => 7, 'name' => 'price_policy'));
        Permission::create(array('id' => 8, 'name' => 'documents'));
        Permission::create(array('id' => 9, 'name' => 'promotional code'));
        Permission::create(array('id' => 10, 'name' => 'customize'));
        Permission::create(array('id' => 11, 'name' => 'payments detail'));
        Permission::create(array('id' => 12, 'name' => 'week statement'));
        Permission::create(array('id' => 13, 'name' => 'settings')); 
        Permission::create(array('id' => 14, 'name' => 'admin'));

        $this->command->info('Permissions created!');
    }

}
