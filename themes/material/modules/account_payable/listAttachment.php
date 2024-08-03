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
                <th colspan="2">Attachment PO No : <?=$no_po?></th>
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
            <?php endif;?>
            <tr>
                <th colspan="2">Attachment POE No : <?=$no_poe?></th>
            </tr>
            <?php if(count($att_poe)>0):?>
            <?php $n = 0;?>
            <?php foreach ($att_poe as $i => $poe):?>
                <?php $n++;?>
                <tr>
                <td><?=$n?></td>
                <td><a href="<?=site_url('dashboard/open_attachment/' . $poe['id'].'/mrp')?>" target="_blank"><?=$poe['file'];?></a></td>
                </tr>
            <?php endforeach;?>
            <?php else:?>
            <tr>
                <td colspan="2" style="text-align: center;">No Attachment</td>
            </tr>
            <?php endif;?>

            <tr>
                <th colspan="2">Attachment Request</th>
            </tr>
            <?php if(count($att_request)>0):?>
            <?php $n = 0;?>
            <?php foreach ($att_request as $i => $att_req):?>
                <?php $n++;?>
                <tr>
                <td><?=$n?></td>
                <td><a href="<?=site_url('dashboard/open_attachment/' . $att_req['id'].'/budgetcontrol')?>" target="_blank"><?=$att_req['file'];?></a></td>
                </tr>
            <?php endforeach;?>          
            <?php else:?>
            <tr>
                <td colspan="2" style="text-align: center;">No Attachment</td>
            </tr>
            <?php endif;?>
        </tbody>
        </table>
    </div>
</div>