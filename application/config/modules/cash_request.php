<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['cash_request']['visible']        = TRUE;
$config['module']['cash_request']['main_warehouse'] = FALSE;
$config['module']['cash_request']['parent']         = 'finance';
$config['module']['cash_request']['label']          = 'Cash Request';
$config['module']['cash_request']['name']           = 'Cash Request';
$config['module']['cash_request']['route']          = 'cash_request';
$config['module']['cash_request']['view']           = config_item('module_path') .'cash_request/';
$config['module']['cash_request']['language']       = 'cash_request_lang';
$config['module']['cash_request']['helper']         = 'cash_request_helper';
$config['module']['cash_request']['table']          = 'tb_cash_request';
$config['module']['cash_request']['model']          = 'Cash_Request_Model';
$config['module']['cash_request']['permission']     = array(
    'index'               => 'AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'info'                => 'AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'print'               => 'AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'document'            => 'AP STAFF,SUPER ADMIN',//tambhan supervisor
    'payment'             => 'FINANCE SUPERVISOR,TELLER,SUPER ADMIN',
    'approval'            => 'FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'check'               => 'FINANCE MANAGER,SUPER ADMIN',
    'approve'             => 'VP FINANCE,SUPER ADMIN',
    'manage_attachment'   => 'SUPER ADMIN,TELLER,AP STAFF,FINANCE SUPERVISOR',
    'cancel'              => 'AP STAFF,SUPER ADMIN',
    'change_account'      => 'AP STAFF,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
);
