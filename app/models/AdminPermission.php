<?php

class AdminPermission extends \Eloquent {
	protected $fillable = array('id', 'admin_id', 'permission_id');

	protected $table = 'admin_permission';
}