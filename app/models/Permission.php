<?php

class Permission extends \Eloquent {
	protected $fillable = array('id', 'name');


	protected $table = 'permission';
}