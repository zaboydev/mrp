<div class="row">
    <div class="col-md-12">
        <table style="width: 100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Link</th>
                </tr>
            </thead>
        <tbody>
          <tr>
            <th colspan="2">Attachment POE</th>
          </tr>
          <?php if(count($entity)>0):?>
          <?php $n = 0;?>
          <?php foreach ($entity as $i => $detail):?>
            <?php $n++;?>
            <tr>
              <td><?=$n?></td>
              <td><a href="<?=site_url('dashboard/open_attachment/' . $detail['id'].'/mrp')?>" target="_blank"><?=$detail['file'];?></a></td>
            </tr>
          <?php endforeach;?>
          <?php else:?>
          <tr>
            <td colspan="2" style="text-align: center;">No Attachment</td>
          </tr>
          <?Php endif;?>
        </tbody>
        </table>
    </div>
</div>