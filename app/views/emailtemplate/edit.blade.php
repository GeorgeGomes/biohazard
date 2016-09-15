@extends('layout')

@section('content')

<style>
    .text_edit {
    width: 500px;
    height: 300px;
    resize: none;
}

#view_text {
    width: 500px;
}

</style>

<div >
    
    <form method="post" id="basic-form" action="{{ URL::Route('EmailTemplateUpdate') }}"  enctype="multipart/form-data">
        
        <input type="hidden" name="id" value="<?= $id ?>"/> 
        
        <label >{{trans('email.key');}}</label>

        <?php if($id == 0){?>
            <input type="text" class="form-control" id="key" name="key" placeholder="Key" value="<?= $key ?>"></br>
        <?php } else{ ?>
            <input readonly type="text" class="form-control" id="key" placeholder="Key" name="key" value="<?= $key ?>"></br>
        <?php }?>

        <label >{{trans('email.from');}}</label>
        <input type="text" class="form-control" id="from" name="from" placeholder="From" value="<?= $from ?>"></br>

        <label >{{trans('email.subject');}}</label>
        <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" value="<?= $subject ?>"></br>
        
        <label >{{trans('email.copy_emails');}}</label>
        <input type="text" class="form-control" id="copy_emails" name="copy_emails" placeholder="Copy emails" value="<?= $copy_emails ?>"></br>
        
        <div id="body" class="tab-pane fade">
            <div style="margin: 15px;">

                <div class="form-group">
                    <textarea name="content" id="content" rows="30">
                        <?= $content ?>
                    </textarea>
                </div>

                <button id="save" class="btn btn-success" type="submit" >{{trans('keywords.save_change');}}</button>
            </div>
        </div>
    </form>
</div>

<script src="{{ asset_url(); }}/javascript/ckeditor/ckeditor.js"></script>
<script>
    //CKEDITOR.config.htmlEncodeOutput = false;
    CKEDITOR.config.entities = false ;
    CKEDITOR.config.basicEntities = false;
    CKEDITOR.config.allowedContent=true;
    CKEDITOR.config.protectedSource.push(/<\?[\s\S]*?\?>/g);
    CKEDITOR.replace( 'content' );
</script>

<script type="text/javascript">
$("#basic-form").validate({
  rules: {
    title: "required",
    body: "required",
  
  }
});

</script>

@stop