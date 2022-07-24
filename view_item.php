<?php

include('lib/common.php');
// written by GTusername3

if (!isset($_SESSION['email'])) {
	header('Location: login.php');
	exit();
}

$query = "SELECT first_name, last_name " .
		 "FROM User " .
		 "INNER JOIN Trader ON User.email = Trader.email " .
		 "WHERE User.email = '{$_SESSION['email']}'";

$result = mysqli_query($db, $query);
include('lib/show_queries.php');

if (!empty($result) && (mysqli_num_rows($result) > 0) ) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $count = mysqli_num_rows($result);
    $user_fullname = $row['first_name'] . " " . $row['last_name'];
} else {
        array_push($error_msg,  "SELECT ERROR: User profile <br>" . __FILE__ ." line:". __LINE__ );
}

?>

<?php include("lib/header.php"); ?>
		<title>Trade Plaza View Items</title>
	</head>

	<body>
        <div id="main_container">
		    <?php include("lib/menu.php"); ?>

			<div class="center_content">
				<div class="center_left">

						<div class="title_name"><?php print $user_fullname; ?></div>

						<div class="features">
							<div class="profile_section">

								<div class="subtitle">Item counts</div>
								<table>
									<tr>
										<td class="heading">Board Games</td>
										<td class="heading">Playing card Games</td>
										<td class="heading">Computer Games</td>
										<td class="heading">Collectable Card Game</td>
										<td class="heading">Video Games</td>
										<td class="heading">Total</td>
									</tr>
								</table>

<?php
																			$query = "SELECT game_type, count(*) as COUNT" .
																							 "FROM ListedItem " .
																							 "INNER JOIN User ON user.email = ListedItem.email " .
																							 "WHERE ListedItem.email='{$_SESSION['email']}'" .
																							 "AND item_number IS NOT NULL " .
																							 "Group BY game_type";
																							 print "<tr>";
			                                         print "<td>{$row['game_type']} </td>";
			 																			  print "<td>{$row['count']} </td>";
																							print "</tr>";

?>

						<div class='profile_section'>
            <div class="subtitle">View Items</div>
							<table>
								<tr>
									<td class="heading">Item Number</td>
									<td class="heading">Title</td>
									<td class="heading">Description</td>
									<td class="heading">Condition_type</td>
									<td class="heading">Game_type</td>
									<td class="heading">Detail</td>
								</tr>

								<?php
                                    $query = "SELECT item_number, title, description, condition_type, game_type " .
                                             "FROM ListedItem " .
                                             "INNER JOIN User ON user.email = ListedItem.email " .
                                             "WHERE ListedItem.email='{$_SESSION['email']}'" .
                                             "AND item_number IS NOT NULL " .
                                             "ORDER BY item_number DESC";

                                    $result = mysqli_query($db, $query);
                                     if (!empty($result) && (mysqli_num_rows($result) == 0) ) {
                                         array_push($error_msg,  "SELECT ERROR: No Item found <br>" . __FILE__ ." line:". __LINE__ );
                                    }

                                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                                        print "<tr>";
                                        print "<td>{$row['item_number']} </td>";
																				print "<td>{$row['title']} </td>";
                                        print "<td>{$row['description']}</td>";
                                        print "<td>{$row['condition_type']}</td>";
																				print "<td>{$row['game_type']}</td>";
																				print "<td><a href='item.php?item_number=$item_number'>Detail </td>";
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
