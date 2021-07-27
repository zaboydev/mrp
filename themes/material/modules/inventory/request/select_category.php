

<div class="card style-default-bright">
    <div class="card-head style-primary-dark">
        <header>Select Category</header>

        <div class="tools">
            <div class="btn-group">
                <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
                    <i class="md md-close"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12 col-lg-12">

                        <h4>Cost Center : <?= $cost_center_name;?></h4>

                        <p class="lead">Please select category: </p>                        
                            
                        <ul class="nav nav-pills nav-stacked">
                            <?php foreach ($entity as $item) : ?>
                                <li>
                                    <a href="<?= site_url($module['route'] . '/create/' . $annual_cost_center_id.'/'.$item['id']); ?>"><?= strtoupper($item['category_name']); ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>