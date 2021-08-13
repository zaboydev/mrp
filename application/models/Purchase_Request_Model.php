<?php defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_Request_Model extends MY_Model
{
  protected $connection;
  protected $categories;
  protected $budget_year;
  protected $budget_month;

  public function __construct()
  {
    parent::__construct();

    $this->connection   = $this->load->database('budgetcontrol', TRUE);
    $this->categories   = $this->getCategories();
    $this->budget_year  = find_budget_setting('Active Year');
    $this->budget_month = find_budget_setting('Active Month');
  }

  public function getSelectedColumns()
  {
    if ($_SESSION['request']['request_to'] == 0) {
      $return =  array(
        'tb_inventory_purchase_requisitions.id'                       => NULL,
        'tb_inventory_purchase_requisitions.pr_number'                => 'Document Number',
        'tb_inventory_purchase_requisitions.pr_date'                  => 'Document Date',
        'tb_inventory_purchase_requisitions.required_date'            => 'Required Date',
        'tb_products.product_name'                                    => 'Description',
        'tb_products.product_code'                                    => 'Part Number',
        'tb_products.part_number as min_qty'                          => 'Min. Qty',
        null                                    => 'On Hand. Qty',
        'tb_inventory_purchase_requisition_details.quantity'          => 'Quantity Request',
        '(tb_inventory_purchase_requisition_details.quantity - tb_inventory_purchase_requisition_details.sisa) as process_qty'          => 'Quantity POE',
        'tb_inventory_purchase_requisitions.status'                   => 'Status',
        'tb_inventory_purchase_requisitions.suggested_supplier'       => 'Suggested Supplier',
        'tb_inventory_purchase_requisitions.deliver_to'               => 'Deliver To',
        'tb_inventory_purchase_requisitions.created_by'               => 'Request By',
        'tb_inventory_purchase_requisition_details.notes'                    => 'Notes',
      );
      if (config_item('auth_role') == 'CHIEF OF MAINTANCE' || config_item('auth_role') == 'FINANCE MANAGER') {
        $return['tb_inventory_purchase_requisition_details.price']  = 'Price';
        $return['tb_inventory_purchase_requisition_details.total']  = 'Total';
      }
    } else {
      $return =  array(
        'tb_inventory_purchase_requisition_details.id'                       => NULL,
        'tb_inventory_purchase_requisitions.pr_number'                => 'Document Number',
        'tb_inventory_purchase_requisitions.pr_date'                  => 'Document Date',
        'tb_inventory_purchase_requisitions.required_date'            => 'Required Date',
        'tb_inventory_purchase_requisitions.item_category'            => 'Category',
        'tb_inventory_purchase_requisition_details.product_name'      => 'Description',
        'tb_inventory_purchase_requisition_details.part_number as product_code'    => 'Part Number',
        'tb_inventory_purchase_requisition_details.serial_number'     => 'Serial Number',
        // 'tb_inventory_purchase_requisition_details.additional_info'   => 'Additional Info',
        'tb_master_items.minimum_quantity as min_qty'                  => 'Min. Qty',
        'tb_inventory_purchase_requisitions.notes as pr_notes'         => 'On Hand. Qty',
        'tb_inventory_purchase_requisition_details.quantity'          => 'Quantity Request',
        '(tb_inventory_purchase_requisition_details.quantity - tb_inventory_purchase_requisition_details.sisa) as process_qty'          => 'Quantity POE',
        'tb_inventory_purchase_requisition_details.status'                   => 'Status',
        'tb_inventory_purchase_requisition_details.budget_status'                   => 'Budget Status',
        // 'tb_inventory_purchase_requisitions.suggested_supplier'       => 'Suggested Supplier',
        // 'tb_inventory_purchase_requisitions.deliver_to'               => 'Deliver To',
        'tb_inventory_purchase_requisitions.created_by'               => 'Request By',
        'tb_inventory_purchase_requisition_details.notes'                    => 'Notes',
      );
      if (config_item('auth_role') == 'PROCUREMENT' || config_item('auth_role') == 'CHIEF OF MAINTANCE' || config_item('auth_role') == 'SUPER ADMIN' || config_item('auth_role') == 'FINANCE MANAGER') {
        $return['tb_inventory_purchase_requisitions.approved_notes']  = 'Note';
      }
      if (config_item('auth_role') == 'CHIEF OF MAINTANCE' || config_item('auth_role') == 'FINANCE MANAGER') {
        
        $return['tb_inventory_purchase_requisition_details.price']  = 'Price';
        $return['tb_inventory_purchase_requisition_details.total']  = 'Total';
      }
    }

    return $return;
  }

  public function getSearchableColumns()
  {
    if ($_SESSION['request']['request_to'] == 0) {
      return array(
        'tb_inventory_purchase_requisitions.pr_number',
        // 'tb_product_categories.category_name',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_inventory_purchase_requisition_details.additional_info',
        'tb_inventory_purchase_requisitions.status',
        'tb_inventory_purchase_requisitions.suggested_supplier',
        'tb_inventory_purchase_requisitions.deliver_to',
        'tb_inventory_purchase_requisitions.created_by',
        'tb_inventory_purchase_requisition_details.notes',
      );
    } else {
      return array(
        'tb_inventory_purchase_requisitions.pr_number',
        'tb_inventory_purchase_requisitions.item_category',
        'tb_inventory_purchase_requisition_details.product_name',
        'tb_inventory_purchase_requisition_details.part_number',
        'tb_inventory_purchase_requisition_details.serial_number',
        'tb_inventory_purchase_requisition_details.budget_status',
        'tb_inventory_purchase_requisitions.created_by',
        'tb_inventory_purchase_requisition_details.notes',
      );
    }
  }

  public function getOrderableColumns()
  {
    if ($_SESSION['request']['request_to'] == 0) {
      return array(
        null,
        'tb_inventory_purchase_requisitions.pr_number',
        'tb_inventory_purchase_requisitions.pr_date',
        'tb_inventory_purchase_requisitions.required_date',
        // 'tb_product_categories.category_name',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_inventory_purchase_requisition_details.additional_info',
        'tb_inventory_purchase_requisition_details.quantity',
        '(tb_inventory_purchase_requisition_details.quantity - tb_inventory_purchase_requisition_details.sisa)',
        'tb_inventory_purchase_requisitions.status',
        'tb_inventory_purchase_requisitions.suggested_supplier',
        'tb_inventory_purchase_requisitions.deliver_to',
        'tb_inventory_purchase_requisitions.created_by',
        'tb_inventory_purchase_requisition_details.notes',
      );
    } else {
      return array(
        null,
        'tb_inventory_purchase_requisitions.pr_number',
        'tb_inventory_purchase_requisitions.pr_date',
        'tb_inventory_purchase_requisitions.required_date',
        'tb_inventory_purchase_requisitions.item_category',
        'tb_inventory_purchase_requisition_details.product_name',
        'tb_inventory_purchase_requisition_details.part_number',
        'tb_inventory_purchase_requisition_details.serial_number',
        'tb_inventory_purchase_requisition_details.additional_info',
        'tb_inventory_purchase_requisition_details.quantity',
        '(tb_inventory_purchase_requisition_details.quantity - tb_inventory_purchase_requisition_details.sisa)',
        'tb_inventory_purchase_requisition_details.status',
        'tb_inventory_purchase_requisitions.suggested_supplier',
        'tb_inventory_purchase_requisitions.deliver_to',
        'tb_inventory_purchase_requisitions.created_by',
        'tb_inventory_purchase_requisition_details.notes',
      );
    }
  }

  private function searchIndex()
  {
    $db = $_SESSION['request']['request_to'] == 0 ? $this->connection : $this->db;
    if (!empty($_POST['columns'][3]['search']['value'])) {
      $search_required_date = $_POST['columns'][3]['search']['value'];
      $range_date  = explode(' ', $search_required_date);

      $db->where('tb_inventory_purchase_requisitions.required_date >= ', $range_date[0]);
      $db->where('tb_inventory_purchase_requisitions.required_date <= ', $range_date[1]);
    }

    if (!empty($_POST['columns'][4]['search']['value'])) {
      $search_status = $_POST['columns'][4]['search']['value'];
      if ($search_status != 'all') {
        $db->where_in('tb_inventory_purchase_requisition_details.status', $search_status);
        // $db->where_in('tb_inventory_purchase_requisition_details.status', ['waiting','pending','close','open','rejected','pending']);
      }
      // else{
      //   $db->where_in('tb_inventory_purchase_requisition_details.status', $search_status);
      // }
    } else {
      if (config_item('auth_role') == 'FINANCE MANAGER') {
        $db->where('tb_inventory_purchase_requisition_details.status', 'pending');
      } elseif (config_item('auth_role') == 'OPERATION SUPPORT') {
        $db->where('tb_inventory_purchase_requisition_details.status', 'review operation support');
      } elseif (config_item('auth_role') == 'CHIEF OF MAINTANCE') {
        $db->where('tb_inventory_purchase_requisition_details.status', 'waiting');
      } else {
        //  $db->where('tb_inventory_purchase_requisition_details.status', 'waiting');
      }
    }

    if (!empty($_POST['columns'][8]['search']['value'])) {
      $search_category = $_POST['columns'][8]['search']['value'];
      if ($_SESSION['request']['request_to'] == 0) {
        $db->where('UPPER(tb_product_categories.category_name)', strtoupper($search_category));
      } else {
        $db->where('UPPER(tb_inventory_purchase_requisitions.item_category)', strtoupper($search_category));
      }
    }

    $i = 0;

    foreach ($this->getSearchableColumns() as $item) {
      if($_SESSION['request']['request_to']==0){
        if ($_POST['search']['value']) {
          $term = strtoupper($_POST['search']['value']);

          if ($i === 0) {
            $this->connection->group_start();
            $this->connection->like('UPPER(' . $item . ')', $term);
          } else {
            $this->connection->or_like('UPPER(' . $item . ')', $term);
          }

          if (count($this->getSearchableColumns()) - 1 == $i)
            $this->connection->group_end();
        }

        $i++;
      }else{
        if ($_POST['search']['value']) {
          $term = strtoupper($_POST['search']['value']);

          if ($i === 0) {
            $this->db->group_start();
            $this->db->like('UPPER(' . $item . ')', $term);
          } else {
            $this->db->or_like('UPPER(' . $item . ')', $term);
          }

          if (count($this->getSearchableColumns()) - 1 == $i)
            $this->db->group_end();
        }

        $i++;
      }
      
    }
  }

  function getCategories()
  {
    $categories = array();
    $category   = array();

    foreach (config_item('auth_inventory') as $inventory) {
      $category[] = strtoupper($inventory);
    }

    $this->connection->select('id');
    $this->connection->from('tb_product_categories');
    $this->connection->where_in('UPPER(category_name)', $category);

    $query  = $this->connection->get();

    foreach ($query->result_array() as $key => $value) {
      $categories[] = $value['id'];
    }

    return $categories;
  }

  function getIndex($return = 'array')
  {
    if ($_SESSION['request']['request_to'] == 0) {
      $this->connection->select(array_keys($this->getSelectedColumns()));
      $this->connection->from('tb_inventory_purchase_requisitions');
      $this->connection->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id', 'LEFT');
      $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
      $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
      // $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
      // $this->connection->where_in('tb_product_categories.id', $this->categories);
      // $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);

      $this->searchIndex();

      $column_order = $this->getOrderableColumns();

      if (isset($_POST['order'])) {
        foreach ($_POST['order'] as $key => $order) {
          $this->connection->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
        }
      } else {
        $this->connection->order_by('id', 'desc');
      }

      if ($_POST['length'] != -1)
        $this->connection->limit($_POST['length'], $_POST['start']);
      $query = $this->connection->get();
    } else {
      $this->db->select(array_keys($this->getSelectedColumns()));
      $this->db->from('tb_inventory_purchase_requisitions');
      $this->db->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id', 'LEFT');
      $this->db->join('tb_budget', 'tb_budget.id = tb_inventory_purchase_requisition_details.budget_id', 'left');
      $this->db->join('tb_budget_cot', 'tb_budget.id_cot = tb_budget_cot.id', 'left');
      $this->db->join('tb_master_items', 'tb_master_items.id = tb_budget_cot.id_item', 'left');
      $categories = config_item('auth_inventory');
      // $this->db->where_in('tb_inventory_purchase_requisitions.item_category', $categories);
      // $this->db->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);

      $this->searchIndex();

      $column_order = $this->getOrderableColumns();

      if (isset($_POST['order'])) {
        foreach ($_POST['order'] as $key => $order) {
          $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
        }
      } else {
        $this->db->order_by('id', 'desc');
      }

      if ($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
      $query = $this->db->get();
    }

    if ($return === 'object') {
      return $query->result();
    } elseif ($return === 'json') {
      return json_encode($query->result());
    } else {
      return $query->result_array();
    }
  }

  function countIndexFiltered()
  {
    if ($_SESSION['request']['request_to'] == 1) {
      $this->db->select(array_keys($this->getSelectedColumns()));
      $this->db->from('tb_inventory_purchase_requisitions');
      $this->db->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id', 'LEFT');
      $this->db->join('tb_budget', 'tb_budget.id = tb_inventory_purchase_requisition_details.budget_id', 'left');
      $this->db->join('tb_budget_cot', 'tb_budget.id_cot = tb_budget_cot.id', 'left');
      $this->db->join('tb_master_items', 'tb_master_items.id = tb_budget_cot.id_item', 'left');
      $categories = config_item('auth_inventory');
      // $this->db->where_in('tb_inventory_purchase_requisitions.item_category', $categories);
      // $this->db->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);

      $this->searchIndex();

      $query = $this->db->get();

      return $query->num_rows();
    } else {
      $this->connection->from('tb_inventory_purchase_requisitions');
      $this->connection->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id');
      $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
      $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
      $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
      $this->connection->where_in('tb_product_categories.id', $this->categories);
      // $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);

      $this->searchIndex();

      $query = $this->connection->get();

      return $query->num_rows();
    }
  }

  public function countIndex()
  {
    if ($_SESSION['request']['request_to'] == 1) {
      $this->db->select(array_keys($this->getSelectedColumns()));
      $this->db->from('tb_inventory_purchase_requisitions');
      $this->db->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id', 'LEFT');
      $this->db->join('tb_budget', 'tb_budget.id = tb_inventory_purchase_requisition_details.budget_id', 'left');
      $this->db->join('tb_budget_cot', 'tb_budget.id_cot = tb_budget_cot.id', 'left');
      $this->db->join('tb_master_items', 'tb_master_items.id = tb_budget_cot.id_item', 'left');
      $categories = config_item('auth_inventory');
      // $this->db->where_in('tb_inventory_purchase_requisitions.item_category', $categories);
      // $this->db->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);

      $this->searchIndex();

      $query = $this->db->get();

      return $query->num_rows();
    } else {
      $this->connection->from('tb_inventory_purchase_requisitions');
      $this->connection->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id');
      $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
      $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
      $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
      $this->connection->where_in('tb_product_categories.id', $this->categories);
      // $this->connection->like('tb_inventory_purchase_requisitions.pr_number', $this->budget_year);

      // $this->searchIndex();

      $query = $this->connection->get();

      return $query->num_rows();
    }
  }

  public function findById($id)
  {
    if ($_SESSION['request']['request_to'] == 0) {
      $this->connection->select('tb_inventory_purchase_requisitions.*, tb_product_categories.category_name');
      $this->connection->from('tb_inventory_purchase_requisitions');
      $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
      $this->connection->where('tb_inventory_purchase_requisitions.id', $id);

      $query    = $this->connection->get();
      $request  = $query->unbuffered_row('array');

      $select = array(
        'tb_inventory_purchase_requisition_details.*',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_inventory_monthly_budgets.product_id',
        'SUM(tb_inventory_monthly_budgets.mtd_quantity) AS fyp_quantity',
        'SUM(tb_inventory_monthly_budgets.mtd_budget) AS fyp_budget',
        'SUM(tb_inventory_monthly_budgets.mtd_used_quantity) AS fyp_used_quantity',
        'SUM(tb_inventory_monthly_budgets.mtd_used_budget) AS fyp_used_budget',
      );

      $group_by = array(
        'tb_inventory_purchase_requisition_details.id',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_inventory_monthly_budgets.product_id',
      );

      $this->connection->select($select);
      $this->connection->from('tb_inventory_purchase_requisition_details');
      $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
      $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
      $this->connection->where('tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id', $id);
      $this->connection->group_by($group_by);

      $query = $this->connection->get();

      foreach ($query->result_array() as $key => $value) {
        $request['items'][$key] = $value;

        $this->connection->from('tb_inventory_monthly_budgets');
        $this->connection->where('tb_inventory_monthly_budgets.product_id', $value['product_id']);
        $this->connection->where('tb_inventory_monthly_budgets.month_number', $this->budget_month);
        $this->connection->where('tb_inventory_monthly_budgets.year_number', $this->budget_year);

        $query = $this->connection->get();
        $row   = $query->unbuffered_row('array');

        $request['items'][$key]['mtd_quantity'] = $row['mtd_quantity'];
        $request['items'][$key]['mtd_budget'] = $row['mtd_budget'];
        $request['items'][$key]['mtd_used_quantity'] = $row['mtd_used_quantity'];
        $request['items'][$key]['mtd_used_budget'] = $row['mtd_used_budget'];
        $request['items'][$key]['ytd_quantity'] = $row['ytd_quantity'];
        $request['items'][$key]['ytd_budget'] = $row['ytd_budget'];
        $request['items'][$key]['ytd_used_quantity'] = $row['ytd_used_quantity'];
        $request['items'][$key]['ytd_used_budget'] = $row['ytd_used_budget'];
      }
    } else {

      $this->db->select('tb_inventory_purchase_requisition_details.*');
      $this->db->from('tb_inventory_purchase_requisition_details');
      $this->db->where('tb_inventory_purchase_requisition_details.id', $id);

      $query_detail    = $this->db->get();
      $request_detail  = $query_detail->unbuffered_row();

      $this->db->select('tb_inventory_purchase_requisitions.*');
      $this->db->from('tb_inventory_purchase_requisitions');
      $this->db->where('tb_inventory_purchase_requisitions.id', $request_detail->inventory_purchase_requisition_id);

      $query    = $this->db->get();
      $request  = $query->unbuffered_row('array');

      $select = array(
        'tb_inventory_purchase_requisition_details.*',
        // 'tb_master_items.description',
        // 'tb_master_items.part_number',
        'tb_budget.id_cot',
        'SUM(tb_budget.mtd_quantity) AS fyp_quantity',
        'SUM(tb_budget.mtd_budget) AS fyp_budget',
        'SUM(tb_budget.mtd_used_quantity) AS fyp_used_quantity',
        'SUM(tb_budget.mtd_used_budget) AS fyp_used_budget',
        'tb_master_items.minimum_quantity',
      );

      $group_by = array(
        'tb_inventory_purchase_requisition_details.id',
        // 'tb_master_items.description',
        'tb_master_items.minimum_quantity',
        // 'tb_master_items.part_number',
        'tb_budget.id_cot',
      );

      $this->db->select($select);
      $this->db->from('tb_inventory_purchase_requisition_details');
      $this->db->join('tb_budget', 'tb_budget.id = tb_inventory_purchase_requisition_details.budget_id', 'left');
      $this->db->join('tb_budget_cot', 'tb_budget_cot.id = tb_budget.id_cot', 'left');
      $this->db->join('tb_master_items', 'tb_master_items.id = tb_budget_cot.id_item', 'left');
      $this->db->where('tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id', $request_detail->inventory_purchase_requisition_id);
      $this->db->group_by($group_by);

      $query = $this->db->get();

      foreach ($query->result_array() as $key => $value) {
        $request['items'][$key] = $value;
        $request['items'][$key]['min_qty'] = search_min_qty($value['part_number']);

        $this->db->from('tb_budget');
        $this->db->where('tb_budget.id_cot', $value['id_cot']);
        $this->db->where('tb_budget.id', $value['budget_id']);
        // $this->db->where('tb_budget.month_number','<', $value['budget_id']);

        $query = $this->db->get();
        $row   = $query->unbuffered_row('array');

        $request['items'][$key]['mtd_quantity'] = $row['mtd_quantity'];
        $request['items'][$key]['mtd_budget'] = $row['mtd_budget'];
        $request['items'][$key]['mtd_used_quantity'] = $row['mtd_used_quantity'];
        $request['items'][$key]['mtd_used_budget'] = $row['mtd_used_budget'];
        $request['items'][$key]['ytd_quantity'] = $row['ytd_quantity'];
        $request['items'][$key]['ytd_budget'] = $row['ytd_budget'];
        $request['items'][$key]['ytd_used_quantity'] = $row['ytd_used_quantity'];
        $request['items'][$key]['ytd_used_budget'] = $row['ytd_used_budget'];
        $request['items'][$key]['on_hand_qty'] = $this->tb_on_hand_stock($value['id'])->sum;

        $request['items'][$key]['history']          = $this->getHistory($value['id_cot'],$request['order_number']);
      }
    }


    return $request;
  }

  public function getHistory($id_cot,$order_number)
  {

    // if ($_SESSION['request']['request_to'] == 1){
        $select = array(
          'tb_inventory_purchase_requisitions.pr_number',
          'tb_inventory_purchase_requisitions.pr_date',
          'tb_inventory_purchase_requisitions.created_by',
          'tb_inventory_purchase_requisition_details.id',
          'tb_inventory_purchase_requisition_details.quantity',
          'tb_inventory_purchase_requisition_details.unit',
          'tb_inventory_purchase_requisition_details.price',
          'tb_inventory_purchase_requisition_details.total',
          'sum(case when tb_purchase_order_items.quantity is null then 0.00 else tb_purchase_order_items.quantity end) as "poe_qty"',  
          'sum(case when tb_purchase_order_items.total_amount is null then 0.00 else tb_purchase_order_items.total_amount end) as "poe_value"',  
          'sum(case when tb_po_item.quantity is null then 0.00 else tb_po_item.quantity end) as "po_qty"',  
          'sum(case when tb_po_item.total_amount is null then 0.00 else tb_po_item.total_amount end) as "po_value"',
          'sum(case when tb_receipt_items.received_quantity is null then 0.00 else tb_receipt_items.received_quantity end) as "grn_qty"',  
          'sum(case when tb_receipt_items.received_total_value is null then 0.00 else tb_receipt_items.received_total_value end) as "grn_value"',       
        );

        $group = array(
          'tb_inventory_purchase_requisitions.pr_number',
          'tb_inventory_purchase_requisitions.pr_date',
          'tb_inventory_purchase_requisitions.created_by',
          'tb_inventory_purchase_requisition_details.id',
          'tb_inventory_purchase_requisition_details.quantity',
          'tb_inventory_purchase_requisition_details.unit',
          'tb_inventory_purchase_requisition_details.price',
          'tb_inventory_purchase_requisition_details.total',
        );

        $this->db->select($select);
        $this->db->from('tb_inventory_purchase_requisition_details');
        $this->db->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
        $this->db->join('tb_budget', 'tb_budget.id = tb_inventory_purchase_requisition_details.budget_id', 'left');
        $this->db->join('tb_purchase_order_items', 'tb_inventory_purchase_requisition_details.id = tb_purchase_order_items.inventory_purchase_request_detail_id','left');
        $this->db->join('tb_po_item', 'tb_po_item.poe_item_id = tb_purchase_order_items.id','left');
        $this->db->join('tb_receipt_items', 'tb_receipt_items.purchase_order_item_id = tb_po_item.id','left');
        $this->db->where('tb_budget.id_cot', $id_cot);
        $this->db->where('tb_inventory_purchase_requisitions.order_number <',$order_number);
        $this->db->group_by($group);
        $query  = $this->db->get();
        $return = $query->result_array();

        return $return;
    // }
        
  }

  public function findPrlById($id)
  {
    if ($_SESSION['request']['request_to'] == 0) {
      $this->connection->select('tb_inventory_purchase_requisitions.*, tb_product_categories.category_name');
      $this->connection->from('tb_inventory_purchase_requisitions');
      $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_inventory_purchase_requisitions.product_category_id');
      $this->connection->where('tb_inventory_purchase_requisitions.id', $id);

      $query    = $this->connection->get();
      $request  = $query->unbuffered_row('array');

      $select = array(
        'tb_inventory_purchase_requisition_details.*',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_inventory_monthly_budgets.product_id',
        'SUM(tb_inventory_monthly_budgets.mtd_quantity) AS fyp_quantity',
        'SUM(tb_inventory_monthly_budgets.mtd_budget) AS fyp_budget',
        'SUM(tb_inventory_monthly_budgets.mtd_used_quantity) AS fyp_used_quantity',
        'SUM(tb_inventory_monthly_budgets.mtd_used_budget) AS fyp_used_budget',
      );

      $group_by = array(
        'tb_inventory_purchase_requisition_details.id',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_inventory_monthly_budgets.product_id',
      );

      $this->connection->select($select);
      $this->connection->from('tb_inventory_purchase_requisition_details');
      $this->connection->join('tb_inventory_monthly_budgets', 'tb_inventory_monthly_budgets.id = tb_inventory_purchase_requisition_details.inventory_monthly_budget_id');
      $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
      $this->connection->where('tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id', $id);
      $this->connection->group_by($group_by);

      $query = $this->connection->get();

      foreach ($query->result_array() as $key => $value) {
        $request['items'][$key] = $value;

        $this->connection->from('tb_inventory_monthly_budgets');
        $this->connection->where('tb_inventory_monthly_budgets.product_id', $value['product_id']);
        $this->connection->where('tb_inventory_monthly_budgets.month_number', $this->budget_month);
        $this->connection->where('tb_inventory_monthly_budgets.year_number', $this->budget_year);

        $query = $this->connection->get();
        $row   = $query->unbuffered_row('array');

        $request['items'][$key]['mtd_quantity'] = $row['mtd_quantity'];
        $request['items'][$key]['mtd_budget'] = $row['mtd_budget'];
        $request['items'][$key]['mtd_used_quantity'] = $row['mtd_used_quantity'];
        $request['items'][$key]['mtd_used_budget'] = $row['mtd_used_budget'];
        $request['items'][$key]['ytd_quantity'] = $row['ytd_quantity'];
        $request['items'][$key]['ytd_budget'] = $row['ytd_budget'];
        $request['items'][$key]['ytd_used_quantity'] = $row['ytd_used_quantity'];
        $request['items'][$key]['ytd_used_budget'] = $row['ytd_used_budget'];
      }
    } else {

      $this->db->select('tb_inventory_purchase_requisitions.*');
      $this->db->from('tb_inventory_purchase_requisitions');
      $this->db->where('tb_inventory_purchase_requisitions.id', $id);

      $query    = $this->db->get();
      $request  = $query->unbuffered_row('array');

      $select = array(
        'tb_inventory_purchase_requisition_details.*',
        'tb_inventory_purchase_requisition_details.product_name',
        'tb_inventory_purchase_requisition_details.part_number',
        'tb_master_items.minimum_quantity',
        'tb_budget.id_cot',
        'SUM(tb_budget.mtd_quantity) AS fyp_quantity',
        'SUM(tb_budget.mtd_budget) AS fyp_budget',
        'SUM(tb_budget.mtd_used_quantity) AS fyp_used_quantity',
        'SUM(tb_budget.mtd_used_budget) AS fyp_used_budget',
      );

      $group_by = array(
        'tb_inventory_purchase_requisition_details.id',
        // 'tb_master_items.description',
        // 'tb_master_items.part_number',
        'tb_budget.id_cot',
        'tb_master_items.minimum_quantity',
      );

      $this->db->select($select);
      $this->db->from('tb_inventory_purchase_requisition_details');
      $this->db->join('tb_budget', 'tb_budget.id = tb_inventory_purchase_requisition_details.budget_id','left');
      $this->db->join('tb_budget_cot', 'tb_budget_cot.id = tb_budget.id_cot', 'left');
      $this->db->join('tb_master_items', 'tb_master_items.id = tb_budget_cot.id_item', 'left');
      $this->db->where('tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id', $id);
      $this->db->group_by($group_by);

      $query = $this->db->get();

      foreach ($query->result_array() as $key => $value) {
        $request['items'][$key] = $value;
        $request['items'][$key]['min_qty'] = search_min_qty($value['part_number']);

        $this->db->from('tb_budget');
        $this->db->where('tb_budget.id_cot', $value['id_cot']);
        // $this->db->where('tb_budget.month_number', $this->budget_month);
        $this->db->where('tb_budget.id', $value['budget_id']);

        $query = $this->db->get();
        $row   = $query->unbuffered_row('array');

        $request['items'][$key]['mtd_quantity'] = $row['mtd_quantity'];
        $request['items'][$key]['mtd_budget'] = $row['mtd_budget'];
        $request['items'][$key]['mtd_used_quantity'] = $row['mtd_used_quantity'];
        $request['items'][$key]['mtd_used_budget'] = $row['mtd_used_budget'];
        $request['items'][$key]['ytd_quantity'] = $row['ytd_quantity'];
        $request['items'][$key]['ytd_budget'] = $row['ytd_budget'];
        $request['items'][$key]['ytd_used_quantity'] = $row['ytd_used_quantity'];
        $request['items'][$key]['ytd_used_budget'] = $row['ytd_used_budget'];
        $request['items'][$key]['on_hand_qty'] = $this->tb_on_hand_stock($value['id'])->sum;
        $request['items'][$key]['info_on_hand_qty'] = $this->info_on_hand($value['id']);
        $request['items'][$key]['history']          = $this->getHistory($value['id_cot'],$request['order_number']);
        // $request['items'][$key]['count_info_on_hand_qty'] = $this->info_on_hand($value['id'])->num_rows();
      }
    }


    return $request;
  }

  public function find_item_by_id($id)
  {
    if ($_SESSION['request']['request_to'] == 1) {

      $this->db->select('tb_inventory_purchase_requisition_details.*, tb_inventory_purchase_requisitions.pr_number, tb_inventory_purchase_requisitions.created_by');
      $this->db->from('tb_inventory_purchase_requisition_details');
      $this->db->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
      $this->db->where('tb_inventory_purchase_requisition_details.id', $id);

      $query_detail    = $this->db->get();
      $request_detail  = $query_detail->unbuffered_row('array');
    }


    return $request_detail;
  }

  public function isDocumentNumberExists($pr_number)
  {
    $this->connection->where('pr_number', $pr_number);
    $query = $this->connection->get('tb_inventory_purchase_requisitions');

    if ($query->num_rows() > 0)
      return true;

    return false;
  }

  public function isValidDocumentQuantity($pr_number)
  {
    $this->connection->select_sum('tb_receipt_items.received_quantity', 'received_quantity');
    $this->connection->select_sum('tb_stock_in_stores.quantity', 'stored_quantity');
    $this->connection->select('tb_receipt_items.pr_number');
    $this->connection->from('tb_receipt_items');
    $this->connection->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->connection->where('tb_receipt_items.pr_number', $pr_number);
    $this->connection->group_by('tb_receipt_items.pr_number');

    $query  = $this->connection->get();
    $row    = $query->unbuffered_row('array');

    if ($row['received_quantity'] === $row['received_quantity'])
      return true;

    return false;
  }

  public function save_change_item()
  {
    $this->db->trans_begin();

    $this->db->set('description', strtoupper($this->input->post('description')));
    $this->db->set('part_number', strtoupper($this->input->post('part_number')));
    $this->db->where('id', $this->input->post('id'));
    $this->db->update('tb_inventory_purchase_requisition_details');

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();

    return TRUE;
  }

  public function save()
  {
    $document_id          = (isset($_SESSION['request']['id'])) ? $_SESSION['request']['id'] : NULL;
    $document_edit        = (isset($_SESSION['request']['edit'])) ? $_SESSION['request']['edit'] : NULL;
    $order_number         = $_SESSION['request']['order_number'];
    $pr_number            = $_SESSION['request']['pr_number'];
    $pr_date              = $_SESSION['request']['pr_date'];
    $required_date        = $_SESSION['request']['required_date'];
    $deliver_to           = (empty($_SESSION['request']['deliver_to'])) ? NULL : $_SESSION['request']['deliver_to'];
    $suggested_supplier   = (empty($_SESSION['request']['suggested_supplier'])) ? NULL : $_SESSION['request']['suggested_supplier'];
    $created_by           = (empty($_SESSION['request']['created_by'])) ? NULL : $_SESSION['request']['created_by'];
    $category             = find_product_category($_SESSION['request']['category']);
    $product_category_id  = $category['id'];
    $notes                = (empty($_SESSION['request']['notes'])) ? NULL : $_SESSION['request']['notes'];
    $unbudgeted           = 0;

    $this->connection->trans_begin();
    $this->db->trans_begin();


    $request_to = $_SESSION['request']['request_to'];
    if ($request_to == 0) {
      if ($document_id === NULL) {
        $this->connection->set('product_category_id', $product_category_id);
        $this->connection->set('order_number', $order_number);
        $this->connection->set('pr_number', $pr_number);
        $this->connection->set('pr_date', $pr_date);
        $this->connection->set('required_date', $required_date);
        $this->connection->set('suggested_supplier', $suggested_supplier);
        $this->connection->set('deliver_to', $deliver_to);
        $this->connection->set('status', 'pending');
        $this->connection->set('notes', $notes);
        $this->connection->set('created_by', $created_by);
        $this->connection->set('updated_by', config_item('auth_person_name'));
        $this->connection->set('created_at', date('Y-m-d H:i:s'));
        $this->connection->set('updated_at', date('Y-m-d H:i:s'));
        $this->connection->insert('tb_inventory_purchase_requisitions');

        $document_id = $this->connection->insert_id();
      } else {
        $this->connection->set('required_date', $required_date);
        $this->connection->set('suggested_supplier', $suggested_supplier);
        $this->connection->set('deliver_to', $deliver_to);
        $this->connection->set('status', 'pending');
        $this->connection->set('notes', $notes);
        $this->connection->set('updated_at', date('Y-m-d'));
        $this->connection->set('updated_by', config_item('auth_person_name'));
        $this->connection->where('id', $document_id);
        $this->connection->update('tb_inventory_purchase_requisitions');

        $this->connection->where('inventory_purchase_requisition_id', $document_id);
        $this->connection->delete('tb_inventory_purchase_requisition_details');
      }
      // request from budget control
      foreach ($_SESSION['request']['items'] as $key => $data) {
        if (empty($data['inventory_monthly_budget_id']) || $data['inventory_monthly_budget_id'] == NULL) {
          // NEW GROUP
          $this->connection->select('tb_product_groups.id');
          $this->connection->from('tb_product_groups');
          $this->connection->where('UPPER(tb_product_groups.group_name)', strtoupper($data['group_name']));

          $query  = $this->connection->get();

          if ($query->num_rows() == 0) {
            $this->connection->set('product_category_id', $product_category_id);
            $this->connection->set('group_name', $data['group_name']);
            $this->connection->set('group_code', strtoupper($data['group_name']));
            $this->connection->set('group_type', 'inventory');
            $this->connection->set('created_at', date('Y-m-d'));
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->set('updated_at', date('Y-m-d'));
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->insert('tb_product_groups');

            $product_group_id = $this->connection->insert_id();
          } else {
            $product_group    = $query->unbuffered_row();
            $product_group_id = $product_group->id;
          }

          // NEW UNIT
          $this->connection->select('tb_product_measurements.id');
          $this->connection->from('tb_product_measurements');
          $this->connection->where('UPPER(tb_product_measurements.measurement_symbol)', strtoupper($data['unit']));

          $query  = $this->connection->get();

          if ($query->num_rows() == 0) {
            $this->connection->set('measurement_name', $data['unit']);
            $this->connection->set('measurement_symbol', strtolower($data['unit']));
            $this->connection->set('created_at', date('Y-m-d'));
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->set('updated_at', date('Y-m-d'));
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->insert('tb_product_measurements');

            $product_measurement_id = $this->connection->insert_id();
          } else {
            $product_measurement    = $query->unbuffered_row();
            $product_measurement_id = $product_measurement->id;
          }

          // NEW PRODUCT
          $this->connection->select('tb_products.id');
          $this->connection->from('tb_products');
          $this->connection->where('UPPER(tb_products.product_code)', strtoupper($data['part_number']));

          $query  = $this->connection->get();

          if ($query->num_rows() == 0) {
            $this->connection->set('product_measurement_id', $product_measurement_id);
            $this->connection->set('product_group_id', $product_group_id);
            $this->connection->set('product_name', $data['product_name']);
            $this->connection->set('product_code', $data['part_number']);
            $this->connection->set('part_number', $data['part_number']);
            $this->connection->set('product_type', 'inventory');
            $this->connection->set('unit_measurement', $data['unit']);
            $this->connection->set('price', $data['price']);
            $this->connection->set('additional_info', $data['additional_info']);
            $this->connection->set('created_at', date('Y-m-d'));
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->set('updated_at', date('Y-m-d'));
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->insert('tb_products');

            $product_id = $this->connection->insert_id();
          } else {
            $product    = $query->unbuffered_row();
            $product_id = $product->id;
          }

          // NEW PRICE
          $this->connection->from('tb_product_purchase_prices');
          $this->connection->where('tb_product_purchase_prices.product_id', $product_id);

          $query  = $this->connection->get();

          if ($query->num_rows() == 0) {
            $this->connection->set('price_before', 0);
            $this->connection->set('current_price', $data['price']);
            $this->connection->set('cur_date', date('Y-m-d'));
            $this->connection->set('created_at', date('Y-m-d'));
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->set('updated_at', date('Y-m-d'));
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->insert('tb_product_purchase_prices');

            $purchase_price_id = $this->connection->insert_id();
          } else {
            $purchase_price    = $query->unbuffered_row();
            $purchase_price_id = $purchase_price->id;

            $this->connection->set('price_before', $purchase_price->current_price);
            $this->connection->set('current_price', $data['price']);
            $this->connection->set('cur_date', date('Y-m-d'));
            $this->connection->set('updated_at', date('Y-m-d'));
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->where('id', $purchase_price_id);
            $this->connection->update('tb_product_purchase_prices');
          }

          // NEW BUDGET
          $this->connection->from('tb_inventory_monthly_budgets');
          $this->connection->where('tb_inventory_monthly_budgets.product_id', $product_id);
          $this->connection->where('tb_inventory_monthly_budgets.month_number', $this->budget_month);
          $this->connection->where('tb_inventory_monthly_budgets.year_number', $this->budget_year);

          $query  = $this->connection->get();

          if ($query->num_rows() == 0) {
            $this->connection->set('product_id', $product_id);
            $this->connection->set('month_number', $this->budget_month);
            $this->connection->set('year_number', $this->budget_year);
            $this->connection->set('initial_quantity', floatval(0));
            $this->connection->set('initial_budget', floatval(0));
            $this->connection->set('mtd_quantity', floatval(0));
            $this->connection->set('mtd_budget', floatval(0));
            $this->connection->set('mtd_used_quantity', floatval(0));
            $this->connection->set('mtd_used_budget', floatval(0));
            $this->connection->set('mtd_used_quantity_import', floatval(0));
            $this->connection->set('mtd_used_budget_import', floatval(0));
            $this->connection->set('mtd_prev_month_quantity', floatval(0));
            $this->connection->set('mtd_prev_month_budget', floatval(0));
            $this->connection->set('mtd_prev_month_used_quantity', floatval(0));
            $this->connection->set('mtd_prev_month_used_budget', floatval(0));
            $this->connection->set('mtd_prev_month_used_quantity_import', floatval(0));
            $this->connection->set('mtd_prev_month_used_budget_import', floatval(0));
            $this->connection->set('ytd_quantity', floatval(0));
            $this->connection->set('ytd_budget', floatval(0));
            $this->connection->set('ytd_used_quantity', floatval(0));
            $this->connection->set('ytd_used_budget', floatval(0));
            $this->connection->set('ytd_used_quantity_import', floatval(0));
            $this->connection->set('ytd_used_budget_import', floatval(0));
            $this->connection->set('created_at', date('Y-m-d'));
            $this->connection->set('created_by', config_item('auth_person_name'));
            $this->connection->set('updated_at', date('Y-m-d'));
            $this->connection->set('updated_by', config_item('auth_person_name'));
            $this->connection->insert('tb_inventory_monthly_budgets');

            $inventory_monthly_budget_id = $this->connection->insert_id();

            for ($m = 1; $m < $this->budget_month; $m++) {
              // PREV BUDGET
              $this->connection->set('product_id', $product_id);
              $this->connection->set('month_number', $m);
              $this->connection->set('year_number', $this->budget_year);
              $this->connection->set('initial_quantity', floatval(0));
              $this->connection->set('initial_budget', floatval(0));
              $this->connection->set('mtd_quantity', floatval(0));
              $this->connection->set('mtd_budget', floatval(0));
              $this->connection->set('mtd_used_quantity', floatval(0));
              $this->connection->set('mtd_used_budget', floatval(0));
              $this->connection->set('mtd_used_quantity_import', floatval(0));
              $this->connection->set('mtd_used_budget_import', floatval(0));
              $this->connection->set('mtd_prev_month_quantity', floatval(0));
              $this->connection->set('mtd_prev_month_budget', floatval(0));
              $this->connection->set('mtd_prev_month_used_quantity', floatval(0));
              $this->connection->set('mtd_prev_month_used_budget', floatval(0));
              $this->connection->set('mtd_prev_month_used_quantity_import', floatval(0));
              $this->connection->set('mtd_prev_month_used_budget_import', floatval(0));
              $this->connection->set('ytd_quantity', floatval(0));
              $this->connection->set('ytd_budget', floatval(0));
              $this->connection->set('ytd_used_quantity', floatval(0));
              $this->connection->set('ytd_used_budget', floatval(0));
              $this->connection->set('ytd_used_quantity_import', floatval(0));
              $this->connection->set('ytd_used_budget_import', floatval(0));
              $this->connection->set('created_at', date('Y-m-d'));
              $this->connection->set('created_by', config_item('auth_person_name'));
              $this->connection->set('updated_at', date('Y-m-d'));
              $this->connection->set('updated_by', config_item('auth_person_name'));
              $this->connection->insert('tb_inventory_monthly_budgets');
            }

            for ($am = 12; $am > $this->budget_month; $am--) {
              // PREV BUDGET
              $this->connection->set('product_id', $product_id);
              $this->connection->set('month_number', $am);
              $this->connection->set('year_number', $this->budget_year);
              $this->connection->set('initial_quantity', floatval(0));
              $this->connection->set('initial_budget', floatval(0));
              $this->connection->set('mtd_quantity', floatval(0));
              $this->connection->set('mtd_budget', floatval(0));
              $this->connection->set('mtd_used_quantity', floatval(0));
              $this->connection->set('mtd_used_budget', floatval(0));
              $this->connection->set('mtd_used_quantity_import', floatval(0));
              $this->connection->set('mtd_used_budget_import', floatval(0));
              $this->connection->set('mtd_prev_month_quantity', floatval(0));
              $this->connection->set('mtd_prev_month_budget', floatval(0));
              $this->connection->set('mtd_prev_month_used_quantity', floatval(0));
              $this->connection->set('mtd_prev_month_used_budget', floatval(0));
              $this->connection->set('mtd_prev_month_used_quantity_import', floatval(0));
              $this->connection->set('mtd_prev_month_used_budget_import', floatval(0));
              $this->connection->set('ytd_quantity', floatval(0));
              $this->connection->set('ytd_budget', floatval(0));
              $this->connection->set('ytd_used_quantity', floatval(0));
              $this->connection->set('ytd_used_budget', floatval(0));
              $this->connection->set('ytd_used_quantity_import', floatval(0));
              $this->connection->set('ytd_used_budget_import', floatval(0));
              $this->connection->set('created_at', date('Y-m-d'));
              $this->connection->set('created_by', config_item('auth_person_name'));
              $this->connection->set('updated_at', date('Y-m-d'));
              $this->connection->set('updated_by', config_item('auth_person_name'));
              $this->connection->insert('tb_inventory_monthly_budgets');
            }
          } else {
            $inventory_monthly_budget    = $query->unbuffered_row();
            $inventory_monthly_budget_id = $inventory_monthly_budget->id;
          }
        } else {
          $inventory_monthly_budget_id = $data['inventory_monthly_budget_id'];
          $this->connection->set('status', 'approved');
          $this->connection->where('id', $document_id);
          $this->connection->update('tb_inventory_purchase_requisitions');

          // old budget 
          $this->connection->where('id', $inventory_monthly_budget_id);
          $temp = $this->connection->get('tb_inventory_monthly_budgets')->row();
          $year = $this->budget_year;
          $month = $this->budget_month - 1;
          $ytd_used_budget = 0;
          $ytd_used_quantity = 0;
          if ($month > 0) {
            $this->connection->where('product_id', $temp->product_id);
            $this->connection->where('year_number', $year);
            $this->connection->where('month_number', $month);
            $old = $this->connection->get('tb_inventory_monthly_budgets')->row();
            $ytd_used_quantity = $old->ytd_used_quantity;
            $ytd_used_budget = $old->ytd_used_budget;
          }
          $this->connection->where('product_id', $temp->product_id);
          $this->connection->where('year_number', $year);
          $this->connection->where('month_number', $month);

          //insert data on used budget 
          $this->connection->set('inventory_monthly_budget_id', $inventory_monthly_budget_id);
          $this->connection->set('inventory_purchase_requisition_id', $document_id);
          $this->connection->set('pr_number', $data['pr_number']);
          $this->connection->set('year_number', $this->budget_year);
          $this->connection->set('month_number', $this->budget_month);
          $this->connection->set('product_name', $data['product_name']);
          $this->connection->set('product_group', $data['group_name']);
          $this->connection->set('product_code', $data['part_number']);
          $this->connection->set('additional_info', $data['additional_info']);
          $this->connection->set('used_budget', $data['total']);
          $this->connection->set('used_quantity', $data['quantity']);
          $this->connection->set('created_at', date('Y-m-d H:i:s'));
          $this->connection->set('created_by', config_item('auth_person_name'));
          $this->connection->set('part_number', $data['part_number']);
          $this->connection->insert('tb_inventory_used_budgets');

          $this->connection->select('sum(used_quantity) as qty ,sum(used_budget) as value');
          $this->connection->from('tb_inventory_used_budgets');
          $this->connection->where('inventory_monthly_budget_id', $inventory_monthly_budget_id);
          $used_budget = $this->connection->get()->row();
          $this->connection->set('ytd_used_quantity', ($used_budget->qty + $ytd_used_quantity));
          $this->connection->set('ytd_used_budget', ($used_budget->value + $ytd_used_budget));
          $this->connection->set('mtd_used_quantity', ($used_budget->qty));
          $this->connection->set('mtd_used_budget', ($used_budget->value));
          $this->connection->where('id', $inventory_monthly_budget_id);
          $this->connection->update('tb_inventory_monthly_budgets');
        }

        $this->connection->set('inventory_purchase_requisition_id', $document_id);
        $this->connection->set('inventory_monthly_budget_id', $inventory_monthly_budget_id);
        $this->connection->set('part_number', $data['part_number']);
        $this->connection->set('additional_info', $data['additional_info']);
        $this->connection->set('unit', $data['unit']);
        $this->connection->set('sort_order', floatval($key));
        $this->connection->set('quantity', floatval($data['quantity']));
        $this->connection->set('sisa', floatval($data['quantity']));
        $this->connection->set('price', floatval($data['price']));
        $this->connection->set('total', floatval($data['total']));
        $this->connection->insert('tb_inventory_purchase_requisition_details');
      }
    } else {
      $this->db->select('tb_inventory_purchase_requisitions.*');
      $this->db->from('tb_inventory_purchase_requisitions');
      $this->db->where('order_number', $order_number);
      $inventory = $this->db->get();
      if ($inventory->num_rows() == 0) {
        $this->db->set('item_category', $_SESSION['request']['category']);
        $this->db->set('order_number', $order_number);
        $this->db->set('pr_number', $pr_number);
        $this->db->set('pr_date', $pr_date);
        $this->db->set('required_date', $required_date);
        $this->db->set('suggested_supplier', $suggested_supplier);
        $this->db->set('deliver_to', $deliver_to);
        $this->db->set('status', 'waiting');
        $this->db->set('notes', $notes);
        $this->db->set('created_by', $created_by);
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->set('created_at', date('Y-m-d H:i:s'));
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->insert('tb_inventory_purchase_requisitions');

        $document_id = $this->db->insert_id();
      } else {
        $document_id = $inventory->row()->id;
        $this->db->set('required_date', $required_date);
        $this->db->set('suggested_supplier', $suggested_supplier);
        $this->db->set('deliver_to', $deliver_to);
        $this->db->set('status', 'waiting');
        $this->db->set('notes', $notes);
        $this->db->set('updated_at', date('Y-m-d'));
        $this->db->set('updated_by', config_item('auth_person_name'));
        $this->db->where('id', $document_id);
        $this->db->update('tb_inventory_purchase_requisitions');

        $this->db->where('inventory_purchase_requisition_id', $document_id);
        $this->db->delete('tb_inventory_purchase_requisition_details');
      }


      foreach ($_SESSION['request']['items'] as $key => $data) {
        if (isItemUnitExists($data['unit']) === FALSE) {
          $this->db->set('unit', strtoupper($data['unit']));
          $this->db->set('created_by', config_item('auth_person_name'));
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->insert('tb_master_item_units');
        }
        $serial_number = (empty($data['serial_number'])) ? NULL : $data['serial_number'];
        if (isItemExists($data['part_number'], $serial_number) === FALSE) {
          $this->db->set('part_number', strtoupper($data['part_number']));
          $this->db->set('serial_number', $serial_number);
          // $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
          $this->db->set('description', strtoupper($data['product_name']));
          $this->db->set('group', strtoupper($data['group_name']));
          $this->db->set('minimum_quantity', floatval(1));
          $this->db->set('unit', strtoupper($data['unit']));
          // $this->db->set('kode_stok', strtoupper($data['kode_stok']));
          $this->db->set('created_by', config_item('auth_person_name'));
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->set('unit_pakai', $data['unit']);
          $this->db->insert('tb_master_items');
          $item_id = $this->db->insert_id();
        } else {
          $item_id = getItemId($data['part_number'], $serial_number);
        }
        if (isPartNumberExists($data['part_number']) === FALSE) {
          $this->db->set('part_number', strtoupper($data['part_number']));
          $this->db->set('min_qty', floatval(1));
          $this->db->set('item_id', $item_id);        
          $this->db->set('qty', 0);
          $this->db->set('description', strtoupper($data['product_name']));
          $this->db->set('unit', strtoupper($data['unit']));
          $this->db->set('group', strtoupper($data['group_name']));
          // $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
          $this->db->set('current_price', floatval(1));
          $this->db->insert('tb_master_part_number');
        } else {
          $part_number_id = getParNumberId($data['part_number']);
          $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
          $this->db->where('id', $part_number_id);
          $this->db->update('tb_master_part_number');
        }
        if (empty($data['inventory_monthly_budget_id']) || $data['inventory_monthly_budget_id'] == NULL)
        {
          $unbudgeted++;
          //input ke tb_unbudgeted
          $this->db->set('year_number', date('Y'));
          $this->db->set('amount', floatval(0));
          $this->db->set('quantity', floatval(0));
          $this->db->set('previous_budget', floatval(0));
          $this->db->set('previous_quantity', floatval(0));
          $this->db->set('new_budget', floatval($data['total']));
          $this->db->set('new_quantity', floatval($data['quantity']));
          $this->db->set('notes', $data['additional_info']);
          $this->db->set('created_at', date('Y-m-d H:i:s'));
          $this->db->set('created_by', config_item('auth_person_name'));
          $this->db->insert('tb_inventory_unbudgeted');
          $budget_id_sementara = $this->db->insert_id();

          $this->db->set('inventory_purchase_requisition_id', $document_id);
          // $this->db->set('budget_id', $inventory_monthly_budget_id);
          $this->db->set('part_number', $data['part_number']);
          $this->db->set('serial_number', trim($serial_number));
          $this->db->set('product_name', $data['product_name']);
          $this->db->set('additional_info', $data['additional_info']);
          $this->db->set('unit', $data['unit']);
          $this->db->set('sort_order', floatval($key));
          $this->db->set('quantity', floatval($data['quantity']));
          $this->db->set('sisa', floatval($data['quantity']));
          $this->db->set('price', floatval($data['price']));
          $this->db->set('total', floatval($data['total']));
          $this->db->set('status', 'pending');
          $this->db->set('budget_status', 'unbudgeted');
          $this->db->set('budget_id_sementara', $budget_id_sementara);
          $this->db->set('reference_ipc', $data['reference_ipc']);
          $this->db->set('last_activity', 'purchase created');
          $this->db->insert('tb_inventory_purchase_requisition_details');
          $prl_item_id = $this->db->insert_id();


          // return TRUE;
        } else {
          $inventory_monthly_budget_id = $data['inventory_monthly_budget_id'];
          if (!empty($data['relocation_item']) || $data['relocation_item'] != NULL) {
            //input ke tb_relocation
            $this->db->set('origin_budget_id', $data['relocation_item']);
            $this->db->set('destination_budget_id', $inventory_monthly_budget_id);
            $this->db->set('amount', floatval($data['need_budget']));
            $this->db->set('year_number', date('Y'));
            $this->db->set('month_number', date('m'));
            $this->db->set('relocation_date_time', date('Y-m-d H:i:s'));
            $this->db->set('relocation_by', config_item('auth_person_name'));
            $this->db->insert('tb_inventory_relocation_budgets');
            $budget_id_sementara = $this->db->insert_id();

            $this->db->set('inventory_purchase_requisition_id', $document_id);
            $this->db->set('budget_id', $inventory_monthly_budget_id);
            $this->db->set('part_number', $data['part_number']);
            $this->db->set('serial_number', trim($serial_number));
            $this->db->set('product_name', $data['product_name']);
            $this->db->set('additional_info', $data['additional_info']);
            $this->db->set('unit', $data['unit']);
            $this->db->set('sort_order', floatval($key));
            $this->db->set('quantity', floatval($data['quantity']));
            $this->db->set('sisa', floatval($data['quantity']));
            $this->db->set('price', floatval($data['price']));
            $this->db->set('total', floatval($data['total']));
            $this->db->set('status', 'waiting');
            $this->db->set('budget_status', 'relocation');
            $this->db->set('budget_id_sementara', $budget_id_sementara);
            $this->db->set('reference_ipc', $data['reference_ipc']);
            $this->db->set('last_activity', 'purchase created');
            $this->db->insert('tb_inventory_purchase_requisition_details');
            $prl_item_id = $this->db->insert_id();

            $this->db->set('budget_id', $inventory_monthly_budget_id);
            $this->db->set('inventory_purchase_requisition_id', $document_id);
            $this->db->set('pr_number', $data['pr_number']);
            $this->db->set('year_number', date('Y'));
            $this->db->set('month_number', date('m'));
            $this->db->set('product_name', $data['product_name']);
            $this->db->set('product_group', $data['group_name']);
            $this->db->set('product_code', $data['part_number']);
            $this->db->set('additional_info', $data['additional_info']);
            $this->db->set('used_budget', $data['total']);
            $this->db->set('used_quantity', $data['quantity']);
            $this->db->set('created_at', date('Y-m-d H:i:s'));
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('part_number', $data['part_number']);
            $this->db->insert('tb_used_budgets');

            //update budget yang direlokasi (dipindahkan)
            $this->db->where('id', $data['relocation_item']);
            $oldBudgetrelocation = $this->db->get('tb_budget')->row();
            $budget_month = $oldBudgetrelocation->month_number;
            // for ($i=$this->budget_month; $i < 13 ; $i++) { 
            for ($i = $budget_month; $i < 13; $i++) {
              // $this->db->set('mtd_used_budget', 'mtd_used_budget + '.$data['total'],FALSE);
              // $this->db->set('mtd_used_quantity', 'mtd_used_quantity + '.$data['quantity'],FALSE);
              $this->db->set('ytd_used_budget', 'ytd_used_budget + ' . $data['need_budget'], FALSE);
              // $this->db->set('ytd_used_quantity', 'ytd_used_quantity + '.$data['quantity'],FALSE);
              $this->db->where('id_cot', $oldBudgetrelocation->id_cot);
              $this->db->where('month_number', $i);
              $this->db->update('tb_budget');
            }

            $this->db->set('mtd_used_budget', '"mtd_used_budget" + ' . $data['need_budget'], FALSE);
            // $this->db->set('mtd_used_quantity', 'mtd_used_quantity + '.$data['quantity'],FALSE);
            $this->db->where('id', $data['relocation_item']);
            $this->db->update('tb_budget');
            //update budget yang direlokasi (dipindahkan)

          } else {
            $budget_id = $data['budget_id'];


            $this->db->set('budget_id', $inventory_monthly_budget_id);
            $this->db->set('inventory_purchase_requisition_id', $document_id);
            $this->db->set('pr_number', $data['pr_number']);
            $this->db->set('year_number', date('Y'));
            $this->db->set('month_number', date('m'));
            $this->db->set('product_name', $data['product_name']);
            $this->db->set('product_group', $data['group_name']);
            $this->db->set('product_code', $data['part_number']);
            $this->db->set('additional_info', $data['additional_info']);
            $this->db->set('used_budget', $data['total']);
            $this->db->set('used_quantity', $data['quantity']);
            $this->db->set('created_at', date('Y-m-d H:i:s'));
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('part_number', $data['part_number']);
            $this->db->insert('tb_used_budgets');



            $this->db->set('inventory_purchase_requisition_id', $document_id);
            $this->db->set('budget_id', $inventory_monthly_budget_id);
            $this->db->set('part_number', strtoupper($data['part_number']));
            $this->db->set('serial_number', trim($serial_number));
            $this->db->set('product_name', strtoupper($data['product_name']));
            $this->db->set('additional_info', $data['additional_info']);
            $this->db->set('unit', $data['unit']);
            $this->db->set('sort_order', floatval($key));
            $this->db->set('quantity', floatval($data['quantity']));
            $this->db->set('sisa', floatval($data['quantity']));
            $this->db->set('price', floatval($data['price']));
            $this->db->set('total', floatval($data['total']));
            $this->db->set('status', 'waiting');
            $this->db->set('reference_ipc', $data['reference_ipc']);
            $this->db->set('last_activity', 'purchase created');
            $this->db->insert('tb_inventory_purchase_requisition_details');
            $prl_item_id = $this->db->insert_id();
          }

          //update budget tujuan relokasi (dipindahkan)
          $this->db->where('id', $inventory_monthly_budget_id);
          $oldBudget = $this->db->get('tb_budget')->row();
          $budget_month = date('m');
          // for ($i=$this->budget_month; $i < 13 ; $i++) { 
          for ($i = $budget_month; $i < 13; $i++) {
            // $this->db->set('mtd_used_budget', 'mtd_used_budget + '.$data['total'],FALSE);
            // $this->db->set('mtd_used_quantity', 'mtd_used_quantity + '.$data['quantity'],FALSE);
            $this->db->set('ytd_used_budget', 'ytd_used_budget + ' . $data['total'], FALSE);
            $this->db->set('ytd_used_quantity', 'ytd_used_quantity + ' . $data['quantity'], FALSE);
            $this->db->where('id_cot', $oldBudget->id_cot);
            $this->db->where('month_number', $i);
            $this->db->update('tb_budget');
          }

          $this->db->set('mtd_used_budget', '"mtd_used_budget" + ' . $data['total'], FALSE);
          $this->db->set('mtd_used_quantity', 'mtd_used_quantity + ' . $data['quantity'], FALSE);
          $this->db->where('id', $inventory_monthly_budget_id);
          $this->db->update('tb_budget');
          //update budget tujuan relokasi (dipindahkan)

        }

        $this->db->select(
          array(
            'tb_stock_in_stores.warehouse',
            'sum(quantity) as qty',
            'tb_master_items.unit'
          )
        );
        $this->db->from('tb_stock_in_stores');
        //tambahan
        $this->db->join('tb_stocks', 'tb_stocks.id=tb_stock_in_stores.stock_id');
        $this->db->join('tb_master_items', 'tb_master_items.id=tb_stocks.item_id');
        $this->db->group_by('tb_master_items.part_number,tb_stock_in_stores.warehouse,tb_master_items.unit');
        //tambahan
        $this->db->where('tb_master_items.part_number', $data['part_number']);
        $query =  $this->db->get();
        $result = $query->result_array();

        foreach ($result as $data) {
          $this->db->set('prl_item_id', $prl_item_id);
          $this->db->set('warehouse', $data['warehouse']);
          $this->db->set('on_hand_stock', $data['qty']);
          // $this->db->set('unit', $data['unit']);
          $this->db->insert('tb_purchase_request_items_on_hand_stock');
        }
      }
    }

    if (($this->connection->trans_status() === FALSE) && ($this->db->trans_status() === FALSE))
      return FALSE;

    $this->connection->trans_commit();
    $this->db->trans_commit();
    if($this->config->item('access_from')!='localhost'){
      if ($unbudgeted > 0) {
        $this->send_mail_finance($document_id);
      } else {
        $this->send_mail($document_id);
      }
    }

    return TRUE;
  }

  public function delete()
  {
    $this->connection->trans_begin();

    $id = $this->input->post('id');

    $this->connection->select('pr_number, warehouse');
    $this->connection->where('id', $id);
    $this->connection->from('tb_inventory_purchase_requisitions');

    $query = $this->connection->get();
    $row   = $query->unbuffered_row('array');

    $pr_number  = $row['pr_number'];
    $warehouse        = $row['warehouse'];

    $this->connection->select('tb_receipt_items.id, tb_receipt_items.stock_in_stores_id, tb_receipt_items.received_quantity, tb_receipt_items.received_unit_value, tb_stock_in_stores.stock_id, tb_stock_in_stores.serial_id, tb_stock_in_stores.stores');
    $this->connection->from('tb_receipt_items');
    $this->connection->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
    $this->connection->where('tb_receipt_items.pr_number', $pr_number);

    $query  = $this->connection->get();
    $result = $query->result_array();

    foreach ($result as $data) {
      $this->connection->set('stock_id', $data['stock_id']);
      $this->connection->set('serial_id', $data['serial_id']);
      $this->connection->set('warehouse', $warehouse);
      $this->connection->set('stores', $data['stores']);
      $this->connection->set('date_of_entry', date('Y-m-d'));
      $this->connection->set('period_year', config_item('period_year'));
      $this->connection->set('period_month', config_item('period_month'));
      $this->connection->set('document_type', 'REMOVAL');
      $this->connection->set('pr_number', $pr_number);
      $this->connection->set('issued_to', 'DELETE DOCUMENT');
      $this->connection->set('issued_by', config_item('auth_person_name'));
      $this->connection->set('quantity', 0 - floatval($data['received_quantity']));
      $this->connection->set('unit_value', floatval($data['received_unit_value']));
      $this->connection->insert('tb_stock_cards');

      $this->connection->where('id', $data['id']);
      $this->connection->delete('tb_receipt_items');

      $this->connection->where('id', $data['stock_in_stores_id']);
      $this->connection->delete('tb_stock_in_stores');
    }

    $this->connection->where('id', $id);
    $this->connection->delete('tb_inventory_purchase_requisitions');

    if ($this->connection->trans_status() === FALSE)
      return FALSE;

    $this->connection->trans_commit();
    return TRUE;
  }

  public function searchBudget($category)
  {
    $query = "";
    if ($_SESSION['request']['request_to'] == 0) {
      $this->column_select = array(
        'tb_inventory_monthly_budgets.*',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_product_groups.group_name',
        'tb_product_measurements.measurement_symbol',
        'tb_product_purchase_prices.current_price',
      );

      $this->connection->select($this->column_select);
      $this->connection->from('tb_inventory_monthly_budgets');
      $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
      $this->connection->join('tb_product_purchase_prices', 'tb_product_purchase_prices.product_id = tb_products.id');
      $this->connection->join('tb_product_measurements', 'tb_product_measurements.id = tb_products.product_measurement_id');
      $this->connection->join('tb_product_groups', 'tb_product_groups.id = tb_products.product_group_id');
      $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_product_groups.product_category_id');
      $this->connection->where('tb_inventory_monthly_budgets.year_number', $this->budget_year);
      $this->connection->where('tb_inventory_monthly_budgets.month_number', $this->budget_month);
      $this->connection->where('UPPER(tb_product_categories.category_name)', strtoupper($category));
      $this->connection->where('tb_inventory_monthly_budgets.ytd_budget - tb_inventory_monthly_budgets.ytd_used_budget > ', 0, FALSE);
      $this->connection->order_by('tb_products.product_name ASC, tb_products.product_code ASC');
      $query  = $this->connection->get();
    } else {
      $this->db->select('tb_budget.*,tb_master_items.description as "product_name", tb_master_items.part_number as "product_code", tb_master_items.group as "group_name", tb_master_items.current_price,tb_master_items.unit as "measurement_symbol"');
      $this->db->from('tb_budget');
      $this->db->join('tb_budget_cot', 'tb_budget.id_cot = tb_budget_cot.id ');
      $this->db->join('tb_master_items', 'tb_master_items on tb_master_items.id = tb_budget_cot.id_item');
      $this->db->join('tb_master_item_groups', 'tb_master_item_groups on tb_master_items.group = tb_master_item_groups.group');
      $this->db->where('UPPER(tb_master_item_groups.category)', $_SESSION['request']['category']);
      // $this->db->where('tb_budget.ytd_budget - tb_budget.ytd_used_budget > ', 0, FALSE);
      $this->db->where('tb_budget.mtd_budget - tb_budget.mtd_used_budget > ', 0, FALSE);
      $this->db->order_by('tb_master_items.description ASC, tb_master_items.part_number ASC');
      // $this->db->where('tb_budget_cot.year', $this->budget_year);
      $this->db->where('tb_budget_cot.year', date('Y'));
      $this->db->where('tb_budget_cot.status', 'APPROVED');
      // $this->db->where('tb_budget.month_number', $this->budget_month);
      $this->db->where('tb_budget.month_number', date('m'));

      $query  = $this->db->get();
    }

    $result = $query->result_array();
    foreach ($result as $key => $value) {
      // $result[$key]['maximum_quantity'] = $value['ytd_quantity'] - $value['ytd_used_quantity'];
      // $result[$key]['maximum_price'] = $value['ytd_budget'] - $value['ytd_used_budget'];
      $result[$key]['maximum_quantity'] = $value['mtd_quantity'] - $value['mtd_used_quantity'];
      $result[$key]['maximum_price'] = $value['mtd_budget'] - $value['mtd_used_budget'];

      $this->db->from('tb_master_items');
      $this->db->where('UPPER(tb_master_items.part_number)', strtoupper($value['product_code']));

      $query  = $this->db->get();

      if ($query->num_rows() > 0) {
        $master_item = $query->unbuffered_row('array');

        $result[$key]['minimum_quantity'] = $master_item['minimum_quantity'];

        $this->db->select('tb_stocks.total_quantity, tb_stocks.average_value');
        $this->db->from('tb_stocks');
        $this->db->where('tb_stocks.item_id', $master_item['id']);
        $this->db->where('tb_stocks.condition', 'SERVICEABLE');

        $query  = $this->db->get();

        if ($query->num_rows() > 0) {
          $stock = $query->unbuffered_row('array');

          $result[$key]['on_hand_quantity'] = $stock['total_quantity'];
          $result[$key]['price']            = $value['current_price'];
        } else {
          $result[$key]['on_hand_quantity'] = 0;
          $result[$key]['price']            = $value['current_price'];
        }
      } else {
        $result[$key]['minimum_quantity'] = 0;
        $result[$key]['on_hand_quantity'] = 0;
        $result[$key]['price']            = $value['current_price'];
      }
    }
    return $result;
  }

  public function searchBudgetForRelocation($category)
  {
    $query = "";
    if ($_SESSION['request']['request_to'] == 0) {
      $this->column_select = array(
        'tb_inventory_monthly_budgets.*',
        'tb_products.product_name',
        'tb_products.product_code',
        'tb_product_groups.group_name',
        'tb_product_measurements.measurement_symbol',
        'tb_product_purchase_prices.current_price',
      );

      $this->connection->select($this->column_select);
      $this->connection->from('tb_inventory_monthly_budgets');
      $this->connection->join('tb_products', 'tb_products.id = tb_inventory_monthly_budgets.product_id');
      $this->connection->join('tb_product_purchase_prices', 'tb_product_purchase_prices.product_id = tb_products.id');
      $this->connection->join('tb_product_measurements', 'tb_product_measurements.id = tb_products.product_measurement_id');
      $this->connection->join('tb_product_groups', 'tb_product_groups.id = tb_products.product_group_id');
      $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_product_groups.product_category_id');
      $this->connection->where('tb_inventory_monthly_budgets.year_number', $this->budget_year);
      $this->connection->where('tb_inventory_monthly_budgets.month_number', $this->budget_month);
      $this->connection->where('UPPER(tb_product_categories.category_name)', strtoupper($category));
      $this->connection->where('tb_inventory_monthly_budgets.ytd_budget - tb_inventory_monthly_budgets.ytd_used_budget > ', 0, FALSE);
      $this->connection->order_by('tb_products.product_name ASC, tb_products.product_code ASC');
      $query  = $this->connection->get();
    } else {
      $this->db->select('tb_budget.*,tb_master_items.description as "product_name", tb_master_items.part_number as "product_code", tb_master_items.group as "group_name", tb_master_items.current_price,tb_master_items.unit as "measurement_symbol"');
      $this->db->from('tb_budget');
      $this->db->join('tb_budget_cot', 'tb_budget.id_cot = tb_budget_cot.id ');
      $this->db->join('tb_master_items', 'tb_master_items on tb_master_items.id = tb_budget_cot.id_item');
      $this->db->join('tb_master_item_groups', 'tb_master_item_groups on tb_master_items.group = tb_master_item_groups.group');
      // $this->db->where('UPPER(tb_master_item_groups.category)', strtoupper($category));
      $this->db->where('tb_budget.mtd_budget - tb_budget.mtd_used_budget > ', 0, FALSE);
      $this->db->order_by('tb_master_items.description ASC, tb_master_items.part_number ASC');
      // $this->db->where('tb_budget_cot.year', $this->budget_year);
      $this->db->where('tb_budget_cot.year', date('Y'));
      $this->db->where('tb_budget_cot.status', 'APPROVED');
      // $this->db->where('tb_budget.month_number', $this->budget_month);
      $this->db->where('tb_budget.month_number !=', date('m'));
      // $this->db->or_where('tb_budget.month_number >', date('m'));
      $this->db->order_by('tb_budget.id', 'asc');

      $query  = $this->db->get();
    }

    $result = $query->result_array();
    foreach ($result as $key => $value) {
      // $result[$key]['maximum_quantity'] = $value['ytd_quantity'] - $value['ytd_used_quantity'];
      // $result[$key]['maximum_price'] = $value['ytd_budget'] - $value['ytd_used_budget'];
      $result[$key]['maximum_quantity'] = $value['mtd_quantity'] - $value['mtd_used_quantity'];
      $result[$key]['maximum_price'] = $value['mtd_budget'] - $value['mtd_used_budget'];
      $result[$key]['bulan'] = $this->bulan($value['month_number']);

      $this->db->from('tb_master_items');
      $this->db->where('UPPER(tb_master_items.part_number)', strtoupper($value['product_code']));

      $query  = $this->db->get();

      if ($query->num_rows() > 0) {
        $master_item = $query->unbuffered_row('array');

        $result[$key]['minimum_quantity'] = $master_item['minimum_quantity'];

        $this->db->select('tb_stocks.total_quantity, tb_stocks.average_value');
        $this->db->from('tb_stocks');
        $this->db->where('tb_stocks.item_id', $master_item['id']);
        $this->db->where('tb_stocks.condition', 'SERVICEABLE');

        $query  = $this->db->get();

        if ($query->num_rows() > 0) {
          $stock = $query->unbuffered_row('array');

          $result[$key]['on_hand_quantity'] = $stock['total_quantity'];
          $result[$key]['price']            = $value['current_price'];
        } else {
          $result[$key]['on_hand_quantity'] = 0;
          $result[$key]['price']            = $value['current_price'];
        }
      } else {
        $result[$key]['minimum_quantity'] = 0;
        $result[$key]['on_hand_quantity'] = 0;
        $result[$key]['price']            = $value['current_price'];
      }
    }
    return $result;
  }

  function bulan($bln)
  {
    switch ($bln) {
      case 1:
        $return = 'Januari';
        break;
      case 2:
        $return = 'Februari';
        break;
      case 3:
        $return = 'Maret';
        break;
      case 4:
        $return = 'April';
        break;
      case 5:
        $return = 'Mei';
        break;
      case 6:
        $return = 'Juni';
        break;
      case 7:
        $return = 'Juli';
        break;
      case 8:
        $return = 'Agustus';
        break;
      case 9:
        $return = 'September';
        break;
      case 10:
        $return = 'Oktober';
        break;
      case 11:
        $return = 'November';
        break;
      case 12:
        $return = 'Desember';
        break;
        // default: $return = '';
    }
    return $return;
  }

  public function searchItemUnbudgeted($category)
  {
    $this->db->select('id_item');
    $this->db->where('status', '!=', 'REJECTED');
    $this->db->from('tb_budget_cot');
    $query_cot = $this->db->get();
    $budget_cot = $query_cot->unbuffered_row('array');

    $this->column_select = array(
      'tb_master_items.id',
      'tb_master_items.group as group_name',
      'tb_master_items.description as product_name',
      'tb_master_items.part_number as product_code',
      'tb_master_items.alternate_part_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_stocks.total_quantity AS on_hand_quantity',
      'tb_master_items.current_price AS price'
    );

    $this->db->select($this->column_select);
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_stocks', 'tb_stocks.item_id = tb_master_items.id');
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    $this->db->where('tb_master_item_groups.category', $category);
    $this->db->where_not_in('tb_master_items.id', $budget_cot['item_id']);
    $this->db->order_by('tb_master_items.group ASC, tb_master_items.description ASC');
    //echo $this->db->_compile_select();
    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function searchItemsByPartNumber($category)
  {
    $this->column_select = array(
      'tb_master_items.id',
      'tb_master_items.group',
      'tb_master_items.description',
      'tb_master_items.part_number',
      'tb_master_items.serial_number',
      'tb_master_items.alternate_part_number',
      'tb_master_items.minimum_quantity',
      'tb_master_items.unit',
      'tb_stocks.total_quantity AS on_hand_quantity',
      'tb_master_items.current_price AS price'
    );

    $this->db->select($this->column_select);
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->join('tb_stocks', 'tb_stocks.item_id = tb_master_items.id');
    $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
    $this->db->where('tb_master_item_groups.category', $category);

    $this->db->order_by('tb_master_items.group ASC, tb_master_items.description ASC');
    //echo $this->db->_compile_select();
    $query  = $this->db->get();
    $result = $query->result_array();

    return $result;
  }

  public function searchItemsByProductName($category)
  {
    $this->db->select('tb_master_items.description');
    $this->db->from('tb_master_items');
    $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
    $this->db->where('UPPER(tb_master_item_groups.category)', strtoupper($category));

    $query  = $this->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['description'];
    }

    return $return;
  }

  public function searchItemGroups($category)
  {
    $this->connection->select('tb_product_groups.group_name');
    $this->connection->from('tb_product_groups');
    $this->connection->join('tb_product_categories', 'tb_product_categories.id = tb_product_groups.product_category_id');
    $this->connection->where('UPPER(tb_product_categories.category_name)', strtoupper($category));

    $query  = $this->connection->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['group_name'];
    }

    return $return;
  }

  public function getAvailableVendors($category)
  {
    $this->db->select('tb_master_vendors.vendor');
    $this->db->from('tb_master_vendors');
    $this->db->join('tb_master_vendor_categories', 'tb_master_vendors.vendor = tb_master_vendor_categories.vendor');
    $this->db->where('UPPER(tb_master_vendors.status)', 'AVAILABLE');
    $this->db->where('UPPER(tb_master_vendor_categories.category)', strtoupper($category));

    $query  = $this->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['vendor'];
    }

    return $return;
  }
  public function cancel($id)
  {
    $detail = $this->findById($id);
    if ($_SESSION['request']['request_to'] == 0) {
      $this->connection->where('inventory_purchase_requisition_id', $id);
      $query = $this->connection->get('tb_inventory_purchase_requisition_details');
      $inventory_purchase_requisition_details = $query->result();
      foreach ($inventory_purchase_requisition_details as $key) {
        $this->connection->where('id', $key->inventory_monthly_budget_id);
        $query = $this->connection->get('tb_inventory_monthly_budgets');
        $oldBudget =  $query->row();
        $month_number = $oldBudget->month_number;
        $year_number = $oldBudget->year_number;
        $product_id = $oldBudget->product_id;
        for ($i = $month_number; $i < 13; $i++) {
          $this->connection->set('mtd_used_budget', 'mtd_used_budget - ' . $key->total, FALSE);
          $this->connection->set('ytd_used_budget', 'ytd_used_budget - ' . $key->total, FALSE);
          $this->connection->set('mtd_used_quantity', 'mtd_used_quantity - ' . $key->quantity, FALSE);
          $this->connection->set('ytd_used_quantity', 'ytd_used_quantity - ' . $key->quantity, FALSE);
          $this->connection->where('month_number', $i);
          $this->connection->where('year_number', $year_number);
          $this->connection->where('product_id', $product_id);
          $this->connection->update('tb_inventory_monthly_budgets');
        }
        $this->connection->where('inventory_purchase_requisition_id', $id);
        $this->connection->where('inventory_monthly_budget_id', $key->inventory_monthly_budget_id);
        $this->connection->delete('tb_inventory_used_budgets');
      }
      $this->connection->set('status', 'canceled');
      $this->connection->where('id', $id);
      $this->connection->update('tb_inventory_purchase_requisitions');
    } else {
      $this->db->where('inventory_purchase_requisition_id', $id);
      $query = $this->db->get('tb_inventory_purchase_requisition_details');
      $inventory_purchase_requisition_details = $query->result();
      foreach ($inventory_purchase_requisition_details as $key) {
        $this->db->where('id', $key->budget_id);
        $query = $this->db->get('tb_budget');
        $oldBudget =  $query->row();
        $month_number = $oldBudget->month_number;
        $id_cot = $oldBudget->id_cot;
        for ($i = $month_number; $i < 13; $i++) {
          $this->db->set('mtd_used_budget', 'mtd_used_budget - ' . $key->total, FALSE);
          $this->db->set('ytd_used_budget', 'ytd_used_budget - ' . $key->total, FALSE);
          $this->db->set('mtd_used_quantity', 'mtd_used_quantity - ' . $key->quantity, FALSE);
          $this->db->set('ytd_used_quantity', 'ytd_used_quantity - ' . $key->quantity, FALSE);
          $this->db->where('month_number', $i);
          $this->db->where('id_cot', $id_cot);
          $this->db->update('tb_budget');
        }
        $this->db->where('inventory_purchase_requisition_id', $id);
        $this->db->where('budget_id', $key->budget_id);
        $this->db->delete('tb_used_budgets');
      }
      $this->db->set('status', 'canceled');
      $this->db->where('id', $id);
      $this->db->update('tb_inventory_purchase_requisitions');
    }
    return true;
  }

  public function approve($id, $price)
  {
    $this->db->trans_begin();


    $this->db->from('tb_inventory_purchase_requisition_details');
    $this->db->where('id', $id);

    $query  = $this->db->get();
    $row    = $query->unbuffered_row('array');
    $inventory_purchase_requisition_id  = $row['inventory_purchase_requisition_id'];
    $status_budget = $row['budget_status'];

    $this->db->from('tb_inventory_purchase_requisitions');
    $this->db->where('id', $inventory_purchase_requisition_id);

    $query_pr  = $this->db->get();
    $row_pr    = $query_pr->unbuffered_row('array');
    // $inventory_purchase_requisition_id  = $row['inventory_purchase_requisition_id'];
    // $status_budget = $row_pr['status_budget'];
    $id_budget_sementara = $row['budget_id_sementara'];

    if ($row['status'] == 'review operation support') {
      $this->db->set('status', 'open');
      $this->db->where('id', $id);
      $this->db->update('tb_inventory_purchase_requisition_details');

      $this->db->set('operation_review_by', config_item('auth_person_name'));
      // $this->db->set('approved_notes', strtoupper($rejected_note));
      $this->db->where('id', $inventory_purchase_requisition_id);
      $this->db->update('tb_inventory_purchase_requisitions');
    }

    if ($row['status'] == 'pending') {
      if ($status_budget == 'unbudgeted') {

        // if($row['price']==0){

        // }else{
        //   $new_price = $row['price'];
        //   $new_total = floatval($row['quantity'] * $new_price);
        // }
        $new_price = $price;
        $new_total = floatval($row['quantity'] * $new_price);

        $this->db->set('finance_approve_by', config_item('auth_person_name'));
        $this->db->set('finance_approve_at', date('Y-m-d'));
        $this->db->where('id', $inventory_purchase_requisition_id);
        $this->db->update('tb_inventory_purchase_requisitions');

        $this->db->set('price', floatval($new_price));
        $this->db->set('total', floatval($new_total));
        $this->db->where('id', $id);
        $this->db->update('tb_inventory_purchase_requisition_details');
        $this->db->set('status', 'waiting');
        $this->db->where('id', $id);
        $this->db->update('tb_inventory_purchase_requisition_details');

        $this->db->order_by('id', "asc")
          ->limit(1)
          ->like('part_number', strtoupper($row['part_number']))
          ->from('tb_master_items');
        $query_item = $this->db->get();
        $row_item   = $query_item->unbuffered_row('array');
        $id_item = $row_item['id'];

        $this->db->order_by('id', "desc")
          ->limit(1)
          // ->like('year', $row['part_number'])
          ->from('tb_budget_cot');
        $query_cot = $this->db->get();
        $row_cot   = $query_cot->unbuffered_row('array');
        $hours     = 1000;
        $year      = date('Y');

        $this->db->from('tb_budget_cot');
        $this->db->where('id_item', $id_item);
        $this->db->where('year', $year);
        $budget_cot = $this->db->get();
        if ($budget_cot->num_rows() == 0) {
          $this->db->set('id_item', $id_item);
          $this->db->set('hours', $hours);
          $this->db->set('year', $year);
          $this->db->set('id_kelipatan', 1);
          $this->db->set('onhand', 0);
          $this->db->set('qty_standar', 1);
          $this->db->set('status', 'APPROVED');
          $this->db->set('item_part_number', strtoupper($row['part_number']));
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->set('updated_at', date('Y-m-d'));
          $this->db->insert('tb_budget_cot');
          $id_cot = $this->db->insert_id();
        } else {
          $row_budget_cot = $budget_cot->unbuffered_row('array');
          $id_cot = $row_budget_cot['id'];
        }

        $this->db->from('tb_budget');
        $this->db->where('id_cot', $id_cot);
        $this->db->where('month_number', date('m'));
        $tb_budget = $this->db->get();
        if ($tb_budget->num_rows() == 0) {
          //buat budget baru
          // for ($i=1; $i <13 ; $i++) {
          $this->db->set('id_cot', $id_cot);
          // $this->db->set('month_number', $i);
          $this->db->set('month_number', date('m'));
          // $this->db->set('year_number', $this->budget_year);
          $this->db->set('initial_quantity', floatval(0));
          $this->db->set('initial_budget', floatval(0));
          $this->db->set('mtd_quantity', floatval(0));
          $this->db->set('mtd_budget', floatval(0));
          $this->db->set('mtd_used_quantity', floatval(0));
          $this->db->set('mtd_used_budget', floatval(0));
          $this->db->set('mtd_used_quantity_import', floatval(0));
          $this->db->set('mtd_used_budget_import', floatval(0));
          $this->db->set('mtd_prev_month_quantity', floatval(0));
          $this->db->set('mtd_prev_month_budget', floatval(0));
          $this->db->set('mtd_prev_month_used_quantity', floatval(0));
          $this->db->set('mtd_prev_month_used_budget', floatval(0));
          $this->db->set('mtd_prev_month_used_quantity_import', floatval(0));
          $this->db->set('mtd_prev_month_used_budget_import', floatval(0));
          $this->db->set('ytd_quantity', floatval(0));
          $this->db->set('ytd_budget', floatval(0));
          $this->db->set('ytd_used_quantity', floatval(0));
          $this->db->set('ytd_used_budget', floatval(0));
          $this->db->set('ytd_used_quantity_import', floatval(0));
          $this->db->set('ytd_used_budget_import', floatval(0));
          $this->db->set('created_at', date('Y-m-d'));
          $this->db->set('created_by', config_item('auth_person_name'));
          $this->db->set('updated_at', date('Y-m-d'));
          $this->db->set('updated_by', config_item('auth_person_name'));
          $this->db->insert('tb_budget');
          $inventory_monthly_budget_id = $this->db->insert_id();
          // }
        } else {
          $this->db->from('tb_budget');
          $this->db->where('id_cot', $id_cot);
          $this->db->where('month_number', date('m'));
          $tb_budget = $this->db->get();
          $row_budget = $tb_budget->unbuffered_row('array');
          $inventory_monthly_budget_id = $row_budget['id'];
        }

        $this->db->set('mtd_quantity', 'mtd_quantity + ' . $row['quantity'], FALSE);
        $this->db->set('mtd_budget', 'mtd_budget + ' . $new_total, FALSE);
        $this->db->set('mtd_used_budget', 'mtd_used_budget + ' . $new_total, FALSE);
        $this->db->set('ytd_used_budget', 'ytd_used_budget + ' . $new_total, FALSE);
        $this->db->set('mtd_used_quantity', 'mtd_used_quantity + ' . $row['quantity'], FALSE);
        $this->db->set('ytd_used_quantity', 'ytd_used_quantity + ' . $row['quantity'], FALSE);
        $this->db->set('ytd_quantity', 'ytd_quantity + ' . $row['quantity'], FALSE);
        $this->db->set('ytd_budget', 'ytd_budget + ' . $new_total, FALSE);
        $this->db->where('id', $inventory_monthly_budget_id);
        $this->db->update('tb_budget');
        //      for ($i=date('m')+2; $i <13 ; $i++) {
        //         $this->db->set('ytd_quantity', 'ytd_quantity + '.$row['quantity'],FALSE);
        //           $this->db->set('ytd_budget', 'ytd_budget + '.$row['total'],FALSE);
        //           $this->db->set('ytd_used_budget', 'ytd_used_budget + '.$row['total'],FALSE);
        //           $this->db->set('ytd_used_quantity', 'ytd_used_quantity + '.$row['quantity'],FALSE);
        //           $this->db->where('month_number', $i);
        //           $this->db->where('id_cot', $id_cot);
        //           $this->db->update('tb_budget');
        //      }


        $this->db->set('budget_id', $inventory_monthly_budget_id);
        $this->db->where('id', $id);
        $this->db->update('tb_inventory_purchase_requisition_details');

        // if ($row['price'] == 0) {
        //   $new_price = $price;
        //   $new_total = floatval($row['quantity'] * $new_price);          

        // }
        $this->db->set('new_budget', floatval($new_total));
        $this->db->set('inventory_monthly_budget_id', $inventory_monthly_budget_id);
        $this->db->set('status', 'approved');
        $this->db->where('id', $id_budget_sementara);
        $this->db->update('tb_inventory_unbudgeted');

        // $budget_id = $data['budget_id'];


        $this->db->set('budget_id', $inventory_monthly_budget_id);
        $this->db->set('inventory_purchase_requisition_id', $inventory_purchase_requisition_id);
        $this->db->set('pr_number', $row_pr['pr_number']);
        $this->db->set('year_number', date('Y'));
        $this->db->set('month_number', date('m'));
        $this->db->set('product_name', $row['product_name']);
        $this->db->set('product_group', $row['group_name']);
        $this->db->set('product_code', $row['part_number']);
        $this->db->set('additional_info', $row['additional_info']);
        $this->db->set('used_budget', $new_total);
        $this->db->set('used_quantity', $row['quantity']);
        $this->db->set('created_at', date('Y-m-d H:i:s'));
        $this->db->set('created_by', config_item('auth_person_name'));
        $this->db->set('part_number', $row['part_number']);
        $this->db->insert('tb_used_budgets');
      }
    }

    if ($row['status'] == 'waiting') {
      if ($status_budget == 'relocation') {
        $this->db->set('status', 'approved');
        $this->db->where('id', $id_budget_sementara);
        $this->db->update('tb_inventory_relocation_budgets');

        $this->db->from('tb_inventory_relocation_budgets');
        $this->db->where('id', $id_budget_sementara);
        $query_relocation_budget  = $this->db->get();
        $row_relocation_budget    = $query_relocation_budget->unbuffered_row('array');
        $origin_budget_id  = $row_relocation_budget['origin_budget_id'];
        $destination_budget_id  = $row_relocation_budget['destination_budget_id'];


        $this->db->set('mtd_budget', 'mtd_budget + ' . $row_relocation_budget['amount'], FALSE);
        $this->db->where('id', $destination_budget_id);
        $this->db->update('tb_budget');
      }

      if ($row_pr['item_category'] == 'BAHAN BAKAR') {
        $this->db->set('status', 'review operation support');
        $this->db->where('id', $id);
        $this->db->update('tb_inventory_purchase_requisition_details');
      } else {
        $this->db->set('status', 'open');
        $this->db->where('id', $id);
        $this->db->update('tb_inventory_purchase_requisition_details');
      }

      // $status_prl = 0;
      $this->db->from('tb_inventory_purchase_requisition_details');
      $this->db->where_not_in('tb_inventory_purchase_requisition_details.status', ['open', 'review operation support']);
      $this->db->where('inventory_purchase_requisition_id', $inventory_purchase_requisition_id);
      $query_pr_item  = $this->db->get();
      $status_prl = $query_pr_item->num_rows();

      if ($status_prl == 0) {
        $this->db->set('status', 'approved');
      }
      $this->db->set('approved_date', date('Y-m-d'));
      $this->db->set('approved_by', config_item('auth_person_name'));
      $this->db->set('approved_notes', strtoupper('approved'));
      $this->db->where('id', $inventory_purchase_requisition_id);
      $this->db->update('tb_inventory_purchase_requisitions');
    }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  function multi_reject($id_purchase_order, $notes)
  {
    $this->db->trans_begin();
    $x = 0;
    $return = 0;
    $rejected_note = '';
    foreach ($id_purchase_order as $id) {
      $this->db->where('id', $id);
      $tb_purchase_order_items = $this->db->get('tb_inventory_purchase_requisition_details')->result();

      $this->db->set('status', 'rejected');
      $this->db->set('notes', $notes[$x]);
      // $this->db->set('approved_by', config_item('auth_person_name'));
      $this->db->where('id', $id);
      $check = $this->db->update('tb_inventory_purchase_requisition_details');

      $this->db->from('tb_inventory_purchase_requisition_details');
      $this->db->where('id', $id);

      $query  = $this->db->get();
      $row    = $query->unbuffered_row('array');
      $inventory_purchase_requisition_id  = $row['inventory_purchase_requisition_id'];


      $rejected_note = $rejected_note . ' ' . $notes[$x];

      if ($row['budget_status'] == 'relocation') {
        $this->db->where('id', $row['budget_id']);
        $query = $this->db->get('tb_budget');
        $oldBudget =  $query->row();
        $month_number = $oldBudget->month_number;
        $id_cot = $oldBudget->id_cot;
        for ($i = $month_number; $i < 13; $i++) {
          $this->db->set('mtd_used_budget', 'mtd_used_budget - ' . $row['total'], FALSE);
          $this->db->set('ytd_used_budget', 'ytd_used_budget - ' . $row['total'], FALSE);
          $this->db->set('mtd_used_quantity', 'mtd_used_quantity - ' . $row['quantity'], FALSE);
          $this->db->set('ytd_used_quantity', 'ytd_used_quantity - ' . $row['quantity'], FALSE);
          $this->db->where('month_number', $i);
          $this->db->where('id_cot', $id_cot);
          $this->db->update('tb_budget');
        }
        $this->db->where('inventory_purchase_requisition_id', $row['inventory_purchase_requisition_id']);
        $this->db->where('budget_id', $row['budget_id']);
        $this->db->delete('tb_used_budgets');

        $this->db->where('id', $row['budget_id_sementara']);
        $query_relocation = $this->db->get('tb_inventory_relocation_budgets');
        $relocation =  $query_relocation->row();
        $origin_budget_id = $relocation->origin_budget_id;
        $this->db->set('mtd_used_budget', 'mtd_used_budget - ' . $relocation['total'], FALSE);
        $this->db->set('ytd_used_budget', 'ytd_used_budget - ' . $relocation['total'], FALSE);
        // $this->db->where('month_number', $i);
        $this->db->where('id', $origin_budget_id);
        $this->db->update('tb_budget');

        $this->db->set('status', 'rejected');
        $this->db->where('id', $row['budget_id_sementara']);
        $this->db->update('tb_inventory_relocation_budgets');
      } elseif ($row['budget_status'] == 'unbudgeted') {
        $this->db->set('status', 'rejected');
        $this->db->where('id', $row['budget_id_sementara']);
        $this->db->update('tb_inventory_unbudgeted');
      } else {
        $this->db->where('id', $row['budget_id']);
        $query = $this->db->get('tb_budget');
        $oldBudget =  $query->row();
        $month_number = $oldBudget->month_number;
        $id_cot = $oldBudget->id_cot;
        for ($i = $month_number; $i < 13; $i++) {
          $this->db->set('mtd_used_budget', 'mtd_used_budget - ' . $row['total'], FALSE);
          $this->db->set('ytd_used_budget', 'ytd_used_budget - ' . $row['total'], FALSE);
          $this->db->set('mtd_used_quantity', 'mtd_used_quantity - ' . $row['quantity'], FALSE);
          $this->db->set('ytd_used_quantity', 'ytd_used_quantity - ' . $row['quantity'], FALSE);
          $this->db->where('month_number', $i);
          $this->db->where('id_cot', $id_cot);
          $this->db->update('tb_budget');
        }
        $this->db->where('inventory_purchase_requisition_id', $row['inventory_purchase_requisition_id']);
        $this->db->where('budget_id', $row['budget_id']);
        $this->db->delete('tb_used_budgets');
      }

      $this->db->set('rejected_date', date('Y-m-d'));
      $this->db->set('rejected_by', config_item('auth_person_name'));
      $this->db->set('rejected_notes', strtoupper($rejected_note));
      $this->db->where('id', $inventory_purchase_requisition_id);
      $this->db->update('tb_inventory_purchase_requisitions');

      if ($check) {
        $return++;
      }
      $x++;
      // $this->send_mail_approved($id,'rejected');
    }

    // if(($return == $x)&&($return > 0)){
    //   return true;
    // }else{
    //   return false;
    // }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  function multi_closing($id_purchase_order, $notes)
  {
    $this->db->trans_begin();
    $x = 0;
    $return = 0;
    $rejected_note = '';
    foreach ($id_purchase_order as $id) {
      // $this->db->where('id', $id);
      // $tb_purchase_order_items = $this->db->get('tb_inventory_purchase_requisition_details')->result();
      $inventory_purchase_request_detail_id = $id;
      $this->db->set('sisa', 0);
      $this->db->set('status', 'closed');
      $this->db->where('id', $inventory_purchase_request_detail_id);
      $check = $this->db->update('tb_inventory_purchase_requisition_details');
      // $check = $this->db->update('tb_inventory_purchase_requisition_details');


      $this->db->set('closing_by', config_item('auth_person_name'));
      $this->db->set('notes', $notes[$x]);
      $this->db->set('purchase_request_detail_id', $inventory_purchase_request_detail_id);
      $this->db->insert('tb_purchase_request_closures');

      if ($check) {
        $return++;
      }
      $x++;
      // $this->send_mail_approved($id,'rejected');
    }

    // if(($return == $x)&&($return > 0)){
    //   return true;
    // }else{
    //   return false;
    // }

    if ($this->db->trans_status() === FALSE)
      return FALSE;

    $this->db->trans_commit();
    return TRUE;
  }

  function findItemByPartNumber($part_number)
  {
    $query = $this->db->select('*')
      ->from('tb_master_items')
      ->where('part_number =', strtoupper($part_number))
      ->limit(1)
      ->get();
    return $query->unbuffered_row('array');
  }

  function findItemBudget($part_number)
  {
    $this->column_select = array(
      // 'tb_budget.id',
      // 'tb_budget.ytd_quantity',
      // 'tb_budget.ytd_used_quantity',
      // 'tb_budget.ytd_used_budget',
      // 'tb_budget.ytd_budget',
      // 'tb_budget.mtd_quantity',
      // 'tb_budget.mtd_used_quantity',
      // 'tb_budget.mtd_used_budget',
      // 'tb_budget.mtd_budget'
      'tb_budget.id',
      'ytd_quantity',
      'ytd_used_quantity',
      'ytd_used_budget',
      'ytd_budget',
      'mtd_quantity',
      'mtd_used_quantity',
      'mtd_used_budget',
      'mtd_budget'
    );

    $this->db->select($this->column_select);
    $this->db->from('tb_budget');
    $this->db->join('tb_budget_cot', 'tb_budget.id_cot=tb_budget_cot.id');
    $this->db->join('tb_master_items', 'tb_budget_cot.id_item=tb_master_items.id');
    $this->db->where('tb_master_items.part_number', $part_number);
    // $this->db->where('tb_budget_cot.year', $this->budget_year);
    $this->db->where('tb_budget_cot.year', date('Y'));
    $this->db->where('tb_budget_cot.status', 'APPROVED');
    // $this->db->where('tb_budget.month_number', $this->budget_month);
    $this->db->where('tb_budget.month_number', date('m'));
    $query  = $this->db->get();
    // if($query->num_rows() > 0 ){
    //   $row    = $query->unbuffered_row();
    //   $kurs_dollar   = $row->kurs_dollar;
    // }else{
    //   $kurs_dollar=0;
    // }
    return $query;
  }

  public function send_mail($doc_id)
  {
    $this->db->from('tb_inventory_purchase_requisitions');
    $this->db->where('id', $doc_id);
    $query = $this->db->get();
    $row = $query->unbuffered_row('array');

    $recipientList = $this->getNotifRecipient(9);
    $recipient = array();
    foreach ($recipientList as $key) {
      array_push($recipient, $key->email);
    }

    $from_email = "bifa.acd@gmail.com";
    $to_email = "aidanurul99@rocketmail.com";

    //Load email library 
    $this->load->library('email');
    // $config = array();
    // $config['protocol'] = 'mail';
    // $config['smtp_host'] = 'smtp.live.com';
    // $config['smtp_user'] = 'bifa.acd@gmail.com';
    // $config['smtp_pass'] = 'b1f42019';
    // $config['smtp_port'] = 587;
    // $config['smtp_auth']        = true;
    // $config['mailtype']         = 'html';
    // $this->email->initialize($config);
    $this->email->set_newline("\r\n");
    $message = "<p>Dear Chief of Maintenance</p>";
    $message .= "<p>Berikut permintaan Purchase Request dari Gudang :</p>";
    $message .= "<ul>";
    $message .= "</ul>";
    $message .= "<p>No Purchase Request : " . $row['pr_number'] . "</p>";
    $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
    $message .= "<p>[ <a href='http://119.2.51.138:7323/purchase_request/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Material Resource Planning');
    $this->email->to($recipient);
    $this->email->subject('Permintaan Approval Purchase Request No : ' . $row['pr_number']);
    $this->email->message($message);

    //Send mail 
    if ($this->email->send())
      return true;
    else
      return $this->email->print_debugger();
  }

  public function send_mail_finance($doc_id)
  {
    $this->db->from('tb_inventory_purchase_requisitions');
    $this->db->where('id', $doc_id);
    $query = $this->db->get();
    $row = $query->unbuffered_row('array');

    $recipientList = $this->getNotifRecipient(14);
    $recipient = array();
    foreach ($recipientList as $key) {
      array_push($recipient, $key->email);
    }

    $from_email = "bifa.acd@gmail.com";
    $to_email = "aidanurul99@rocketmail.com";

    //Load email library 
    $this->load->library('email');
    // $config = array();
    // $config['protocol'] = 'mail';
    // $config['smtp_host'] = 'smtp.live.com';
    // $config['smtp_user'] = 'bifa.acd@gmail.com';
    // $config['smtp_pass'] = 'b1f42019';
    // $config['smtp_port'] = 587;
    // $config['smtp_auth']        = true;
    // $config['mailtype']         = 'html';
    // $this->email->initialize($config);
    $this->email->set_newline("\r\n");
    $message = "<p>Dear Finance Manager</p>";
    $message .= "<p>Berikut permintaan Purchase Request dari Gudang :</p>";
    $message .= "<ul>";
    $message .= "</ul>";
    $message .= "<p>No Purchase Request : " . $row['pr_number'] . "</p>";
    $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
    $message .= "<p>[ <a href='http://119.2.51.138:7323/purchase_request/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Material Resource Planning');
    $this->email->to($recipient);
    $this->email->subject('Permintaan Approval Purchase Request No : ' . $row['pr_number']);
    $this->email->message($message);

    //Send mail 
    if ($this->email->send())
      return true;
    else
      return $this->email->print_debugger();
  }

  public function send_mail_approved($item_id, $tipe)
  {
    $this->db->from('tb_inventory_purchase_requisition_details');
    $this->db->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id=tb_inventory_purchase_requisitions.id');
    $this->db->where('tb_inventory_purchase_requisition_details.id', $item_id);
    $query = $this->db->get();
    $row = $query->unbuffered_row('array');

    $recipientList = $this->getNotifApproval($row['created_by']);
    $recipient = array();
    foreach ($recipientList as $key) {
      array_push($recipient, $key->email);
    }

    $from_email = "bifa.acd@gmail.com";
    $to_email = "aidanurul99@rocketmail.com";

    //Load email library 
    $this->load->library('email');
    // $config = array();
    // $config['protocol'] = 'mail';
    // $config['smtp_host'] = 'smtp.live.com';
    // $config['smtp_user'] = 'bifa.acd@gmail.com';
    // $config['smtp_pass'] = 'b1f42019';
    // $config['smtp_port'] = 587;
    // $config['smtp_auth']        = true;
    // $config['mailtype']         = 'html';
    // $this->email->initialize($config);
    $this->email->set_newline("\r\n");
    // $message = "<p>Dear Finance</p>";
    $message = "<p>Hello</p>";
    $message .= "<p>Item dibawah ini telah di " . ucfirst($tipe) . " :</p>";
    $message .= "<ul>";
    $message .= "<li>No Purchase Request : <strong>" . $row['pr_number'] . "</strong></li>";
    $message .= "<li>Part Number: <strong>" . $row['part_number'] . "</strong></li>";
    $message .= "<li>Deskription : <strong>" . $row['product_name'] . "</strong></li>";
    $message .= "<li>Qty Request : <strong>" . print_number($row['quantity'], 2) . " " . $row['unit'] . "</strong></li>";
    $message .= "<li>Total : <strong>" . print_number($row['total'], 2) . "</strong></li>";
    $message .= "</ul>";
    // $message .= "<p>No Purchase Request : ".$row['pr_number']."</p>";    
    $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
    $message .= "<p>[ <a href='http://119.2.51.138:7323/purchase_request/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Material Resource Planning');
    $this->email->to($recipient);
    $this->email->subject('Notification Purchase Request No : ' . $row['pr_number']);
    $this->email->message($message);

    //Send mail 
    if ($this->email->send())
      return true;
    else
      return $this->email->print_debugger();
  }

  public function getNotifRecipient($level)
  {
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('auth_level', $level);
    return $this->db->get('')->result();
  }

  public function getNotifApproval($level)
  {
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('person_name', $level);
    return $this->db->getauth_leresult();
  }

  public function countOnhand($part_number)
  {
    $this->db->select('sum(quantity)');
    $this->db->from('tb_stock_in_stores');
    //tambahan
    $this->db->join('tb_stocks', 'tb_stocks.id=tb_stock_in_stores.stock_id');
    $this->db->join('tb_master_items', 'tb_master_items.id=tb_stocks.item_id');
    $this->db->group_by('tb_master_items.part_number');
    //tambahan
    $this->db->where('tb_master_items.part_number', $part_number);
    return $this->db->get('')->row();
  }

  public function tb_on_hand_stock($prl_item_id)
  {
    $this->db->select('sum(on_hand_stock)');
    $this->db->from('tb_purchase_request_items_on_hand_stock');
    //tambahan
    // $this->db->join('tb_stocks', 'tb_stocks.id=tb_stock_in_stores.stock_id');
    // $this->db->join('tb_master_items', 'tb_master_items.id=tb_stocks.item_id');
    $this->db->group_by('tb_purchase_request_items_on_hand_stock.prl_item_id');
    //tambahan
    $this->db->where('tb_purchase_request_items_on_hand_stock.prl_item_id', $prl_item_id);
    return $this->db->get('')->row();
  }

  public function findPrlByPoeItemid($poe_item_id)
  {
    // $this->db->select('tb_inventory_purchase_requisition_details.*');
    // $this->db->from('tb_inventory_purchase_requisition_details');
    // $this->db->where('tb_inventory_purchase_requisition_details.id', $id);

    // $query_detail    = $this->db->get();
    // $request_detail  = $query_detail->unbuffered_row();
    $this->db->select('tb_inventory_purchase_requisitions.id');
    $this->db->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id=tb_inventory_purchase_requisitions.id');
    $this->db->join('tb_purchase_order_items', 'tb_purchase_order_items.inventory_purchase_request_detail_id=tb_inventory_purchase_requisition_details.id');
    $this->db->from('tb_inventory_purchase_requisitions');
    $this->db->where('tb_purchase_order_items.id', $poe_item_id);
    $query    = $this->db->get();
    $poe_item  = $query->unbuffered_row('array');
    $id = $poe_item['id'];

    $this->db->select('tb_inventory_purchase_requisitions.*');
    $this->db->from('tb_inventory_purchase_requisitions');
    $this->db->where('tb_inventory_purchase_requisitions.id', $id);

    $query    = $this->db->get();
    $request  = $query->unbuffered_row('array');

    $select = array(
      'tb_inventory_purchase_requisition_details.*',
      'tb_inventory_purchase_requisition_details.product_name',
      'tb_inventory_purchase_requisition_details.part_number',
      'tb_master_items.minimum_quantity',
      'tb_budget.id_cot',
      'SUM(tb_budget.mtd_quantity) AS fyp_quantity',
      'SUM(tb_budget.mtd_budget) AS fyp_budget',
      'SUM(tb_budget.mtd_used_quantity) AS fyp_used_quantity',
      'SUM(tb_budget.mtd_used_budget) AS fyp_used_budget',
    );

    $group_by = array(
      'tb_inventory_purchase_requisition_details.id',
      // 'tb_master_items.description',
      // 'tb_master_items.part_number',
      'tb_budget.id_cot',
      'tb_master_items.minimum_quantity',
    );

    $this->db->select($select);
    $this->db->from('tb_inventory_purchase_requisition_details');
    $this->db->join('tb_budget', 'tb_budget.id = tb_inventory_purchase_requisition_details.budget_id', 'left');
    $this->db->join('tb_budget_cot', 'tb_budget_cot.id = tb_budget.id_cot', 'left');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_budget_cot.id_item', 'left');
    $this->db->where('tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id', $id);
    $this->db->group_by($group_by);

    $query = $this->db->get();

    foreach ($query->result_array() as $key => $value) {
      $request['items'][$key] = $value;
      $request['items'][$key]['min_qty'] = search_min_qty($value['part_number']);

      $this->db->from('tb_budget');
      $this->db->where('tb_budget.id_cot', $value['id_cot']);
      $this->db->where('tb_budget.month_number', $this->budget_month);

      $query = $this->db->get();
      $row   = $query->unbuffered_row('array');

      $request['items'][$key]['mtd_quantity'] = $row['mtd_quantity'];
      $request['items'][$key]['mtd_budget'] = $row['mtd_budget'];
      $request['items'][$key]['mtd_used_quantity'] = $row['mtd_used_quantity'];
      $request['items'][$key]['mtd_used_budget'] = $row['mtd_used_budget'];
      $request['items'][$key]['ytd_quantity'] = $row['ytd_quantity'];
      $request['items'][$key]['ytd_budget'] = $row['ytd_budget'];
      $request['items'][$key]['ytd_used_quantity'] = $row['ytd_used_quantity'];
      $request['items'][$key]['ytd_used_budget'] = $row['ytd_used_budget'];
      $request['items'][$key]['on_hand_qty'] = $this->tb_on_hand_stock($value['id'])->sum;
      $request['items'][$key]['info_on_hand_qty'] = $this->info_on_hand($value['id']);
    }


    return $request;
  }

  public function send_mail_approval($id, $ket, $by)
  {
    $item_message = '<tbody>';

    $recipient = array();
    $this->db->select(
      array(
        'tb_inventory_purchase_requisitions.pr_number',
        'tb_inventory_purchase_requisition_details.product_name',
        'tb_inventory_purchase_requisition_details.part_number',
        'tb_inventory_purchase_requisition_details.quantity',
        'tb_inventory_purchase_requisition_details.total',
        'tb_inventory_purchase_requisition_details.unit',
      )
    );
    $this->db->from('tb_inventory_purchase_requisition_details');
    $this->db->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisitions.id=tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
    $this->db->where_in('tb_inventory_purchase_requisition_details.id', $id);
    $query = $this->db->get();
    $row = $query->result_array();

    foreach ($row as $item) {
      $item_message .= "<tr>";
      $item_message .= "<td>" . $item['pr_number'] . "</td>";
      $item_message .= "<td>" . $item['part_number'] . "</td>";
      $item_message .= "<td>" . $item['product_name'] . "</td>";
      $item_message .= "<td>" . print_number($item['quantity'], 2) . "</td>";
      $item_message .= "<td>" . $item['unit'] . "</td>";
      //$item_message .= "<td>".print_number($item['total'],2)."</td>";         
      $item_message .= "</tr>";
    }
    $item_message .= '</tbody>';

    $this->db->select('tb_inventory_purchase_requisitions.created_by,tb_auth_users.email');
    $this->db->from('tb_inventory_purchase_requisitions');
    $this->db->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
    $this->db->join('tb_auth_users', 'tb_inventory_purchase_requisitions.created_by = tb_auth_users.person_name');
    $this->db->group_by('tb_inventory_purchase_requisitions.created_by,tb_auth_users.email');
    $this->db->where_in('tb_inventory_purchase_requisition_details.id', $id);
    $query_po = $this->db->get();
    $row_po   = $query_po->result_array();
    foreach ($row_po as $key) {
      array_push($recipient, $key['email']);
    }



    $from_email = "bifa.acd@gmail.com";
    $to_email = "aidanurul99@rocketmail.com";
    if ($ket == 'approve') {
      $ket_level = 'Disetujui';
    } else {
      $ket_level = 'Ditolak';
    }
    // if($level==14){
    //   $ket_level = 'Finance Manager';
    // }elseif ($level==10) {
    //   $ket_level = 'Head Of School';
    // } elseif($level==11){
    //   $ket_level = 'Chief Of Finance';
    // }elseif($level==3){
    //   $ket_level = 'VP Finance';
    // }

    //Load email library 
    $this->load->library('email');
    // $config = array();
    // $config['protocol'] = 'mail';
    // $config['smtp_host'] = 'smtp.live.com';
    // $config['smtp_user'] = 'bifa.acd@gmail.com';
    // $config['smtp_pass'] = 'b1f42019';
    // $config['smtp_port'] = 587;
    // $config['smtp_auth']        = true;
    // $config['mailtype']         = 'html';
    // $this->email->initialize($config);
    $this->email->set_newline("\r\n");
    $message = "<p>Hello</p>";
    $message .= "<p>Item Berikut telah " . $ket_level . " oleh " . $by . "</p>";
    $message .= "<table>";
    $message .= "<thead>";
    $message .= "<tr>";
    $message .= "<th>No. Doc.</th>";
    $message .= "<th>Part Number</th>";
    $message .= "<th>Description</th>";
    $message .= "<th>Qty Order</th>";
    $message .= "<th>Unit</th>";
    $message .= "<th>Total Val. Order</th>";
    $message .= "</tr>";
    $message .= "</thead>";
    $message .= $item_message;
    $message .= "</table>";
    // $message .= "<p>No Purchase Request : ".$row['document_number']."</p>";    
    $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
    $message .= "<p>[ <a href='http://119.2.51.138:7323/purchase_request/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Material Resource Planning');
    $this->email->to($recipient);
    $this->email->subject('Notification Approval');
    $this->email->message($message);

    //Send mail 
    if ($this->email->send())
      return true;
    else
      return $this->email->print_debugger();
  }

  public function send_mail_next_approval($id, $user_id)
  {
    $item_message = '<tbody>';

    $recipient = array();
    $this->db->select(
      array(
        'tb_inventory_purchase_requisitions.pr_number',
        'tb_inventory_purchase_requisition_details.product_name',
        'tb_inventory_purchase_requisition_details.part_number',
        'tb_inventory_purchase_requisition_details.quantity',
        'tb_inventory_purchase_requisition_details.total',
        'tb_inventory_purchase_requisition_details.unit',
      )
    );
    $this->db->from('tb_inventory_purchase_requisition_details');
    $this->db->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisitions.id=tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
    $this->db->where_in('tb_inventory_purchase_requisition_details.id', $id);
    $query = $this->db->get();
    $row = $query->result_array();

    foreach ($row as $item) {
      $item_message .= "<tr>";
      $item_message .= "<td>" . $item['pr_number'] . "</td>";
      $item_message .= "<td>" . $item['part_number'] . "</td>";
      $item_message .= "<td>" . $item['product_name'] . "</td>";
      $item_message .= "<td>" . print_number($item['quantity'], 2) . "</td>";
      $item_message .= "<td>" . $item['unit'] . "</td>";
      //$item_message .= "<td>".print_number($item['total'],2)."</td>";         
      $item_message .= "</tr>";
    }
    $item_message .= '</tbody>';

    // $this->db->select('tb_inventory_purchase_requisitions.created_by,tb_auth_users.email');
    // $this->db->from('tb_inventory_purchase_requisitions');
    // $this->db->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
    // $this->db->join('tb_auth_users', 'tb_inventory_purchase_requisitions.created_by = tb_auth_users.person_name');
    // $this->db->group_by('tb_inventory_purchase_requisitions.created_by,tb_auth_users.email');
    // $this->db->where_in('tb_inventory_purchase_requisition_details.id', $id);
    // $query_po = $this->db->get();
    // $row_po   = $query_po->result_array();
    // foreach ($row_po as $key) {
    //   array_push($recipient, $key['email']);
    // }
    $recipientList = $this->getNotifRecipient($user_id);
    $recipient = array();
    foreach ($recipientList as $key) {
      array_push($recipient, $key->email);
    }



    $from_email = "bifa.acd@gmail.com";
    $to_email = "aidanurul99@rocketmail.com";
    // if ($ket == 'approve') {
    //   $ket_level = 'Disetujui';
    // } else {
    //   $ket_level = 'Ditolak';
    // }
    // if($level==14){
    //   $ket_level = 'Finance Manager';
    // }elseif ($level==10) {
    //   $ket_level = 'Head Of School';
    // } elseif($level==11){
    //   $ket_level = 'Chief Of Finance';
    // }elseif($level==3){
    //   $ket_level = 'VP Finance';
    // }

    //Load email library 
    $this->load->library('email');
    // $config = array();
    // $config['protocol'] = 'mail';
    // $config['smtp_host'] = 'smtp.live.com';
    // $config['smtp_user'] = 'bifa.acd@gmail.com';
    // $config['smtp_pass'] = 'b1f42019';
    // $config['smtp_port'] = 587;
    // $config['smtp_auth']        = true;
    // $config['mailtype']         = 'html';
    // $this->email->initialize($config);
    $this->email->set_newline("\r\n");
    $message = "<p>Hello</p>";
    $message .= "<p>Item Berikut perlu persetujuan</p>";
    $message .= "<table>";
    $message .= "<thead>";
    $message .= "<tr>";
    $message .= "<th>No. Doc.</th>";
    $message .= "<th>Part Number</th>";
    $message .= "<th>Description</th>";
    $message .= "<th>Qty Order</th>";
    $message .= "<th>Unit</th>";
    $message .= "<th>Total Val. Order</th>";
    $message .= "</tr>";
    $message .= "</thead>";
    $message .= $item_message;
    $message .= "</table>";
    // $message .= "<p>No Purchase Request : ".$row['document_number']."</p>";    
    $message .= "<p>Silakan klik link dibawah ini untuk menuju list permintaan</p>";
    $message .= "<p>[ <a href='http://119.2.51.138:7323/purchase_request/' style='color:blue; font-weight:bold;'>Material Resource Planning</a> ]</p>";
    $message .= "<p>Thanks and regards</p>";
    $this->email->from($from_email, 'Material Resource Planning');
    $this->email->to($recipient);
    $this->email->subject('Notification Approval');
    $this->email->message($message);

    //Send mail 
    if ($this->email->send())
      return true;
    else
      return $this->email->print_debugger();
  }

  public function getNotifRecipient_approval($name)
  {
    $this->db->select('email');
    $this->db->from('tb_auth_users');
    $this->db->where('person_name', $name);
    return $this->db->get('')->result();
  }

  public function info_on_hand($prl_item_id)
  {
    $select_prl_item = array(
      'tb_inventory_purchase_requisition_details.part_number',
      'tb_inventory_purchase_requisition_details.product_name',
      'tb_master_items.unit',
      'tb_inventory_purchase_requisitions.pr_number',
      'tb_master_items.minimum_quantity',
      // 'tb_purchase_order_items.ttd_issued_by'
    );
    $this->db->select($select_prl_item);
    $this->db->from('tb_inventory_purchase_requisition_details');
    $this->db->join('tb_budget', 'tb_budget.id = tb_inventory_purchase_requisition_details.budget_id', 'left');
    $this->db->join('tb_budget_cot', 'tb_budget_cot.id = tb_budget.id_cot', 'left');
    $this->db->join('tb_master_items', 'tb_master_items.id = tb_budget_cot.id_item', 'left');
    $this->db->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id');
    $this->db->where('tb_inventory_purchase_requisition_details.id', $prl_item_id);
    $query  = $this->db->get();
    $prl_item    = $query->unbuffered_row('array');

    $select = array(
      'tb_purchase_request_items_on_hand_stock.*',
      // 'tb_po_item.purchase_request_number',
      // 'tb_purchase_order_items.ttd_issued_by'
    );

    $this->db->select($select);
    $this->db->from('tb_purchase_request_items_on_hand_stock');
    $this->db->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.id = tb_purchase_request_items_on_hand_stock.prl_item_id', 'left');
    $this->db->where('tb_purchase_request_items_on_hand_stock.prl_item_id', $prl_item_id);
    $query = $this->db->get();
    $prl_item['items_count'] = $query->num_rows();

    foreach ($query->result_array() as $key => $value) {
      $prl_item['items'][$key] = $value;
      $prl_item['items'][$key]['unit'] = $prl_item['unit'];
    }


    return $prl_item;
  }
}
