<?php

class EmailTemplateController extends \BaseController {

	// tabela de emails
	public function index() {
		$emailtemplate = EmailTemplate::paginate(50);
		$title = ucwords(trans('customize.email_template')); /* 'Email View' */
		return View::make('emailtemplate.emails')
			->with('title', $title)
			->with('page', 'emails')
			->with('emailtemplate', $emailtemplate);
	}

	//edita template de email
	public function edit_template($id)
	{
		$emailtemplate = EmailTemplate::find($id);
		$content = "";
		if ($emailtemplate) {
			$id = $emailtemplate->id;
			$subject = $emailtemplate->subject;
			$from = $emailtemplate->from;
			$key = $emailtemplate->key;
			$copy_emails = $emailtemplate->copy_emails;
			$path = app_path().'/views/emails/'.$key.'.blade.php';

			if (file_exists($path)) {
    			$content = file_get_contents($path, "rw+");	
			}else{
				$content = "Arquivo inexistente";
			}
			
		}else {
			$id = 0;
			$key = "";
			$subject = "";
			$from = Settings::getAdminEmail();
			$copy_emails = "";
			$page_title = trans('adminController.add_info');
		}

		$title = ucwords(trans('customize.edit')); /* 'Email View' */
		return View::make('emailtemplate.edit')
			->with('title', $title)
			->with('id', $id)
			->with('subject', $subject)
			->with('from', $from)
			->with('copy_emails', $copy_emails)
			->with('content', $content)
			->with('key', $key)
			->with('page', 'email-template');
	}

	//deleta o template de email a partir do id
	public function delete_template($id)
	{
		$emailtemplate = EmailTemplate::find($id);

		if ($emailtemplate) {
			$key = $emailtemplate->key;
			$path = app_path().'/views/emails/'.$key.'.blade.php';
			
			if (file_exists($path = app_path().'/views/emails/'.$key.'.blade.php')) {
	        	//unlink($path);
		    } else {

		    }

		}
		$id = Request::segment(4);

		EmailTemplate::where('id', $id)->delete();
		return Redirect::to("/admin/email_template");
	}


	//Cria novo template de email se ainda não existir
	public function update_template()
	{
		$id = Input::get('id');
		$key = Input::get('key');
		$from = Input::get('from');
		$subject = Input::get('subject');
		$copy_emails = Input::get('copy_emails');
		$content = Input::get('content');

		$path = app_path().'/views/emails/';
		$sufix = '.blade.php';
		$filePath = app_path().'/views/emails/'.$key.$sufix ;

		if ($id == 0) {
			$emailtemplate = new EmailTemplate;
		} else {
			$emailtemplate = EmailTemplate::find($id);
		}

		$validator = Validator::make(
			array(
				'key' 		=> $key,
				'subject' 	=> $subject,
				'from'		=> $from,
			),
			array(
				'key' 		=> 'required',
				'subject' 	=> 'required',
				'from' 		=> 'required',
			)
		);

		if($validator->fails()){
			return Redirect::to("admin/email_template/edit/".$id)->withErrors($validator);
		} else {

			$emailtemplate->key = $key;
			$emailtemplate->from = $from;
			$emailtemplate->subject	= $subject;
			$emailtemplate->copy_emails	= $copy_emails;
			$emailtemplate->save();

			$content = str_replace("&gt;", ">", $content);
			$content = str_replace("&lt;", "<", $content);

			File::put($filePath, $content);

			return Redirect::to("/admin/email_template");
		}
	}

	public function destroy($id)
	{
		//
	}


}
