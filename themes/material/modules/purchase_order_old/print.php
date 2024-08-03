<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Please set below vars in your controller
 *
 * @var $page_header string
 * @var PAGE_TITLE string
 * @var $page_desc string
 * @var $page_nav array
 * @var $page['content'] string
 * @var $page_styles array
 * @var $page['script'] string
 * @var $message string
 *
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=PAGE_TITLE;?> | BWD Material Resource Planning</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/fonts/Lato/lato.css');?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/css/AdminLTE.min.css');?>">
    <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/css/pdf.css');?>">

    <style>
        @page {
            /* ensure you append the header/footer name with 'html_' */
            header: html_pageHeader; /* sets <htmlpageheader name="MyCustomHeader"> as the header */
            footer: html_pageFooter; /* sets <htmlpagefooter name="MyCustomFooter"> as the footer */
        }
    </style>
    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="wrapper">
    <!-- create HEADER -->
    <htmlpageheader name="pageHeader">
        <header>
            <div class="address logo">
                <img src="<?=base_url('themes/admin_lte/assets/images/logo.png');?>">
            </div>
            <div class="address central">
                <p>
                    <strong>Bill to</strong>
                    <br><?=nl2br($entity['bill_to']);?>
                </p>
            </div>
            <div class="address branch">
                <p>
                    <strong>Delivery to</strong>
                    <br><?=nl2br($entity['delivery_to']);?>
                </p>
            </div>
        </header>
        <div style="clear: both"></div>

        <h1 class="page-header">
            PURCHASE ORDER
        </h1>

        <div style="clear: both"></div>
    </htmlpageheader>

    <htmlpagefooter name="pageFooter">
        Page {PAGENO}, <?=$entity['po_number'];?>, Ref POE#<?=$entity['reference_poe'];?>
    </htmlpagefooter>

    <section>
        <table>
            <tr>
                <td valign="bottom" rowspan="2">
                    <p>
                        Number: <?=$entity['po_number'];?><br>
                        Date: <?=nice_date($entity['po_date'], 'F d, Y');?><br>
                        Ref Quotation: <?=$entity['reference_quotation'];?>
                    </p>

                    <p>&nbsp;</p>
                    <p>Dear Sir/Madame, <br>This is to confirm of the order of the followings:</p>
                </td>
                <th>To</th>
            </tr>
            <tr>
                <td valign="top"><?=nl2br($entity['vendor_address']);?></td>
            </tr>
        </table>

        <table class="table table-striped">
            <thead>
            <tr>
                <th>No</th>
                <th>Item</th>
                <th>Part#</th>
                <th>Alt. Part#</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Core Charge</th>
                <th>Total Amount</th>
                <th>Notes</th>
            </tr>
            </thead>
            <tbody>
            <?php $n = 0;?>
            <?php foreach ($entity['item'] as $i => $item):?>
                <?php $n++;?>
                <?php $total = ($item['unit_price'] * $item['quantity']) + ($item['core_charge'] * $item['quantity']);?>
                <tr>
                    <td class="no-space">
                        <?=$n;?>
                    </td>
                    <td>
                        <?=$item['item_name'];?>
                    </td>
                    <td>
                        <?=$item['part_number'];?>
                    </td>
                    <td>
                        <?=$item['alternate_part_number'];?>
                    </td>
                    <td>
                        <?=number_format($item['quantity'], 2);?>
                    </td>
                    <td>
                        <?=number_format($item['unit_price'], 2);?>
                    </td>
                    <td>
                        <?=number_format($item['core_charge'], 2);?>
                    </td>
                    <td>
                        <?=number_format($total, 2);?>
                    </td>
                    <td>
                        <?=$item['notes'];?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>

        <?php if ($entity['notes']):?>
        <p><?=$entity['notes'];?></p>
        <?php endif;?>

        <div class="clear"></div>

        <table class="condensed center" style="margin-top: 20px;">
            <tr>
                <td width="33%">
                    Issued by,
                    <br>Procurement
                </td>
                <td width="34%">
                    Checked by,
                    <br>Budget Control
                </td>
                <td width="33%">
                    Approved by,
                    <br>CFO
                </td>
            </tr>
        </table>
    </section>
</div>

</body>
</html>