<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['usage_jurnal']['visible']        = TRUE;
$config['module']['usage_jurnal']['main_warehouse'] = TRUE;
$config['module']['usage_jurnal']['parent']         = 'accounting_report';
$config['module']['usage_jurnal']['label']          = 'Usage Jurnal';
$config['module']['usage_jurnal']['name']           = 'Usage Jurnal';
$config['module']['usage_jurnal']['route']          = 'usage_jurnal';
$config['module']['usage_jurnal']['view']           = config_item('module_path') .'usage_jurnal/';
$config['module']['usage_jurnal']['language']       = 'jurnal_lang';
$config['module']['usage_jurnal']['helper']         = 'material_slip_helper';
$config['module']['usage_jurnal']['table']          = 'tb_jurnal';
$config['module']['usage_jurnal']['model']          = 'Usage_Jurnal_Model';
$config['module']['usage_jurnal']['permission']     = array(
  'index'     => 'PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'info'      => 'PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'print'     => 'PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'document'  => 'PIC STOCK,SUPERVISOR,CHIEF OF MAINTANCE,SUPER ADMIN',//tambhan supervisor
  'payment'   => 'FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'import'    => 'PIC STOCK,SUPERVISOR,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',//tambhan supervisor
);
