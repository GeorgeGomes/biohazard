@if(Session::has('success'))
<div class="alert alert-success">
	{{ Session::get('success') }}
</div>
@endif

@if ( Session::has('flash_message') )
<div class="alert {{ Session::get('flash_type') }}">
	{{ Session::get('flash_message') }}
</div>
@endif

@if($errors && $errors->any())
<div class="alert alert-danger">
	{{ HTML::ul($errors->all()) }}
</div>
@endif
