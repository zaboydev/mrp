<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['jurnal_payment']['visible']        = FALSE;
$config['module']['jurnal_payment']['main_warehouse'] = TRUE;
$config['module']['jurnal_payment']['parent']         = 'accounting_report';
$config['module']['jurnal_payment']['label']          = 'Payment Jurnal';
$config['module']['jurnal_payment']['name']           = 'Payment Jurnal';
$config['module']['jurnal_payment']['route']          = 'Payment_Jurnal';
$config['module']['jurnal_payment']['view']           = config_item('module_path') . 'jurnal_payment/';
$config['module']['jurnal_payment']['language']       = 'jurnal_lang';
$config['module']['jurnal_payment']['helper']         = 'material_slip_helper';
$config['module']['jurnal_payment']['table']          = 'tb_jurnal';
$config['module']['jurnal_payment']['model']          = 'Payment_Jurnal_Model';
$config['module']['jurnal_payment']['permission']     = array(
    'index'     => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',
    'info'      => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',
    'print'     => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',
    'document'  => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN', //tambhan supervisor
    'payment'   => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',
    'import'    => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',//tambhan supervisor
);
