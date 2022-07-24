<?php

include('lib/common.php');
// written by Awang497

if (!isset($_SESSION['email'])) {
	header('Location: login.php');
	exit();
}

    $query = "SELECT first_name, last_name, nickname " .
		 "FROM User " .
		 "WHERE User.email='{$_SESSION['email']}'";

    $result = mysqli_query($db, $query);
    include('lib/show_queries.php');
    //echo mysqli_num_rows($result);
    if ( !is_bool($result) && (mysqli_num_rows($result) > 0) ) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    } else {
        array_push($error_msg,  "Query ERROR: Failed to get User profile...<br>" . __FILE__ ." line:". __LINE__ );
    }
?>

<?php include("lib/header.php"); ?>
<title>Trade Plaza Profile</title>
</head>

<body>
		<div id="main_container">
    <?php include("lib/menu.php"); ?>

    <div class="center_content">

        <div class="center_left">
            <div class="title_name">Welcome,
                <?php print $row['first_name'] . ' ' . $row['last_name'];
                print ' (';
                print $row['nickname'];
                print ')';
                print '!'?>
							</div>

<div class="main_container">
<div class="text_box_main">
    <table>
        <tr>
            <td class="heading">Average Response Time</td>
            <?php
                $query = "SELECT ROUND(AVG(DATEDIFF(t.date_of_response, t.date_of_proposal)),1) AS 'response_time' ".
                        "FROM trade t JOIN listedItem li on t.desired_item_num=li.item_number ".
                        "WHERE t.date_of_response IS NOT NULL AND li.email='{$_SESSION['email']}'";

                $result = mysqli_query($db, $query);
                $avgRepTime = mysqli_fetch_array($result)['response_time'];

                if (is_null($avgRepTime)){

                  print '<td style="color:#000000">None</td>';
                }
                else if ($avgRepTime <= 7 && $avgRepTime>0) {
                  print '<td style="color:#008000">'.$avgRepTime.'</td>';
                } else if ($avgRepTime <= 14 && $avgRepTime>7.1) {
                  print '<td style="color:#FFFF00">'.$avgRepTime.'</td>';
                } else if ($avgRepTime < 20.9 && $avgRepTime>=14.1) {
                  print '<td style="color:#FFA500">'.$avgRepTime.'</td>';
                } else if ($avgRepTime <= 27.9 && $avgRepTime>=21) {
                  print '<td style="color:#FF0000">'.$avgRepTime.'</td>';
                } else if ($avgRepTime >= 28) {
                  print ' <td style="color:#FF0000"><span style="font-weight:bold">'.$avgRepTime.'</span></td>';
                } 

            ?>
        </tr>
        <tr>
            <td class="heading">Unaccepted Trade</td>
            <?php
                $query = "SELECT COUNT(li.item_number) AS PendingTradesasCounterpart " .
                "FROM trade t JOIN listedItem li on t.desired_item_num=li.item_number ".
                "WHERE t.accepted IS NULL AND li.email='{$_SESSION['email']}'".
                "GROUP BY li.email";

                $result = mysqli_query($db, $query);
                $pendingTrade = mysqli_fetch_array($result)['PendingTradesasCounterpart'];

                if (is_null($pendingTrade)){
                  print '<td>None</td>';
                }
                else if ($pendingTrade <= 2.) {
                  print '<td> <a href="trade_response.php"/>'.$pendingTrade.'</td>';
                } else {
                  print '<td> <a href="trade_response.php" class="redbold"/>'.$pendingTrade.'</td>';
                }
            ?>
        </tr>
        <tr>
            <td class="heading">Trader Rank</td>
            <?php
                $query = "SELECT u.email, count.count FROM user u ".
                           "LEFT JOIN (".
                           "SELECT li.email, COUNT(li.email) as count ".
                           "FROM listeditem li ".
                           "WHERE li.item_number IN (SELECT DISTINCT desired_item_num as item_number FROM trade WHERE accepted = 1 UNION SELECT DISTINCT proposed_item_num as item_number FROM trade WHERE accepted = 1) ".
                           "GROUP BY li.email) count ON ".
                           "count.email = u.email ".
                           "WHERE u.email='{$_SESSION['email']}'";

                $result = mysqli_query($db, $query);
                $totalTrade = mysqli_fetch_array($result)['count'];
                if ($totalTrade == 0.){
                  print '<td>None</td>';
                } else if ($totalTrade <= 2.) {
                  print '<td>Aluminium</td>';
                } else if ($totalTrade <= 3.) {
                  print '<td>Bronze</td>';
                } else if ($totalTrade <= 5.) {
                  print '<td>Silver</td>';
                } else if ($totalTrade <= 7.) {
                  print '<td>Gold</td>';
                } else if ($totalTrade <= 9.) {
                  print '<td>Platinum</td>';
                } else {
                  print '<td>Alexandium</td>';
                }
            ?>
        </tr>
    </table>
</div>
</div>


                <?php include("lib/error.php"); ?>

				<div class="clear"></div>

        </div>
		</div>
               <?php include("lib/footer.php"); ?>

		</div>
	</body>
</html>
