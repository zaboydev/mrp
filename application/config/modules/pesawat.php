<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['pesawat']['visible']         = FALSE;
$config['module']['pesawat']['main_warehouse']  = TRUE;
$config['module']['pesawat']['parent']          = 'aircraft';
$config['module']['pesawat']['label']           = 'Aircraft & Component';
$config['module']['pesawat']['name']            = 'pesawat';
$config['module']['pesawat']['route']           = 'pesawat';
$config['module']['pesawat']['view']            = config_item('module_path') .'pesawat/';
$config['module']['pesawat']['language']        = 'kurs_lang';
$config['module']['pesawat']['table']           = 'tb_master_pesawat';
$config['module']['pesawat']['model']           = 'Pesawat_Model';
$config['module']['pesawat']['permission']      = array(
  'create'  => 'PPC,MECHANIC,ADMIN,SUPERVISOR,SUPER ADMIN',
  'edit'    => 'PPC,MECHANIC,ADMIN,SUPERVISOR,SUPER ADMIN',
  'info'    => 'PPC,MECHANIC,ADMIN,SUPERVISOR,SUPER ADMIN',
  'save'    => 'PPC,MECHANIC,ADMIN,SUPERVISOR,SUPER ADMIN',
  'delete'  => 'PPC,MECHANIC,ADMIN,SUPERVISOR,SUPER ADMIN',
  'index'  	=> 'PPC,MECHANIC,ADMIN,SUPERVISOR,SUPER ADMIN',
  'create_component' => 'SUPER ADMIN,PPC,MECHANIC',  
  'create_component_status' => 'SUPER ADMIN,PPC,MECHANIC'
  );
