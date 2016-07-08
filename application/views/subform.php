                <div class="mws-panel grid_8">
                  <div class="mws-panel-header">
                      <span>
                        <?= $label ?>
                        <a class="btn tambah-item pull-right btn-success">TAMBAH ITEM</a>
                      </span>
                    </div>
                    <div class="mws-panel-body no-padding">

                        <?php if (!empty($subform)): foreach ($subform as $brg) : ?>
                            <div class="mws-form-row expandable-form expandable-form-edit">
                                <div class="mws-form-cols">
                                  <?php foreach ($subfields as $sub): 
                                    $field = str_replace('[]', '', $sub[0]);
                                    $field = explode('[', $field);
                                    $field = str_replace(']', '', $field[1]);
                                    $name = $sub[0];
                                    $base_field = explode('[', str_replace('][]', '', $sub[0]));
                                    $base_field = $base_field[1];
                                  ?>
                                  <?php if ($sub === end ($subfields)): ?>
                                  <input type="hidden" name="<?= str_replace($field, 'id', $name) ?>" value="<?= $brg->id ?>" />
                                  <?php endif ?>
                                    <div class="mws-form-col-2-8">
                                        <label class="mws-form-label"><?= $sub[1] ?></label>
                                        <div class="mws-form-item">
                                          <?php if (isset($sub[2])): ?>
                                            <select name="<?= $name ?>" class="small">
                                              <?php foreach ($sub[2] as $value => $option): ?>
                                                <option value="<?= $value ?>" <?= $value == $brg->$field ? 'selected' : '' ?>>
                                                  <?= $option ?>
                                                </option>
                                              <?php endforeach ?>
                                            </select>
                                          <?php else : ?>
                                            <input type="text"
                                            class="small <?= isset ($strings) && in_array($base_field, $strings)?'text-left':'text-right' ?>" 
                                            name="<?= $name ?>" value="<?= $brg->$field ?>">
                                          <?php endif ?>
                                          <?php if ($sub == end ($subfields)): ?>
                                            <a class="btn btn-danger btn-hapus-subform" onclick="$(this).parent().parent().parent().remove()">HAPUS</a>
                                          <?php endif ?>
                                        </div>
                                    </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        <?php endforeach; else: ?>

                            <div class="mws-form-row expandable-form">
                                <div class="mws-form-cols">
                                  <?php foreach ($subfields as $sub):  ?>
                                    <?php
                                      $base_field = explode('[', str_replace('][]', '', $sub[0]));
                                      $base_field = $base_field[1];
                                    ?>
                                    <div class="mws-form-col-2-8">
                                        <label class="mws-form-label"><?= $sub[1] ?></label>
                                        <div class="mws-form-item">
                                          <?php if (isset($sub[2])): ?>
                                            <select name="<?= $sub[0] ?>" class="small">
                                              <?php foreach ($sub[2] as $value => $option): ?>
                                                <option value="<?= $value ?>">
                                                  <?= $option ?>
                                                </option>
                                              <?php endforeach ?>
                                            </select>
                                          <?php else : ?>
                                            <input type="text" class="small <?= isset ($strings) && in_array($base_field, $strings)?'text-left':'text-right' ?>" 
                                            name="<?= $sub[0] ?>" value="">
                                          <?php endif ?>
                                        </div>
                                    </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                          <?php endif ?>

                    </div>      
                </div>