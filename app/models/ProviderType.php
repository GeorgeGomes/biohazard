<?php

use Illuminate\Database\Eloquent\Relations\Model;

class ProviderType extends Eloquent
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'provider_type';
	
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
	protected $fillable = array('name', 'is_default', 'is_visible', 'icon', 'price_per_unit_distance', 'price_per_unit_time', 'base_price', 'base_distance', 'base_time', 'distance_unit', 'time_unit', 'base_price_provider', 'base_price_user', 'commission_rate');
	
	public function categoryService()
	{
		return $this->hasMany('ProviderServices');
	}

	public function hasAssociationByCategoryId($categoryId){
		return ProviderServices::where("provider_id", '=', '0')
								->where("type", '=', $this->id)
								->where("category", '=', $categoryId)
								->count() ;
	}

	public function getDefaultCategories(){
		return ProviderTypeCategory::join('provider_services', 'provider_services.category', '=', 'provider_type_categories.id')
						->select('provider_type_categories.*')
						->where('provider_services.provider_id', '=', '0')
						->where('provider_services.type', '=', $this->id)->get();
	}

	public function hasAssociationByProviderId($providerId){
		return ProviderServices::where("provider_id", '=', $providerId)
								->where("type", '=', $this->id)
								->count() ;
	}

	public static function buildTreeData(){
		$treeData = [] ;
		$providerTypes = self::where("is_visible", '=', '1')->get();

		foreach($providerTypes as $providerType){
			$treeChild = new stdClass ;
			$treeChild->text = $providerType->name ;
			$treeChild->href = $providerType->id ;
			$treeChild->nodes = [] ;
			foreach($providerType->getDefaultCategories() as $category){
				if($category->is_visible){
					$nodeChild = new stdClass ;
					$nodeChild->text = $category->name ;
					$nodeChild->href = $category->id ;
					$treeChild->nodes[] = $nodeChild ;
				}
			}
			$treeData[] = $treeChild ;
		}

		return $treeData ;
	}
}