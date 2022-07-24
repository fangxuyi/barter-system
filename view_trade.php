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

    if (!empty($result) && (mysqli_num_rows($result) > 0) ) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $count = mysqli_num_rows($result);
    } else {
            array_push($error_msg,  "SELECT ERROR: No User information... <br>".  __FILE__ ." line:". __LINE__ );
    }

    $user_name = $row['first_name'] . " " . $row['last_name'];
?>

<?php include("lib/header.php"); ?>

<title>Trade Plaza Trade History</title>
	</head>

	<body>
		<div id="main_container">
    <?php include("lib/menu.php"); ?>

			<div class="center_content">
				<div class="center_left">
					<div class="title_name"><?php print $user_name; ?></div>
					<div class="features">
						<div class="profile_section">
								<div class="subtitle">Trade Counts</div>
										<table>
													<tr>
													<td class="heading">My role</td>
													<td class="heading">Total</td>
													<td class="heading">Accepted</td>
													<td class="heading">Rejected</td>
													<td class="heading">Rejected %</td>
													</tr>

												<?php

													 $query = "SELECT CASE WHEN u1.email = '{$_SESSION['email']}' THEN 'Proposer' ELSE 'Counterparty' END AS my_role, COUNT(trade.proposed_item_num) AS counttotal, SUM(trade.accepted) AS sumaccepted, (COUNT(trade.proposed_item_num) - SUM(trade.accepted)) AS sumrejected, round((COUNT(trade.proposed_item_num) - SUM(trade.accepted))*100/COUNT(trade.proposed_item_num),1) AS percentage " .
															 	 	 "FROM ListedItem " .
																	 "JOIN User u1 ON u1.email = ListedItem.email " .
																	 "JOIN Trade ON Trade.proposed_item_num = ListedItem.item_number " .
																	 "WHERE ListedItem.email='{$_SESSION['email']}' " .
 																   "AND trade.date_of_proposal IS NOT NULL AND trade.date_of_response IS NOT NULL ";

																	 $result = mysqli_query($db, $query);
																	 include('lib/show_queries.php');
																	 if (!empty($result) && (mysqli_num_rows($result) == 0) ) {
																			 array_push($error_msg,  "SELECT ERROR: find Trade <br>" . __FILE__ ." line:". __LINE__ );
																	}
																	 while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
																	

																	 print "<tr>";
																	 print "<td>{$row['my_role']}</td>";
																	 print "<td>{$row['counttotal']}</td>";
																	 print "<td>{$row['sumaccepted']}</td>";
																	 print "<td>{$row['sumrejected']}</td>";
																	 if($row['percentage'] >= 50) {
																	 print "<td><span style='background-color: #FFBFBF'>{$row['percentage']}%</span></td>";
																	 print "</tr>";
																 }
																	 else {
																		 print "<td>{$row['percentage']}%</span></td>";
																		 print "</tr>";
																	 }
																	 print "</tr>";
 																}

 										?>
										<?php

											 $query = "SELECT CASE WHEN u2.email = '{$_SESSION['email']}' THEN 'Counterparty' ELSE 'Proposer' END AS my_role, COUNT(trade.desired_item_num) AS counttotal, SUM(trade.accepted) AS sumaccepted, (COUNT(trade.desired_item_num) - SUM(trade.accepted)) AS sumrejected, round((COUNT(trade.proposed_item_num) - SUM(trade.accepted))*100/COUNT(trade.desired_item_num),2) AS percentage " .
															 "FROM ListedItem " .
															 "JOIN User u2 ON u2.email = ListedItem.email " .
															 "JOIN Trade ON Trade.desired_item_num = ListedItem.item_number " .
															 "WHERE ListedItem.email='{$_SESSION['email']}' " .
															 "AND trade.date_of_proposal IS NOT NULL AND trade.date_of_response IS NOT NULL ";

															 $result = mysqli_query($db, $query);
															 include('lib/show_queries.php');
															 if (!empty($result) && (mysqli_num_rows($result) == 0) ) {
																	 array_push($error_msg,  "SELECT ERROR: find Trade <br>" . __FILE__ ." line:". __LINE__ );
															}
															 while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

															 print "<tr>";
															 print "<td>{$row['my_role']}</td>";
															 print "<td>{$row['counttotal']}</td>";
															 print "<td>{$row['sumaccepted']}</td>";
															 print "<td>{$row['sumrejected']}</td>";
															 if($row['percentage'] >= 50) {
															 print "<td><span style='background-color: #FFBFBF'>{$row['percentage']}%</span></td>";
															 print "</tr>";
														 }
															 else {
																 print "<td>{$row['percentage']}%</span></td>";
																 print "</tr>";
															 }
														}

										?>

									</table>
								</div>
						<div class="profile_section">
							<div class="subtitle">Trade History</div>
							<table>
								<tr>
									<td class="heading">Proposed Date</td>
									<td class="heading">Accepted/Rejected Date</td>
									<td class="heading">Trade Status</td>
									<td class="heading">Response time(days)</td>
									<td class="heading">My role</td>
									<td class="heading">Proposed Item</td>
									<td class="heading">Desired Item</td>
									<td class="heading">Other User</td>
									<td class="heading">Detail</td>
								</tr>

							<?php
                                $query = "SELECT trade.date_of_proposal, trade.date_of_response, trade.proposed_item_num,trade.desired_item_num, CASE WHEN trade.accepted = 1 THEN 'Accepted' WHEN trade.accepted = 0 THEN 'Rejected' ELSE 'Waiting'
END AS trade_status, ListedItem.title AS proposed_item_title,l1.title AS desired_item_title,  CASE WHEN u1.email = '{$_SESSION['email']}' THEN 'Proposer' ELSE 'Counterparty' END AS my_role, CASE WHEN u1.email = '{$_SESSION['email']}' THEN u2.nickname ELSE u1.nickname END AS trade_nickname,
DATEDIFF(trade.date_of_response,trade.date_of_proposal) AS DATEDIFF " .
																				 "FROM ListedItem " .
                                         "JOIN User u1 ON u1.email = ListedItem.email " .
																				 "JOIN Trade ON trade.proposed_item_num = ListedItem.item_number " .
																				 "JOIN ListedItem l1 ON l1.item_number = trade.desired_item_num " .
                                         "JOIN User u2 ON u2.email = l1.email " .
                                         "WHERE ListedItem.email='{$_SESSION['email']}' " .
 																   			 "AND date_of_proposal IS NOT NULL AND date_of_response IS NOT NULL ".
																				 "UNION ALL " .
																				 "SELECT trade.date_of_proposal, trade.date_of_response, trade.proposed_item_num,trade.desired_item_num, CASE WHEN trade.accepted = 1 THEN 'Accepted' WHEN trade.accepted = 0 THEN 'Rejected' ELSE 'Waiting'
END AS trade_status, l1.title AS proposed_item_title,ListedItem.title AS desired_item_title,  CASE WHEN u1.email = '{$_SESSION['email']}' THEN 'Proposer' ELSE 'Counterparty' END AS my_role, CASE WHEN u1.email = '{$_SESSION['email']}' THEN u2.nickname ELSE u1.nickname END AS trade_nickname,
DATEDIFF(trade.date_of_response,trade.date_of_proposal) AS DATEDIFF " .
				 																	"FROM ListedItem " .
				                                  "JOIN User u2 ON u2.email = ListedItem.email " .
				 																	"JOIN Trade ON trade.desired_item_num = ListedItem.item_number " .
				 																	"JOIN ListedItem l1 ON l1.item_number = trade.proposed_item_num " .
				                                  "JOIN User u1 ON u1.email = l1.email " .
				                                  "WHERE ListedItem.email='{$_SESSION['email']}' " .
 																   				"AND date_of_proposal IS NOT NULL AND date_of_response IS NOT NULL ".
                                          "ORDER BY date_of_response DESC, date_of_proposal ASC ";

                                $result = mysqli_query($db, $query);

																 if (!empty($result) && (mysqli_num_rows($result) == 0) ) {
																		 array_push($error_msg,  "SELECT ERROR: find Trade <br>" . __FILE__ ." line:". __LINE__ );
																}

																while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
																	  $desired_item_num = urlencode($row['desired_item_num']);
																	  $proposed_item_num = urlencode($row['proposed_item_num']);

																		print "<tr>";
																		print "<td>{$row['date_of_proposal']}</td>";
																		print "<td>{$row['date_of_response']}</td>";
																		print "<td>{$row['trade_status']}</td>";
																		print "<td>{$row['DATEDIFF']}</td>";
																		print "<td>{$row['my_role']}</td>";
																		print "<td>{$row['proposed_item_title']}</td>";
																		print "<td>{$row['desired_item_title']}</td>";
																		print "<td>{$row['trade_nickname']}</td>";
																		print "<td><a href='trade_detail.php?proposed_item_num=$proposed_item_num&desired_item_num=$desired_item_num'>Detail</a></td>";
																		print "</tr>";
																}

										?>

					 </table>
				</div>


							 </div>
						</div>

		                <?php include("lib/error.php"); ?>

						<div class="clear"></div>
					</div>

		               <?php include("lib/footer.php"); ?>

				</div>
			</body>
		</html>
