<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?><html>
  <head>
    <title><?=isset($sTitle) ? $sTitle : "Dimensions demo site"?></title>
    <meta charset="utf8">
    <script src="http://code.jquery.com/jquery-1.9.0.min.js"></script>
    <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <script src="js/dim_split.js"></script>
    <script src="js/jstree/jquery.jstree.js"></script>
    <script src="js/dim_tree.js"></script>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css"/>
  </head>
  <body>
      <div>
