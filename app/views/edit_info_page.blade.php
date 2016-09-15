
@extends('layout')

@section('content')

 <div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?= $title ?></h3>
	</div><!-- /.box-header -->
	<!-- form start -->
	 <form method="post" id="basic-form" action="{{ URL::Route('AdminInformationUpdate') }}"  enctype="multipart/form-data">
	<input type="hidden" name="id" value="<?= $id ?>">

		<div class="box-body">
			<div class="form-group">
				<label>{{trans('blade.title');}}</label>
				<input type="text" name="title" class="form-control" placeholder="Title" value="<?= $info_title ?>">                                   
			</div>                          

			<div class="form-group">
				<label>{{trans('provider.icon');}}</label>

			   
				 <input type="file" name="icon" class="form-control" >
				 <br>
				  <?php if($icon != "") {?>
				<img src="<?= $icon; ?>" height="50" width="50">
				<?php } ?><br>
				
				<p class="help-block">{{trans('provider.image_upload');}}</p>
			</div>

			<div class="form-group">
				<label>{{trans('provider.type');}}</label>
				
				<select class="form-control" name="type" > 
					<?php if( $type == 'user') {?>                                   >
						<option value="user" selected="selected">{{trans('adminController.client');}}</option>
						<option value="provider">{{trans('adminController.provider');}}</option>
						<option value="both" >{{trans('adminController.both');}}</option>
					<?php }
					else if( $type == 'provider'){?>
						<option value="user" >{{trans('adminController.client');}}</option>
						<option value="provider" selected="selected">{{trans('adminController.provider');}}</option>
						<option value="both">{{trans('adminController.both');}}</option>
					<?php }
					else{?>
						<option value="user" >{{trans('adminController.client');}}</option>
						<option value="provider" >{{trans('adminController.provider');}}</option>
						<option value="both" selected="selected">{{trans('adminController.both');}}</option>
					<?}?>
				</select>
			</div>
		
			<div class="form-group">
			<label>{{trans('provider.description');}} </label>
			<textarea id="editor1" name="description" rows="10" cols="124">
				<?= $description ?>  
			</textarea>
			</div>
		</div><!-- /.box-body -->

		<div class="box-footer">

			<button id="add_info" type="submit" class="btn btn-primary btn-flat btn-block">{{trans('keywords.save_change');}}</button>
		</div>
	</form>
</div>

<?php
if($success == 1) { ?>
<script type="text/javascript">
	alert("{{trans('blade.page_success');}}");
</script>
<?php } ?>
<?php
if($success == 3) { ?>
<script type="text/javascript">
	alert("{{trans('keywords.config_wrong_alert');}}");
</script>
<?php } ?>

<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>

<script type="text/javascript">
$("#basic-form").validate({
  rules: {
	title: "required",
	description: "required",
  
  }
});

</script>

@stop