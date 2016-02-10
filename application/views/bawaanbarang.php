                <div class="mws-panel grid_8">
                  <div class="mws-panel-header">
                      <span class="text-right">
                        <a class="btn btn-success tambah-barang">+ TAMBAH BARANG</a>
                      </span>
                    </div>
                    <div class="mws-panel-body no-padding">
                      <form class="mws-form form-bawaan-detail" action="" method="POST">

                        <?php if (isset($subform1)): foreach ($subform as $brg) : ?>
                            <div class="mws-form-row">
                                <div class="mws-form-cols">
                                  <?php foreach ($subfields1 as $sub): 
                                    $field = str_replace('[]', '', $sub[0]);
                                    $name = str_replace('[]', "[$brg->id]", $sub[0]);
                                  ?>
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
                                            class="small text-right <?= $sub[0]=='waktu'?'mws-dtpicker':'' ?>" 
                                            name="<?= $name ?>" 
                                            value="<?= $brg->$field ?>">
                                          <?php endif ?>
                                        </div>
                                    </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        <?php endforeach; else: ?>

                            <div class="mws-form-row">
                                <div class="mws-form-cols">
                                  <?php foreach ($subfields1 as $sub):  ?>
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
                                            <input type="text" 
                                            class="small text-right <?= $sub[0]=='waktu'?'mws-dtpicker':'' ?>" 
                                            name="<?= $sub[0] ?>" 
                                            value="">
                                          <?php endif ?>
                                        </div>
                                    </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                          <?php endif ?>

                      </form>
                    </div>      
                </div>
                <h3>HEN, BAWAAN PRODUK GOES HERE..</h3>