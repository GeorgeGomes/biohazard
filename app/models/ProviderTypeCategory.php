<?php

use Illuminate\Database\Eloquent\Relations\Model;

class ProviderTypeCategory extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'provider_type_categories';
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = true;
	
	/**
	 * MASS ASSIGNMENT
	 * define which attributes are mass assignable (for security)
	 * we only want this attribute able to be filled
	 */
	protected $fillable = array('name', 'is_visible', 'is_default');
	
	public function categoryService()
	{
		return $this->hasMany('ProviderServices');
	}


	public function hasAssociationByTypeId($typeId){
		return ProviderServices::where("provider_id", '=', '0')
								->where("category", '=', $this->id)
								->where("type", '=', $typeId)
								->count() ;
	}

	public function getAssociationByProviderIdAndTypeId($providerId, $typeId){
		return ProviderServices::where("provider_id", '=', $providerId)
								->where("category", '=', $this->id)
								->where("type", '=', $typeId)
								->first() ;
	}
}