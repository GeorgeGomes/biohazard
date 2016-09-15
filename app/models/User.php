<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent  implements UserInterface, RemindableInterface{

	use UserTrait, RemindableTrait, SoftDeletingTrait;

	protected $dates = ['deleted_at'];

    protected $table = 'user';
	
	public function myCode(){
        return $this->hasOne('Code', 'id', 'my_code');
    }
	
	public function networkingCode(){
        return $this->hasOne('Code', 'id', 'networking_code');
    }

}
