
              <div class="mws-stat-container clearfix text-right table-panel">
                <a class="mws-stat" href="<?= current_url() . '/form' ?>">
                  <span class="mws-stat-icon icol32-add"></span>
                  <span class="mws-stat-content">
                    <h4>INPUT</h4>
                  </span>
                </a>
              </div>

              <div class="mws-panel grid_8">
                  <div class="mws-panel-header">
                      <span id="pagetitle"></span>
                    </div>
                    <div class="mws-panel-body no-padding">
                        <?php include APPPATH . '/views/message.php'; ?>
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
                                <tr onclick="window.location='<?= current_url() . "/form/$tb->id" ?>'">
                                  <td><?= $no ?></td>
                                  <?php foreach ($thead as $th): ?>
                                  <td><?= $tb->$th[0] ?></td>
                                  <?php endforeach ?>
                                </tr>
                              <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>