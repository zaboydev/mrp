<div class="card card-underline style-default-bright">
  <div class="card-head style-primary-dark">
    <header><?= strtoupper($module['label']); ?></header>

    <div class="tools">
      <div class="btn-group">
        <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
          <i class="md md-close"></i>
        </a>
      </div>
    </div>
  </div>

  <div class="card-body">
    <div class="row" id="document_master">
      <div class="col-sm-12 col-md-4 col-md-push-8">
        <div class="well">
          <div class="clearfix">
            <div class="pull-left">TRANSACTION NO : </div>
            <div class="pull-right"><?= print_string($entity['transaction_number']); ?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left"> DATE: </div>
            <div class="pull-right"><?= print_date($entity['date']); ?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left"> Last Update at: </div>
            <div class="pull-right"><?= print_date($entity['created_at']); ?></div>
          </div>

          <div class="clearfix">
            <div class="pull-left"> Last Update vy: </div>
            <div class="pull-right"><?= print_string($entity['created_by']); ?></div>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-8 col-md-pull-4">
        <dl class="dl-inline">
          <dt>Notes</dt>
          <dd><?= $entity['notes']; ?></dd>
        </dl>
      </div>
    </div>
    <div class="row" id="document_details">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-striped table-nowrap">
            <thead id="table_header">
              <th>No</th>
              <th>Account</th>
              <th>Debit</th>
              <th>Credit</th>
            </thead>
            <tbody id="table_contents">
              <?php $n = 0; $total_amount = array();?>
              <?php foreach ($entity['items'] as $i => $item):?>
                <?php 
                  $n++;
                ?>
                <tr>
                  <td align="right">
                    <?=print_number($n);?>
                  </td>
                  <td>
                    (<?=print_string($item['coa']);?>) <?=print_string($item['group']);?>
                  </td>                  
                  <td align="right">
                    <?=print_number($item['debit'], 2);?>
                  </td> 
                  <td align="right">
                    <?=print_number($item['kredit'], 2);?>
                  </td>                  
                </tr>
              <?php endforeach;?>
            </tbody>            
          </table>
        </div>
      </div>      
    </div>
  </div>
  <?php
      $today    = date('Y-m-d');
      $date     = strtotime('-30 day',strtotime($today));
      $data     = date('Y-m-d',$date);
  ?>
  <div class="card-foot">
    <div class="pull-left">
      
    </div>
    <div class="pull-right">
      <?php if (is_granted($module, 'document') && $entity['created_at'] >= $data):?>
      <a href="<?= site_url($module['route'] . '/edit/' . $category); ?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
        <i class="md md-edit"></i>
        <small class="top right">edit</small>
      </a>
      <?php endif;?>
      <a href="<?=site_url($module['route'] .'/print_pdf/'. $category);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
        <i class="md md-print"></i>
        <small class="top right">print</small>
      </a>
    </div>
  </div>
</div>
<script type="text/javascript">
  function popup(mylink, windowname) {
    var height = window.innerHeight;
    var widht;
    var href;

    if (screen.availWidth > 768) {
      width = 769;
    } else {
      width = screen.availWidth;
    }

    var left = (screen.availWidth / 2) - (width / 2);
    var top = 0;
    // var top = (screen.availHeight / 2) - (height / 2);

    if (typeof(mylink) == 'string') href = mylink;
    else href = mylink.href;

    window.open(href, windowname, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left);

    if (!window.focus) return true;
    else return false;
  }
</script>