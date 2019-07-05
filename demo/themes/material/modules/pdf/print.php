<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
	<meta charset="utf-8" />
	<title><?=PAGE_TITLE;?></title>
  <link rel="stylesheet" type="text/css" href="<?=base_url('themes/admin_lte/assets/css/pdf-styles.css');?>">
	</head>
	<body>
    <div class="contents">
			<h1 class="page-title">
        <?=PAGE_TITLE;?>
        <small>Bali Widya Dirgantara Document</small>
      </h1>
		</div>

    <table>
      <thead>
        <tr>
          <?php foreach ($table['header'] as $th):?>
            <th class="no-space"><?=$th;?></th>
          <?php endforeach;?>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($table['body'] as $tbody):?>
          <tr>
            <?php foreach ($tbody as $td):?>
              <td class="no-space" align="<?=(is_numeric($td)) ? 'right':'left';?>"><?=$td;?></td>
            <?php endforeach;?>
          </tr>
        <?php endforeach;?>
      </tbody>

      <?php if ($table['footer'] != NULL):?>
        <tfoot>
          <tr>
            <?php foreach ($table['footer'] as $tf):?>
              <th align="<?=(is_numeric($td)) ? 'right':'left';?>"><?=$tf;?></th>
            <?php endforeach;?>
          </tr>
        </tfoot>
      <?php endif;?>
    </table>

	</body>
</html>
