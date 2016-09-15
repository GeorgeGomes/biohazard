<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class PermissionIndexTableSeed extends Seeder {

    public function run() {
        Permission::create(array('id' => 15, 'name' => 'index'));

        $this->command->info('Permissions created!');
    }

}
