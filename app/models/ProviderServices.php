<?php

use Illuminate\Database\Eloquent\Relations\Model;

class ProviderServices extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'provider_services';
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;
	
	/**
	 * MASS ASSIGNMENT
	 * define which attributes are mass assignable (for security)
	 * we only want these attributes able to be filled
	 */
	protected $fillable = array('provider_id', 'category', 'type', 'is_visible', 'price_per_unit_distance', 'price_per_unit_time', 'base_price', 'base_distance', 'base_time', 'distance_unit', 'time_unit', 'base_price_provider', 'base_price_user', 'commission_rate');
	
	/**
	 * Finds one row in the categories table associated with 'category' 
	 *
	 * @return HasOne object
	 */
	public function getTypeCategory()
	{
		return $this->hasOne('ProviderTypeCategory', 'id', 'category');
	}
	
	/**
	 * Finds one row in the services table associated with 'type'
	 *
	 * @return HasOne object
	 */
	public function getType()
	{
		return $this->hasOne('ProviderType', 'id', 'type');
	}

	public static function findByProviderIdAndTypeIdAndCategoryId($provider_id, $type_id, $category_id){
		return self::where("provider_id", '=', $provider_id)
					->where("type", '=', $type_id)
					->where("category", '=', $category_id)->first();
	}

	public static function findByProviderIdAndTypeId($provider_id, $type_id){
		return self::where("provider_id", '=', $provider_id)
					->where("type", '=', $type_id)
					->first();
	}

	public static function findDefaultByTypeIdAndCategoryId($type_id, $category_id = null){

		$default =  self::where("provider_id", '=', 0)
					->where("type", '=', $type_id)
					->where("category", '=', $category_id)->first();

		if($default) return $default ;
		else {
			return ProviderType::where("id", '=', $type_id)->first();
		}

	}

	public static function findRecursive($provider_id, $type_id, $category_id){
		$providerService = self::findByProviderIdAndTypeIdAndCategoryId($provider_id, $type_id, $category_id);

		if(!$providerService){
			$providerService = self::where("provider_id", '=', 0)
					->where("type", '=', $type_id)
					->where("category", '=', $category_id)->first();
		}

		return $providerService ;
	}
}