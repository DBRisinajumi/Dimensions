<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?><html>
    <head>
        <meta charset="utf-8"/>
        <title><?=isset($sTitle) ? $sTitle : "Dimensions demo site"?></title>
        <script src="http://code.jquery.com/jquery-1.9.0.min.js"></script>
        <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
        <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
        <link rel="stylesheet" href="css/style.css"/>
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css"/>
    </head>
    <body>
        <div>
            <h1>Dimension demo</h1>
            <ul id="menu">
                <li><a href="reset.php" title="Reset all data">Reset data</a></li>
                <li><a href="tree.php" title="Define tree levels">Define levels</a></li>
                <li><a href="split.php" title="Add dimensions to record">Add dimensions</a></li>
                <li><a href="report.php" title="View data grid">Data grid</a></li>
            </ul>
        </div>