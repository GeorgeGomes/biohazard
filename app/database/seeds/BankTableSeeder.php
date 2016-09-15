<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class BankTableSeeder extends Seeder {

    public function run() {
        Bank::updateOrCreate(array('id' => 1, 'name' => 'Banco do Brasil', 'code' => '001'));
        Bank::updateOrCreate(array('id' => 2, 'name' => 'Caixa Econômica Federal', 'code' => '104'));
        Bank::updateOrCreate(array('id' => 3, 'name' => 'Bradesco', 'code' => '237'));
        Bank::updateOrCreate(array('id' => 4, 'name' => 'Santander', 'code' => '033'));
        Bank::updateOrCreate(array('id' => 5, 'name' => 'Itaú Unibanco', 'code' => '341'));
        Bank::updateOrCreate(array('id' => 6, 'name' => 'Mercantil do Brasil', 'code' => '389'));
        Bank::updateOrCreate(array('id' => 7, 'name' => 'BANCOOB', 'code' => '756'));
        Bank::updateOrCreate(array('id' => 8, 'name' => 'Citibank', 'code' => '745'));
        Bank::updateOrCreate(array('id' => 9, 'name' => 'HSBC ', 'code' => '399'));
        
    }

}
