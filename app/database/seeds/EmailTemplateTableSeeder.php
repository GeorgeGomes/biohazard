<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class EmailTemplateTableSeeder extends Seeder {

    public function run() {

        DB::table('email_template')->delete();

        EmailTemplate::updateOrCreate(array( 'id' => 1, 'key' => 'approve_provider_mail', 'subject' => trans('email.approve_provider_mail') , 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 2, 'key' => 'contact_mail', 'subject' => trans('email.contact_mail'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 3, 'key' => 'decline_provider_mail', 'subject' => trans('email.decline_provider_mail'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 4, 'key' => 'new_request', 'subject' => trans('email.new_request'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 5, 'key' => 'payment_charged', 'subject' => trans('email.payment_charged'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 6, 'key' => 'payment_made_client', 'subject' => trans('email.payment_made_client'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 7, 'key' => 'provider_new_register', 'subject' => trans('email.provider_new_register'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 8, 'key' => 'request_unanswered', 'subject' => trans('email.request_unanswered'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 9, 'key' => 'user_new_register', 'subject' => trans('email.user_new_register'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 10, 'key' => 'user_request_accept_by_driver', 'subject' => trans('email.user_request_accept_by_driver'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 11, 'key' => 'finalemail', 'subject' => trans('email.finalemail'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 12, 'key' => 'forgotpassword', 'subject' => trans('email.forgotpassword'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 13, 'key' => 'invoice', 'subject' => trans('email.invoice'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 14, 'key' => 'layout', 'subject' => trans('email.layout'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 15, 'key' => 'providerregister', 'subject' => trans('email.providerregister'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 16, 'key' => 'reset_password', 'subject' => trans('email.reset_password'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 17, 'key' => 'userregister', 'subject' => trans('email.userregister'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        EmailTemplate::updateOrCreate(array('id' => 18, 'key' => 'reminder', 'subject' => trans('email.reminder'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));

        EmailTemplate::updateOrCreate(array('id' => 19, 'key' => 'contact_mail_user', 'subject' => trans('email.contact_mail_user'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));

        EmailTemplate::updateOrCreate(array('id' => 20, 'key' => 'contact_mail_provider', 'subject' => trans('email.contact_mail_user'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));

        EmailTemplate::updateOrCreate(array('id' => 21, 'key' => 'prelaunching', 'subject' => trans('email.prelaunching'), 'copy_emails' => Settings::getAdminEmail(), 'from' => Settings::getAdminEmail()));
        
    }

}
