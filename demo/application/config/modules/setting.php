<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['setting']['visible']     = TRUE;
$config['module']['setting']['main_warehouse']   = TRUE;
$config['module']['setting']['parent']      = 'setting';
$config['module']['setting']['label']       = 'Settings';
$config['module']['setting']['name']        = 'setting';
$config['module']['setting']['route']       = 'setting';
$config['module']['setting']['view']        = config_item('module_path') .'setting/';
$config['module']['setting']['language']    = 'setting_lang';
$config['module']['setting']['table']       = 'tb_settings';
$config['module']['setting']['model']       = 'Setting_Model';
$config['module']['setting']['permission']  = array(
  'index'     => 'ADMIN,PIC PROCUREMENT,PIC STOCK,OTHER',
  'warehouse' => 'ADMIN',
  );
