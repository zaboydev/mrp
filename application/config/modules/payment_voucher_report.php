<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['payment_voucher_report']['visible']        = TRUE;
$config['module']['payment_voucher_report']['main_warehouse'] = FALSE;
$config['module']['payment_voucher_report']['parent']         = 'finance_report';
$config['module']['payment_voucher_report']['label']          = 'Payment Voucher Report';
$config['module']['payment_voucher_report']['name']           = 'Payment Voucher Report';
$config['module']['payment_voucher_report']['route']          = 'payment_voucher_report';
$config['module']['payment_voucher_report']['view']           = config_item('module_path') . 'payment_voucher_report/';
$config['module']['payment_voucher_report']['language']       = 'prl_poe_lang';
$config['module']['payment_voucher_report']['helper']         = 'material_slip_helper';
$config['module']['payment_voucher_report']['table']          = 'tb_purchase_orders';
$config['module']['payment_voucher_report']['model']          = 'Payment_Model';
$config['module']['payment_voucher_report']['permission']     = array(
    'index'     => 'PIC STAFF,AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN,CHIEF OPERATION OFFICER,CHIEF OF FINANCE,HEAD OF SCHOOL',
);
