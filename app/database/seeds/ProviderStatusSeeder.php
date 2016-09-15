<?php

class ProviderStatusSeeder extends Seeder {
	
	public function run() {
		
		DB::table('provider_status')->delete();
		
		// seed our status table
		
		$statusAprovado = ProviderStatus::create(array(
			'name'			=> 'APROVADO',
		));
		
		$statusRejeitado = ProviderStatus::create(array(
			'name'			=> 'REJEITADO',
		));
		
		$statusAnalise = ProviderStatus::create(array(
			'name'			=> 'EM_ANALISE',
		));
		
		$statusSuspeso = ProviderStatus::create(array(
			'name'			=> 'SUSPENSO',
		));
		
		$this->command->info('ProviderStatus created!');
		
		// finds all providers
		$providers = Provider::all();
		
		// Randomize the status for each provider
		$array_statusId = array($statusAprovado->id, $statusRejeitado->id, $statusAnalise->id, $statusSuspeso->id);
		
		foreach ($providers as $provider) {
			// set provider status
			$rand_keys = array_rand($array_statusId);
			$provider->status_id = $array_statusId[$rand_keys];
			$provider->save();
		}
		
		$this->command->info('ProviderStatus associated with Providers!');
	}
}