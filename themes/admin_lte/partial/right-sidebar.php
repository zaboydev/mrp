<aside class="control-sidebar control-sidebar-light">
<!--     <div class="btn-group">
        <a href="" class="btn btn-link">
            <i class="fa fa-arrow-circle-left"></i>
            Back to the list
        </a>
    </div>
 -->
    <h4 class="sidebar-title">Data Options</h4>
    
    <ul class="nav nav-pills nav-stacked">
        <?php if (isset($page_nav)):?>
            <?php foreach ($page_nav as $nav):?>
                <li role="presentation"><?=$nav;?></li>
            <?php endforeach;?>
        <?php endif;?>
    </ul>

    <?php if (isset($sidebar)):?>
        <?php $this->load->view($sidebar);?>
    <?php endif;?>
</aside>

<!-- Add the sidebar's background. This div must be placed
   immediately after the control sidebar and should be left empty. -->
<div class='control-sidebar-bg'></div>
