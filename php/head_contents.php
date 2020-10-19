<head>
    <title><?php echo TITLE; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="shortcut icon" href="/favicon.ico">   
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9">	<!-- Disable compatibility mode for IE browsing -->

    <link rel="stylesheet" type="text/css" href="/css/common.css?ver=<?php echo CSS_VERSION; ?>">

    <script>
        function toggle_editPanel(div_id) {
            var x = document.getElementById(div_id);

            if (x.style.display === "none") {
                x.style.display = "table-row";
            } else {
                x.style.display = "none";
            }
        } 
    </script>
</head>