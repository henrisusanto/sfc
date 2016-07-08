                <div class="mws-panel grid_8">
                  <div class="mws-panel-header">
                      <span id="pagetitle"></span>
                    </div>
                    <div class="mws-panel-body no-padding">
                      <?php include APPPATH . '/views/message.php'; ?>
                        <?php foreach ($fields as $field): ?>
                          <?php if (isset($field[2])): ?>
                            <div class="mws-form-inline">
                              <div class="mws-form-row">
                                <label class="mws-form-label"><?= $field[1] ?></label>
                                <div class="mws-form-item">
                                  <select name="<?= $field[0] ?>" class="small">
                                    <?php foreach ($field[2] as $value => $option): ?>
                                      <option value="<?= $value ?>" <?= isset($form)&&$form[$field[0]]==$value?'selected':'' ?>>
                                        <?= $option ?>
                                      </option>
                                    <?php endforeach ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                          <?php else : ?>
                            <div class="mws-form-inline">
                              <div class="mws-form-row">
                                <label class="mws-form-label"><?= $field[1] ?></label>
                                <div class="mws-form-item">
                                  <input type="text" 
                                    class="small text-right <?= $field[0]=='waktu'?'mws-dtpicker':'' ?>" 
                                    name="<?= $field[0] ?>" 
                                    value="<?= isset($form)?$form[$field[0]]:'' ?>">
                                </div>
                              </div>
                            </div>
                          <?php endif ?>
                        <?php endforeach ?>

                        <!-- <div class="mws-button-row"> -->
                          <!-- <input type="submit" value="Submit" class="btn btn-danger">
                          <input type="reset" value="Reset" class="btn "> -->
                        <!-- </div> -->

                    </div>      
                </div>