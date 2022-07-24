<?php

include('lib/common.php');
// written by fyi33

function calculate_distance($row) {

    $deltalat = deg2rad($row["lat1"]) - deg2rad($row["lat2"]);
    $deltalon = deg2rad($row["lon1"]) - deg2rad($row["lon2"]);
    $a = sin($deltalat / 2) * sin($deltalat / 2) + cos(deg2rad($row["lat1"])) * cos(deg2rad($row["lat2"])) * sin($deltalon / 2) * sin($deltalon / 2);
    $distance = 3958.75 * 2 * atan2(sqrt($a), sqrt(1 - $a));
    return round($distance, 2);

}

function calculate_rank($count) {

    if ($count == 0.){
      return 'None';
    } else if ($count <= 2.) {
      return 'Aluminium';
    } else if ($count <= 3.) {
      return 'Bronze';
    } else if ($count <= 5.) {
      return 'Silver';
    } else if ($count <= 7.) {
      return 'Gold';
    } else if ($count <= 9.) {
      return 'Platinum';
    } else {
      return 'Alexandium';
    }

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['accept'])) {
        $accept_value = $_POST['accept'];
        $tradenumbers = explode(" ", $accept_value);
        $desired_item_num = $tradenumbers[0];
        $proposed_item_num = $tradenumbers[1];

        $query = "UPDATE trade SET accepted = 1, date_of_response = '". date("Y-m-d") ."' WHERE desired_item_num = " . $desired_item_num . " AND proposed_item_num = " . $proposed_item_num;
        $result_accept = mysqli_query($db, $query);

        $query1 = "SELECT listeditem.email, user.first_name FROM listeditem JOIN user ON user.email = listeditem.email WHERE item_number = " . $proposed_item_num;
        $result1 = mysqli_query($db, $query1);
        $row = mysqli_fetch_array($result1);

        if ($result_accept == 1) {
            echo '<script type="text/javascript">alert("Trade is accepted! \nContact proposer '.$row["email"].' \nName '.$row["first_name"].'")</script>';
        } else {
            array_push($error_msg,  "ERROR Trying to Accept Trades" );
        }
    }
    if(isset($_POST['reject'])) {
        $reject_value = $_POST['reject'];
        $tradenumbers = explode(" ", $reject_value);
        $desired_item_num = $tradenumbers[0];
        $proposed_item_num = $tradenumbers[1];

        $query = "UPDATE trade SET accepted = 0, date_of_response = '". date("Y-m-d") ."' WHERE desired_item_num = " . $desired_item_num . " AND proposed_item_num = " . $proposed_item_num;
        $result_reject = mysqli_query($db, $query);

        if ($result_reject == 1) {
            echo '<script type="text/javascript">alert("Trade is rejected!")</script>';
        } else {
            array_push($error_msg,  "ERROR Trying to Reject Trades" );
        }
    }
}

?>

<?php include("lib/header.php"); ?>
<title>Trade Response</title>
	</head>

	<body>
		<div id="response_table_container">
        <?php include("lib/menu.php"); ?>

        <div class="trade_response_center">
            <table>
                <tr>
                    <th>Date</th>
                    <th>Desired Item</th>
                    <th>Proposer</th>
                    <th>Rank</th>
                    <th>Distance</th>
                    <th>Proposed Item</th>
                    <th>Decision</th>
                </tr>
               <?php

                   $query = "SELECT t.date_of_proposal, t.desired_item_num, t.proposed_item_num, l1.title as desired_item_title, l2.title as proposed_item_title, u1.nickname, rank.count, p1.latitude as lat1, p1.longitude as lon1, p2.latitude as lat2, p2.longitude as lon2 FROM trade t ".
                          "JOIN listeditem l1 ON l1.item_number = t.desired_item_num ". //counterpart
                          "JOIN listeditem l2 ON l2.item_number = t.proposed_item_num ". //proposer
                          "JOIN user u1 ON l2.email = u1.email ". //proposer
                          "JOIN user u2 ON l1.email = u2.email ". //counterpart
                          "JOIN postalcode p1 ON p1.postal_code = u1.postal_code ". //proposer
                          "JOIN postalcode p2 ON p2.postal_code = u2.postal_code ". //counterpart
                          "JOIN (SELECT u.email, count.count FROM user u ".
                                 "LEFT JOIN (".
                                 "SELECT li.email, COUNT(li.email) as count ".
                                 "FROM listeditem li ".
                                 "WHERE li.item_number IN (SELECT DISTINCT desired_item_num as item_number FROM trade WHERE accepted = 1 UNION SELECT DISTINCT proposed_item_num as item_number FROM trade WHERE accepted = 1) ".
                                 "GROUP BY li.email) count ON ".
                                 "count.email = u.email) rank ON rank.email = u1.email ". //proposer rank
                          "WHERE l1.email = '{$_SESSION['email']}' and t.accepted IS NULL ".
                          "ORDER BY t.date_of_proposal";
                   $result = mysqli_query($db, $query);

                   if ( $result != 1 ) {
                      array_push($error_msg,  "ERROR Pulling Trades" );
                   }

                   if (mysqli_num_rows($result) == 0) {
                       echo '<script>window.location.href="welcome.php"</script>';
                   }

                   while($rw = mysqli_fetch_array($result))
                   {
                        print "<tr>";
                        print "<td>".$rw["date_of_proposal"]."</td>";
                        print '<td> <a href="item_detail.php?item_number='.urlencode($rw["desired_item_num"]).'"/>'.$rw["desired_item_title"]."</td>";
                        print "<td>".$rw["nickname"]."</td>";
                        print '<td>'.calculate_rank($rw["count"]).'</td>';
                        print "<td>".calculate_distance($rw)."</td>";
                        print '<td> <a href="item_detail.php?item_number=' . urlencode($rw["proposed_item_num"]).'"/>'.$rw["proposed_item_title"]."</td>";
                        print "<td>";
                        print '<form method="post" enctype="multipart/form-data">';
                        print '<button name="accept" value="'.$rw["desired_item_num"].' '.$rw["proposed_item_num"].'">Accept</>';
                        print '<button name="reject" value="'.$rw["desired_item_num"].' '.$rw["proposed_item_num"].'">Reject</>';
                        print "</form>";
                        print "</td>";
                        print "</tr>";
                   } ?>
            </table>
        <?php include("lib/error.php"); ?>
        <div class="clear"></div>
        </div>
        <?php include("lib/footer.php"); ?>

		</div>
	</body>
</html>
