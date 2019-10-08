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
						<th><?= print_number($grandtotal, 2); ?></th>
						<th></th>
					</tr>
				</tfoot>
			</table>

			<div class="clear"></div>

			<?= (empty($entity['notes'])) ? '' : '<p>Note: ' . nl2br($entity['notes']) . '</p>'; ?>

			<div class="clear"></div>

			<?php if ($entity['default_currency'] == 'IDR' && $grandtotal >= 15000000) : ?>
				<table class="condensed" style="margin-top: 20px;">
					<tr>
						<!-- <td width="2%" valign="top" align="center">&nbsp;</td> -->
						<td width="16%" valign="top" align="center">
							<p>
								Issued by,
								<br />Procurement
								<br />
								<?php if ($entity['issued_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['issued_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['issued_by']; ?>
							</p>
						</td>
						<td width="16%" valign="top" align="center">
							<p>
								Checked by,
								<br />Finance
								<br />
								<?php if ($entity['checked_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['checked_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['checked_by']; ?>
							</p>
						</td>
						<td width="16%" valign="top" align="center">
							<p>
								Knowledge by,
								<br />HOS
								<br />
								<?php if ($entity['known_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['known_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['known_by']; ?>
							</p>
						</td>
						<td width="16%" valign="top" align="center">
							<p>
								Approved by,
								<br />COO
								<br />
								<?php if ($entity['coo_review'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['known_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['known_by']; ?>
							</p>
						</td>
						<td width="16%" valign="top" align="center">
							<p>
								Knowledge by,
								<br />VP Finance
								<br />
								<?php if ($entity['check_review_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['check_review_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['check_review_by']; ?>
							</p>
						</td>
						<td width="16%" valign="top" align="center">
							<p>
								Approved by,
								<br />CFO
								<br />
								<?php if ($entity['approved_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['approved_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['approved_by']; ?>
							</p>
						</td>
						<!-- <td width="2%" valign="top" align="center">&nbsp</td> -->
					</tr>
				</table>
			<?php elseif ($entity['default_currency'] == 'USD' && $grandtotal >= 1500) : ?>
				<table class="condensed" style="margin-top: 20px;">
					<tr>
						<!-- <td width="2%" valign="top" align="center">&nbsp;</td> -->
						<td width="16%" valign="top" align="center">
							<p>
								Issued by,
								<br />Procurement
								<br />
								<?php if ($entity['issued_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['issued_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['issued_by']; ?>
							</p>
						</td>
						<td width="16%" valign="top" align="center">
							<p>
								Checked by,
								<br />Finance
								<br />
								<?php if ($entity['checked_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['checked_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['checked_by']; ?>
							</p>
						</td>
						<td width="16%" valign="top" align="center">
							<p>
								Knowledge by,
								<br />HOS
								<br />
								<?php if ($entity['known_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['known_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['known_by']; ?>
							</p>
						</td>
						<td width="16%" valign="top" align="center">
							<p>
								Approved by,
								<br />COO
								<br />
								<?php if ($entity['coo_review'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['known_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['known_by']; ?>
							</p>
						</td>
						<td width="16%" valign="top" align="center">
							<p>
								Knowledge by,
								<br />VP Finance
								<br />
								<?php if ($entity['check_review_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['check_review_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['check_review_by']; ?>
							</p>
						</td>
						<td width="16%" valign="top" align="center">
							<p>
								Approved by,
								<br />CFO
								<br />
								<?php if ($entity['approved_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['approved_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['approved_by']; ?>
							</p>
						</td>
						<!-- <td width="2%" valign="top" align="center">&nbsp</td> -->
					</tr>
				</table>
			<?php else : ?>
				<table class="condensed" style="margin-top: 20px;">
					<tr>
						<td width="25%" valign="top" align="center">
							<p>
								Issued by,
								<br />Procurement
								<br />
								<?php if ($entity['issued_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['issued_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['issued_by']; ?>
							</p>
						</td>
						<td width="25%" valign="top" align="center">
							<p>
								Checked by,
								<br />Finance
								<br />
								<?php if ($entity['checked_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['checked_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['checked_by']; ?>
							</p>
						</td>
						<td width="25%" valign="top" align="center">
							<p>
								Approved by,
								<br />HOS
								<br />
								<?php if ($entity['known_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['known_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['known_by']; ?>
							</p>
						</td>
						<td width="25%" valign="top" align="center">
							<p>
								Checked by,
								<br />VP Finance
								<br />
								<?php if ($entity['check_review_by'] != '') : ?>
									<img src="<?= base_url('ttd_user/' . get_ttd($entity['check_review_by'])); ?>" width="auto" height="50">
								<?php endif; ?>
								<br />
								<br /><?= $entity['check_review_by']; ?>
							</p>
						</td>
					</tr>
				</table>
			<?php endif; ?>


		</section>
	</div>

</body>

</html>