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
  'create'  => 'PROCUREMENT,ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'edit'    => 'PROCUREMENT,ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'info'    => 'PROCUREMENT,ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'save'    => 'PROCUREMENT,ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'delete'  => 'PROCUREMENT,ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'index'  	=> 'PROCUREMENT,ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  );
