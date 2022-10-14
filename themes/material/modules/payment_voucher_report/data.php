<table class="table table-bordered table-nowrap" id="table-document">
    <thead>
        <tr>
            <th>Date</th>
            <th>Document Number</th>
            <th>Name</th>
            <th>No Cheque</th>
            <th>Currency</th>
            <th>Account</th>
            <th>Amount</th>
            <th>Notes</th>
            <th>Attachment</th>
        </tr>                                                    
    </thead>
    <tbody>
        <?php foreach ($items as $i => $item) : ?>            
        <tr>
            <td>
                <?= print_date($item['tanggal'],'d/m/Y'); ?>
            </td>
            <td>
                <a class="link btn btn-sm btn-info-transaksi" data-href="<?=$item['link']?>">
                <?= print_string($item['document_number']); ?>
                </a>                
            </td>
            <td>
                <?= print_string($item['vendor']); ?>
            </td>
            <td>
                <?= print_string($item['no_cheque']); ?>
            </td>
            <td>
                <?= print_string($item['currency']); ?>
            </td>
            <td>
                <?= print_string($item['coa_kredit']); ?> - <?= print_string($item['akun_kredit']); ?>
            </td>
            <td>
                <?= print_number($item['amount_paid'],2); ?>
            </td>
            <td>
                <?=$item['notes'];?>
            </td>
            <td>
                <?php if($item['attachment']>0):?>
                    <a type="button" data-href="<?=$item['link_attachment']?>" class="btn btn-icon-toggle btn-info btn-sm btn-open-attachment">
                       <i class="fa fa-eye"></i>
                    </a>
                <?php endif;?>
            </td>
        </tr>   
        <?php endforeach; ?>
        
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">Total</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
<script>
    $(".btn-info-transaksi").on('click', function(e) {
        console.log('klik')
        var dataModal = $('#data-modal');
        // $(dataModal).modal('show');
        $.ajax({
            type: "GET",
            url: $(this).data('href'),
            cache: false,
            success: function(response) {
                var obj = $.parseJSON(response);
                $(dataModal)
                  .find('.modal-body')
                  .empty()
                  .append(obj.info);

                $(dataModal).modal('show');
              
            },
            error: function(xhr, ajaxOptions, thrownError) {
              console.log(xhr.status);
              console.log(xhr.responseText);
              console.log(thrownError);
            }
        });
    });

    $(".btn-open-attachment").on('click', function(e) {
        
        $.ajax({
            type: "GET",
            url: $(this).data('href'),
            cache: false,
            success: function(response) {
                var data = jQuery.parseJSON(response)
                $("#listViewAttachment").html("")
                $("#attachment_modal").modal("show");
                $.each(data, function(i, item) {
                    var text = '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td><a href="<?= base_url() ?>' + item.file + '" target="_blank">' + item.file + '</a></td>' +
                    '</tr>';
                    $("#listViewAttachment").append(text);
                });
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(xhr.responseText);
                console.log(thrownError);
            }
        });
    });
</script>
