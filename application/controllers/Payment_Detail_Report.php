<?php defined('BASEPATH') or exit('No direct script access allowed');

class Payment_Detail_Report extends MY_Controller
{
    protected $module;
    protected $id_item = 0;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['payment_detail_report'];
        $this->load->helper($this->module['helper']);
        $this->load->model($this->module['model'], 'model');
        $this->load->library('upload');
        $this->load->helper('string');
        $this->data['module'] = $this->module;
    }

    public function index_data_source()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] . '/denied');

        if (is_granted($this->module, 'index') === FALSE) {
            $return['type'] = 'danger';
            $return['info'] = "You don't have permission to access this page!";
        } else {
            $entities = $this->model->getIndexDetailReport();
            $data     = array();
            $no       = $_POST['start'];
            $total_idr      = array();
            $total_usd      = array();

            foreach ($entities as $row) {
                $attachment = $this->model->checkAttachment($row['id']);
                $account = ($row['coa_kredit']!=NULL)?print_string($row['coa_kredit']).' '.print_string($row['akun_kredit']):'--select account--';
                $no++;
                $col = array();
                if (is_granted($this->module, 'approval') === TRUE) {
                if ($row['status'] == 'WAITING CHECK BY FIN SPV' && config_item('auth_role')=='FINANCE SUPERVISOR') {
                    $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                }else if ($row['status'] == 'WAITING REVIEW BY FIN MNG' && config_item('auth_role')=='FINANCE MANAGER') {
                    if(config_item('auth_warehouse')=='JAKARTA'){
                    if($row['base']=='JAKARTA'){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }else{
                        $col[] = print_number($no);
                    }
                    }else{
                    if($row['base']!='JAKARTA'){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    }else{
                        $col[] = print_number($no);
                    }
                    }
                }else if ($row['status'] == 'WAITING REVIEW BY HOS' && config_item('auth_role')=='HEAD OF SCHOOL') {
                    $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                }else if ($row['status'] == 'WAITING REVIEW BY VP FINANCE' && config_item('auth_role')=='VP FINANCE') {
                    $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                }else if ($row['status'] == 'WAITING REVIEW BY CEO' && config_item('auth_role')=='CHIEF OPERATION OFFICER') {
                    $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                }else if ($row['status'] == 'WAITING REVIEW BY CFO' && config_item('auth_role')=='CHIEF OF FINANCE') {
                    $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                }else{
                    $col[] = print_number($no);
                }
                }else{
                $col[] = print_number($no);
                }        
                $col[]  = '<a class="link" data-id="openPo" href="javascript:;" data-item-row="' . $row['id'] . '" data-href="'.site_url($this->module['route'] .'/print_pdf/'. $row['id']).'" target="_blank" >'.print_string($row['no_transaksi']).'</a>';
                $col[]  = print_date($row['tanggal']);
                $col[]  = print_string($row['no_cheque']);
                // $col[]  = print_string($row['document_number']);
                $col[]  = print_string($row['vendor']);
                // $col[]  = print_string($row['part_number']);
                // $col[]  = print_string($row['description']);
                $col[]  = print_string($row['currency']);
                // $col[]  = print_string($row['coa_kredit']).' '.print_string($row['akun_kredit']);
                $col[]  = '<a href="javascript:;" data-id="item" data-item-row="' . $row['id'] . '" data-href="' . site_url($this->module['route'] . '/change_account/' . $row['id']) . '">' . $account . '</a>'.'<input type="hidden" id="coa_kredit_' . $row['id'] . '" autocomplete="off" value="' . $row['coa_kredit'] . '"/>';
                if($row['currency']=='IDR'){
                $col[]  = print_number($row['amount_paid'], 2);
                $col[]  = print_number(0, 2);
                }else{
                $col[]  = print_number(0, 2);
                $col[]  = print_number($row['amount_paid'], 2);
                }        
                $col[]  = print_string($row['status']);
                $col[] = $attachment == 0 ? '' : '<a href="#" data-id="' . $row["id"] . '" class="btn btn-icon-toggle btn-info btn-sm ">
                            <i class="fa fa-eye"></i>
                            </a>';
                $col[]  = print_string($row['base']);
                $col[]  = print_string($row['created_by']);
                $col[]  = print_date($row['created_at']);

                if($row['currency']=='IDR'){
                $total_idr[] = $row['amount_paid'];
                }else{
                $total_usd[] = $row['amount_paid'];
                }
                

                $col['DT_RowId'] = 'row_' . $row['id'];
                $col['DT_RowData']['pkey']  = $row['id'];

                if ($this->has_role($this->module, 'info')) {
                // $col['DT_RowAttr']['onClick']     = '$(this).popup();';
                $col['DT_RowAttr']['onClick']     = '';
                $col['DT_RowAttr']['data-id']     = $row['id'];
                $col['DT_RowAttr']['data-target'] = '#data-modal';
                $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] . '/info/' . $row['id']);
                }

                $data[] = $col;
            }

            $result = array(
                "draw"            => $_POST['draw'],
                "recordsTotal"    => $this->model->countIndexDetailReport(),
                "recordsFiltered" => $this->model->countIndexFilteredDetailReport(),
                "data"            => $data,
                "total"           => array(
                7 => print_number(array_sum($total_idr), 2),
                8 => print_number(array_sum($total_usd), 2),
                )
            );
        }

        echo json_encode($result);
    }

    public function index()
    {
        $this->authorized($this->module, 'index');
            unset($_SESSION['payment_request']);

        $this->data['page']['title']            = $this->module['label'];
        $this->data['grid']['column']           = array_values($this->model->getSelectedColumnsDetailReport());
        $this->data['grid']['data_source']      = site_url($this->module['route'] . '/index_data_source');
        $this->data['grid']['fixed_columns']    = 2;
        $this->data['grid']['summary_columns']  = array(7,8);

        $this->data['grid']['order_columns']    = array();
        // $this->data['grid']['order_columns']    = array(
        //   0   => array( 0 => 1,  1 => 'desc' ),
        //   1   => array( 0 => 2,  1 => 'desc' ),
        //   2   => array( 0 => 3,  1 => 'asc' ),
        //   3   => array( 0 => 4,  1 => 'asc' ),
        //   4   => array( 0 => 5,  1 => 'asc' ),
        //   5   => array( 0 => 6,  1 => 'asc' ),
        //   6   => array( 0 => 7,  1 => 'asc' ),
        //   7   => array( 0 => 8,  1 => 'asc' ),
        // );

        $this->render_view($this->module['view'] . '/index');
    }

    public function download_all($id)
    {
        //download bpv
        $entity = $this->model->findById($id);

        $this->data['entity']           = $entity;
        $this->data['page']['title']    = ($entity->status=='PAID')? $entity->type.' PAYMENT VOUCHER':strtoupper($this->module['label']);
        $this->data['page']['content']  = $this->module['view'] .'/print_pdf';

        $html = $this->load->view($this->pdf_theme, $this->data, true);

        $pdfFilePath = str_replace('/', '-', $entity['document_number']);
        $filename = $pdfFilePath.".pdf";

        if(cekDirektori("./download/".$pdfFilePath)){
        $this->load->library('m_pdf');

        $pdf = $this->m_pdf->load(null, 'A4-L');
        $pdf->WriteHTML($html);
        // $pdf->Output($pdfFilePath, "I");
        $pdf->Output("./download/".$pdfFilePath."/".$filename, "F");
        }

        //PO
        $path_po = array();
        $path_po[0]['path'] = $pdfFilePath."/".$filename;
        $path_po[0]['file_name'] = $filename;
        $n=1;

        $path_att = array();
        $n_att = 0;
        foreach($entity['attachment'] as $key => $attachment){
            $file  = explode('/', $attachment['file']);
            $path_att[$n_att]['path'] = $attachment['file'];
            $path_att[$n_att]['file_name'] = end($file);
            $path_att[$n_att]['tipe_att'] = 'payment';
            $n_att++;
        }

        foreach($entity['po'] as $key => $item){
            $purchase_order_id = $item['id_po'];
            if($purchase_order_id!=null){
                //purchase order
                if($item['tipe_po']=='INVENTORY MRP'){
                    $modules_name = 'purchase_order';
                    $entity_po  = $this->model->findPurchaseOrderById($purchase_order_id,$item['tipe_po']);
                }elseif ($item['tipe_po']=='EXPENSE') {
                    $modules_name = 'expense_purchase_order';
                    $entity_po  = $this->model->findPurchaseOrderById($purchase_order_id,$item['tipe_po']);
                }elseif ($item['tipe_po']=='CAPEX') {
                    $modules_name = 'capex_purchase_order';
                    $entity_po  = $this->model->findPurchaseOrderById($purchase_order_id,$item['tipe_po']);
                }elseif ($item['tipe_po']=='INVENTORY') {
                    $modules_name = 'inventory_purchase_order';
                    $entity_po  = $this->model->findPurchaseOrderById($purchase_order_id,$item['tipe_po']);
                }

                $this->data['entity']           = $entity_po;
                if (strpos($entity_po['document_number'], 'W') !== FALSE){
                    $this->data['page']['title']    = 'WORK ORDER';
                }else{
                    $this->data['page']['title']    = 'PURCHASE ORDER';
                }
                // $this->data['page']['content']  = $this->modules['expense_purchase_order']['view'] .'/print_pdf';

                $html = $this->load->view($this->modules[$modules_name]['view'] . '/pdf', $this->data, true);

                $filename_po = str_replace('/', '-', $entity_po['document_number']).".pdf";

                if(cekDirektori("./download/".$pdfFilePath)){
                    $this->load->library('m_pdf');

                    $pdf = $this->m_pdf->load(null, 'A4-L');
                    $pdf->WriteHTML($html);
                    // $pdf->Output($pdfFilePath, "I");
                    $pdf->Output("./download/".$pdfFilePath."/".$filename_po, "F");
                    $path_po[$n]['path'] = $pdfFilePath."/".$filename_po;
                    $path_po[$n]['file_name'] = $filename_po;
                    $n++;
                }
                foreach($entity_po['attachment'] as $key => $attachment){
                    $file  = explode('/', $attachment['file']);
                    $path_att[$n_att]['path'] = $attachment['file'];
                    $path_att[$n_att]['file_name'] = end($file);
                    $path_att[$n_att]['tipe_att'] = 'order';
                    $n_att++;
                }

                //purchase order evaluation
                $poe_ids = array();
                foreach ($item['items'] as $key => $item_po) {
                    if(!in_array($item_po['poe_id'],$poe_ids)){
                        $poe_ids[] = $item_po['poe_id'];
                    }          
                }

                if(!empty($poe_ids)){
                    foreach ($poe_ids as $key => $poe_id) {
                        if($item['tipe_po']=='INVENTORY MRP'){
                            $modules_name = 'purchase_order_evaluation';
                            $entity_poe  = $this->model->findPurchaseOrderEvaluationById($poe_id,$item['tipe_po']);
                        }elseif ($item['tipe_po']=='EXPENSE') {
                            $modules_name = 'expense_order_evaluation';
                            $entity_poe  = $this->model->findPurchaseOrderEvaluationById($poe_id,$item['tipe_po']);
                        }elseif ($item['tipe_po']=='CAPEX') {
                            $modules_name = 'capex_order_evaluation';
                            $entity_poe  = $this->model->findPurchaseOrderEvaluationById($poe_id,$item['tipe_po']);
                        }elseif ($item['tipe_po']=='INVENTORY') {
                            $modules_name = 'inventory_order_evaluation';
                            $entity_poe  = $this->model->findPurchaseOrderEvaluationById($poe_id,$item['tipe_po']);
                        }
                
                        $this->data['entity']           = $entity_poe;
                        $this->data['page']['title']    = 'PURCHASE ORDER EVALUATION';
                        $this->data['page']['content']  = $this->modules[$modules_name]['view'] .'/print_pdf';

                        $html = $this->load->view($this->pdf_theme, $this->data, true);
                
                        $filename_poe = str_replace('/', '-', $entity_poe['evaluation_number']).".pdf";
                
                        if(cekDirektori("./download/".$pdfFilePath)){
                            $this->load->library('m_pdf');
                    
                            $pdf = $this->m_pdf->load(null, 'A4-L');
                            $pdf->WriteHTML($html);
                            // $pdf->Output($pdfFilePath, "I");
                            $pdf->Output("./download/".$pdfFilePath."/".$filename_poe, "F");
                            $path_po[$n]['path'] = $pdfFilePath."/".$filename_poe;
                            $path_po[$n]['file_name'] = $filename_poe;
                            $n++;
                        }
                        foreach($entity_poe['attachment'] as $key => $attachment){
                            $file  = explode('/', $attachment['file']);
                            $path_att[$n_att]['path'] = $attachment['file'];
                            $path_att[$n_att]['file_name'] = end($file);
                            $path_att[$n_att]['tipe_att'] = 'evaluation';
                            $n_att++;
                        }

                        $request_item_ids = array();
                        foreach ($entity_poe['request'] as $key => $request) {
                            if(!in_array($request['inventory_purchase_request_detail_id'],$request_item_ids)){
                                $request_item_ids[] = $request['inventory_purchase_request_detail_id'];
                            }          
                        }
                    }
                }

                if(!empty($request_item_ids)){
                $request_ids = array();
                    foreach ($request_item_ids as $key => $request_item_id) {
                        $request_id = $this->model->getRequestIdByItemId($request_item_id,$item['tipe_po']);
                        if(!in_array($request['id'],$request_ids)){
                            $request_ids[] = $request_id;
                        }
                    }
                }

                if(!empty($request_ids)){
                    foreach ($request_ids as $key => $request_id) {
                        if($item['tipe_po']=='INVENTORY MRP'){
                            $modules_name = 'purchase_request';
                            $entity_request  = $this->model->findPurchaseRequestById($request_id,$item['tipe_po']);
                        }elseif ($item['tipe_po']=='EXPENSE') {
                            $modules_name = 'expense_request';
                            $entity_request  = $this->model->findPurchaseRequestById($request_id,$item['tipe_po']);
                        }elseif ($item['tipe_po']=='CAPEX') {
                            $modules_name = 'capex_request';
                            $entity_request  = $this->model->findPurchaseRequestById($request_id,$item['tipe_po']);
                        }elseif ($item['tipe_po']=='INVENTORY') {
                            $modules_name = 'inventory_request';
                            $entity_request  = $this->model->findPurchaseRequestById($request_id,$item['tipe_po']);
                        }
                
                        $this->data['entity']           = $entity_request;
                        if ($item['tipe_po']!='INVENTORY MRP') {
                            $this->data['page']['title']    = $item['tipe_po'].' REQUEST';
                        }else{
                            $this->data['page']['title']    = 'PURCHASE REQUEST';
                        }
                        
                        $this->data['page']['content']  = $this->modules[$modules_name]['view'] .'/print_pdf';

                        $html = $this->load->view($this->pdf_theme, $this->data, true);
                
                        $filename_request = str_replace('/', '-', $entity_request['pr_number']).".pdf";
                
                        if(cekDirektori("./download/".$pdfFilePath)){
                            $this->load->library('m_pdf');
                    
                            $pdf = $this->m_pdf->load(null, 'A4-L');
                            $pdf->WriteHTML($html);
                            // $pdf->Output($pdfFilePath, "I");
                            $pdf->Output("./download/".$pdfFilePath."/".$filename_request, "F");
                            $path_po[$n]['path'] = $pdfFilePath."/".$filename_request;
                            $path_po[$n]['file_name'] = $filename_request;
                            $n++;
                        }
                        foreach($entity_request['attachment'] as $key => $attachment){
                            $file  = explode('/', $attachment['file']);
                            $path_att[$n_att]['path'] = $attachment['file'];
                            $path_att[$n_att]['file_name'] = end($file);
                            $path_att[$n_att]['tipe_att'] = 'request';
                            $n_att++;
                        }
                    }
                }
                
                
            }
        }
        

        // echo json_encode($path_att);

        $create_zip = new ZipArchive();
        $zip_name = "./download/".$pdfFilePath.".zip";

        if ($create_zip->open($zip_name, ZipArchive::CREATE)!==TRUE) {
            exit("cannot open the zip file <$zip_name>\n");
        }
        foreach($path_po as $key=>$po){
            $create_zip->addFile("./download/".$pdfFilePath."/".$po['file_name'] ,$po['file_name']);//add pdf PO
        }
        foreach($path_att as $key=>$att){
            $create_zip->addFile($att['path'] ,$att['file_name']);//add attachment
        }     
        $create_zip->close();

        redirect($zip_name);
        
    }

}
