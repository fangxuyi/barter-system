<?php
// (A) PROCESS THE FORM
//process($_POST["item_number"]);

// (B) THEN SET THE RESPONSE MESSAGE
//$message = "SUCCESSFUL!";
// $message = "FAILURE!";
?>

<title>SUCCESSFUL</title>
</head>
<body>
    <div id="main_container">
        <div class="center_content">
            <div class="text_box">
                    <div class="header">SUCCESS!</div>
                    <div class="title">Your item has been listed</div>
                    <div class="title">Your item number is <?php print $item_number; ?></div>
                    <input type="button" value="Close" onclick="self.close()">
                </div>
                <div class="clear"></div>
            </div>

        </div>
    </body>
</html>
