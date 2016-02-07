
              <!-- Statistics Button Container -->
              <div class="mws-stat-container clearfix text-right">
                  
                    <!-- Statistic Item -->

                    <a class="mws-stat" href="<?= $tablePage ?>">
                      <span class="mws-stat-icon icol32-arrow-undo"></span>
                        <span class="mws-stat-content">
                          <h4>KEMBALI</h4>
                        </span>
                    </a>

                    <?php if (isset($form)): ?>
                    <a class="mws-stat hapus">
                      <span class="mws-stat-icon icol32-cancel"></span>
                        <span class="mws-stat-content">
                          <h4>HAPUS</h4>
                        </span>
                    </a>
                    <div id="mws-jui-dialog">
                      <div class="mws-dialog-inner text-center">
                        <h3>APAKAH ANDA YAKIN ?</h3>
                        <a class="btn btn-success" href="<?= str_replace('form', 'delete', current_url()) ?>">YA</a>
                        <a class="btn tidak btn-danger">TIDAK</a>
                      </div>
                    </div>
                    <?php endif ?>

                    <a class="mws-stat" href="javascript:$('form').submit()">
                      <span class="mws-stat-icon icol32-accept"></span>
                        <span class="mws-stat-content">
                          <h4>SIMPAN</h4>
                        </span>
                    </a>
                </div>

                <div class="mws-panel grid_8">
                  <div class="mws-panel-header">
                      <!-- <span>Inline Form</span> -->
                    </div>
                    <div class="mws-panel-body no-padding">
                      <form class="mws-form" action="" method="POST">

                        <?php foreach ($fields as $field): ?>
                        <div class="mws-form-inline">
                          <div class="mws-form-row">
                            <label class="mws-form-label"><?= $field[1] ?></label>
                            <div class="mws-form-item">
                              <input type="text" 
                                class="small" 
                                name="<?= $field[0] ?>" 
                                value="<?= isset($form)?$form[$field[0]]:'' ?>">
                            </div>
                          </div>
                        </div>
                        <?php endforeach ?>

                        <!-- <div class="mws-button-row"> -->
                          <!-- <input type="submit" value="Submit" class="btn btn-danger">
                          <input type="reset" value="Reset" class="btn "> -->
                        <!-- </div> -->
                      </form>
                    </div>      
                </div>