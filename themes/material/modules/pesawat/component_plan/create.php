<?= form_open(site_url($module['route'] . '/save'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-create-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form'
)); ?>

<div class="card style-default-bright">
    <div class="card-head style-primary-dark">
        <header>Create New <?= $module['label']; ?></header>

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
            <div class="col-sm-12 col-lg-8">
                <div class="form-group" style="padding-top: 20px;">
                    <select name="aircraft_register" id="aircraft_register" data-tag-name="aircraft_register" class="form-control input-sm select2" style="width: 100%" required>
                        <option value="">-- SELECT Aircraft --</option>
                        <?php foreach (pesawat() as $pesawat) : ?>
                        <option value="<?= $pesawat; ?>"><?= $pesawat; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="aircraft_register">Aircraft</label>
                </div> 
                
                <div class="form-group">
                    <input type="date" name="date" id="date" class="form-control">
                    <label for="date">Date</label>
                </div>

                <div class="form-group">
                    <input type="text" name="year_plan" id="year_plan" class="form-control" maxlength="4" pattern="[0-9]{4}">
                    <label for="date">Year Plan</label>
                </div>

                <div class="form-group">
                    <input type="text" name="description" id="description" class="form-control" required>
                    <label for="description">Description</label>
                </div>

                <div class="form-group">
                    <input type="text" name="part_number" id="part_number" class="form-control input-sm input-autocomplete" data-source="<?= site_url($module['route'] . '/search_items_by_part_number/'); ?>" required>
                    <label for="part_number">Part Number</label>
                </div>

                <div class="form-group">
                    <input type="text" name="alternate_part_number" id="alternate_part_number" class="form-control">
                    <label for="alternate_part_number">Alternate Part Number</label>
                </div>

                <div class="form-group">
                    <select name="unit" id="unit" class="form-control" required>
                    <?php foreach (available_units() as $unit) : ?>
                        <option value="<?= $unit; ?>">
                        <?= $unit; ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                    <label for="unit">Unit of Measurement</label>
                </div>

                <div class="form-group">
                    <select name="group" id="group" class="form-control" required>
                    <?php foreach (available_item_groups(config_item('auth_inventory')) as $i => $group) : ?>
                        <option value="<?= $group; ?>">
                        <?= $group; ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                    <label for="group">Group</label>
                </div>
            </div>

            <div class="col-sm-12 col-lg-4">
                <div class="form-group">
                    <input type="number" name="planing_quantity" id="planing_quantity" class="form-control">
                    <label for="planing_quantity">Planning Quantity</label>
                </div>

                <div class="form-group">
                    <textarea name="remarks" id="remarks" class="form-control" rows="4"></textarea>
                    <label for="remarks">Remarks</label>
                </div>
            </div>
        </div>
    </div>

    <div class="card-foot">
        <button type="submit" id="modal-create-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction pull-right" data-title="save and create">
        <i class="md md-save"></i>
        </button>
    </div>
</div>

<?= form_close(); ?>

<script>
    $(document).ready(function () {
        $.ajax({
                url: $('input[id="part_number"]').data('source'),
                dataType: "json",
                success: function(resource) {
                    $('input[id="part_number"]').autocomplete({
                        autoFocus: true,
                        minLength: 1,

                        source: function(request, response) {
                            var results = $.ui.autocomplete.filter(resource, request.term);
                            response(results.slice(0, 5));
                        },

                        focus: function(event, ui) {
                            return false;
                        },

                        select: function(event, ui) {
                            $('input[id="part_number"]').val(ui.item.part_number);
                            $('input[id="alternate_part_number"]').val(ui.item.alternate_part_number);
                            $('input[id="description"]').val(ui.item.description);
                            $('select[id="group"]').val(ui.item.group).trigger('change');
                            $('input[id="unit"]').val(ui.item.unit);

                            return false;
                        }
                    })
                    .data("ui-autocomplete")._renderItem = function(ul, item) {
                        $(ul).addClass('list divider-full-bleed');

                        return $("<li class='tile'>")
                        .append('<a class="tile-content ink-reaction"><div class="tile-text">' + item.label + '</div></a>')
                        .appendTo(ul);
                    };
                }
            });
    });
</script>