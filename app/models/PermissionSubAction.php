<?php

class PermissionSubAction extends \Eloquent {
	protected $fillable = array('id', 'name', 'parent_id');


	protected $table = 'permission_sub_action';

}