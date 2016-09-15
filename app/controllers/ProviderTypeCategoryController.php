<?php

class ProviderTypeCategoryController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// get all the nerds
		$providertypecategories = ProviderTypeCategory::all();

		// load the view and pass the nerds
		return View::make('providertypecategory.index')
			->with('title', trans('providertypecategory.index'))
			->with('page', 'providertypecategory.index')
			->with('providertypecategories', $providertypecategories);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$services = ProviderType::all();

		// load the create form (app/views/providertypecategory/create.blade.php)
		return View::make('providertypecategory.create')
				->with('title', trans('providertypecategory.create'))
				->with('services', $services)
				->with('page', 'providertypecategory.create');
	}



	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// validate
		// read more on validation at http://laravel.com/docs/validation
		$rules = array(
			'name'       => 'required',
			'is_visible' => 'max:2',
			'is_default' => 'max:2'
		);

		$validator = Validator::make(Input::all(), $rules);

		// process the login
		if ($validator->fails()) {
			return Redirect::to('admin/providertypecategory/create')
				->withErrors($validator);
		} else {
			// store
			$providertypecategory 				= new ProviderTypeCategory;
			$providertypecategory->name       	= Input::get('name');
			$providertypecategory->is_visible   = Input::has('is_visible');
			$providertypecategory->is_default 	= Input::has('is_default');
			$providertypecategory->save();

			// save services
			if(Input::has('services')){
				foreach (Input::get('services') as $service_id) {

					$providerType = ProviderType::find($service_id);

					$providerService 							= new ProviderServices;
					$providerService->provider_id 				= 0 ; 
					$providerService->type 						= $providerType->id; 
					$providerService->category					= $providertypecategory->id ; 
					$providerService->price_per_unit_distance 	= $providerType->price_per_unit_distance; 
					$providerService->price_per_unit_time 		= $providerType->price_per_unit_time; 
					$providerService->base_price 				= $providerType->base_price ; 
					$providerService->base_distance 			= $providerType->base_distance ; 
					$providerService->base_time 				= $providerType->base_time ; 
					$providerService->distance_unit 			= $providerType->base_price ; 
					$providerService->time_unit 				= $providerType->time_unit ; 
					$providerService->base_price_provider 		= $providerType->base_price_provider ; 
					$providerService->base_price_user 			= $providerType->base_price_user ; 
					$providerService->commission_rate 			= $providerType->commission_rate ; 
					$providerService->is_visible				= $providertypecategory->is_visible ;

					$providerService->save();
				}
			}

			// redirect
			Session::flash('message', trans('providertypecategory.save_sucess'));
			return Redirect::to('admin/providertypecategory');
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		// get the providertypecategory
		$providertypecategory = ProviderTypeCategory::find($id);

		// show the view and pass the providertypecategory to it
		return View::make('providertypecategory.show')
			->with('title', trans('providertypecategory.show'))
			->with('page', 'providertypecategory.show')
			->with('providertypecategory', $providertypecategory);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		// get the providertypecategory
		$providertypecategory = ProviderTypeCategory::find($id);

		$services = ProviderType::all();

		// show the edit form and pass the nerd
		return View::make('providertypecategory.edit')
			->with('title', trans('providertypecategory.edit'))
			->with('page', 'providertypecategory.edit')
			->with('services', $services)
			->with('providertypecategory', $providertypecategory);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// validate
		// read more on validation at http://laravel.com/docs/validation
		$rules = array(
			'name'       => 'required',
			'is_visible'      => 'max:2',
			'is_default' => 'max:2'
		);
		$validator = Validator::make(Input::all(), $rules);

		// process the login
		if ($validator->fails()) {
			return Redirect::to('admin/providertypecategory/' . $id . '/edit')
				->withErrors($validator);
		} else {
			// store
			$providertypecategory 				= ProviderTypeCategory::find($id);
			$providertypecategory->name       	= Input::get('name');
			$providertypecategory->is_visible   = boolval(Input::has('is_visible'));
			$providertypecategory->is_default 	= boolval(Input::has('is_default'));
			$providertypecategory->save();

			$idsProviderServices = array ();

			// save services
			if(Input::has('services')){
				foreach (Input::get('services') as $service_id) {
					$providerService = ProviderServices::findByProviderIdAndTypeIdAndCategoryId(0, $service_id, $providertypecategory->id);

					$providerType = ProviderType::find($service_id);

					if(!$providerService){
						$providerService = new ProviderServices;
						$providerService->provider_id 				= 0 ; 
						$providerService->type 						= $providerType->id; 
						$providerService->category					= $providertypecategory->id ; 
						$providerService->price_per_unit_distance 	= $providerType->price_per_unit_distance; 
						$providerService->price_per_unit_time 		= $providerType->price_per_unit_time; 
						$providerService->base_price 				= $providerType->base_price ; 
						$providerService->base_distance 			= $providerType->base_distance ; 
						$providerService->base_time 				= $providerType->base_time ; 
						$providerService->distance_unit 			= $providerType->base_price ; 
						$providerService->time_unit 				= $providerType->time_unit ; 
						$providerService->base_price_provider 		= $providerType->base_price_provider ; 
						$providerService->base_price_user 			= $providerType->base_price_user ; 
						$providerService->commission_rate 			= $providerType->commission_rate ; 
						$providerService->is_visible				= $providertypecategory->is_visible ;

						$providerService->save();
					}

					array_push($idsProviderServices, $providerService->id);
				}
			}

			// remove servicos foram desselecionados
			if(count($idsProviderServices)){
				ProviderServices::where('provider_id', '=', '0')
								->where('category', '=', $providertypecategory->id)
								->whereNotIn('id', $idsProviderServices)
								->delete();
			}

			// redirect
			Session::flash('message', trans('providertypecategory.update_sucess'));
			return Redirect::to('admin/providertypecategory');
		}
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		 // delete
        $providertypecategory = ProviderTypeCategory::find($id);
        $providertypecategory->delete();

        // redirect
        Session::flash('message', trans('providertypecategory.delete_sucess'));
        return Redirect::to('admin/providertypecategory');
	}


}
