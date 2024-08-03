<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['konsolidasi']['visible']         = FALSE;
$config['module']['konsolidasi']['main_warehouse']  = TRUE;
$config['module']['konsolidasi']['parent']          = 'document';
$config['module']['konsolidasi']['label']           = 'Laporan Konsolidasi GRN-MS';
$config['module']['konsolidasi']['name']            = 'konsolidasi';
$config['module']['konsolidasi']['route']           = 'konsolidasi';
$config['module']['konsolidasi']['view']            = config_item('module_path') .'konsolidasi/';
$config['module']['konsolidasi']['language']        = 'konsolidasi_lang';
$config['module']['konsolidasi']['table']           = 'tb_konsolidasi_ms_grn';
$config['module']['konsolidasi']['model']           = 'Konsolidasi_Model';
$config['module']['konsolidasi']['permission']      = array(
  'create'  => 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'edit'    => 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'info'    => 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'save'    => 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'delete'  => 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'index'  	=> 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  );
