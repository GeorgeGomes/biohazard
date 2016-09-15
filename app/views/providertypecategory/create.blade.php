@extends('layout')

<!-- app/views/providertypecategory/create.blade.php -->

@section('content')

<nav class="navbar navbar-inverse">
	<div class="navbar-header">
		<a class="navbar-brand" href="{{ URL::to('admin/providertypecategory') }}">{{ trans('providertypecategory.title');}}</a>
	</div>
	<ul class="nav navbar-nav">
		<li><a href="{{ URL::to('admin/providertypecategory') }}">{{ trans('customize.view_all');}}</a></li>
	</ul>
</nav>

<!-- if there are creation errors, they will show here -->
{{ HTML::ul($errors->all()) }}

{{ Form::open(array('url' => 'admin/providertypecategory')) }}

	<div class="form-group">
		{{ Form::label('name', trans('providertypecategory.name')) }}
		{{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
	</div>

	<div class="form-group">
		{{ Form::label('is_visible', trans('providertypecategory.is_visible')) }}
		{{ Form::checkbox('is_visible', Input::old('is_visible'), false, array('class' => 'form-control icheckbox_minimal')) }}
	</div>

	<div class="form-group">
		{{ Form::label('is_default', trans('providertypecategory.is_default')) }}
		{{ Form::checkbox('is_default', Input::old('is_default'), false, array('class' => 'form-control icheckbox_minimal')) }}
	</div>


	<div class="form-group">
		<label>{{trans('providertypecategory.services')}}</label> </br>
			@foreach($services as $service)
				<input class="icheckbox_minimal" id="services" name="services[]" type="checkbox" value="{{$service->id}}">
				{{ $service->name}}
			@endforeach
	</div>


	{{ Form::submit(trans('customize.create'), array('class' => 'btn btn-primary right')) }}

{{ Form::close() }}


@stop