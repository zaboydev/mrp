<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Account_Payable extends MY_Controller
{
  protected $module;
  protected $id_item=0;

  public function __construct()
  {
    parent::__construct();

    $this->module = $this->modules['account_payable'];
    $this->load->model($this->module['model'], 'model');
    $this->load->helper($this->module['helper']);
    $this->data['module'] = $this->module;
  }

 public function index(){
    $this->authorized($this->module, 'index');

    $this->data['page']['title']            = $this->module['label'];
    $this->data['grid']['column']           = array_values($this->model->getSelectedColumns());
    $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
    $this->data['grid']['fixed_columns']    = 2;
    $this->data['grid']['summary_columns']  = array( 5, 6 );

    $this->data['grid']['order_columns']    = array();
    $this->render_view($this->module['view'] .'/index');
 }
 public function index_data_source(){
   if (is_granted($this->module, 'index') === FALSE){
      $return['type'] = 'danger';
      $return['info'] = "You don't have permission to access this page!";
    } else {
      $entities = $this->model->getIndex();
      $data     = array();
      $no       = $_POST['start'];
      $quantity     = array();
      $unit_value   = array();
      $total_value  = array();

      foreach ($entities as $row){
        $no++;
        $col = array();
        $col[]  = print_number($no);
        $col[]  = print_string($row['document_no']);
        $col[]  = print_date($row['tanggal']);
        $col[]  = print_string($row['no_grn']);
        $col[]  = print_string($row['vendor']);
        $col[]  = print_string($row['amount']);
        $col[]  = print_string($row['sisa']);
        $col[]  = print_string($row['status']);
        $quantity[] = $row['amount'];

        $col['DT_RowId'] = 'row_'. $row['id'];
        $col['DT_RowData']['pkey']  = $row['id'];

        if ($this->has_role($this->module, 'info')){
          $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/info/'. $row['id']);
          $col['DT_RowAttr']['data-id'] = $row['id'];
        }

        $data[] = $col;
      }

      $result = array(
        "draw"            => $_POST['draw'],
        "recordsTotal"    => $this->model->countIndex(),
        "recordsFiltered" => $this->model->countIndexFiltered(),
        "data"            => $data,
        "total"           => array(
          5 => print_number(array_sum($quantity), 2),6 => print_number(array_sum($quantity), 2),
        )
      );
    }

    echo json_encode($result);
 }
public function urgent($id)
{
  $result['status'] = "failed" ;
  $urgent = $this->model->urgent($id);
  if($urgent){
    $result['status'] = "success";
    $this->sendEmail();
  }
  echo json_encode($result);
}
public function sendEmail()
  {
    $recipientList = $this->model->getNotifRecipient();
    $recipient = array();
    foreach ($recipientList as $key ) {
      array_push($recipient, $key->email);
    }
    $this->load->library('email');

    $this->email->from('bifa.Team@gmail.com', 'Bifa Team');
    $this->email->to($recipient);
    $html = '<html><head> 
                        <meta http-equiv="\&quot;Content-Type\&quot;" content="\&quot;text/html;" charset="utf-8\&quot;">
                        <style>
                            .content {
                                max-width: 500px;
                                margin: auto;
                            }
                            .title{
                                width: 60%;
                            }

                        </style></head>
                        
                        <body> 
                            <div class="content">
<div bgcolor="#0aa89e">
    <table align="center" bgcolor="#fff" border="0" cellpadding="0" cellspacing="0" style="background-color:#fff;margin:5% auto;width:100%;max-width:600px">
        
        <tbody><tr>
            <td>
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#0aa89e" style="padding:10px 15px;font-size:14px">
                    <tbody><tr>
                        <td width="60%" align="left" style="padding:5px 0 0">
                            <span style="font-size:18px;font-weight:300;color:#ffffff">
                                BIFA
                            </span>
                        </td>
                        <td width="40%" align="right" style="padding:5px 0 0">
                            <span style="font-size:18px;font-weight:300;color:#ffffff">
                                Notification
                            </span>
                        </td>
                    </tr>
                </tbody></table>
            </td>
        </tr>        
        <tr>
            <td style="padding:25px 15px 10px">
                <table width="100%">
                    <tbody><tr>
                        <td>
                            <h1 style="margin:0;font-size:16px;font-weight:bold;line-height:24px;color:rgba(0,0,0,0.70)">Halo Team</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="margin:0;font-size:16px;line-height:24px;color:rgba(0,0,0,0.70)">Ada item baru di Account Payable yang memerlukan pembayaran segera</p>
                        </td>
                    </tr>
                </tbody></table>
            </td>
        </tr>
    </tbody></table>
<p>&nbsp;<br></p>
</div>

                            </div>  
                                    
                        
</body></html>';
    $this->email->subject('Notification');
    $this->email->message($html);

    $this->email->send();
  }
}
