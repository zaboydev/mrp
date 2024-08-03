<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_Order_Evaluation_Old_Model extends MY_Model
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
        $this->table        = config_item('module')['purchase_order_evaluation']['table'];
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

    public function find_requests()
    {
        $this->connection->select('ipr.*, pc.category_name');
        $this->connection->from('tb_stocks_purchase_requisitions ipr');
        $this->connection->join('tb_product_categories pc', 'pc.id = ipr.product_category_id');
        $this->connection->where_in('ipr.product_category_id', $this->category_id);
        $this->connection->where_not_in('ipr.id', $this->find_ordered_request());
        $this->connection->where('ipr.status', 'approved');
        $query = $this->connection->get();

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
        if (isset($_SESSION['poe']['vendor'])){
            foreach ($_SESSION['poe']['vendor'] as $key => $value){
                $vendor_ids[] = $value['vendor_id'];
            }

            $this->db->where_not_in('v.id', $vendor_ids);
        }

        $this->db->select('v.*');
        $this->db->from('tb_master_vendors v');

        $query = $this->db->get();

        return $query->result();
    }

    public function find_vendor_by_id($id)
    {
        $this->db->select('v.vendor_name');
        $this->db->from('tb_master_vendors v');
        $this->db->where('v.id', $id);

        $query = $this->db->get();
        $row   = $query->row();

        return $row->vendor_name;
    }

    public function find_budgets_by_group($group_id = NULL)
    {
        if ($group_id === NULL)
            $group_id = $_SESSION['request']['group_id'];

        $this->connection->select('imb.*, p.product_name, p.product_code, p.part_number, pm.measurement_symbol, ppp.current_price');
        $this->connection->from('tb_stocks_monthly_budgets imb');
        $this->connection->join('tb_products p', 'p.id = imb.product_id');
        $this->connection->join('tb_product_measurements pm', 'pm.id = p.product_measurement_id');
        $this->connection->join('tb_product_purchase_prices ppp', 'ppp.product_id = p.id');
        $this->connection->where('p.product_group_id', $group_id);
        $this->connection->where('imb.year_number', $this->active_year);
        $this->connection->where('imb.month_number', $this->active_month);

        if (isset($_SESSION['request']['detail']) && empty($_SESSION['request']['detail']) === FALSE){
            $items = $_SESSION['request']['detail'];

            foreach ($items as $key => $value)
                $ids[] = $key;

            $this->connection->where_not_in('imb.id', $ids);
        }

        $query = $this->connection->get();

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
        $this->db->from('tb_purchase_order_evaluations poe');
        $this->db->select('poe.*');
        $this->db->where('poe.poe_no', $id);
        $query = $this->db->get();
        $entity = $query->row_array();

        $this->db->from('tb_purchase_order_evaluation_vendors poev');
        $this->db->select('poev.*');
        $this->db->where('poev.poe_no', $id);
        $query = $this->db->get();
        $entity['vendor'] = $query->result_array();

        $this->db->from('tb_purchase_order_evaluation_requests poer');
        $this->db->select('poer.*');
        $this->db->where('poer.poe_no', $id);
        $query = $this->db->get();
        $entity['request'] = $query->result_array();

        foreach ($entity['request'] as $r => $request){
            $this->db->from('tb_purchase_order_evaluation_request_items poeri');
            $this->db->select('poeri.*');
            $this->db->where('poeri.poe_request_id', $request['id']);
            $query = $this->db->get();
            $entity['request'][$r]['item'] = $query->result_array();

            foreach ($entity['request'][$r]['item'] as $i => $item){
                $this->db->from('tb_purchase_order_evaluation_request_item_vendor poeriv');
                $this->db->select('poeriv.*');
                $this->db->where('poeriv.poe_request_item_id', $item['id']);
                $query = $this->db->get();
                $entity['request'][$r]['item'][$i]['vendor'] = $query->result_array();
            }
        }

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
            'reference' => $_SESSION['poe']['reference'],
            'notes'     => $_SESSION['poe']['notes'],
       );

        $this->db->trans_begin();

        if ($this->input->post('id') == NULL){
            $id = (string)$this->find_last_number();

            $entity['poe_no']       = $id;
            $entity['poe_date']     = date('Y-m-d');
            $entity['created_at']   = $this->datetime;
            $entity['created_by']   = config_item('auth_person_name');

            $this->db->set($entity)
                ->insert(config_item('module')['purchase_order_evaluation']['table']);
        } else {
            $id = $this->input->post('id');

            $this->db->set($entity)
                ->where('poe_no', $id)
                ->update(config_item('module')['purchase_order_evaluation']['table']);
        }

        // delete old details
        $this->db->where('poe_no', $id);
        $this->db->delete('tb_purchase_order_evaluation_requests');

        $this->db->where('poe_no', $id);
        $this->db->delete('tb_purchase_order_evaluation_vendors');

        foreach ($_SESSION['poe']['vendor'] as $v => $vendor){
            $vendor_entity['poe_no']        = $id;
            $vendor_entity['vendor_id']     = $vendor['vendor_id'];
            $vendor_entity['vendor_name']   = $vendor['vendor_name'];

            $this->db->set($vendor_entity)
                ->insert('tb_purchase_order_evaluation_vendors');
        }

        foreach ($_SESSION['poe']['request'] as $p => $pr){
            $pr_entity['poe_no']    = $id;
            $pr_entity['pr_id']     = $pr['pr_id'];
            $pr_entity['pr_number'] = $pr['pr_number'];

            $this->db->set($pr_entity)
                ->insert('tb_purchase_order_evaluation_requests');

            $poe_request_id = $this->db->insert_id('tb_purchase_order_evaluation_requests_id_seq');

            foreach ($pr['item'] as $i => $item){
                $request_item_entity['poe_request_id']      = $poe_request_id;
                $request_item_entity['imb_id']              = $item['imb_id'];
                $request_item_entity['item_name']           = $item['item_name'];
                $request_item_entity['item_code']           = $item['item_code'];
                $request_item_entity['item_part_number']    = $item['item_part_number'];
                $request_item_entity['item_alternate_part_number']= $item['item_alternate_part_number'];
                $request_item_entity['item_quantity']       = $item['item_quantity'];
                $request_item_entity['notes']              = $item['notes'];

                $this->db->set($request_item_entity)
                    ->insert('tb_purchase_order_evaluation_request_items');

                $poe_request_item_id = $this->db->insert_id('tb_purchase_order_evaluation_request_items_id_seq');

                foreach ($item['vendor'] as $v => $vendor){
                    $item_vendor_entity['poe_request_item_id']  = $poe_request_item_id;
                    $item_vendor_entity['vendor_id']            = $vendor['vendor_id'];
                    $item_vendor_entity['unit_price']           = $vendor['unit_price'];
                    $item_vendor_entity['core_charge']          = $vendor['core_charge'];
                    $item_vendor_entity['selected']             = $vendor['selected'];

                    $this->db->set($item_vendor_entity)
                        ->insert('tb_purchase_order_evaluation_request_item_vendor');
                }
            }
        }

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();

        return TRUE;
    }
}
