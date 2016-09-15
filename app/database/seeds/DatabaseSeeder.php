<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('SettingsTableSeeder');

		$this->call('DocumentTableSeed');

		$this->call('TypeTableSeed');

		$this->call('KeywordsTableSeed');

		$this->call('IconsTableSeeder');
		
		$this->call('CategoryServiceSeeder');
		
		$this->call('ProviderStatusSeeder');

		$this->call('BankTableSeeder');

		$this->call('CountryTableSeeder');
		
		$this->call('MapsSettingsTableSeed');

		$this->call('CarNumberFormatSettingsTableSeed');

		$this->call('PermissionTableSeed');
		
		$this->call('PermissionIndexTableSeed');

		$this->call('PermissionSubActionTableSeed');
	}

}
