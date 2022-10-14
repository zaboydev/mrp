<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Internal_Delivery_Shipping_Model extends MY_Model
{
    public function __construct()

    {
        parent::__construct();
    }

    public function getSelectedColumns()
    {
        $selected = array(
            'tb_returns.id'                       => NULL,
            'tb_returns.document_number'          => 'Document Number',
            'tb_returns.status'                   => 'Status',
            'tb_returns.issued_date'              => 'Date',
            'tb_returns.category'                 => 'Category',
            'tb_returns.warehouse'                => 'Base',
            'tb_return_items.description'         => 'Description',
            'tb_return_items.part_number'         => 'Part Number',
            'tb_return_items.serial_number'       => 'Serial Number',
            'tb_return_items.condition'           => 'Condition',
            'tb_return_items.issued_quantity'     => 'Quantity',
            'tb_return_items.unit'                => 'Unit',
            'tb_return_items.awb_number'          => 'AWB Number',
            'tb_return_items.remarks'             => 'Remarks',
            'tb_returns.issued_to'                => 'Sent To',
            'tb_returns.issued_by'                => 'Released By',
            // 'tb_return_items.received_from'            => 'Received From',
        );

        if (config_item('auth_role') != 'PIC STOCK'){
            $selected['tb_return_items.issued_unit_value']  = 'Value';
            $selected['tb_return_items.issued_total_value'] = 'Total Value IDR';
        }

        return $selected;
    }

    public function getSearchableColumns()
    {
        return array(
            'tb_returns.document_number',
            'tb_returns.category',
            'tb_returns.warehouse',
            'tb_return_items.description',
            'tb_return_items.part_number',
            'tb_return_items.serial_number',
            'tb_return_items.condition',
            'tb_return_items.unit',
            'tb_return_items.awb_number',
            'tb_return_items.remarks',
            'tb_returns.issued_to',
            'tb_returns.issued_by',
            // 'tb_returns.received_from'
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            'tb_returns.document_number',
            'tb_returns.category',
            'tb_returns.warehouse',
            'tb_return_items.description',
            'tb_return_items.part_number',
            'tb_return_items.serial_number',
            'tb_return_items.condition',
            'tb_return_items.unit',
            'tb_return_items.awb_number',
            'tb_return_items.remarks',
            'tb_returns.issued_to',
            'tb_returns.issued_by',
            // 'tb_returns.received_from'
        );
    }

    private function searchIndex()
    {
        if (!empty($_POST['columns'][2]['search']['value'])){
            $search_issued_date = $_POST['columns'][2]['search']['value'];
            $range_issued_date  = explode(' ', $search_issued_date);

            $this->db->where('tb_returns.issued_date >= ', $range_issued_date[0]);
            $this->db->where('tb_returns.issued_date <= ', $range_issued_date[1]);
        }

        $i = 0;

        foreach ($this->getSearchableColumns() as $item){
            if ($_POST['search']['value']){
                $term = strtoupper($_POST['search']['value']);

                if ($i === 0){
                    $this->db->group_start();
                    $this->db->like('UPPER('.$item.')', $term);
                } else {
                    $this->db->or_like('UPPER('.$item.')', $term);
                }

                if (count($this->getSearchableColumns()) - 1 == $i)
                    $this->db->group_end();
            }

        $i++;
        }
    }

    function getIndex($return = 'array')
    {
        $this->db->select(array_keys($this->getSelectedColumns()));
        $this->db->from('tb_returns');
        $this->db->join('tb_return_items', 'tb_return_items.return_id = tb_returns.id');
        $this->db->where_in('tb_returns.category', config_item('auth_inventory'));
        $this->db->where_in('tb_returns.warehouse', config_item('auth_warehouses'));
        $this->db->like('tb_returns.document_number', 'SID');

        $this->searchIndex();

        $column_order = $this->getOrderableColumns();

        if (isset($_POST['order'])){
            foreach ($_POST['order'] as $key => $order){
                $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
            }
        } else {
            $this->db->order_by('id', 'desc');
        }

        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);

        $query = $this->db->get();

        if ($return === 'object'){
            return $query->result();
        } elseif ($return === 'json'){
            return json_encode($query->result());
        } else {
            return $query->result_array();
        }
    }

    function countIndexFiltered()
    {
        $this->db->from('tb_returns');
        $this->db->join('tb_return_items', 'tb_return_items.return_id = tb_returns.id');
        $this->db->where_in('tb_returns.category', config_item('auth_inventory'));
        $this->db->where_in('tb_returns.warehouse', config_item('auth_warehouses'));
        $this->db->like('tb_returns.document_number', 'SID');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->from('tb_returns');
        $this->db->join('tb_return_items', 'tb_return_items.return_id = tb_returns.id');
        $this->db->where_in('tb_returns.category', config_item('auth_inventory'));
        $this->db->where_in('tb_returns.warehouse', config_item('auth_warehouses'));
        $this->db->like('tb_returns.document_number', 'SID');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->db->where('id', $id);

        $query    = $this->db->get('tb_returns');
        $issued   = $query->unbuffered_row('array');

        $select = array(
            'tb_return_items.*'
        );

        $this->db->select($select);
        $this->db->from('tb_return_items');
        $this->db->where('tb_return_items.return_id', $issued['id']);

        $query = $this->db->get();
        $left_received_quantity = array();

        foreach ($query->result_array() as $key => $value){
            $issued['items'][$key] = $value;
            $left_received_quantity[] = $value['left_process_quantity'];
            if (empty($issued['category'])){
                $this->db->select('category');
                $this->db->from('tb_master_item_groups');
                $this->db->where('group', $value['group']);

                $query = $this->db->get();
                $icat  = $query->unbuffered_row();

                $issued['category'] = $icat->category;
            }
        }
        $issued['left_process_quantity'] = array_sum($left_received_quantity);

        return $issued;
    }

    public function isDocumentNumberExists($document_number)
    {
        $this->db->where('document_number', $document_number);
        $query = $this->db->get('tb_returns');

        if ($query->num_rows() > 0)
            return true;

        return false;
    }

    public function isValidDocumentQuantity($document_number)
    {
        $this->db->select_sum('tb_receipt_items.received_quantity', 'received_quantity');
        $this->db->select_sum('tb_stock_in_stores.quantity', 'stored_quantity');
        $this->db->select('tb_receipt_items.document_number');
        $this->db->from('tb_receipt_items');
        $this->db->join('tb_stock_in_stores', 'tb_stock_in_stores.id = tb_receipt_items.stock_in_stores_id');
        $this->db->where('tb_receipt_items.document_number', $document_number);
        $this->db->group_by('tb_receipt_items.document_number');

        $query  = $this->db->get();
        $row    = $query->unbuffered_row('array');

        if ($row['received_quantity'] === $row['received_quantity'])
        return true;

        return false;
    }

    public function save()
    {
        $document_id      = (isset($_SESSION['shipping_internal']['id'])) ? $_SESSION['shipping_internal']['id'] : NULL;
        $document_edit    = (isset($_SESSION['shipping_internal']['edit'])) ? $_SESSION['shipping_internal']['edit'] : NULL;
        $document_number  = $_SESSION['shipping_internal']['document_number'] . shipping_delivery_format_number();
        $issued_date      = $_SESSION['shipping_internal']['issued_date'];
        $source           = $_SESSION['shipping_internal']['source'];
        $issued_by        = (empty($_SESSION['shipping_internal']['issued_by'])) ? NULL : $_SESSION['shipping_internal']['issued_by'];
        $issued_to        = (empty($_SESSION['shipping_internal']['issued_to'])) ? NULL : $_SESSION['shipping_internal']['issued_to'];
        $issued_address   = (empty($_SESSION['shipping_internal']['issued_address'])) ? NULL : $_SESSION['shipping_internal']['issued_address'];
        $sent_by          = (empty($_SESSION['shipping_internal']['sent_by'])) ? NULL : $_SESSION['shipping_internal']['sent_by'];
        $known_by         = (empty($_SESSION['shipping_internal']['known_by'])) ? NULL : $_SESSION['shipping_internal']['known_by'];
        $approved_by      = (empty($_SESSION['shipping_internal']['approved_by'])) ? NULL : $_SESSION['shipping_internal']['approved_by'];
        $warehouse        = $_SESSION['shipping_internal']['warehouse'];
        $category         = $_SESSION['shipping_internal']['category'];
        $notes            = (empty($_SESSION['shipping_internal']['notes'])) ? NULL : $_SESSION['shipping_internal']['notes'];

        $this->db->trans_begin();

        if ($document_id === NULL){
            $this->db->set('document_number', $document_number);
            $this->db->set('issued_to', $issued_to);
            $this->db->set('issued_address', $issued_address);
            $this->db->set('issued_date', $issued_date);
            $this->db->set('issued_by', $issued_by);
            $this->db->set('sent_by', $sent_by);
            $this->db->set('known_by', $known_by);
            $this->db->set('approved_by', $approved_by);
            $this->db->set('category', $category);
            $this->db->set('warehouse', $warehouse);
            $this->db->set('notes', $notes);
            $this->db->set('source', $source);
            $this->db->set('status', 'APPROVED');
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_returns');
            $document_id = $this->db->insert_id();
        } else {
            /**
             * EDIT DOCUMENT
             * decrease quantity
             * create document revision
             */

            // if($source=='stock'){
                
            // }

            $this->db->select('tb_returns.*');
            $this->db->where('id', $document_id);
            $this->db->from('tb_returns');

            $query = $this->db->get();
            $row   = $query->unbuffered_row('array');

            $old_document_number  = $row['document_number'];
            $old_warehouse        = $row['warehouse'];

            $this->db->select('tb_return_items.*');
            $this->db->from('tb_return_items');
            $this->db->where('tb_return_items.return_id', $document_id);

            $query = $this->db->get();

            foreach ($query->result_array() as $data) {
                if ($data['internal_delivery_item_id'] != null) {
                    $this->db->where('id', $data['internal_delivery_item_id']);
                    $this->db->set('left_received_quantity', 'left_received_quantity +' . $data['issued_quantity'], FALSE);
                    $this->db->update('tb_internal_delivery_items');
                }
                
            }

            /**
             * CREATE RETURN DOCUMENT
             */
            $this->db->set('document_number', $document_number);
            $this->db->set('issued_date', $issued_date);
            $this->db->set('issued_to', $issued_to);
            $this->db->set('issued_address', $issued_address);
            $this->db->set('issued_by', $issued_by);
            $this->db->set('known_by', $known_by);
            $this->db->set('approved_by', $approved_by);
            $this->db->set('warehouse', $warehouse);
            $this->db->set('category', $category);
            $this->db->set('notes', $notes);
            $this->db->set('source', $source);
            $this->db->set('status', 'APPROVED');
            $this->db->set('updated_at', date('Y-m-d'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->where('id', $document_id);
            $this->db->update('tb_returns');

            /**
             * DELETE OLD RETURN ITEMS
             */
            $this->db->where('return_id', $document_id);
            $this->db->delete('tb_return_items');

            /**
             * DELETE OLD STOCK
             */
            // $this->db->where('reference_document', $document_edit);
            // $this->db->delete('tb_stock_in_stores');
        }

        /**
         * PROCESSING RETURN ITEMS
         */
        foreach ($_SESSION['shipping_internal']['items'] as $key => $data){
            if (!empty($data['internal_delivery_item_id'])) {
                $this->db->from('tb_internal_delivery_items');
                $this->db->where('tb_internal_delivery_items.id', $data['internal_delivery_item_id']);
        
                $query  = $this->db->get();
                $row    = $query->unbuffered_row('array');
                $qty    = floatval($row['left_received_quantity']) - floatval($data['issued_quantity']);
        
                $this->db->where('id', $data['internal_delivery_item_id']);
                $this->db->set('left_received_quantity', 'left_received_quantity -' . $data['issued_quantity'], FALSE);
                $this->db->update('tb_internal_delivery_items');
        
                $left_qty_internal_delivery = countLeftQuantityInternalDelivery($row['internal_delivery_id']);
                if ($left_qty_internal_delivery == 0) {
                    $this->db->where('id', $row['internal_delivery_id']);
                    $this->db->set('status', 'CLOSED');
                    $this->db->update('tb_internal_delivery');
                }
            }

            /**
                * INSERT INTO RETURN ITEMS
            */
            $this->db->set('return_id', $document_id);
            if(!empty($data['stock_in_stores_id'])){
                $this->db->set('stock_in_stores_id', $data['stock_in_stores_id']);
            }
            if(!empty($data['internal_delivery_item_id'])){
                $this->db->set('internal_delivery_item_id', $data['internal_delivery_item_id']);
            }            
            $this->db->set('part_number', strtoupper($data['part_number']));
            $this->db->set('serial_number', strtoupper($data['serial_number']));
            $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
            $this->db->set('description', strtoupper($data['description']));
            $this->db->set('group', strtoupper($data['group']));
            $this->db->set('unit', strtoupper($data['unit']));
            $this->db->set('stores', strtoupper($data['stores']));
            $this->db->set('issued_quantity', floatval($data['issued_quantity']));
            $this->db->set('left_process_quantity', floatval($data['issued_quantity']));
            $this->db->set('issued_unit_value', floatval($data['issued_unit_value']));
            $this->db->set('issued_total_value', floatval($data['issued_unit_value']) * floatval($data['issued_quantity']));
            $this->db->set('insurance_unit_value', floatval($data['insurance_unit_value']));
            $this->db->set('insurance_currency', $data['insurance_currency']);
            $this->db->set('awb_number', $data['awb_number']);
            $this->db->set('remarks', $data['remarks']);
            $this->db->set('condition', $data['condition']);
            $this->db->set('received_from', $data['received_from']);
            $this->db->insert('tb_return_items');
        
        }

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function delete()
    {
        $this->db->trans_begin();

        $id = $this->input->post('id');

        $this->db->select('tb_returns.*');
        $this->db->where('id', $id);
        $this->db->from('tb_returns');

        $query = $this->db->get();
        $row   = $query->unbuffered_row('array');

        $old_document_number  = $row['document_number'];
        $old_warehouse        = $row['warehouse'];

        $this->db->select('tb_return_items.*');
        $this->db->from('tb_return_items');
        $this->db->where('tb_return_items.return_id', $id);

        $query = $this->db->get();

        foreach ($query->result_array() as $data) {
            if ($data['internal_delivery_item_id'] != null) {
                $this->db->where('id', $data['internal_delivery_item_id']);
                $this->db->set('left_received_quantity', 'left_received_quantity +' . $data['issued_quantity'], FALSE);
                $this->db->update('tb_internal_delivery_items');
            }                
        }            

        /**
            * DELETE OLD RETURN ITEMS
        */

        $this->db->where('return_id', $document_id);
        $this->db->delete('tb_return_items');

        $this->db->where('id', $id);
        $this->db->delete('tb_returns');

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function searchItemsBySerial($category)
    {
        $this->column_select = array(
        'tb_master_items.serial_number',
        'tb_master_items.id',
        'tb_master_items.group',
        'tb_master_items.description',
        'tb_master_items.part_number',
        'tb_master_items.alternate_part_number',
        'tb_master_items.minimum_quantity',
        'tb_master_items.unit'
        );

        $this->db->select($this->column_select);
        $this->db->from('tb_master_items');
        $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
        $this->db->join('tb_stocks', 'tb_stocks.item_id = tb_master_items.id');
        // $this->db->where('tb_stocks.condition', 'SERVICEABLE');
        $this->db->where('tb_master_items.serial_number IS NOT NULL', NULL, FALSE);
        $this->db->where('tb_stocks.total_quantity', 0);
        $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
        $this->db->where('tb_master_item_groups.category', $category);

        $this->db->order_by('tb_master_items.serial_number ASC');

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
        'tb_master_items.alternate_part_number',
        'tb_master_items.minimum_quantity',
        'tb_master_items.unit',
        'tb_master_items.serial_number',
        );

        $this->db->select($this->column_select);
        $this->db->from('tb_master_items');
        $this->db->join('tb_master_item_groups', 'tb_master_item_groups.group = tb_master_items.group');
        $this->db->where('tb_master_item_groups.status', 'AVAILABLE');
        $this->db->where('tb_master_item_groups.category', $category);

        $this->db->order_by('tb_master_items.group ASC, tb_master_items.description ASC');

        $query  = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function getSelectedColumnsReceipt()
    {
        $return = array(
            'tb_internal_delivery.id'                          => NULL,
            'tb_internal_delivery.document_number'             => 'Document Number',
            'tb_internal_delivery.received_date'               => 'Received Date',
            'tb_internal_delivery.status'                      => 'Status',
            'tb_internal_delivery.category'                    => 'Category',
            'tb_internal_delivery.warehouse'                   => 'Base',
            'tb_internal_delivery_items.description'           => 'Description',
            'tb_internal_delivery_items.part_number'           => 'Part Number',
            'tb_internal_delivery_items.alternate_part_number' => 'Alt. Part Number',
            'tb_internal_delivery_items.serial_number'         => 'Serial Number',
            'tb_internal_delivery_items.condition'             => 'Condition',
            'tb_internal_delivery_items.quantity'              => 'Quantity',
            'tb_internal_delivery_items.unit'                  => 'Unit',
            'tb_internal_delivery_items.remarks'               => 'Remarks',
            'tb_internal_delivery.received_from'               => 'Received From',
            'tb_internal_delivery.received_by'                 => 'Received By',
            'tb_internal_delivery.sent_by'                     => 'Sent By',
        );

        if (config_item('auth_role') != 'PIC STOCK'){
            $return['tb_internal_delivery_items.unit_price']  = 'Value';
            $return['tb_internal_delivery_items.total_amount'] = 'Total Value';
        }

        return $return;
    }

    public function getSearchableColumnsReceipt()
    {
        $return = array(
            'tb_internal_delivery.document_number',
            'tb_internal_delivery.status',
            'tb_internal_delivery.category',
            'tb_internal_delivery.warehouse',
            'tb_internal_delivery.category',
            'tb_internal_delivery.warehouse',
            'tb_internal_delivery_items.description',
            'tb_internal_delivery_items.part_number',
            'tb_internal_delivery_items.alternate_part_number',
            'tb_internal_delivery_items.serial_number',
            'tb_internal_delivery_items.condition',
            'tb_internal_delivery_items.unit',
            'tb_internal_delivery_items.remarks',
            'tb_internal_delivery.received_from',
            'tb_internal_delivery.received_by',
            'tb_internal_delivery.sent_by',
        );

        return $return;
    }

    public function getOrderableColumnsReceipt()
    {
        $return = array(
            null,
            'tb_internal_delivery.document_number',
            'tb_internal_delivery.received_date',
            'tb_internal_delivery.status',
            'tb_internal_delivery.category',
            'tb_internal_delivery.warehouse',
            'tb_internal_delivery.category',
            'tb_internal_delivery.warehouse',
            'tb_internal_delivery_items.description',
            'tb_internal_delivery_items.part_number',
            'tb_internal_delivery_items.alternate_part_number',
            'tb_internal_delivery_items.serial_number',
            'tb_internal_delivery_items.condition',
            'tb_internal_delivery_items.unit',
            'tb_internal_delivery_items.remarks',
            'tb_internal_delivery.received_from',
            'tb_internal_delivery.received_by',
            'tb_internal_delivery.sent_by',
        );

        if (config_item('auth_role') != 'PIC STOCK'){
            $return[] = 'tb_internal_delivery_items.unit_price';
            $return[] = 'tb_internal_delivery_items.total_amount';
        }

        return $return;
    }

    private function searchIndexReceipt()
    {
        if (!empty($_POST['columns'][2]['search']['value'])){
            $search_received_date = $_POST['columns'][2]['search']['value'];
            $range_received_date  = explode(' ', $search_received_date);

            $this->db->where('tb_internal_delivery.received_date >= ', $range_received_date[0]);
            $this->db->where('tb_internal_delivery.received_date <= ', $range_received_date[1]);
        }

        $i = 0;

        foreach ($this->getSearchableColumns() as $item){
            if ($_POST['search']['value']){
                $term = strtoupper($_POST['search']['value']);

                if ($i === 0){
                $this->db->group_start();
                $this->db->like('UPPER('.$item.')', $term);
                } else {
                $this->db->or_like('UPPER('.$item.')', $term);
                }

                if (count($this->getSearchableColumns()) - 1 == $i)
                $this->db->group_end();
            }

            $i++;
        }
    }

    function getIndexReceipt($return = 'array')
    {
        $this->db->select(array_keys($this->getSelectedColumnsReceipt()));
        $this->db->from('tb_internal_delivery');
        $this->db->join('tb_internal_delivery_items', 'tb_internal_delivery_items.internal_delivery_id = tb_internal_delivery.id');
        $this->db->where_in('tb_internal_delivery.category', config_item('auth_inventory'));
        $this->db->where_in('tb_internal_delivery.warehouse', config_item('auth_warehouses'));
        $this->db->like('tb_internal_delivery.document_number', 'SID');

        $this->searchIndexReceipt();

        $column_order = $this->getOrderableColumnsReceipt();

        if (isset($_POST['order'])){
        foreach ($_POST['order'] as $key => $order){
            $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
        }
        } else {
        $this->db->order_by('tb_internal_delivery.received_date', 'asc');
        }

        if ($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);

        $query = $this->db->get();

        if ($return === 'object'){
        return $query->result();
        } elseif ($return === 'json'){
        return json_encode($query->result());
        } else {
        return $query->result_array();
        }
    }

    function countIndexFilteredReceipt()
    {
        $this->db->from('tb_internal_delivery');
        $this->db->join('tb_internal_delivery_items', 'tb_internal_delivery_items.internal_delivery_id = tb_internal_delivery.id');
        $this->db->where_in('tb_internal_delivery.category', config_item('auth_inventory'));
        $this->db->where_in('tb_internal_delivery.warehouse', config_item('auth_warehouses'));
        $this->db->like('tb_internal_delivery.document_number', 'SID');

        $this->searchIndexReceipt();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndexReceipt()
    {
        $this->db->from('tb_internal_delivery');
        $this->db->join('tb_internal_delivery_items', 'tb_internal_delivery_items.internal_delivery_id = tb_internal_delivery.id');
        $this->db->where_in('tb_internal_delivery.category', config_item('auth_inventory'));
        $this->db->where_in('tb_internal_delivery.warehouse', config_item('auth_warehouses'));
        $this->db->like('tb_internal_delivery.document_number', 'SID');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function receipt()
    {
        $this->db->trans_begin();

        $id = $this->input->post('id');

        $this->db->set('received_by', config_item('auth_username'));
        $this->db->set('received_date', date('Y-m-d'));
        $this->db->set('status','RECEIVED');
        $this->db->where('id', $id);
        $this->db->update('tb_internal_delivery');

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function searchInternalDeliveryItem($category)
    {
        $this->column_select = array(
            'tb_internal_delivery_items.id',
            'tb_internal_delivery_items.unit_price as unit_value',
            'tb_internal_delivery_items.serial_number',
            'tb_internal_delivery_items.part_number',
            'tb_internal_delivery_items.description',
            'tb_internal_delivery_items.alternate_part_number',
            'tb_internal_delivery_items.left_received_quantity as quantity',
            'tb_internal_delivery_items.unit',
            'tb_internal_delivery.received_from',
            'tb_internal_delivery.received_date',
            'tb_internal_delivery.document_number',
            'tb_internal_delivery_items.group',
            'tb_internal_delivery_items.unit as unit_pakai',
            'tb_internal_delivery_items.condition',
        );

        $this->db->select($this->column_select);
        $this->db->from('tb_internal_delivery_items');
        $this->db->join('tb_internal_delivery', 'tb_internal_delivery.id = tb_internal_delivery_items.internal_delivery_id');
        $this->db->where('tb_internal_delivery.category', $category);
        $this->db->where('tb_internal_delivery.warehouse',$_SESSION['shipping_internal']['warehouse']);
        // $this->db->where_in('tb_internal_delivery.status', ['APPROVED']);
        $this->db->where('tb_internal_delivery_items.left_received_quantity > ', 0);
        $this->db->group_by(array(
            'tb_internal_delivery_items.id',
            'tb_internal_delivery_items.unit_price',
            'tb_internal_delivery_items.serial_number',
            'tb_internal_delivery_items.part_number',
            'tb_internal_delivery_items.description',
            'tb_internal_delivery_items.alternate_part_number',
            'tb_internal_delivery_items.left_received_quantity',
            'tb_internal_delivery_items.unit',
            'tb_internal_delivery.received_from',
            'tb_internal_delivery.document_number',
            'tb_internal_delivery_items.group',
            'tb_internal_delivery_items.condition',
            'tb_internal_delivery.received_date'
        ));

        $this->db->order_by('tb_internal_delivery.document_number ASC');
        $query  = $this->db->get();
        $result = $query->result_array();

        return $result;
        
    }

    public function infoSelecteditem($id)
    {
        $this->column_select = array(
            'tb_internal_delivery_items.id',
            'tb_internal_delivery_items.unit_price as unit_value',
            'tb_internal_delivery_items.serial_number',
            'tb_internal_delivery_items.part_number',
            'tb_internal_delivery_items.description',
            'tb_internal_delivery_items.alternate_part_number',
            'tb_internal_delivery_items.left_received_quantity as quantity',
            'tb_internal_delivery_items.unit',
            'tb_internal_delivery.received_from',
            'tb_internal_delivery.received_date',
            'tb_internal_delivery.document_number',
            'tb_internal_delivery_items.group',
            'tb_internal_delivery_items.unit as unit_pakai',
            'tb_internal_delivery_items.condition',
        );
    
        $this->db->select($this->column_select);
        $this->db->from('tb_internal_delivery_items');
        $this->db->join('tb_internal_delivery', 'tb_internal_delivery.id = tb_internal_delivery_items.internal_delivery_id');
        $this->db->where('tb_internal_delivery_items.id', $id);
        $this->db->group_by(array(
            'tb_internal_delivery_items.id',
            'tb_internal_delivery_items.unit_price',
            'tb_internal_delivery_items.serial_number',
            'tb_internal_delivery_items.part_number',
            'tb_internal_delivery_items.description',
            'tb_internal_delivery_items.alternate_part_number',
            'tb_internal_delivery_items.left_received_quantity',
            'tb_internal_delivery_items.unit',
            'tb_internal_delivery.received_from',
            'tb_internal_delivery.document_number',
            'tb_internal_delivery_items.group',
            'tb_internal_delivery_items.condition',
            'tb_internal_delivery.received_date'
        ));
    
        $this->db->order_by('tb_internal_delivery.document_number ASC');
        $query  = $this->db->get();
        $result = $query->unbuffered_row('array');
    

        return $result;
    
    }

    public function findStores($warehouse, $category)
    {
        $this->db->select('tb_master_stores.stores');
        $this->db->from('tb_master_stores');
        $this->db->where('UPPER(tb_master_stores.warehouse)', strtoupper($warehouse));
        $this->db->where_in('tb_master_stores.category', $category);
        $this->db->where('status', 'AVAILABLE');
        $this->db->order_by('stores', 'ASC');

        $query  = $this->db->get();
        $result = $query->result();

        $data  = array();

        foreach ($result as $row){
            if ($row->stores != null)
                $data[] = $row->stores;
        }

        return json_encode($data);
    }

    public function isDocumentReceiptExists($document_number)
    {
        $this->db->where('document_number', $document_number);
        $query = $this->db->get('tb_internal_delivery');

        if ($query->num_rows() < 1)
            return true;

        return false;
    }

    public function getDocumentId($document_number)
    {
        $this->db->where('document_number', $document_number);
        $query = $this->db->get('tb_internal_delivery');
        $row    = $query->unbuffered_row('array');

        return $row['id'];
    }

    public function save_receive($id)
    {
        $this->db->trans_begin();

        $category         = $this->input->post('category');
        $warehouse        = $this->input->post('warehouse');
        $document_number  = $this->input->post('document_number');
        $received_from    = $this->input->post('received_from');
        $received_date    = $this->input->post('received_date');
        $received_by      = $this->input->post('received_by');
        $notes              = $this->input->post('notes');

        $id_tb_issuances = $id;
        if ($this->model->isDocumentReceiptExists($document_number)){
            $this->db->set('document_number', $document_number);
            $this->db->set('received_from', $received_from);
            $this->db->set('received_date', $received_date);
            $this->db->set('sent_by', $sent_by);
            $this->db->set('received_by', $received_by);
            $this->db->set('category', $category);
            $this->db->set('warehouse', $warehouse);
            $this->db->set('send_to_warehouse', $warehouse);
            $this->db->set('notes', $notes);
            $this->db->set('status', 'APPROVED');
            $this->db->set('type', '1');
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_internal_delivery');
            $document_id = $this->db->insert_id();
        }else{
            $document_id = $this->model->getDocumentId($document_number);
        }

        

        foreach ($_POST['items'] as $id => $data){
            if ($data['received_quantity'] > 0) {
                $serial_number = (empty($data['serial_number'])) ? NULL : $data['serial_number'];
                $item_id = getItemId($data['part_number'], $data['description'], $serial_number);
                if (!empty($data['serial_number'])) {
                    $serial     = getSerial($item_id, $data['serial_number']);
                    $serial_id  = $serial->id;
                }else{
                    $serial_id = NULL;
                }
                

                /**
                 * CREATE STORES IF NOT EXISTS
                 */
                if (isStoresExists($data['stores']) === FALSE && isStoresExists($data['stores'], $category) === FALSE) {
                    $this->db->set('stores', strtoupper($data['stores']));
                    $this->db->set('warehouse', $warehouse);
                    $this->db->set('category', $category);
                    $this->db->set('created_by', config_item('auth_person_name'));
                    $this->db->set('updated_by', config_item('auth_person_name'));
                    $this->db->insert('tb_master_stores');
                }

                /**
                    * INSERT INTO DELIVERY ITEMS
                */
                $this->db->set('internal_delivery_id', $document_id);
                $this->db->set('part_number', strtoupper($data['part_number']));
                $this->db->set('serial_number', strtoupper($data['serial_number']));
                $this->db->set('alternate_part_number', strtoupper($data['alternate_part_number']));
                $this->db->set('description', strtoupper($data['description']));
                $this->db->set('group', strtoupper($data['group']));
                $this->db->set('minimum_quantity', floatval($data['minimum_quantity']));
                $this->db->set('unit', strtoupper($data['unit']));
                $this->db->set('stores', strtoupper($data['stores']));
                $this->db->set('condition', strtoupper($data['condition']));
                $this->db->set('quantity', floatval($data['received_quantity']));
                $this->db->set('left_received_quantity', floatval($data['received_quantity']));
                $this->db->set('unit_price', floatval($data['unit_price']));
                $this->db->set('total_amount', floatval($data['unit_price']) * floatval($data['received_quantity']));
                $this->db->set('remarks', $data['remarks']);
                $this->db->set('return_item_id', $data['return_item_id']);
                $this->db->insert('tb_internal_delivery_items');

                /**
                    * UPDATE RETURN ITEMS
                */
                $this->db->from('tb_return_items');
                $this->db->where('tb_return_items.id', $data['return_item_id']);
        
                $query  = $this->db->get();
                $row    = $query->unbuffered_row('array');
                $qty    = floatval($row['left_process_quantity']) - floatval($data['received_quantity']);
        
                $this->db->where('id', $data['return_item_id']);
                $this->db->set('left_process_quantity', 'left_process_quantity -' . $data['received_quantity'], FALSE);
                $this->db->update('tb_return_items');
        
                // $left_qty_return_item = countLeftQuantityReturnItem($row['return_id']);
                if (closeReturnDocument($row['return_id'])) {
                    $this->db->where('id', $row['return_id']);
                    $this->db->set('status', 'CLOSED');
                    $this->db->update('tb_returns');
                }
            }        
        }

        $left_qty = getLeftQty($document_number);
        if($left_qty ==0 ){
        $this->db->set('received_date', $received_date);
        $this->db->set('received_by', $received_by);
        $this->db->where('id', $id_tb_issuances);
        $this->db->update('tb_issuances'); 
        }

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function findByIdReceipt($id)
    {
        $this->db->where('id', $id);

        $query    = $this->db->get('tb_internal_delivery');
        $delivery = $query->unbuffered_row('array');

        $select = array(
            'tb_internal_delivery_items.*'
        );

        $this->db->select($select);
        $this->db->from('tb_internal_delivery_items');
        $this->db->where('tb_internal_delivery_items.internal_delivery_id', $id);

        $query = $this->db->get();

        foreach ($query->result_array() as $key => $value){
            $delivery['items'][$key] = $value;

            if (empty($delivery['category'])){
                $this->db->select('category');
                $this->db->from('tb_master_item_groups');
                $this->db->where('group', $value['group']);

                $query = $this->db->get();
                $icat  = $query->unbuffered_row();

                $delivery['category'] = $icat->category;
            }
        }

        return $delivery;
    }
}
