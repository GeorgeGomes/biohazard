<nav class="navbar navbar-inverse">
	<div class="navbar-header">
		<a class="navbar-brand" href="{{ URL::to('providerterycategory') }}">{{ trans('providertypecategory.title');}}</a>
	</div>
	<ul class="nav navbar-nav">
		<li><a href="{{ URL::to('providerterycategory') }}">{{ trans('customize.view_all');}}</a></li>
		<li><a href="{{ URL::to('providerterycategory/create') }}">{{ trans('customize.create');}}</a>
	</ul>
</nav>