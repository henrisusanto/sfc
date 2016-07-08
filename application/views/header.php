
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
<meta charset="utf-8">

<!-- Viewport Metatag -->
<meta name="viewport" content="width=device-width,initial-scale=1.0">

<?php foreach ($css as $c): ?><link rel="stylesheet" type="text/css" href="<?= $c ?>" media="screen"><?php endforeach ?>

<title>SFC - Super Fried Chicken</title>

</head>

<body>

  <!-- Header -->
  <div id="mws-header" class="clearfix">
    
      <!-- Logo Container -->
      <div id="mws-logo-container">
        
          <!-- Logo Wrapper, images put within this wrapper will always be vertically centered -->
          <div id="mws-logo-wrap" onclick="window.location='<?= site_url() ?>'">
              <img src="<?= base_url("assets/images/mws-logo.png") ?>" alt="mws admin">
          </div>
        </div>
        
    </div>
    
    <!-- Start Main Wrapper -->
    <div id="mws-wrapper">
    
      <!-- Necessary markup, do not remove -->
    <div id="mws-sidebar-stitch"></div>
    <div id="mws-sidebar-bg"></div>
        
