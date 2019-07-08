<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['pesawat']['visible']         = TRUE;
$config['module']['pesawat']['main_warehouse']  = TRUE;
$config['module']['pesawat']['parent']          = 'master';
$config['module']['pesawat']['label']           = 'Aircraft';
$config['module']['pesawat']['name']            = 'pesawat';
$config['module']['pesawat']['route']           = 'pesawat';
$config['module']['pesawat']['view']            = config_item('module_path') .'pesawat/';
$config['module']['pesawat']['language']        = 'kurs_lang';
$config['module']['pesawat']['table']           = 'tb_master_pesawat';
$config['module']['pesawat']['model']           = 'Pesawat_Model';
$config['module']['pesawat']['permission']      = array(
  'create'  => 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'edit'    => 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'info'    => 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'save'    => 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'delete'  => 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'index'  	=> 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  );
