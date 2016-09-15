<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class PermissionSubActionTableSeed extends Seeder {

    public function run() {
        DB::table('permission_sub_action')->delete();

        PermissionSubAction::create(array('id' => 201, 'name' => 'provider_edit', 'parent_id' => '2'));
        PermissionSubAction::create(array('id' => 202, 'name' => 'provider_doc', 'parent_id' => '2'));
        PermissionSubAction::create(array('id' => 203, 'name' => 'provider_reject', 'parent_id' => '2'));
        PermissionSubAction::create(array('id' => 204, 'name' => 'provider_analize', 'parent_id' => '2'));
        PermissionSubAction::create(array('id' => 205, 'name' => 'provider_suspended', 'parent_id' => '2'));
        PermissionSubAction::create(array('id' => 206, 'name' => 'provider_delete', 'parent_id' => '2'));
        PermissionSubAction::create(array('id' => 207, 'name' => 'provider_bank', 'parent_id' => '2'));
        PermissionSubAction::create(array('id' => 208, 'name' => 'provider_hist', 'parent_id' => '2'));
        PermissionSubAction::create(array('id' => 209, 'name' => 'provider_approve', 'parent_id' => '2'));
        PermissionSubAction::create(array('id' => 210, 'name' => 'provider_pendent', 'parent_id' => '2'));
        PermissionSubAction::create(array('id' => 211, 'name' => 'provider_inactive', 'parent_id' => '2'));
        PermissionSubAction::create(array('id' => 301, 'name' => 'request_map', 'parent_id' => '3'));
        PermissionSubAction::create(array('id' => 302, 'name' => 'request_charge', 'parent_id' => '3'));
        PermissionSubAction::create(array('id' => 303, 'name' => 'request_transfer', 'parent_id' => '3'));
        PermissionSubAction::create(array('id' => 401, 'name' => 'user_edit', 'parent_id' => '4'));
        PermissionSubAction::create(array('id' => 402, 'name' => 'user_history', 'parent_id' => '4'));
        PermissionSubAction::create(array('id' => 403, 'name' => 'user_cupon', 'parent_id' => '4'));
        PermissionSubAction::create(array('id' => 404, 'name' => 'user_add_request', 'parent_id' => '4'));
        PermissionSubAction::create(array('id' => 405, 'name' => 'user_delete', 'parent_id' => '4'));
        PermissionSubAction::create(array('id' => 501, 'name' => 'reviews_provider', 'parent_id' => '5'));
        PermissionSubAction::create(array('id' => 502, 'name' => 'reviews_user', 'parent_id' => '5'));
        PermissionSubAction::create(array('id' => 503, 'name' => 'reviews_delete', 'parent_id' => '5'));
        PermissionSubAction::create(array('id' => 601, 'name' => 'info_add', 'parent_id' => '6'));
        PermissionSubAction::create(array('id' => 602, 'name' => 'info_edit', 'parent_id' => '6'));
        PermissionSubAction::create(array('id' => 603, 'name' => 'info_delete', 'parent_id' => '6'));
        PermissionSubAction::create(array('id' => 701, 'name' => 'price_service_add', 'parent_id' => '7'));
        PermissionSubAction::create(array('id' => 702, 'name' => 'price_service_edit', 'parent_id' => '7'));
        PermissionSubAction::create(array('id' => 703, 'name' => 'price_service_delete', 'parent_id' => '7'));
        PermissionSubAction::create(array('id' => 801, 'name' => 'documents_add', 'parent_id' => '8'));
        PermissionSubAction::create(array('id' => 802, 'name' => 'documents_edit', 'parent_id' => '8'));
        PermissionSubAction::create(array('id' => 803, 'name' => 'documents_delete', 'parent_id' => '8'));
        PermissionSubAction::create(array('id' => 901, 'name' => 'promotional code_add', 'parent_id' => '9'));
        PermissionSubAction::create(array('id' => 902, 'name' => 'promotional code_edit', 'parent_id' => '9'));
        PermissionSubAction::create(array('id' => 903, 'name' => 'promotional code_active', 'parent_id' => '9'));
        PermissionSubAction::create(array('id' => 904, 'name' => 'promotional code_inactive', 'parent_id' => '9'));
        PermissionSubAction::create(array('id' => 1101, 'name' => 'payments detail_map', 'parent_id' => '11'));
        PermissionSubAction::create(array('id' => 1102, 'name' => 'payments detail_charge', 'parent_id' => '11'));
        PermissionSubAction::create(array('id' => 1301, 'name' => 'settings_install', 'parent_id' => '13'));
        PermissionSubAction::create(array('id' => 1401, 'name' => 'admin_add', 'parent_id' => '14'));
        PermissionSubAction::create(array('id' => 1402, 'name' => 'admin_edit', 'parent_id' => '14'));
        PermissionSubAction::create(array('id' => 1403, 'name' => 'admin_delete', 'parent_id' => '14'));

        $this->command->info('PermissionSubAction created!');
    }

}
