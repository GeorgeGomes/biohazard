@extends('layout')

<!-- app/views/providertypecategory/index.blade.php -->

@section('content')

<nav class="navbar navbar-inverse">
	<div class="navbar-header">
		<a class="navbar-brand" href="{{ URL::to('admin/providertypecategory') }}">{{ trans('providertypecategory.title');}}</a>
	</div>
	<ul class="nav navbar-nav">
		<li><a href="{{ URL::to('admin/providertypecategory/create') }}">{{ trans('customize.create');}}</a>
	</ul>
</nav>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<td>ID</td>
			<td>{{ trans('providertypecategory.name');}}</td>
			<td>{{ trans('providertypecategory.is_visible');}}</td>
			<td>{{ trans('providertypecategory.is_default');}}</td>
			<td>{{ trans('customize.actions');}}</td>
		</tr>
	</thead>
	<tbody>
	@foreach($providertypecategories as $key => $providertypecategory)
		<tr>
			<td>{{ $providertypecategory->id }}</td>
			<td>{{ $providertypecategory->name }}</td>
			<td>{{ $providertypecategory->is_visible }}</td>
			<td>{{ $providertypecategory->is_default }}</td>

			<!-- we will also add show, edit, and delete buttons -->
			<td>

				<!-- delete the nerd (uses the destroy method DESTROY /providertypecategory/{id} -->
				<!-- we will add this later since its a little more complicated than the other two buttons -->
			 	{{ Form::open(array('url' => 'admin/providertypecategory/' . $providertypecategory->id, 'class' => 'pull-right')) }}
                    {{ Form::hidden('_method', 'DELETE') }}
                    {{ Form::submit(trans('customize.delete'), array('class' => 'btn btn-warning')) }}
                {{ Form::close() }}

				<!-- show the nerd (uses the show method found at GET /providertypecategory/{id} -->	
				<a class="btn btn-small btn-success" href="{{ URL::to('admin/providertypecategory/' . $providertypecategory->id) }}">{{ trans('customize.show');}}</a> 
				

				<!-- edit this nerd (uses the edit method found at GET /providertypecategory/{id}/edit -->
				<a class="btn btn-small btn-info" href="{{ URL::to('admin/providertypecategory/' . $providertypecategory->id . '/edit') }}">{{ trans('customize.edit');}}</a>

			</td>
		</tr>
	@endforeach
	</tbody>
</table>

@stop