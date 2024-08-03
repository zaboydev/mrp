<div id="import-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="import-modal-label" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <?=form_open_multipart(site_url($module['route'] .'/import'), array('autocomplete' => 'off', 'class' => 'form form-validate form-xhr ui-front'));?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

          <h4 class="modal-title" id="import-modal-label">Import Data</h4>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label for="userfile">CSV File</label>

            <input type="file" name="userfile" id="userfile" required>
          </div>

          <div class="form-group">
            <label>Value Delimiter</label>

            <div class="radio">
              <input type="radio" name="delimiter" id="delimiter_2" value=";" checked>
              <label for="delimiter_2">Semicolon ( ; )</label>
            </div>

            <div class="radio">
              <input type="radio" name="delimiter" id="delimiter_1" value=",">
              <label for="delimiter_1">Comma ( , )</label>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-block btn-primary ink-reaction">Import Data</button>
        </div>
      <?=form_close();?>
    </div>
  </div>
</div>
