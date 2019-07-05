<?=form_open(site_url($module['route'] .'/import'), array('autocomplete' => 'off', 'class' => 'form form-validate floating-label'));?>

  <div class="form-group">
    <input type="file" name="userfile" id="userfile" required>
    <label for="userfile">CSV File</label>
  </div>

  <div class="form-group">
    <div class="radio">
      <label class="radio-inline">
        <input type="radio" name="delimiter" id="delimiter[2]" value=";" checked> Semicolon ( ; )
      </label>
      <label class="radio-inline">
        <input type="radio" name="delimiter" id="delimiter[1]" value=","> Comma ( , )
      </label>
    </div>
    <label for="delimiter">Value Delimiter</label>
  </div>

  <div class="form-footer">
    <button type="submit" class="btn btn-block btn-primary ink-reaction">Import Data</button>
  </div>
<?=form_close();?>
