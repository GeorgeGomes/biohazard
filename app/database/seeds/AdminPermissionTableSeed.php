<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class AdminPermissionTableSeed extends Seeder {

    public function run() {
        DB::table('admin_permission')->delete();

        $admins = Admin::all();

        foreach($admins as $admin){
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '1'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '2'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '201'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '202'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '203'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '204'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '205'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '206'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '207'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '208'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '209'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '3'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '301'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '302'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '303'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '4'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '401'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '402'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '403'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '404'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '405'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '5'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '501'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '502'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '503'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '6'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '601'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '602'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '603'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '7'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '701'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '702'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '703'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '8'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '801'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '802'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '803'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '9'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '901'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '902'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '903'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '904'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '10'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '11'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '1101'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '1102'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '12'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '13'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '1301'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '14'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '1401'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '1402'));
            AdminPermission::create(array('admin_id' => $admin->id, 'permission_id' => '1403'));
        }

        $this->command->info('AdminPermission created!');
    }

}
