@extends('layout')

@section('content')

<div class="col-lg-4 col-xs-6">
	<script type="text/javascript">

	</script>    
	</br>
	<a id="addinfo" href="{{ URL::Route('EmailTemplateEdit', 0) }}"><input type="button" class="btn btn-info btn-flat btn-block" value="{{trans('blade.new_email')}}"></a>
	
	</br>	

</div>

<div class="box box-info tbl-box">
	
		<table class="table table-bordered">
			<tbody>
				<tr>
					<th>{{ trans('provider.id_grid');}}</th>
					<th>{{ trans('email.key');}}</th>
					<th>{{ trans('email.copy_emails');}}</th>
					<th>{{ trans('provider.action_grid');}}</th>
					<?php 
						
					?>
				</tr>
				<?php foreach ($emailtemplate as $emailtemplate) { ?>
				<tr>
					<td><?= $emailtemplate->id ?></td>
					<td><?= $emailtemplate->key ?></td>
					<td><?= $emailtemplate->copy_emails ?></td>
					<td>
						<a id="edit" href="{{ URL::Route('EmailTemplateEdit', $emailtemplate->id) }}"><input type="button" class="btn btn-success" value="{{trans('blade.edit')}}"></a>
						
						<a id="delete" href="{{ URL::Route('EmailTemplateDelete', $emailtemplate->id) }}"><input type="button" class="btn btn-danger" value="{{trans('blade.delete')}}"></a>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
</div>



<script type="text/javascript">
	function codeAddress() {
				
	}
</script>


@stop