<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['ap_jurnal']['visible']        = TRUE;
$config['module']['ap_jurnal']['main_warehouse'] = TRUE;
$config['module']['ap_jurnal']['parent']         = 'accounting_report';
$config['module']['ap_jurnal']['label']          = 'AP Jurnal';
$config['module']['ap_jurnal']['name']           = 'AP Jurnal';
$config['module']['ap_jurnal']['route']          = 'ap_jurnal';
$config['module']['ap_jurnal']['view']           = config_item('module_path') .'ap_jurnal/';
$config['module']['ap_jurnal']['language']       = 'jurnal_lang';
$config['module']['ap_jurnal']['helper']         = 'material_slip_helper';
$config['module']['ap_jurnal']['table']          = 'tb_jurnal';
$config['module']['ap_jurnal']['model']          = 'Ap_Jurnal_Model';
$config['module']['ap_jurnal']['permission']     = array(
  'index'     => 'FINANCE,VP FINANCE,SUPER ADMIN',
  'info'      => 'FINANCE,VP FINANCE,SUPER ADMIN',
  'print'     => 'FINANCE,VP FINANCE,SUPER ADMIN',
  'document'  => 'FINANCE,VP FINANCE,SUPER ADMIN',//tambhan supervisor
  'payment'   => 'FINANCE,VP FINANCE,SUPER ADMIN',
  'import'    => 'FINANCE,VP FINANCE,SUPER ADMIN',//tambhan supervisor
);
