<div class="menubar-fixed-panel">
  <div>
    <a class="btn btn-icon-toggle btn-default menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
      <i class="md md-flight"></i>
    </a>
  </div>
  <div class="expanded">
    <a href="<?=site_url();?>">
      <span class="text-lg text-info ">BWD</span>
      <span class="text-lg text-bold text-primary ">MRP</span>
    </a>
  </div>
</div>
<div class="menubar-scroll-panel">

  <ul id="main-menu" class="gui-controls">

    <?php
    foreach (available_modules() as $mainMenu => $subMenu) {
      $parentMenu = config_item('parent');
      $roleUser = config_item('auth_role');
      $parentMenuClass = ( $mainMenu === $module['parent'] ) ? 'active' : '';

      echo '<li class="gui-folder '.$parentMenuClass.'">';
      echo '<a>';
      echo '<div class="gui-icon"><i class="'.$parentMenu[$mainMenu]['icon'].'"></i></div>';
      echo '<span class="title">'.$parentMenu[$mainMenu]['label'].'</span>';
      echo '</a>';
      echo '<ul>';

      foreach ($subMenu as $childMenu){
        $childMenuClass = ($childMenu['permission'] === $module['route']) ? 'active' : '';
        echo '<li class="'.$childMenuClass.'">';
        echo '<a href="'.site_url($childMenu['route']).'">';
        echo '<span class="title">';
        echo $childMenu['label'];
        echo '</span>';
        echo '</a>';

        $roles = explode(',', $childMenu['permission']['index_approval']);
        $roles = array_map('trim', $roles); // Trim spaces to avoid mismatch issues
        if ($childMenu['route'] === 'reimbursement' && in_array(config_item('auth_role'), $roles) != '' ) {
          $childMenuApp = ('reimbursement/approval' === ($module['route'].'/approval')) ? 'active' : '';
          echo '<li class="'.$childMenuApp.'">';
          echo '<a href="'.site_url('reimbursement/approval').'">';
          echo '<span class="title">';
          echo 'Reimbursement Approval';
          echo '</span>';
          echo '</a>';
          echo '</li>';
        }
        echo '</li>';

        // if($childMenu['route'] == 'reimbursement' && $roleUser != 'REIMBURSEMENT'){
        //   $roles = explode(',', $module['permission']['index_approval']);
        //   $roles = array_map('trim', $roles); // Trim spaces to avoid mismatch issues
        //   if(!empty($module['permission']['index_approval'] && $childMenu['parent'] == 'reimbursment')){
        //   $childMenuApp = ('reimbursement/approval' === ($module['route'].'/approval')) ? 'active' : '';
        //   echo '<li class="'.$childMenuApp.'">';
        //   echo '<a href="'.site_url('reimbursement/approval').'">';
        //   echo '<span class="title">';
        //   echo 'Reimbursement Approval';
        //   echo '</span>';
        //   echo '</a>';
        //   echo '</li>';
        // }
      }

      echo '</ul>';
      echo '</li>';
    }
    ?>

  </ul>

  <div class="menubar-foot-panel">
    <small class="no-linebreak hidden-folded">
      <span class="opacity-75">Copyright &copy; 2017</span> <strong>BIFA</strong>
    </small>
  </div>
</div>
