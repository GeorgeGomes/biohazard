<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SchedulePush extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'schedulepush';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Enviar notificação antes de 30 min.';
	//protected $description = 'Sends push notification before half an hr';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->info('Working');
		$date1=date('Y-m-d h:i:s');
		$this->info($date1);
		$date=date_create($date1);
		$msgtime = $date->add(new DateInterval('P0Y0M0DT0H30M0S'))->format('Y-m-d H:i:s'); 
		$requests=Requests::where('later',1)->where('is_started',0)->where('request_start_time','<=',$msgtime)->get();
		if($requests->count()>0){
			$this->info('hola');
		foreach ($requests as $request) {
			$provider=Provider::where('id',$request->confirmed_provider)->first();
			$user=User::where('id',$request->user_id)->first();
			$message="Você possui uma corrida para daqui 30 minutos. O nome do cliente é $user->first_name $user->last_name e seu telefone é $user->phone";
			//$message="You have a ride scheduled in 30 mins from now. Client name is $user->first_name $user->last_name and phone no. is $user->phone";

			$this->info($request->id);
			$this->info($message);
			send_notifications($provider->id,'provider','Corrida em 30 min',$message);
			//send_notifications($provider->id,'provider','Ride in 30 min',$message);
			$request->later=0;
			$request->save();
		}
	}

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	
}
