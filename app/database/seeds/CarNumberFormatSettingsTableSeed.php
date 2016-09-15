<?php
class CarNumberFormatSettingsTableSeed extends Seeder {

    public function run()
    {
        Settings::updateOrCreate(array('tool_tip' => "Car Number Format", 'page' => '8', 'key' => 'car_number_format'));
       
    }

}