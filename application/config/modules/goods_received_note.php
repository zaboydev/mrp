<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['goods_received_note']['visible']          = TRUE;
$config['module']['goods_received_note']['main_warehouse']   = TRUE;
$config['module']['goods_received_note']['parent']           = 'document';
$config['module']['goods_received_note']['label']            = 'Goods Received Note';
$config['module']['goods_received_note']['name']             = 'goods_received_note';
$config['module']['goods_received_note']['route']            = 'goods_received_note';
$config['module']['goods_received_note']['view']             = config_item('module_path') .'goods_received_note/';
$config['module']['goods_received_note']['language']         = 'goods_received_note_lang';
$config['module']['goods_received_note']['helper']           = 'goods_received_note_helper';
$config['module']['goods_received_note']['table']            = 'tb_receipts';
$config['module']['goods_received_note']['model']            = 'Goods_Received_Note_Model';
$config['module']['goods_received_note']['permission']       = array(
  'index'     => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'info'      => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  //penambahan PIC PROCUREMENT
  'document'  => 'PIC STOCK,SUPERVISOR,SUPER ADMIN',
  'print'     => 'PIC STOCK,SUPERVISOR,VP FINANCE,FINANCE,PIC PROCUREMENT,SUPER ADMIN',
  'delete'    => 'SUPERVISOR,SUPER ADMIN',
  'import'	  => 'SUPERVISOR,SUPER ADMIN'
);
