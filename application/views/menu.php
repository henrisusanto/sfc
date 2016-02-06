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
                    <li><a href="<?= site_url($m[1]) ?>"><i class="icon-<?= $m[2] ?>"></i> <?= $m[0] ?></a></li>
                  <?php endforeach ?>
                </ul>
            </div>
        </div>
        
        <!-- Main Container Start -->
        <div id="mws-container" class="clearfix">
        
          <!-- Inner Container Start -->
            <div class="container">