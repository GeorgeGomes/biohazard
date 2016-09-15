<?php

class Utils{

	public static function generateCode(){
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < 3; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		
		$code = new Code();
		$code->code = 'fakecode';
		$code->save();
		$code->code = $randomString . $code->id; 
		$code->save();
		
		return $code->id;
	}

}


?>