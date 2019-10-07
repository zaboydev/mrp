<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['ajax']['visible']    = FALSE;
$config['module']['ajax']['main_warehouse']  = FALSE;
$config['module']['ajax']['parent']     = 'dashboard';
$config['module']['ajax']['name']       = 'ajax';
$config['module']['ajax']['label']      = 'ajax';
$config['module']['ajax']['route']      = 'ajax';
$config['module']['ajax']['view']       = config_item('module_path') .'ajax/';
$config['module']['ajax']['language']   = 'ajax_lang';
$config['module']['ajax']['table']      = '';
$config['module']['ajax']['model']      = 'Ajax_Model';
$config['module']['ajax']['permission'] = array(
  'index' => 'ADMIN,PROCUREMENT,PIC PROCUREMENT,PIC STOCK,OTHER,SUPERVISOR,VP FINANCE,SUPER ADMIN,OPERATION SUPPORT',
  );
