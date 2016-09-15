@extends('web.providerLayout')

@section('content')

<div class="col-md-12 mt">

	@if(Session::has('message'))
	<div class="alert alert-{{ Session::get('type') }}">
		<b>{{ Session::get('message') }}</b> 
	</div>
	@endif

	<?php if ($status == -1) { ?>
		<div class="alert alert-danger">
			<b>{{trans('user_provider_web.upload_all_files')}}</b> 
		</div>
	<?php } elseif ($status == 0) { ?>
		<div class="alert alert-success">
			<b>{{trans('user_provider_web.thanks_send')}}.</b> 
		</div>
	<?php } else { ?>
		<div class="alert alert-success">
			<b>{{trans('user_provider_web.active_account')}}.</b> 
		</div>
	<?php } ?>
	<div class="content-panel">
		<h4>{{trans('user_provider_web.update_doc')}}</h4><br>
		<form class="form-horizontal style-form" method="post" action="{{ URL::Route('providerUpdateDocuments') }}" enctype="multipart/form-data">
			<?php foreach ($documents as $document) { ?>
				<div class="form-group">
					<label class="col-sm-2 col-sm-2 control-label"><?= $document->name ?></label>
					<div class="col-sm-1">
						<?php
						foreach ($provider_document as $provider_documents) {
							if ($document->id == $provider_documents->document_id) {
								?>
								<a href="<?= $provider_documents->url ?>" target="_blank">{{trans('user_provider_web.view_doc')}}</a>
							<?php }
						}
						?>
					</div>
					<div class="col-sm-5" style="">
						<input id="doc" type="file" class="form-control" name="<?= $document->id ?>" >
					</div>
				</div>
			<?php } ?>

			<span class="col-sm-2"></span>
			<button id="upload" type="submit" class="btn btn-info">{{trans('user_provider_web.upload_doc')}}</button>
			<button type="reset" class="btn btn-info">{{trans('user_provider_web.reset')}}</button>
		</form>
	</div>
</div>

@stop 