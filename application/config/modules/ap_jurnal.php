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
  'index'     => 'PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'info'      => 'PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'print'     => 'PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'document'  => 'PIC STOCK,SUPERVISOR,CHIEF OF MAINTANCE,SUPER ADMIN',//tambhan supervisor
  'payment'   => 'FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'import'    => 'PIC STOCK,SUPERVISOR,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',//tambhan supervisor
);
