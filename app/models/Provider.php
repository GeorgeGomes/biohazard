<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\Relations\Model;

class Provider extends Eloquent implements UserInterface, RemindableInterface{

	use UserTrait, RemindableTrait;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
    protected $table = 'provider';
	
	/**
	 * MASS ASSIGNMENT
	 * define which attributes are mass assignable (for security)
	 * we only want these attributes able to be filled
	 */
	protected $fillable = array('status_id');

	// eager loading objects
	public $with = [
		'status'
	];
	
	
	public function myCode(){
        return $this->hasOne('Code', 'id', 'my_code');
    }
	
	public function networkingCode(){
        return $this->hasOne('Code', 'id', 'networking_code');
    }
	
	
	/**
	 * Finds one row in the provider_status table associated with 'status_id' 
	 *
	 * @return HasOne object
	 */
	public function status()
	{
		return $this->hasOne('ProviderStatus', 'id', 'status_id');
	}

	public function changeStatusByName($strNewStatus){
		if($status_id = ProviderStatus::where('name', $strNewStatus)->first()->id){
			$this->status_id = $status_id;
			if($strNewStatus == "APROVADO"){
				$this->is_approved = 1;
			}
			else{
				$this->is_approved = 0;
			}
			$this->save();
		}
	}

	public function changeStatus($action){
		$admin_email = Settings::getAdminEmail();	

		switch($action){
			//aprovar prestador
			case 'APROVADO':
				$this->changeStatusByName("APROVADO");

				$push_title = trans('adminController.you_approved');
				$txt_approve = trans('adminController.approved');

				$email_key = 'approve_provider_mail';
				$email_subject = sprintf("%s %s %s ao %s",
					trans('email.Welcome'),
					$this->first_name,
					$this->last_name,
					Config::get('app.website_title')
				);
				$email_pattern = array(
					'provider_name' => sprintf("%s %s", $this->first_name, $this->last_name),
					'admin_eamil' => $admin_email
				);

				break;

			//recusar prestador
			case 'REJEITADO':
				$this->changeStatusByName("REJEITADO");

				$push_title = trans('adminController.decline');
				$txt_approve = trans('adminController.decline');

				$email_key = 'decline_provider_mail';
				$email_subject = sprintf("%s %s %s ao %s",
					trans('email.Welcome'),
					$this->first_name,
					$this->last_name,
					Config::get('app.website_title')
				);
				$email_pattern = array(
					'provider_name' => sprintf("%s %s", $this->first_name, $this->last_name),
					'admin_eamil' => $admin_email
				);

				break;

			//setar prestador como 'em análise'
			case 'EM_ANALISE':
				$this->changeStatusByName("EM_ANALISE");

				$push_title = trans('adminController.analyse');
				$txt_approve = trans('adminController.analyse');

				$email_key = 'change_status_provider_mail';
				$email_pattern = array(
					'provider_name' => sprintf("%s %s", $this->first_name, $this->last_name),
					'msg_title' => trans('email.status_analyse_title'),
					'msg_body' => trans('email.status_analyse_body'),
					'admin_eamil' => $admin_email
				);
				$email_subject = sprintf("%s - %s", Config::get('app.website_title'), trans('email.status_analyse_title'));

				break;

			//suspender prestador
			case 'SUSPENSO':
				$this->changeStatusByName("SUSPENSO");

				$push_title = trans('adminController.suspend');
				$txt_approve = trans('adminController.suspend');

				$email_key = 'change_status_provider_mail';
				$email_pattern = array(
					'provider_name' => sprintf("%s %s", $this->first_name, $this->last_name),
					'msg_title' => trans('email.status_suspend_title'),
					'msg_body' => trans('email.status_suspend_body'),
					'admin_eamil' => $admin_email
				);
				$email_subject = sprintf("%s - %s", Config::get('app.website_title'), trans('email.status_suspend_title'));
				break;

			//setar prestador como 'pendente'
			case 'PENDENTE':
				$this->changeStatusByName("PENDENTE");

				$push_title = trans('adminController.pendent');
				$txt_approve = trans('adminController.pendent');

				$email_key = 'change_status_provider_mail';
				$email_pattern = array(
					'provider_name' => sprintf("%s %s", $this->first_name, $this->last_name),
					'msg_title' => trans('email.status_pendent_title'),
					'msg_body' => trans('email.status_pendent_body'),
					'admin_eamil' => $admin_email
				);
				$email_subject = sprintf("%s - %s", Config::get('app.website_title'), trans('email.status_pendent_title'));

				break;

			//desativar prestador
			case 'INATIVO':
				$this->changeStatusByName("INATIVO");

				$push_title = trans('adminController.inactive');
				$txt_approve = trans('adminController.inactive');

				$email_key = 'change_status_provider_mail';
				$email_pattern = array(
					'provider_name' => sprintf("%s %s", $this->first_name, $this->last_name),
					'msg_title' => trans('email.status_deactivate_title'),
					'msg_body' => trans('email.status_deactivate_body'),
					'admin_eamil' => $admin_email
				);
				$email_subject = sprintf("%s - %s", Config::get('app.website_title'), trans('email.status_deactivate_title'));

				break;

			default:
				return false;
		}

		//enviar notificação
		$response_array = array(
			'unique_id' => 5,
			'success' => true,
			'id' => $this->id,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'phone' => $this->phone,
			'email' => $this->email,
			'picture' => $this->picture,
			'bio' => $this->bio,
			'address' => $this->address,
			'state' => $this->state,
			'country' => $this->country,
			'zipcode' => $this->zipcode,
			'login_by' => $this->login_by,
			'social_unique_id' => $this->social_unique_id,
			'device_token' => $this->device_token,
			'device_type' => $this->device_type,
			'token' => $this->token,
			'type' => '' ,  #TODO listar todos os tipos
			'is_approved' => $this->is_approved,
			'is_approved_txt' => $txt_approve,
		);
		send_notifications($this->id, "provider", $push_title, $response_array, "imp");

		//enviar email
		email_notification($this->id, 'provider', $email_pattern, $email_subject, $email_key);

		return true;
	}

	public static function getNearest($latitude, $longitude, $typeId = null , $categoryId = null, $notIn = array()){

		$providers = self::getNearests($latitude, $longitude, $typeId, $categoryId, $notIn, 1);

		if($providers)
			return $providers[0];
		else
			return null ;
	}

	public static function getNearests($latitude, $longitude, $typeId = null, $categoryId = null, $notIn = array(), $quantity = 10){

		$distanceSearchRadius 	= Settings::getDefaultSearchRadius();
		$unitMultiply 			= Settings::getDefaultMultiplyUnit();

		if($categoryId != null && trim($categoryId) != "")
			$queryCategory = "provider_services.category= $categoryId and ";
		else
			$queryCategory = null ;

		if($typeId != null && trim($typeId) != "")
			$queryType = "provider_services.type = $typeId and ";
		else
			$queryType = null ;

		if($notIn != null && count($notIn) > 0)
			$queryIdNotIn = "provider.id NOT IN (". join(",", $notIn) .") and " ;
		else
			$queryIdNotIn = null ;

		
		$query = "SELECT provider.*, "
				. "ROUND( $unitMultiply  * 3956 * acos( cos( radians('$latitude') ) * "
				. "cos( radians(latitude) ) * "
				. "cos( radians(longitude) - radians('$longitude') ) + "
				. "sin( radians('$latitude') ) * "
				. "sin( radians(latitude) ) ) ,8) as distance , "
				. "provider_services.type as provider_type "
				. "FROM provider "
				. "inner join provider_services on provider.id = provider_services.provider_id " 
				. "inner join provider_type on provider_services.type = provider_type.id " 
				. "where is_available = 1 and "
				. "is_active = 1 and "
				. "is_approved = 1 and "
				. "provider_type.is_visible = 1 and "
				. $queryCategory
				. $queryType
				. $queryIdNotIn 
				. "ROUND(( $unitMultiply  * 3956 * acos( cos( radians('$latitude') ) * "
				. "cos( radians(latitude) ) * "
				. "cos( radians(longitude) - radians('$longitude') ) + "
				. "sin( radians('$latitude') ) * "
				. "sin( radians(latitude) ) ) ) ,8) <= $distanceSearchRadius and "
				. "provider.deleted_at IS NULL "				
				. "group by provider_services.provider_id ,provider_services.type "
				. "order by distance ASC "
				. "limit $quantity ";

		return DB::select(DB::raw($query));
	}

	public function getLocation(){
		if($this->state)
			return $this->state . ' - '.$this->address_city ;
		else
			return "<span class='badge bg-red'>" . Config::get('app.blank_fiend_val') . "</span>";
	}

	public function getProviderServiceByTypeId($typeId){
		return ProviderServices::where('provider_id', $this->id)->where('type', $typeId)->first();
	}
}
