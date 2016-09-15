<?php

class ProviderServicesSeeder extends Seeder {
	
	public function run() {
		// clear our database

		/*
		DB::table('provider_services')->delete();
		DB::table('provider_type_categories')->delete();
		
		// seed our categories table

		$categoryMoto = ProviderTypeCategory::create(array(
			'name'			=> 'MOTO',
			'is_visible'	=> true,
			'is_default'	=> false,
		));

		$categoryVeiculos = ProviderTypeCategory::create(array(
			'name'			=> 'VEICULOS LEVES',
			'is_visible'	=> true,
			'is_default'	=> false,
		));
		
		$categoryUtilitarios = ProviderTypeCategory::create(array(
			'name'			=> 'UTILITARIOS LEVES',
			'is_visible'	=> true,
			'is_default'	=> false,
		));

		$categorySimples = ProviderTypeCategory::create(array(
			'name'			=> 'SIMPLES',
			'is_visible'	=> true,
			'is_default'	=> false,
		));

		$categoryCodificada = ProviderTypeCategory::create(array(
			'name'			=> 'CODIFICADA',
			'is_visible'	=> true,
			'is_default'	=> false,
		));
		
		$this->command->info('ProviderTypeCategories created!');
		
		// look for existing provider types
		$providerTypes = ProviderType::all();
		
		foreach ($providerTypes as $providerType) {
			
			// seed our provider_services table with general values
			DB::table('provider_services')->insert(array(
				array(
					'provider_id'				=> 0,
					'type'						=> $providerType->id,
					'category'					=> $categoryMoto->id,
					'price_per_unit_distance'	=> 1.5,
					'price_per_unit_time'		=> 1234,
					'base_price'				=> 1234,
					'base_distance'				=> 40,
					'base_time'					=> 1234,
					'distance_unit'				=> 1234,
					'time_unit'					=> 1234,
					'base_price_provider'		=> 80,
					'base_price_user'			=> 120,
					'commission_rate'			=> 50,
					'is_visible'				=> true,
				),
				
				array(
					'provider_id'				=> 0,
					'type'						=> $providerType->id,
					'category'					=> $categoryVeiculos->id,
					'price_per_unit_distance'	=> 1.5,
					'price_per_unit_time'		=> 1234,
					'base_price'				=> 1234,
					'base_distance'				=> 40,
					'base_time'					=> 1234,
					'distance_unit'				=> 1234,
					'time_unit'					=> 1234,
					'base_price_provider'		=> 80,
					'base_price_user'			=> 120,
					'commission_rate'			=> 50,
					'is_visible'				=> true,
				),
				
				array(
					'provider_id'				=> 0,
					'type'						=> $providerType->id,
					'category'					=> $categoryUtilitarios->id,
					'price_per_unit_distance'	=> 1.5,
					'price_per_unit_time'		=> 0,
					'base_price'				=> 0,
					'base_distance'				=> 40,
					'base_time'					=> 0,
					'distance_unit'				=> 1,
					'time_unit'					=> 1,
					'base_price_provider'		=> 80,
					'base_price_user'			=> 120,
					'commission_rate'			=> 50,
					'is_visible'				=> true,
				),
			));
		}
		
		// finds all providers
		$providers = Provider::all();
		
		foreach ($providers as $provider) {
			
			// seed our category_service table with each Provider
			foreach ($providerTypes as $providerType) {
				
				DB::table('provider_services')->insert(array(
					array(
						'provider_id'				=> $provider->id,
						'type'						=> $providerType->id,
						'category'					=> $categoryMoto->id,
						'price_per_unit_distance'	=> 1.5,
						'price_per_unit_time'		=> 0,
						'base_price'				=> 0,
						'base_distance'				=> 40,
						'base_time'					=> 1234,
						'distance_unit'				=> 1234,
						'time_unit'					=> 1234,
						'base_price_provider'		=> 80,
						'base_price_user'			=> 120,
						'commission_rate'			=> 50,
						'is_visible'				=> true,
					),
					
					array(
						'provider_id'				=> $provider->id,
						'type'						=> $providerType->id,
						'category'					=> $categoryVeiculos->id,
						'price_per_unit_distance'	=> 1.5,
						'price_per_unit_time'		=> 1234,
						'base_price'				=> 1234,
						'base_distance'				=> 40,
						'base_time'					=> 1234,
						'distance_unit'				=> 1234,
						'time_unit'					=> 1234,
						'base_price_provider'		=> 80,
						'base_price_user'			=> 120,
						'commission_rate'			=> 50,
						'is_visible'				=> true,
					),
					
					array(
						'provider_id'				=> $provider->id,
						'type'						=> $providerType->id,
						'category'					=> $categoryUtilitarios->id,
						'price_per_unit_distance'	=> 1.5,
						'price_per_unit_time'		=> 1234,
						'base_price'				=> 1234,
						'base_distance'				=> 40,
						'base_time'					=> 1234,
						'distance_unit'				=> 1234,
						'time_unit'					=> 1234,
						'base_price_provider'		=> 80,
						'base_price_user'			=> 120,
						'commission_rate'			=> 50,
						'is_visible'				=> true,
					),
				));
			}
		}
		*/
		$this->command->info('ProviderServices created!');
	}
}