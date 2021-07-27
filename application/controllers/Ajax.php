<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends MY_Controller
{
  protected $module;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['ajax'];
    $this->load->model($this->module['model'], 'model');

    if ($this->input->is_ajax_request() === FALSE)
      redirect($this->module['secure'] .'/denied');
  }

  public function part_number_validation()
  {
    if (array_key_exists('value', $_POST)){
      if (array_key_exists('exception', $_POST)){
        $exception = $this->input->post('exception');
      } else {
        $exception = NULL;
      }

      if ($this->model->part_number_validation($this->input->post('value'), $exception) == TRUE){
        echo json_encode(TRUE);
      } else {
        echo json_encode(FALSE);
      }
    }
  }

  public function item_category_list()
  {
    $entities = $this->model->findAllItemCategories('AVAILABLE');

    echo json_encode($entities);
  }

  public function item_category_validation()
  {
    if (array_key_exists('value', $_POST)){
      if (array_key_exists('exception', $_POST)){
        $exception = $this->input->post('exception');
      } else {
        $exception = NULL;
      }

      if ($this->model->item_category_validation($this->input->post('value'), $exception) == TRUE){
        echo json_encode(TRUE);
      } else {
        echo json_encode(FALSE);
      }
    }
  }

  public function item_category_code_validation()
  {
    if (array_key_exists('value', $_POST)){
      if (array_key_exists('exception', $_POST)){
        $exception = $this->input->post('exception');
      } else {
        $exception = NULL;
      }

      if ($this->model->item_category_code_validation($this->input->post('value'), $exception) == TRUE){
        echo json_encode(TRUE);
      } else {
        echo json_encode(FALSE);
      }
    }
  }

  public function item_application_description_validation()
  {
    if (array_key_exists('value', $_POST)){
      if (array_key_exists('exception', $_POST)){
        $exception = $this->input->post('exception');
      } else {
        $exception = NULL;
      }

      if ($this->model->item_application_description_validation($this->input->post('value'), $exception) == TRUE){
        echo json_encode(TRUE);
      } else {
        echo json_encode(FALSE);
      }
    }
  }

  public function item_group_list()
  {
    $entities = $this->model->findAllItemGroups('AVAILABLE');

    echo json_encode($entities);
  }

  public function item_group_validation()
  {
    if (array_key_exists('value', $_POST)){
      if (array_key_exists('exception', $_POST)){
        $exception = $this->input->post('exception');
      } else {
        $exception = NULL;
      }

      if ($this->model->item_group_validation($this->input->post('value'), $exception) == TRUE){
        echo json_encode(TRUE);
      } else {
        echo json_encode(FALSE);
      }
    }
  }

  public function item_group_code_validation()
  {
    if (array_key_exists('value', $_POST)){
      if (array_key_exists('exception', $_POST)){
        $exception = $this->input->post('exception');
      } else {
        $exception = NULL;
      }

      if ($this->model->item_group_code_validation($this->input->post('value'), $exception) == TRUE){
        echo json_encode(TRUE);
      } else {
        echo json_encode(FALSE);
      }
    }
  }

  public function search_items($category = NULL)
  {
    if ($category === NULL){
      $category = config_item('auth_inventory');
    } else {
      $category = (array)urldecode($category);
    }

    $entities = $this->model->listItems($category);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= 'PN: '. $value['part_number'] .' || ';
      $entities[$key]['label'] .= 'Alt. PN: '. $value['alternate_part_number'] .' || ';
      $entities[$key]['label'] .= 'SN: '. $value['serial_number'];
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }

  public function json_search_stock_general($category = NULL)
  {
    if ($category === NULL)
      $category = config_item('auth_inventory');
    else
      $category = (array)urldecode($category);

    $entities = $this->model->listGeneralStock($category);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= 'PN: '. $value['part_number'] .' || ';
      $entities[$key]['label'] .= 'Alt. PN: '. $value['alternate_part_number'] .' || ';
      $entities[$key]['label'] .= 'SN: '. $value['serial_number'] .' || ';
      $entities[$key]['label'] .= 'On Hand Quantity: <code>'. number_format($value['on_hand_quantity']) .'</code>';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }

  public function get_available_stock($warehouse, $category = NULL)
  {
    if ($category === NULL)
      $category = config_item('auth_inventory');
    else
      $category = (array)urldecode($category);

    $entities = $this->model->getAvailableStock(urldecode($warehouse), $category);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['part_number'];
      $entities[$key]['label'] .= ' || ';
      $entities[$key]['label'] .= $value['condition'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= ($value['serial_number'] !== "") ? "SN: ". $value['serial_number'] ." || " : "";
      $entities[$key]['label'] .= 'Stores: '. $value['stores'] .' || ';
      $entities[$key]['label'] .= 'Received date: '. date('d/m/Y', strtotime($value['received_date'])) .' || ';
      $entities[$key]['label'] .= 'Available Quantity: <code>'. number_format($value['quantity']) .'</code>';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }

  public function json_search_item_on_delivery($warehouse, $category = NULL)
  {
    if ($category === NULL)
      $category = config_item('auth_inventory');
    else
      $category = (array)urldecode($category);

    $entities = $this->model->listItemOnDeliveries(urldecode($warehouse), $category);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= ' || PN: ';
      $entities[$key]['label'] .= $value['part_number'];
      $entities[$key]['label'] .= ' || ';
      $entities[$key]['label'] .= config_item('condition')[$value['condition']];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= ($value['serial_number'] !== "") ? "SN: ". $value['serial_number'] ." || " : "";
      $entities[$key]['label'] .= 'Document Number: '. $value['document_number'] .' || ';
      $entities[$key]['label'] .= 'Received date: '. nice_date($value['date_of_entry'], 'd/m/Y') .' || ';
      $entities[$key]['label'] .= 'Received Quantity: <code>'. number_format($value['quantity']) .'</code>';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }

  public function json_search_item_in_use($category = NULL)
  {
    if ($category === NULL)
      $category = config_item('auth_inventory');
    else
      $category = (array)urldecode($category);

    $entities = $this->model->listItemInUses($category);

    foreach ($entities as $key => $value){
      $entities[$key]['label'] = $value['description'];
      $entities[$key]['label'] .= '<small>';
      $entities[$key]['label'] .= 'PN: '. $value['part_number'] .' || ';
      $entities[$key]['label'] .= 'MS No.: '. $value['document_number'] .' || ';
      $entities[$key]['label'] .= 'Quantity in used: <code>'. number_format($value['available_quantity']) .'</code>';
      $entities[$key]['label'] .= '</small>';
    }

    echo json_encode($entities);
  }

  public function json_item_description($category = NULL)
  {
    if ($category === NULL)
      $category = config_item('auth_inventory');
    else
      $category = (array)urldecode($category);

    $entities = $this->model->listItemDescription($category);

    echo $entities;
  }

  public function json_part_number($category = NULL)
  {
    if ($category === NULL)
      $category = config_item('auth_inventory');
    else
      $category = (array)urldecode($category);

    $entities = $this->model->listItemPartNumber($category);

    echo $entities;
  }

  public function json_alternate_part_number($category = NULL)
  {
    if ($category === NULL)
      $category = config_item('auth_inventory');
    else
      $category = (array)urldecode($category);

    $entities = $this->model->listItemAltPartNumber($category);

    echo $entities;
  }

  public function search_serial_number($category = NULL)
  {
    if ($category === NULL)
      $category = config_item('auth_inventory');
    else
      $category = (array)urldecode($category);

    $entities = $this->model->listItemSerialNumber($category);

    echo $entities;
  }

  public function json_stores($category = NULL, $warehouse = NULL)
  {
    if ($warehouse === NULL){
      if (isset($_SESSION['receipt']['warehouse'])){
    	  $warehouse = $_SESSION['receipt']['warehouse'];
    	} else {
    	  $warehouse = config_item('auth_warehouse');
    	}
    }

    if ($category === NULL)
      $category = config_item('auth_inventory');
    else
      $category = (array)urldecode($category);

    $entities = $this->model->listStores($category, $warehouse);

    echo $entities;
  }

  public function search_item_units()
  {
    $entities = $this->model->listItemUnits();

    echo $entities;
  }

  public function find_item_by_part_number()
  {
    $entities = $this->model->findItemByPartNumber($_POST['part_number']);

    echo $entities;
  }

  public function find_item_by_serial_number()
  {
    $entities = $this->model->findItemBySerialNumber($_POST['serial_number']);

    echo $entities;
  }

  public function stores_list()
  {
    $entities = $this->model->findAllStores('AVAILABLE');

    echo json_encode($entities);
  }

  public function stores_validation()
  {
    if (array_key_exists('value', $_POST)){
      if (array_key_exists('exception', $_POST)){
        $exception = $this->input->post('exception');
      } else {
        $exception = NULL;
      }

      if ($this->model->stores_validation($this->input->post('value'), $exception) == TRUE){
        echo json_encode(TRUE);
      } else {
        echo json_encode(FALSE);
      }
    }
  }

  public function item_unit_list()
  {
    $entities = $this->model->findAllUnitOfMeasurements('AVAILABLE');

    echo json_encode($entities);
  }

  public function unit_validation()
  {
    if (array_key_exists('value', $_POST)){
      if (array_key_exists('exception', $_POST)){
        $exception = $this->input->post('exception');
      } else {
        $exception = NULL;
      }

      if ($this->model->unit_validation($this->input->post('value'), $exception) == TRUE){
        echo json_encode(TRUE);
      } else {
        echo json_encode(FALSE);
      }
    }
  }

  public function user_email_validation()
  {
    if (array_key_exists('value', $_POST)){
      if (array_key_exists('exception', $_POST)){
        $exception = $this->input->post('exception');
      } else {
        $exception = NULL;
      }

      if ($this->model->user_email_validation($this->input->post('value'), $exception) == TRUE){
        echo json_encode(TRUE);
      } else {
        echo json_encode(FALSE);
      }
    }
  }

  public function username_validation()
  {
    if (array_key_exists('value', $_POST)){
      if (array_key_exists('exception', $_POST)){
        $exception = $this->input->post('exception');
      } else {
        $exception = NULL;
      }

      if ($this->model->username_validation($this->input->post('value'), $exception) == TRUE){
        echo json_encode(TRUE);
      } else {
        echo json_encode(FALSE);
      }
    }
  }

  public function vendor_list()
  {
    $entities = $this->model->findAllVendors('AVAILABLE');

    echo json_encode($entities);
  }

  public function vendor_validation()
  {
    if (array_key_exists('value', $_POST)){
      if (array_key_exists('exception', $_POST)){
        $exception = $this->input->post('exception');
      } else {
        $exception = NULL;
      }

      if ($this->model->vendor_validation($this->input->post('value'), $exception) == TRUE){
        echo json_encode(TRUE);
      } else {
        echo json_encode(FALSE);
      }
    }
  }

  public function warehouse_list()
  {
    $entities = $this->model->findAllWarehouses('AVAILABLE');

    echo json_encode($entities);
  }

  public function warehouse_validation()
  {
    if (array_key_exists('value', $_POST)){
      if (array_key_exists('exception', $_POST)){
        $exception = $this->input->post('exception');
      } else {
        $exception = NULL;
      }

      if ($this->model->warehouse_validation($this->input->post('value'), $exception) == TRUE){
        echo json_encode(TRUE);
      } else {
        echo json_encode(FALSE);
      }
    }
  }

  public function department_name_validation()
  {
    if (array_key_exists('value', $_POST)){
      if (array_key_exists('exception', $_POST)){
        $exception = $this->input->post('exception');
      } else {
        $exception = NULL;
      }

      if ($this->model->unit_validation($this->input->post('value'), $exception) == TRUE){
        echo json_encode(TRUE);
      } else {
        echo json_encode(FALSE);
      }
    }
  }
}
