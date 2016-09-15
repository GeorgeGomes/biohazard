<?php
class MapsSettingsTableSeed extends Seeder {

    public function run()
    {
        Settings::updateOrCreate(array('tool_tip' => "Google Maps API Navigation Key", 'page' => '8', 'key' => 'google_maps_api_key'));
       
    }

}