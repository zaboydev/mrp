<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['kurs']['visible']         = TRUE;
$config['module']['kurs']['main_warehouse']  = TRUE;
$config['module']['kurs']['parent']          = 'master';
$config['module']['kurs']['label']           = 'Currency';
$config['module']['kurs']['name']            = 'kurs';
$config['module']['kurs']['route']           = 'kurs';
$config['module']['kurs']['view']            = config_item('module_path') .'kurs/';
$config['module']['kurs']['language']        = 'kurs_lang';
$config['module']['kurs']['table']           = 'tb_master_kurs_dollar';
$config['module']['kurs']['model']           = 'Kurs_Model';
$config['module']['kurs']['permission']      = array(
  'create'  => 'FINANCE,VP FINANCE,SUPER ADMIN',
  'edit'    => 'FINANCE,VP FINANCE,SUPER ADMIN',
  'info'    => 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'save'    => 'FINANCE,VP FINANCE,SUPER ADMIN',
  'delete'  => 'FINANCE,VP FINANCE,SUPER ADMIN',
  'index'  	=> 'ADMIN,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  );
