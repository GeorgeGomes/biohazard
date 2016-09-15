
@extends('layout')

@section('content')

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?= $title ?></h3>
    </div><!-- /.box-header -->
    <!-- form start -->
    <form method="post" id="basic" action="{{ URL::Route('AdminDocumentTypesUpdate') }}"  enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="box-body">
            <div class="form-group">
                <label>{{trans('provider.doc_name');}}</label>
                <input type="text" class="form-control" name="name" placeholder="{{trans('provider.doc_name');}}" value="<?= $name ?>">

            </div>




        </div><!-- /.box-body -->

        <div class="box-footer">


            <button id="doc" type="submit" class="btn btn-primary btn-flat btn-block">{{trans('provider.save');}}</button>
        </div>
    </form>
</div>




<?php if ($success == 1) { ?>
    <script type="text/javascript">
        alert("{{trans('blade.doc_success');}}");
        document.location.href = "{{ URL::Route('AdminDocumentTypes') }}";
    </script>
<?php } ?>
<?php if ($success == 2) { ?>
    <script type="text/javascript">
        alert("{{trans('keywords.config_wrong_alert');}}");
    </script>
<?php } ?>


<script type="text/javascript">
    $("#basic").validate({
        rules: {
            name: "required",
        }
    });

</script>

@stop