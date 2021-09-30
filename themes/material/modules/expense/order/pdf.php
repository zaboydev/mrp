<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Please set below vars in your controller
 *
 * @var $page_header string
 * @var $page_title string
 * @var $page_desc string
 * @var $page_nav array
 * @var $page_content string
 * @var $page_styles array
 * @var $page_script string
 * @var $message string
 *
 */
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?= $page['title']; ?> | BWD Material Resource Planning</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" type="text/css" href="<?= base_url('themes/admin_lte/assets/fonts/Lato/lato.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url('themes/admin_lte/assets/css/pdf.css'); ?>">

	<style>
		@page {
			/* ensure you append the header/footer name with 'html_' */
			header: html_pageHeader;
			/* sets <htmlpageheader name="MyCustomHeader"> as the header */
			footer: html_pageFooter;
			/* sets <htmlpagefooter name="MyCustomFooter"> as the footer */
		}

		@media print {
		    .new-page {
		      	page-break-before: always;
		    }
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
	<div class="container">
		<!-- create HEADER -->
		<htmlpageheader name="pageHeader">
			<header>
				<div class="address logo">
					<img src="<?= base_url('themes/admin_lte/assets/images/logo.png'); ?>">
				</div>
				<div class="address central">
					<p>
						Bill To :
						<br />
						<strong><?= $entity['bill_company']; ?></strong>
						<br />
						<?= nl2br($entity['bill_address']); ?>
						<br />
						<?= $entity['bill_country']; ?>
						<br />
						<?= $entity['bill_attention']; ?>
					</p>
				</div>
				<div class="address branch">
					<p>
						P.O. No. :
						<strong><?= $entity['document_number']; ?></strong>
						<br /><em>Date : <?= nice_date($entity['document_date'], 'F d, Y'); ?></em>
						<!-- <br />Ref. POE : <strong><?= $entity['evaluation_number']; ?></strong> -->
						<br />Ref. Quotation : <?= (empty($entity['reference_quotation'])) ? '-' : '<strong>' . $entity['reference_quotation'] . '</strong>'; ?>
						<br />Department : <?=$entity['department'];?>
						<br />Exp Request : <?=$entity['request_number'];?>
					</p>
				</div>
			</header>

			<h1 class="page-header">
				<?= $page['title']; ?>
			</h1>

			<div style="clear: both"></div>
		</htmlpageheader>

		<htmlpagefooter name="pageFooter">
			<small class="text-muted">
				Page {PAGENO}, printed/saved on {DATE j/m/Y}
			</small>
		</htmlpagefooter>

		<section>
			<div class="clear">
				<div class="pull-left">
					To :
					<br />
					<strong><?= $entity['vendor']; ?></strong>
					<br />
					<?= nl2br($entity['vendor_address']); ?>
					<br />
					<?= $entity['vendor_country']; ?>
					<br />
					<?= $entity['vendor_attention']; ?>
				</div>

				<div class="pull-right">
					<p>
						Deliver To :
						<br />
						<strong><?= $entity['deliver_company']; ?></strong>
						<br />
						<?= nl2br($entity['deliver_address']); ?>
						<br />
						<?= $entity['deliver_country']; ?>
						<br />
						<?= $entity['deliver_attention']; ?>
					</p>
				</div>
			</div>

			<p>
				Dear Sir/Madame,
				<br /><br />This is to confirm of the order of the followings:
			</p>

			<table class="table" style="margin-top: 20px;">
				<thead>
					<tr>
						<th class="middle-alignment"></th>
						<th class="middle-alignment">Description</th>
						<th class="middle-alignment">Part Number</th>
						<th class="middle-alignment">Alt. P/N</th>
						<th class="middle-alignment">Serial Number</th>
						<th class="middle-alignment" colspan="2">Quantity</th>
						<th class="middle-alignment">Unit Price <?= $entity['default_currency']; ?></th>
						<th class="middle-alignment">Core Charge <?= $entity['default_currency']; ?></th>
						<th class="middle-alignment">Total Amount <?= $entity['default_currency']; ?></th>
						<th class="middle-alignment">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?php $n = 0; ?>
					<?php $total_amount = array(); ?>
					<?php foreach ($entity['items'] as $i => $detail) : ?>
						<?php $total_amount[] = $detail['total_amount']; ?>
						<?php $n++; ?>
						<tr id="row_<?= $i; ?>">
							<td width="1">
								<?= $n; ?>
							</td>
							<td>
								<?= print_string($detail['description']); ?>
							</td>
							<td class="no-space">
								<?= print_string($detail['part_number']); ?>
							</td>
							<td class="no-space">
								<?= print_string($detail['alternate_part_number']); ?>
							</td>
							<td class="no-space">
								<?= print_string($detail['serial_number']); ?>
							</td>
							<td>
								<?= print_number($detail['quantity'], 2); ?>
							</td>
							<td>
								<?= print_string($detail['unit']); ?>
							</td>
							<td>
								<?= print_number($detail['unit_price'], 2); ?>
							</td>
							<td>
								<?= print_number($detail['core_charge'], 2); ?>
							</td>
							<td>
								<?= print_number($detail['total_amount'], 2); ?>
							</td>
							<td>
								<?= print_string($detail['remarks']); ?>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php $subtotal       = array_sum($total_amount); ?>
					<?php $after_discount = $subtotal - $entity['discount']; ?>
					<?php $total_taxes    = $after_discount * ($entity['taxes'] / 100); ?>
					<?php $after_taxes    = $after_discount + $total_taxes; ?>
					<?php $grandtotal     = $after_taxes + $entity['shipping_cost']; ?>
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th>Subtotal <?= $entity['default_currency']; ?></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th><?= print_number($subtotal, 2); ?></th>
						<th></th>
					</tr>
					<?php if ($entity['discount'] > 0) : ?>
						<tr>
							<th></th>
							<th>Discount <?= $entity['default_currency']; ?></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th><?= print_number($entity['discount'], 2); ?></th>
							<th></th>
						</tr>
					<?php endif; ?>
					<?php if ($entity['taxes'] > 0) : ?>
						<tr>
							<th></th>
							<th>VAT <?= $entity['taxes']; ?> %</th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th><?= print_number($total_taxes, 2); ?></th>
							<th></th>
						</tr>
					<?php endif; ?>
					<?php if ($entity['shipping_cost'] > 0) : ?>
						<tr>
							<th></th>
							<th>Shipping Cost</th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th><?= print_number($entity['shipping_cost'], 2); ?></th>
							<th></th>
						</tr>
					<?php endif; ?>
					<tr>
						<th></th>
						<th>Grand Total</th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th><?= print_number($grandtotal, 2); ?></th>
						<th></th>
					</tr>
				</tfoot>
			</table>

			<div class="clear"></div>

			<?= (empty($entity['notes'])) ? '' : '<p>Note: ' . nl2br($entity['notes']) . '</p>'; ?>

			<div class="clear"></div>

				<table class="condensed" style="margin-top: 20px;" width="100%">
					<tr>
						<!-- <td width="2%" valign="top" align="center">&nbsp;</td> -->
						<td valign="top" align="center">
							<p>
								Issued by,
								<br />Procurement
								<br />
								<?php if ($entity['issued_by'] != '') : ?>
									<?= print_date($entity['created_at']); ?><br />
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['issued_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['issued_by']; ?>
							</p>
						</td>

						<?php if (($entity['base'] != 'JAKARTA' && $entity['base'] != 'WISNU')) : ?>
						<td valign="top" align="center">
							<p>
								Review by,
								<br />Assistan HOS
								<br />
								<?php if ($entity['check_review_by'] != '') : ?>
									<?= print_date($entity['check_review_at']); ?><br />
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['check_review_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['check_review_by']; ?>
							</p>
						</td>
						<?php endif; ?>

						<td valign="top" align="center">
							<p>
								Review by,
								<br />Procurement Manager
								<br />
								<?php if ($entity['proc_manager_review_by'] != '') : ?>
									<?= print_date($entity['proc_manager_review_at']); ?><br />
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['proc_manager_review_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['proc_manager_review_by']; ?>
							</p>
						</td>
						<td valign="top" align="center">
							<p>
								Checked by,
								<br />Finance
								<br />
								<?php if ($entity['checked_by'] != '') : ?>
									<?= print_date($entity['checked_at']); ?><br />
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['checked_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['checked_by']; ?>
							</p>
						</td>
						<?php if (($entity['base'] != 'JAKARTA')) : ?>
						<td valign="top" align="center">
							<p>
								<?php if (($entity['default_currency'] == 'IDR' && $grandtotal >= 15000000)||($entity['default_currency'] == 'USD' && $grandtotal >= 1500)) : ?>
								Knowledge by,
								<?php else: ?>
								Approved By,
								<?php endif; ?>
								<br />HOS
								<br />
								<?php if ($entity['known_by'] != '') : ?>
									<?= print_date($entity['known_at']); ?><br />
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['known_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['known_by']; ?>
							</p>
						</td>
						<?php else: ?>
						<td valign="top" align="center">
							<p>
								<?php if (($entity['default_currency'] == 'IDR' && $grandtotal >= 15000000)||($entity['default_currency'] == 'USD' && $grandtotal >= 1500)) : ?>
								Knowledge by,
								<?php else: ?>
								Approved By,
								<?php endif; ?>
								<br />VP Finance
								<br />
								<?php if ($entity['check_review_by'] != '') : ?>
									<?= print_date($entity['check_review_at']); ?><br />
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['check_review_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['check_review_by']; ?>
							</p>
						</td>
						<?php endif; ?>

						<?php if (($entity['default_currency'] == 'IDR' && $grandtotal >= 15000000)||($entity['default_currency'] == 'USD' && $grandtotal >= 1500)) : ?>
						<?php if (($entity['base'] != 'JAKARTA')) : ?>
						<td valign="top" align="center">
							<p>
								Approved by,
								<br />COO
								<br />
								<?php if ($entity['coo_review'] != '') : ?>
									<?= print_date($entity['coo_review_at']); ?><br />
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['coo_review'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['coo_review']; ?>
							</p>
						</td>
						<?php else: ?>
						<td valign="top" align="center">
							<p>
								Approved by,
								<br />CFO
								<br />
								<?php if ($entity['approved_by'] != '') : ?>
									<?= print_date($entity['approved_at']); ?><br />
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['approved_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['approved_by']; ?>
							</p>
						</td>	
						<?php endif; ?>
						<?php endif; ?>
					</tr>
				</table>

			<h5 class="new-page">History Purchase</h5>
			<table class="table table-striped table-nowrap">
			  <thead id="table_header">
			    <tr>
			      <th>No</th>
			      <th>Tanggal</th>
			      <th>Purchase Number</th>
			      <th>Qty</th>
			      <th>Unit</th>
			      <!-- <th>Price</th> -->
			      <th>Total</th>
			      <th>POE Qty</th>
			      <th>POE Value</th>
			      <th>PO Qty</th>
			      <th>PO Value</th>
			      <th>GRN Qty</th>
			      <th>GRN Value</th>
			    </tr>
			  </thead>
			  <tbody id="table_contents">
			    <?php $n = 0;?>
			              
			    <?php foreach ($entity['items'] as $i => $detail):?>
			    <?php 
			      $n++;
			    ?>
			    <tr>
			      <td align="right">
			        <?=print_number($n);?>
			      </td>
			      <td>
			        <?=print_string($detail['part_number']);?>
			      </td>
			      <td colspan="11">
			        <?=print_string($detail['description']);?>
			      </td>
			    </tr>
			    <?php 
			      $total_qty        = array();
			      $total            = array();
			      $total_qty_poe    = array();
			      $total_value_poe  = array();
			      $total_qty_po     = array();
			      $total_value_po   = array();
			      $total_qty_grn    = array();
			      $total_value_grn  = array();
			    ?>
			    <?php foreach ($detail['history'] as $i => $history):?>
			    <tr>
			      <?php 
			        $total_qty[]        = $history['quantity'];
			        $total[]            = $history['total'];
			        $total_qty_poe[]    = $history['poe_qty'];
			        $total_value_poe[]  = $history['poe_value'];
			        $total_qty_po[]     = $history['po_qty'];
			        $total_value_po[]   = $history['po_value'];
			        $total_qty_grn[]    = $history['grn_qty'];
			        $total_value_grn[]  = $history['grn_value'];
			      ?>
			      <td></td>
			      <td>
			        <?=print_date($history['pr_date']);?>
			      </td>
			      <td>
			        <?=print_string($history['pr_number']);?>
			      </td>
			      <td align="right">
			        <?=print_number($history['quantity'], 2);?>
			      </td>
			      <td>
			        <?=print_string($detail['unit']);?>
			      </td>
			      <td align="right">
			        <?=print_number($history['total'], 2);?>
			      </td>
			      <td align="right">
			        <?=print_number($history['poe_qty'], 2);?>
			      </td>
			      <td align="right">
			        <?=print_number($history['poe_value'], 2);?>
			      </td>
			      <td align="right">
			        <?=print_number($history['po_qty'], 2);?>
			      </td>
			      <td align="right">
			        <?=print_number($history['po_value'], 2);?>
			      </td>     
			      <td align="right">
			        <?=print_number($history['grn_qty'], 2);?>
			      </td>
			      <td align="right">
			        <?=print_number($history['grn_value'], 2);?>
			      </td>               
			    </tr>                
			    <?php endforeach;?>
			    <?php endforeach;?>
			  </tbody>
			  <tfoot>
			    <tr>
			      <th>Total</th>
			      <th></th>
			      <th></th>
			      <th style="text-align: right;"><?=print_number(array_sum($total_qty), 2);?></th>
			      <!-- <th></th> -->
			      <th></th>
			      <th style="text-align: right;"><?=print_number(array_sum($total), 2);?></th>
			      <th style="text-align: right;"><?=print_number(array_sum($total_qty_po), 2);?></th>
			      <th style="text-align: right;"><?=print_number(array_sum($total_value_poe), 2);?></th>
			      <th style="text-align: right;"><?=print_number(array_sum($total_qty_po), 2);?></th>
			      <th style="text-align: right;"><?=print_number(array_sum($total_value_po), 2);?></th>
			      <th style="text-align: right;"><?=print_number(array_sum($total_qty_grn), 2);?></th>
			      <th style="text-align: right;"><?=print_number(array_sum($total_value_grn), 2);?></th>
			    </tr>
			  </tfoot>
			</table>


		</section>
	</div>

</body>

</html>