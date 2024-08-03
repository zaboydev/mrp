<?php defined('BASEPATH') or exit('No direct script access allowed');

/***************
| GENERAL
****************/
$config['access_control']['general'] = array(
  'change_password' => 'ADMIN,PIC PROCUREMENT,PIC STOCK,OTHER',
);


/***************
| MASTER
****************/
$config['access_control']['user'] = array(
  'index'     => 'ADMIN',
  'create'    => 'ADMIN',
  'import'    => 'ADMIN',
  'edit'      => 'ADMIN'
);

$config['access_control']['warehouse'] = array(
  'index'     => 'ADMIN,PIC PROCUREMENT,PIC STOCK',
  'create'    => 'ADMIN',
  'edit'      => 'ADMIN',
  'import'    => 'ADMIN',
  'delete'    => 'ADMIN',
);

$config['access_control']['stores'] = array(
  'index'     => 'ADMIN,PIC PROCUREMENT,PIC STOCK',
  'create'    => 'ADMIN,PIC STOCK',
  'edit'      => 'ADMIN,PIC STOCK',
  'import'    => 'ADMIN,PIC STOCK',
  'delete'    => 'PIC STOCK',
);

$config['access_control']['aircraft_type'] = array(
  'index'     => 'ADMIN,PIC PROCUREMENT,PIC STOCK',
  'create'    => 'ADMIN',
  'edit'      => 'ADMIN',
  'import'    => 'ADMIN',
);

$config['access_control']['aircraft'] = array(
  'index'     => 'ADMIN,PIC PROCUREMENT,PIC STOCK',
  'create'    => 'ADMIN',
  'edit'      => 'ADMIN',
  'import'    => 'ADMIN',
);

$config['access_control']['vendor'] = array(
  'index'     => 'ADMIN,PIC PROCUREMENT,PIC STOCK',
  'create'    => 'ADMIN,PIC PROCUREMENT,PIC STOCK',
  'edit'      => 'ADMIN,PIC PROCUREMENT,PIC STOCK',
  'import'    => 'ADMIN,PIC PROCUREMENT,PIC STOCK',
);

$config['access_control']['item_unit'] = array(
  'index'     => 'PIC STOCK,PIC PROCUREMENT',
  'create'    => 'PIC STOCK',
  'edit'      => 'PIC STOCK',
  'import'    => 'PIC STOCK',
);

$config['access_control']['group'] = array(
  'index'     => 'PIC STOCK,PIC PROCUREMENT',
  'create'    => 'PIC STOCK',
  'edit'      => 'PIC STOCK',
  'import'    => 'PIC STOCK',
);

$config['access_control']['item'] = array(
  'index'     => 'PIC STOCK,PIC PROCUREMENT',
  'create'    => 'PIC STOCK',
  'edit'      => 'PIC STOCK',
  'show'      => 'PIC STOCK',
  'import'    => 'PIC STOCK',
  'price'     => 'PIC PROCUREMENT',
);


/***************
| SETTINGS
****************/
$config['access_control']['setting'] = array(
  'warehouse' => 'ADMIN',
);


/***************
| INVENTORIES
****************/
$config['access_control']['stock'] = array(
  'index'         => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'in_stores'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'low_stock'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'in_use'        => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'on_delivery'   => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'on_shipping'   => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'on_return'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
);

$config['access_control']['stock'] = array(
  'index'         => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'stock_general' => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'low_stock'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'import'        => 'PIC STOCK',
  'adjustment'    => 'PIC STOCK',
  'delete'        => 'PIC STOCK',
  'show'          => 'PIC STOCK',
);

$config['access_control']['item_in_use'] = array(
  'index'         => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'import'        => 'PIC STOCK',
  'adjustment'    => 'PIC STOCK',
  'show'          => 'PIC STOCK',
);

$config['access_control']['item_on_delivery'] = array(
  'index'         => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'import'        => 'PIC STOCK',
  'adjustment'    => 'PIC STOCK',
  'show'          => 'PIC STOCK',
);

$config['access_control']['item_on_shipping'] = array(
  'index'         => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'import'        => 'PIC STOCK',
  'receiving'     => 'PIC STOCK',
  'adjustment'    => 'PIC STOCK',
  'show'          => 'PIC STOCK',
);

$config['access_control']['item_on_return'] = array(
  'index'         => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'import'        => 'PIC STOCK',
  'adjustment'    => 'PIC STOCK',
  'show'          => 'PIC STOCK',
);


/***************
| DOCUMENTS
****************/
$config['access_control']['stock_general'] = array(
  'index'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
);

$config['access_control']['doc_receipt'] = array(
  'index'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'create'    => 'PIC STOCK',
  'import'    => 'PIC STOCK',
  'edit'      => 'PIC STOCK',
  'show'      => 'PIC STOCK',
);

$config['access_control']['doc_usage'] = array(
  'index'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'create'    => 'PIC STOCK',
  'import'    => 'PIC STOCK',
  'edit'      => 'PIC STOCK',
  'show'      => 'PIC STOCK',
);

$config['access_control']['doc_delivery'] = array(
  'index'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'create'    => 'PIC STOCK',
  'import'    => 'PIC STOCK',
  'edit'      => 'PIC STOCK',
  'show'      => 'PIC STOCK',
);

$config['access_control']['doc_shipment'] = array(
  'index'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'create'    => 'PIC STOCK',
  'import'    => 'PIC STOCK',
  'edit'      => 'PIC STOCK',
  'show'      => 'PIC STOCK',
  'receiving' => 'PIC STOCK',
);

$config['access_control']['doc_return'] = array(
  'index'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'create'    => 'PIC STOCK',
  'import'    => 'PIC STOCK',
  'edit'      => 'PIC STOCK',
  'show'      => 'PIC STOCK',
);

$config['access_control']['purchase_request'] = array(
  'index'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'create'    => 'PIC STOCK',
  'import'    => 'PIC STOCK',
  'edit'      => 'PIC STOCK',
  'show'      => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'budget'    => 'PIC STOCK',
);

$config['access_control']['budget'] = array(
  'index'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'relocation'=> 'PIC STOCK',
  'import'    => 'PIC STOCK',
  'edit'      => 'PIC STOCK',
  'show'      => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
);

$config['access_control']['purchase_order_evaluation'] = array(
  'index'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'create'    => 'PIC PROCUREMENT',
  'import'    => 'PIC PROCUREMENT',
  'edit'      => 'PIC PROCUREMENT',
  'show'      => 'PIC PROCUREMENT',
);

$config['access_control']['purchase_order'] = array(
  'index'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'create'    => 'PIC PROCUREMENT',
  'import'    => 'PIC PROCUREMENT',
  'edit'      => 'PIC PROCUREMENT',
  'show'      => 'PIC PROCUREMENT',
);
$config['access_control']['budget'] = array(
  'index'     => 'PIC STOCK,OTHER,VP FINANCE,PIC PROCUREMENT',
  'create'    => 'PIC PROCUREMENT',
  'import'    => 'PIC PROCUREMENT',
  'edit'      => 'PIC PROCUREMENT',
  'show'      => 'PIC PROCUREMENT',
);
