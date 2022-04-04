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
    'index'               => 'AP STAFF,PIC STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'info'                => 'AP STAFF,PIC STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'print'               => 'AP STAFF,PIC STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'document'            => 'AP STAFF,FINANCE MANAGER,SUPER ADMIN',//tambhan supervisor
    'payment'             => 'AP STAFF,FINANCE SUPERVISOR,TELLER,SUPER ADMIN',
    'approval'            => 'AP STAFF,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
    'check'               => 'AP STAFF,FINANCE MANAGER,SUPER ADMIN',
    'approve'             => 'AP STAFF,VP FINANCE,SUPER ADMIN',
    'manage_attachment'   => 'AP STAFF,SUPER ADMIN,TELLER,AP STAFF,FINANCE SUPERVISOR',
    'cancel'              => 'AP STAFF,PIC STAFF,SUPER ADMIN',
    'change_account'      => 'AP STAFF,PIC STAFF,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
);
