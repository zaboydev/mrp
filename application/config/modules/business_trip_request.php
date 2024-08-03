<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['business_trip_request']['visible']         = TRUE;
$config['module']['business_trip_request']['main_warehouse']   = FALSE;
$config['module']['business_trip_request']['parent']      = 'perjalanan_dinas';
$config['module']['business_trip_request']['label']       = 'Surat Perjalanan Dinas';
$config['module']['business_trip_request']['name']        = 'business_trip_request';
$config['module']['business_trip_request']['route']       = 'business_trip_request';
$config['module']['business_trip_request']['view']        = config_item('module_path') .'business_trip_request/';
$config['module']['business_trip_request']['language']    = 'item_group_lang';
$config['module']['business_trip_request']['helper']      = 'business_trip_request_helper';
$config['module']['business_trip_request']['table']       = 'tb_business_trip_request';
$config['module']['business_trip_request']['model']       = 'Business_Trip_Request_Model';
$config['module']['business_trip_request']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN,PIC STAFF,CHIEF OPERATION OFFICER,HEAD OF SCHOOL',
    'create'  => 'ADMIN,SUPER ADMIN,PIC STAFF',
    'import'  => 'ADMIN,SUPER ADMIN,PIC STAFF',
    'print'   => 'ADMIN,SUPER ADMIN,PIC STAFF,CHIEF OPERATION OFFICER',
    'info'    => 'ADMIN,SUPER ADMIN,PIC STAFF,CHIEF OPERATION OFFICER,HEAD OF SCHOOL',
    'save'    => 'ADMIN,SUPER ADMIN,PIC STAFF',
    'delete'  => 'ADMIN,SUPER ADMIN,PIC STAFF',
    'approval'  => 'SUPER ADMIN,HEAD OF SCHOOL,CHIEF OPERATION OFFICER,HEAD OF SCHOOL',
);
