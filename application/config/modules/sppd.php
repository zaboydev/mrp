<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['sppd']['visible']         = TRUE;
$config['module']['sppd']['main_warehouse']   = FALSE;
$config['module']['sppd']['parent']      = 'perjalanan_dinas';
$config['module']['sppd']['label']       = 'SPPD';
$config['module']['sppd']['name']        = 'sppd';
$config['module']['sppd']['route']       = 'sppd';
$config['module']['sppd']['view']        = config_item('module_path') .'sppd/';
$config['module']['sppd']['language']    = 'item_group_lang';
$config['module']['sppd']['helper']      = 'business_trip_request_helper';
$config['module']['sppd']['table']       = 'tb_business_trip_request';
$config['module']['sppd']['model']       = 'Sppd_Model';
$config['module']['sppd']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN,PIC STAFF,CHIEF OPERATION OFFICER',
    'create'  => 'ADMIN,SUPER ADMIN,PIC STAFF',
    'import'  => 'ADMIN,SUPER ADMIN,PIC STAFF',
    'print'   => 'ADMIN,SUPER ADMIN,PIC STAFF,CHIEF OPERATION OFFICER',
    'info'    => 'ADMIN,SUPER ADMIN,PIC STAFF,CHIEF OPERATION OFFICER',
    'save'    => 'ADMIN,SUPER ADMIN,PIC STAFF',
    'delete'  => 'ADMIN,SUPER ADMIN,PIC STAFF',
    'approval'  => 'SUPER ADMIN,CHIEF OPERATION OFFICER',
);
