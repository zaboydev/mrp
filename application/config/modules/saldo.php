<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['saldo']['visible']        = TRUE;
$config['module']['saldo']['main_warehouse'] = FALSE;
$config['module']['saldo']['parent']         = 'finance';
$config['module']['saldo']['label']          = 'Saldo';
$config['module']['saldo']['name']           = 'Saldo';
$config['module']['saldo']['route']          = 'saldo';
$config['module']['saldo']['view']           = config_item('module_path') .'saldo/';
$config['module']['saldo']['language']       = 'cash_request_lang';
$config['module']['saldo']['helper']         = 'cash_request_helper';
$config['module']['saldo']['table']          = 'tb_jurnal';
$config['module']['saldo']['model']          = 'Saldo_Model';
$config['module']['saldo']['permission']     = array(
    'index'               => 'PIC STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'info'                => 'PIC STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'print'               => 'PIC STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'document'            => 'FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,SUPER ADMIN',//tambhan supervisor
    'payment'             => 'FINANCE SUPERVISOR,TELLER,SUPER ADMIN',
    'approval'            => 'FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'check'               => 'FINANCE MANAGER,SUPER ADMIN',
    'approve'             => 'VP FINANCE,SUPER ADMIN',
    'manage_attachment'   => 'SUPER ADMIN,TELLER,AP STAFF,FINANCE SUPERVISOR',
    'cancel'              => 'PIC STAFF,SUPER ADMIN',
    'change_account'      => 'PIC STAFF,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
);
