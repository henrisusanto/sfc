              <div class="mws-panel grid_8 mws-collapsible">
                <div class="mws-panel-header">
                  <span id="">FILTER LAPORAN</span>
                </div>
                <div class="mws-panel-body no-padding">
                  <form action="" class="mws-form">
                        <?php foreach ($filters as $field): ?>
                          <?php if (isset($field[2])): ?>
                            <div class="mws-form-inline">
                              <div class="mws-form-row">
                                <label class="mws-form-label"><?= $field[1] ?></label>
                                <div class="mws-form-item">
                                  <select name="<?= $field[0] ?>" class="small">
                                    <?php foreach ($field[2] as $value => $option): ?>
                                      <option value="<?= $value ?>" <?= isset($form[$field[0]])&&$form[$field[0]]==$value?'selected':'' ?>>
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
                                    class="small <?= $field[0] == 'since' || $field[0] == 'until' ?'mws-datepicker-wk':'' ?>" 
                                    name="<?= $field[0] ?>" 
                                    value="<?= isset($form[$field[0]])?$form[$field[0]]:'' ?>">
                                </div>
                              </div>
                            </div>
                          <?php endif ?>
                        <?php endforeach ?>

                            <div class="mws-form-inline">
                              <div class="mws-form-row">
                                <label class="mws-form-label"></label>
                                <div class="mws-form-item">
                                  <input type="submit" value="FILTER" class="btn btn-primary" />
                                  <a href="<?= $tablePage ?>" class="btn btn-warning">RESET</a>
                                </div>
                              </div>
                            </div>
                  </form>
                </div>
              </div>
                        
              <div class="mws-panel grid_8">
                  <div class="mws-panel-header">
                      <span id="pagetitle"></span>
                    </div>
                    <div class="mws-panel-body no-padding">
                        <table class="mws-datatable-fn mws-table <?= $entity ?>">
                            <thead>
                                <tr>
                                  <th>NO</th>
                                  <?php foreach ($thead as $th): ?>
                                    <th><?= $th[1] ?></th>
                                  <?php endforeach ?>
                                </tr>
                            </thead>
                            <tbody>
                              <?php $no = 0; foreach ($tbody as $tb): $no++ ?>
                                <tr>
                                  <td><?= $no ?></td>
                                  <?php foreach ($thead as $th): ?>
                                  <td><?= $tb->$th[0] ?></td>
                                  <?php endforeach ?>
                                </tr>
                              <?php endforeach ?>
                            </tbody>
                            <tfoot>
                              <?php foreach ($tfoot as $th): ?>
                                <th><?= $th ?></th>
                              <?php endforeach ?>
                            </tfoot>
                        </table>
                    </div>
                </div>