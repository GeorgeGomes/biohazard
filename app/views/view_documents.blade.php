@extends('layout')

@section('content')

<!--<div class="col-md-6 col-sm-12">

    <div class="box box-danger">
    </div>
</div>

<div class="col-md-6 col-sm-12">

    <div class="box box-danger">
    </div>
</div>-->

<div class="box box-info tbl-box">
    <div align="left" id="paglink"><?php echo $docs->appends(array('type' => Session::get('type'), 'valu' => Session::get('valu')))->links(); ?></div>
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>{{trans('provider.id_provider')}}</th>
                <th>{{trans('provider.name_provider')}}</th>
                <th>{{trans('provider.doc_type')}}</th>
                <th>{{trans('provider.see_down')}}</th>
            </tr>

            <?php foreach ($docs as $doc) { ?>
                <tr>
                    <td>{{$provider->id}}</td>
                    <td>{{$provider->first_name." ".$provider->last_name}}</td>
                    <td><?php $document = Document::where('id', $doc->document_id)->first();
            echo $document->name; ?></td>
                    <td><a href="{{$doc->url}}" target="_blank"><span class="btn btn-info btn-large">{{trans('provider.view')}}</span></a></td>
                </tr>
<?php } ?>
        </tbody>
    </table>
    <div align="left" id="paglink"><?php echo $docs->appends(array('type' => Session::get('type'), 'valu' => Session::get('valu')))->links(); ?></div>
</div>
@stop