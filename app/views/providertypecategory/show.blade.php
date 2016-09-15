@extends('layout')

<!-- app/views/providertypecategory/index.blade.php -->

@section('content')

<nav class="navbar navbar-inverse">
	<div class="navbar-header">
		<a class="navbar-brand" href="{{ URL::to('admin/providertypecategory') }}">{{ trans('providertypecategory.title');}}</a>
	</div>
	<ul class="nav navbar-nav">
		<li><a href="{{ URL::to('admin/providertypecategory') }}">{{ trans('customize.view_all');}}</a></li>
	</ul>
	<ul class="nav navbar-nav">
		<li><a href="{{ URL::to('admin/providertypecategory/' . $providertypecategory->id . '/edit') }}">{{ trans('customize.edit');}}</a>
	</ul>
</nav>

<div class="jumbotron text-center">
	<h2>{{ $providertypecategory->name }}</h2>
	<p>
		<strong>{{ trans('providertypecategory.is_visible');}}</strong> {{ $providertypecategory->is_visible }}<br>
		<strong>{{ trans('providertypecategory.is_default');}}</strong> {{ $providertypecategory->is_default }}
	</p>
</div>


@stop