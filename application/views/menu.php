        <!-- Sidebar Wrapper -->
        <div id="mws-sidebar">
        
            <!-- Hidden Nav Collapse Button -->
            <div id="mws-nav-collapse">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <!-- Main Navigation -->
            <div id="mws-navigation">
                <ul>
                  <?php foreach ($menu as $m): ?>
                    <?php if (!isset($m[3])): ?>
                    <li><a href="<?= site_url($m[1]) ?>"><i class="icon-<?= $m[2] ?>"></i> <?= $m[0] ?></a></li>
                    <?php else: ?>
                      <li>
                        <a href="<?= site_url($m[1]) ?>"><i class="icon-<?= $m[2] ?>"></i> <?= $m[0] ?></a>
                        <ul>
                          <?php foreach ($m[3] as $sm) : ?>
                            <li><a href="<?= site_url($sm[1]) ?>"><?= $sm[0] ?></a></li>
                          <?php endforeach ?>
                        </ul>
                      </li>
                    <?php endif ?>
                  <?php endforeach ?>
                </ul>
            </div>
        </div>
        
        <!-- Main Container Start -->
        <div id="mws-container" class="clearfix">
        
          <!-- Inner Container Start -->
            <div class="container">
