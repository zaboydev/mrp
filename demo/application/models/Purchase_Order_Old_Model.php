<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_Order_Old_Model extends MY_Model
{
  protected $connection;
  protected $datetime;
  protected $table;
  protected $category_id;
  protected $active_year;
  protected $active_month;

  public function __construct()
  {
    parent::__construct(config_item('module')['purchase_request']['table']);

    $this->datetime     = date('Y-m-d H:i:s');
    $this->connection   = $this->load->database('budgetcontrol', TRUE);
    $this->table        = config_item('module')['purchase_order']['table'];
    $this->category_id  = array(4,5,7,8,9);
    $this->active_year  = $this->find_active_year();
    $this->active_month = $this->find_active_month();
  }

  public function find_active_year()
  {
    $this->connection->where('setting_name', 'Active Year');
    $query = $this->connection->get('tb_settings');
    $row   = $query->row();

    return $row->setting_value;
  }

  public function find_active_month()
  {
    $this->connection->where('setting_name', 'Active Month');
    $query = $this->connection->get('tb_settings');
    $row   = $query->row();

    return $row->setting_value;
  }

  public function find_id($pr_number)
  {
    $this->connection->select('id');
    $this->connection->where('pr_number', $pr_number);

    $query = $this->connection->get('tb_stocks_purchase_requisitions');
    $row   = $query->row();

    return $row->id;
  }

  public function find_all()
  {
    $query = $this->db->get($this->table);

    return $query->result();
  }

  public function find_poe()
  {
    $this->db->select('poe.*');
    $this->db->from('tb_purchase_order_evaluations poe');
    // $this->db->where('poe.status', 'approved');
    $query = $this->db->get();

    return $query->result();
  }

  public function find_request_by_number($pr_number)
  {
    $this->connection->select('imb.id, iprd.part_number, iprd.additional_info, iprd.quantity, p.product_code, p.product_name');
    $this->connection->from('tb_stocks_purchase_requisition_details iprd');
    $this->connection->join('tb_stocks_purchase_requisitions ipr', 'ipr.id = iprd.stock_purchase_requisition_id');
    $this->connection->join('tb_stocks_monthly_budgets imb', 'imb.id = iprd.stock_monthly_budget_id');
    $this->connection->join('tb_products p', 'p.id = imb.product_id');
    $this->connection->where('ipr.pr_number', $pr_number);
    $query = $this->connection->get();

    return $query->result();
  }

  public function find_vendors()
  {
    // if (isset($_SESSION['po']['reference_poe'])){
    //     foreach ($_SESSION['poe']['vendor'] as $key => $value){
    //         $vendor_ids[] = $value['vendor_id'];
    //     }

    //     $this->db->where_not_in('v.id', $vendor_ids);
    // }

    $this->db->select('poev.*, v.address, v.phone, v.email');
    $this->db->from('tb_purchase_order_evaluation_vendors poev');
    $this->db->join('tb_master_vendors v', 'v.id = poev.vendor_id');

    $query = $this->db->get();

    return $query->result();
  }

  public function find_vendor_by_id($id)
  {
    $this->db->select('v.*');
    $this->db->from('tb_master_vendors v');
    $this->db->where('v.id', $id);

    $query = $this->db->get();

    return $query->row();
  }

  public function find_items_by_vendor($vendor_id = NULL)
  {
    if ($vendor_id === NULL)
      $vendor_id = $_SESSION['po']['vendor_id'];

    $this->db->select('poeriv.*, poeri.imb_id, poeri.item_name, poeri.item_code, poeri.item_part_number, poeri.item_alternate_part_number, poeri.item_quantity, poeri.notes');
    $this->db->from('tb_purchase_order_evaluation_request_item_vendor poeriv');
    $this->db->join('tb_purchase_order_evaluation_request_items poeri', 'poeri.id = poeriv.poe_request_item_id');
    $this->db->where('poeriv.vendor_id', $vendor_id);
    $this->db->where('poeriv.selected', TRUE);

    $query = $this->db->get();

    return $query->result();
  }

  public function find_budgets_by_ids($ids = NULL)
  {
    if ($ids === NULL){
      $ids   = array();
      $items = $_SESSION['request']['detail'];

      foreach ($items as $key => $value){
        $ids[] = $key;
      }
    }

    $this->connection->select('imb.*, p.product_name, p.product_code, p.part_number, pm.measurement_symbol, ppp.current_price');
    $this->connection->from('tb_stocks_monthly_budgets imb');
    $this->connection->join('tb_products p', 'p.id = imb.product_id');
    $this->connection->join('tb_product_measurements pm', 'pm.id = p.product_measurement_id');
    $this->connection->join('tb_product_purchase_prices ppp', 'ppp.product_id = p.id');
    $this->connection->where_in('imb.id', $ids);
    $query = $this->connection->get();

    return $query->result();
  }

  public function find_one_by_id($id)
  {
    $this->db->from('tb_purchase_orders po');
    $this->db->select('po.*');
    $this->db->where('po.id', $id);
    $query = $this->db->get();
    $entity = $query->row_array();

    $this->db->from('tb_purchase_order_items poi');
    $this->db->select('poi.*');
    $this->db->where('poi.po_id', $id);
    $query = $this->db->get();
    $entity['item'] = $query->result_array();

    return $entity;
  }

  public function find_ordered_request()
  {
    $this->db->from('tb_purchase_order_evaluation_requests poer');
    $this->db->select('poer.pr_id');

    $query = $this->db->get();
    $rows  = $query->result_array();

    $ids = null;

    foreach ($rows as $key => $value){
      $ids[] = $value['pr_id'];
    }

    return $ids;
  }

  public function find_last_number()
  {
    $this->db->select_max('poe_no');
    $query = $this->db->get(config_item('module')['purchase_order_evaluation']['table']);

    $row = $query->row();

    if (count($row) == 0)
      return 1;

    $poe_no = $row->poe_no;

    return $poe_no + 1;
  }

  public function save()
  {
    $entity = array(
      'po_number' => $this->input->post('po_number'),
      'reference_poe' => $this->input->post('reference_poe'),
      'reference_quotation' => $this->input->post('reference_quotation'),
      'vendor_id' => $this->input->post('vendor_id'),
      'vendor_name' => $this->input->post('vendor_name'),
      'vendor_address' => $this->input->post('vendor_address'),
      'bill_to' => $this->input->post('bill_to'),
      'delivery_to' => $this->input->post('delivery_to'),
      'notes' => $this->input->post('notes'),
     );

    $this->db->trans_begin();

    if ($_SESSION['po']['id'] == NULL){
      $entity['po_date']     = date('Y-m-d');
      $entity['created_at']   = $this->datetime;
      $entity['created_by']   = config_item('auth_person_name');

      $this->db->set($entity)
        ->insert(config_item('module')['purchase_order']['table']);

      $id = $this->db->insert_id('tb_purchase_orders_id_seq');
    } else {
      $id = $_SESSION['po']['id'];

      $this->db->set($entity)
        ->where('id', $id)
        ->update(config_item('module')['purchase_order']['table']);
    }

    // delete old details
    $this->db->where('po_id', $id);
    $this->db->delete('tb_purchase_order_items');

    foreach ($_SESSION['po']['item'] as $i => $item){
      $item_entity['po_id']       = $id;
      $item_entity['imb_id']      = $item['imb_id'];
      $item_entity['item_name']   = $item['item_name'];
      $item_entity['item_code']   = $item['item_code'];
      $item_entity['part_number'] = $item['part_number'];
      $item_entity['alternate_part_number'] = $item['alternate_part_number'];
      $item_entity['quantity'] = $item['quantity'];
      $item_entity['unit_price'] = $item['unit_price'];
      $item_entity['core_charge'] = $item['core_charge'];
      $item_entity['notes'] = $item['notes'];

      $this->db->set($item_entity)
        ->insert('tb_purchase_order_items');
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return TRUE;
  }
}
