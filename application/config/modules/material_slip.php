<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['material_slip']['visible']          = TRUE;
$config['module']['material_slip']['main_warehouse']   = FALSE;
$config['module']['material_slip']['parent']           = 'document';
$config['module']['material_slip']['label']            = 'Material Slip';
$config['module']['material_slip']['name']             = 'material_slip';
$config['module']['material_slip']['route']            = 'material_slip';
$config['module']['material_slip']['view']             = config_item('module_path') .'material_slip/';
$config['module']['material_slip']['language']         = 'material_slip_lang';
$config['module']['material_slip']['helper']           = 'material_slip_helper';
$config['module']['material_slip']['table']            = 'tb_issuances';
$config['module']['material_slip']['model']            = 'Material_Slip_Model';
$config['module']['material_slip']['permission']       = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,FINANCE,OTHER,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  'info'      => 'PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'document'  => 'PIC STOCK,SUPERVISOR,SUPER ADMIN',
  'print'     => 'PIC STOCK,SUPERVISOR,VP FINANCE,FINANCE,PIC PROCUREMENT,SUPER ADMIN',
  'delete'    => 'PIC STOCK,SUPERVISOR,SUPER ADMIN',
  'import'    => 'SUPERVISOR,PIC STOCK,SUPER ADMIN'
);
