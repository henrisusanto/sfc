
              <!-- Statistics Button Container -->
              <div class="mws-stat-container clearfix text-right">
                  
                    <!-- Statistic Item -->
                    
                  <a class="mws-stat" href="<?= current_url() . '/form' ?>">
                      <!-- Statistic Icon (edit to change icon) -->
                      <span class="mws-stat-icon icol32-add"></span>

                        <!-- Statistic Content -->
                        <span class="mws-stat-content">
                          <h4>INPUT</h4>
                        </span>
                    </a>
                </div>

              <div class="mws-panel grid_8">
                  <div class="mws-panel-header">
                      <!-- <span><i class="icon-table"></i> Data Table with Numbered Pagination</span> -->
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