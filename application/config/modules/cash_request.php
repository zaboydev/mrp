<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['cash_request']['visible']        = TRUE;
$config['module']['cash_request']['main_warehouse'] = FALSE;
$config['module']['cash_request']['parent']         = 'finance';
$config['module']['cash_request']['label']          = 'Top Up Petty Cash';
$config['module']['cash_request']['name']           = 'Top Up Petty Cash';
$config['module']['cash_request']['route']          = 'cash_request';
$config['module']['cash_request']['view']           = config_item('module_path') .'cash_request/';
$config['module']['cash_request']['language']       = 'cash_request_lang';
$config['module']['cash_request']['helper']         = 'cash_request_helper';
$config['module']['cash_request']['table']          = 'tb_cash_request';
$config['module']['cash_request']['model']          = 'Cash_Request_Model';
$config['module']['cash_request']['permission']     = array(
    'index'               => 'PIC STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'info'                => 'PIC STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'print'               => 'PIC STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'document'            => 'PIC STAFF,SUPER ADMIN',//tambhan supervisor
    'payment'             => '',
    'approval'            => 'FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'check'               => 'FINANCE MANAGER,SUPER ADMIN',
    'approve'             => 'VP FINANCE,SUPER ADMIN',
    'manage_attachment'   => 'SUPER ADMIN,TELLER,AP STAFF,FINANCE SUPERVISOR',
    'cancel'              => 'PIC STAFF,SUPER ADMIN',
    'change_account'      => 'PIC STAFF,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
);
