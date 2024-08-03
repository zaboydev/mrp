<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['payment_detail_report']['visible']        = FALSE;
$config['module']['payment_detail_report']['main_warehouse'] = FALSE;
$config['module']['payment_detail_report']['parent']         = 'account_payable';
$config['module']['payment_detail_report']['label']          = 'Payment Detail Report';
$config['module']['payment_detail_report']['name']           = 'Payment Detail Report';
$config['module']['payment_detail_report']['route']          = 'payment_detail_report';
$config['module']['payment_detail_report']['view']           = config_item('module_path') .'payment/payment_detail_report';
$config['module']['payment_detail_report']['language']       = 'account_payable_lang';
$config['module']['payment_detail_report']['helper']         = 'material_slip_helper';
$config['module']['payment_detail_report']['table']          = 'tb_hutang';
$config['module']['payment_detail_report']['model']          = 'Payment_Model';
$config['module']['payment_detail_report']['permission']     = array(
  'index'               => 'PIC STAFF,AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN,CHIEF OPERATION OFFICER,CHIEF OF FINANCE,HEAD OF SCHOOL',
  'info'                => 'PIC STAFF,AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN,CHIEF OPERATION OFFICER,CHIEF OF FINANCE,HEAD OF SCHOOL',
  'print'               => 'PIC STAFF,AP STAFF,TELLER,FINANCE,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN,CHIEF OPERATION OFFICER,CHIEF OF FINANCE,HEAD OF SCHOOL',
  'document'            => 'PIC STAFF,AP STAFF,SUPER ADMIN',//tambhan supervisor
  'payment'             => 'PIC STAFF,FINANCE SUPERVISOR,TELLER,SUPER ADMIN',
  'approval'            => 'FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
  'check'               => 'FINANCE MANAGER,SUPER ADMIN',
  'approve'             => 'VP FINANCE,SUPER ADMIN',
  'manage_attachment'   => 'PIC STAFF,SUPER ADMIN,TELLER,AP STAFF,FINANCE SUPERVISOR',
  'cancel'              => 'PIC STAFF,AP STAFF,SUPER ADMIN',
  'change_account'      => 'PIC STAFF,AP STAFF,FINANCE SUPERVISOR,FINANCE MANAGER,VP FINANCE,SUPER ADMIN',
  'review'              => ',CHIEF OPERATION OFFICER,CHIEF OF FINANCE,VP FINANCE,HEAD OF SCHOOL'
);
