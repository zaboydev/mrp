<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['spd_payment']['visible']         = TRUE;
$config['module']['spd_payment']['main_warehouse']   = FALSE;
$config['module']['spd_payment']['parent']      = 'perjalanan_dinas';
$config['module']['spd_payment']['label']       = 'SPD Advance';
$config['module']['spd_payment']['name']        = 'spd_payment';
$config['module']['spd_payment']['route']       = 'spd_payment';
$config['module']['spd_payment']['view']        = config_item('module_path') .'business_trip_request/payment';
$config['module']['spd_payment']['language']    = 'item_group_lang';
$config['module']['spd_payment']['helper']      = 'business_trip_request_helper';
$config['module']['spd_payment']['table']       = 'tb_business_trip_request';
$config['module']['spd_payment']['model']       = 'Business_Trip_Request_Model';
$config['module']['spd_payment']['permission']  = array(
    'index'               => 'PIC STAFF,AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN,CHIEF OPERATION OFFICER,CHIEF OF FINANCE,HEAD OF SCHOOL',
    'info'                => 'PIC STAFF,AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN,CHIEF OPERATION OFFICER,CHIEF OF FINANCE,HEAD OF SCHOOL',
    'print'               => 'PIC STAFF,AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN,CHIEF OPERATION OFFICER,CHIEF OF FINANCE,HEAD OF SCHOOL',
    'create'              => 'PIC STAFF,AP STAFF,SUPER ADMIN',//tambhan supervisor
    'payment'             => 'PIC STAFF,FINANCE SUPERVISOR,TELLER,SUPER ADMIN',
    'approval'            => 'FINANCE MANAGER,SUPER ADMIN,HEAD OF SCHOOL',
    'check'               => 'FINANCE MANAGER,SUPER ADMIN',
    'approve'             => 'VP FINANCE,SUPER ADMIN',
    'manage_attachment'   => 'PIC STAFF,SUPER ADMIN,TELLER,AP STAFF,FINANCE SUPERVISOR,HEAD OF SCHOOL',
    'cancel'              => 'PIC STAFF,AP STAFF,SUPER ADMIN',
    'change_account'      => 'PIC STAFF,AP STAFF,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'review'              => ',CHIEF OPERATION OFFICER,CHIEF OF FINANCE,VP FINANCE,HEAD OF SCHOOL'
);
