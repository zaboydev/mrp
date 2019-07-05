<?=form_open_multipart(site_url($module['route'] .'/import'), array('autocomplete' => 'off', 'class' => 'form  force-padding form-validate form-xhr ui-front'));?>

  <div class="form-group">
    <input type="file" name="userfile" id="userfile" required>
    <label for="userfile">CSV File</label>
  </div>

  <div class="form-group">
    <label>Value Delimiter</label>

    <div class="radio">
      <input type="radio" name="delimiter" id="delimiter[2]" value=";" checked>
      <label for="delimiter[2]">
        Semicolon ( ; )
      </label>
    </div>

    <div class="radio">
      <input type="radio" name="delimiter" id="delimiter[1]" value=",">
      <label for="delimiter[1]">
        Comma ( , )
      </label>
    </div>
  </div>

  <div class="form-footer">
    <button type="submit" class="btn btn-block btn-primary ink-reaction">Import Data</button>
  </div>
<?=form_close();?>
