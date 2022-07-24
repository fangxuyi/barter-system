<?php

include('lib/common.php');
// written by xluo99

if (!isset($_SESSION['email'])) {
	header('Location: login.php');
	exit();
}


$query = "SELECT p.postal_code, latitude, longitude FROM postalcode p JOIN user u on p.postal_code=u.postal_code WHERE u.email='{$_SESSION['email']}'";
$result = mysqli_query($db, $query);
if (!empty($result) && (mysqli_num_rows($result) > 0) ){
    $row = mysqli_fetch_array($result);
      $myPostalCode=$row[0];
      $mylat=$row[1];
      $mylon=$row[2];

}

function highlightWords($text, $word){
    $text = preg_replace('#'. preg_quote($word) .'#i', '<span style="background-color:#f8ff00;">\\0</span>', $text);
    return $text;
}




?>





<?php include("lib/header.php"); ?>

		<title>Search Item</title>
	</head>

	<body>
    	<div id="main_container">
            <?php include("lib/menu.php"); ?>

			<div class="center_content">
				<div class="center_left">

					<div class="features">

						<div class="search_section">
							<div class="subtitle">Search for Item</div>

							<form name="searchform" action="search_items.php" method="POST">
								<table>
									<tr>
										<td><input type="radio" name="searchOption" value="useKeyword">By Keyword <input type ="text" name="keyword" /></td>
									</tr>
									<tr>
										<td><input type="radio" name="searchOption" value="myCode">In my postal code</td>
									</tr>
									<tr>
										<td><input type="radio" name="searchOption" value="numofMiles">Within <input type="number" name="mile" size="4"> miles of me</td>
									</tr>
									<tr>
										<td><input type="radio" name="searchOption" value="postal"> In postal code: <input type="text" name="postalCode"></td>
									</tr>


								</table>
								<br><input type="submit" name="submit" value="Search">

							</form>
						</div>



						<div class="profile_section">
						<div class="subtitle">Search Result</div>

						<table>
							<tr>
							<td class="heading">Item #</td>
							<td class="heading">Game Type</td>
							<td class="heading">Title</td>
							<td class="heading">Condition</td>
							<td class="heading">Description</td>
							<td class="heading">Response Time(days)</td>
							<td class="heading">Rank</td>
							<td class="heading">Distance</td>

							</tr>

							<?php
							//When search by keyword
							if(isset($_POST["searchOption"]) && $_POST["searchOption"] == "useKeyword"){
								$keyword = mysqli_real_escape_string($db, $_POST['keyword']);
								print 'Search results: keyword '. $keyword;

								$sql = "SELECT DISTINCT item_number, li.email, type, title, condition_type, IF(CHAR_LENGTH(li.description)>100, CONCAT(LEFT(li.description,100),\"...\"),li.description) as 'description', ROUND((3958.75 *acos(cos(radians($mylat))*cos(radians(p.latitude))*cos(radians(p.longitude)-radians($mylon))+sin(radians($mylat))*sin(radians(p.latitude)))),2) AS distance
								"

								. "FROM listeditem li LEFT JOIN trade t on li.item_number=t.desired_item_num "

								. "JOIN user u ON u.email=li.email "

								. "JOIN postalcode p ON p.postal_code=u.postal_code "

								. "WHERE li.item_number NOT IN (SELECT proposed_item_num from trade where accepted IS NULL OR accepted=1) AND li.item_number NOT IN (SELECT desired_item_num from trade where accepted IS NULL OR accepted=1) AND (li.title LIKE '%$keyword%' OR li.description LIKE '%$keyword%') ORDER BY distance, item_number ASC;";

                                  $result = mysqli_query($db, $sql);
								  if(mysqli_num_rows($result)==0){
									print ' No results found!';
								  }
								  while($row = mysqli_fetch_array($result)){
									$userEmail=$row[1];
									$title =$row[3];
									$desc=$row[5];
									//Get avg response time
									$rtSql = "SELECT ROUND(AVG(DATEDIFF(t.date_of_response, t.date_of_proposal)),1) AS 'response_time' ".
									"FROM trade t JOIN listedItem li on t.desired_item_num=li.item_number ".
									"WHERE t.date_of_response IS NOT NULL AND li.email='$userEmail'";
									$rtresult = mysqli_query($db, $rtSql);
									$rtrow = mysqli_fetch_array($rtresult);
									$avgTime = $rtrow[0];
									//Get completed trade count
							$countQuery = "SELECT u.email, count.count FROM user u ".
                           "LEFT JOIN (".
                           "SELECT li.email, COUNT(li.email) as count ".
                           "FROM listeditem li ".
                           "WHERE li.item_number IN (SELECT DISTINCT desired_item_num as item_number FROM trade WHERE accepted = 1 UNION SELECT DISTINCT proposed_item_num as item_number FROM trade WHERE accepted = 1) ".
                           "GROUP BY li.email) count ON ".
                           "count.email = '$userEmail'";
						   $countResult = mysqli_query($db, $countQuery);
						   $countRow=mysqli_fetch_array($countResult);
						   $numOfTrade=$countRow[1];



									print "<tr>";
									print "<td>".$row["item_number"]."</td>";
									print "<td>".$row["type"]."</td>";

									if(stripos($title, $keyword) !== FALSE){
									print '<td><span style="background-color: #D4F1F4">'.$title.'</span></td>';
								  }
									else {
										print '<td>'.$title.'</td>';
									}
									print "<td>".$row["condition_type"]."</td>";
									if(stripos($desc, $keyword) !== FALSE){
									print '<td><span style="background-color: #D4F1F4">'.$desc.'</span></td>';
									}
									else {
									print '<td>'.$desc.'</td>';
									}


									if ($avgTime == 0){
                                        print '<td style="color:#000000">None</td>';
                                      } else if ($avgTime <= 7 && $avgTime>0) {
                                        print '<td style="color:#008000">'.$avgTime.'</td>';
                                      } else if ($avgTime <= 14 && $avgTime>7.1) {
                                        print '<td style="color:#FFFF00">'.$avgTime.'</td>';
                                      } else if ($avgTime < 20.9 && $avgTime>=14.1) {
                                        print '<td style="color:#FFA500">'.$avgTime.'</td>';
                                      } else if ($avgTime <= 27.9 && $avgTime>=21) {
                                        print '<td style="color:#FF0000">'.$avgTime.'</td>';
                                      } else if ($avgTime >= 28) {
                                        print ' <b><td style="color:#FF0000"><b>'.$avgTime.'</b></td>';
                                      }
									if ($numOfTrade == 0.){
										print '<td>None</td>';
									  } else if ($numOfTrade <= 2.) {
										print '<td>Aluminium</td>';
									  } else if ($numOfTrade <= 3.) {
										print '<td>Bronze</td>';
									  } else if ($numOfTrade <= 5.) {
										print '<td>Silver</td>';
									  } else if ($numOfTrade <= 7.) {
										print '<td>Gold</td>';
									  } else if ($numOfTrade <= 9.) {
										print '<td>Platinum</td>';
									  } else {
										print '<td>Alexandium</td>';
									  }
									print "<td>".$row["distance"]."</td>";
									print "<td> <a href=\"item_detail.php?item_number=".$row['item_number']."\">Detail</a> </td>";

									print "</tr>";
								  }
							}

                          //Search by my postal code
							if(isset($_POST["searchOption"]) && $_POST["searchOption"] == "myCode"){

								print 'Search results: In my postal code';

								$sql = "SELECT DISTINCT item_number, li.email, type, title, condition_type, IF(CHAR_LENGTH(li.description)>100, CONCAT(LEFT(li.description,100),\"...\"),li.description) as 'description', ROUND((3958.75 *acos(cos(radians($mylat))*cos(radians(p.latitude))*cos(radians(p.longitude)-radians($mylon))+sin(radians($mylat))*sin(radians(p.latitude)))),2) AS distance
								"

								. "FROM listeditem li LEFT JOIN trade t on li.item_number=t.desired_item_num "

								. "JOIN user u ON u.email=li.email "

								. "JOIN postalcode p ON p.postal_code=u.postal_code "

								. "WHERE li.item_number NOT IN (SELECT proposed_item_num from trade where accepted IS NULL OR accepted=1) AND li.item_number NOT IN (SELECT desired_item_num from trade where accepted IS NULL OR accepted=1) AND u.postal_code=$myPostalCode ORDER BY distance, item_number ASC;";

                                  $result = mysqli_query($db, $sql);
								  if(mysqli_num_rows($result)==0){
									echo ' No results found!';
								  }
								  while($row = mysqli_fetch_array($result)){
									$userEmail=$row[1];
									//Get avg response time
									$rtSql = "SELECT ROUND(AVG(DATEDIFF(t.date_of_response, t.date_of_proposal)),1) AS 'response_time' ".
									"FROM trade t JOIN listedItem li on t.desired_item_num=li.item_number ".
									"WHERE t.date_of_response IS NOT NULL AND li.email='$userEmail'";
									$rtresult = mysqli_query($db, $rtSql);
									$rtrow = mysqli_fetch_array($rtresult);
									$avgTime = $rtrow[0];

									//Get completed trade count
							$countQuery = "SELECT u.email, count.count FROM user u ".
							"LEFT JOIN (".
							"SELECT li.email, COUNT(li.email) as count ".
							"FROM listeditem li ".
							"WHERE li.item_number IN (SELECT DISTINCT desired_item_num as item_number FROM trade WHERE accepted = 1 UNION SELECT DISTINCT proposed_item_num as item_number FROM trade WHERE accepted = 1) ".
							"GROUP BY li.email) count ON ".
							"count.email = '$userEmail'";
							$countResult = mysqli_query($db, $countQuery);
							$countRow=mysqli_fetch_array($countResult);
							$numOfTrade=$countRow[1];

									print "<tr>";
									print "<td>".$row["item_number"]."</td>";
									print "<td>".$row["type"]."</td>";
									 print "<td>".$row["title"]."</td>";
									print "<td>".$row["condition_type"]."</td>";

									print "<td>".$row["description"]."</td>";
									if ($avgTime == 0){
                                        print '<td style="color:#000000">None</td>';
                                      } else if ($avgTime <= 7 && $avgTime>0) {
                                        print '<td style="color:#008000">'.$avgTime.'</td>';
                                      } else if ($avgTime <= 14 && $avgTime>7.1) {
                                        print '<td style="color:#FFFF00">'.$avgTime.'</td>';
                                      } else if ($avgTime < 20.9 && $avgTime>=14.1) {
                                        print '<td style="color:#FFA500">'.$avgTime.'</td>';
                                      } else if ($avgTime <= 27.9 && $avgTime>=21) {
                                        print '<td style="color:#FF0000">'.$avgTime.'</td>';
                                      } else if ($avgTime >= 28) {
                                        print ' <b><td style="color:#FF0000"><b>'.$avgTime.'</b></td>';
                                      }
									if ($numOfTrade == 0.){
										print '<td>None</td>';
									  } else if ($numOfTrade <= 2.) {
										print '<td>Aluminium</td>';
									  } else if ($numOfTrade <= 3.) {
										print '<td>Bronze</td>';
									  } else if ($numOfTrade <= 5.) {
										print '<td>Silver</td>';
									  } else if ($numOfTrade <= 7.) {
										print '<td>Gold</td>';
									  } else if ($numOfTrade <= 9.) {
										print '<td>Platinum</td>';
									  } else {
										print '<td>Alexandium</td>';
									  }
									print "<td>".$row["distance"]."</td>";
									print "<td> <a href=\"item_detail.php?item_number=".$row['item_number']."\">Detail</a> </td>";
									print "</tr>";
								  }
							}

							//search by in postal code
							if(isset($_POST["searchOption"]) && $_POST["searchOption"] == "postal"){
								$postalCode = mysqli_real_escape_string($db, $_POST['postalCode']);

								print 'Search results: In postal code '. $postalCode;

								$sql = "SELECT DISTINCT item_number, li.email, type, title, condition_type, IF(CHAR_LENGTH(li.description)>100, CONCAT(LEFT(li.description,100),\"...\"),li.description) as 'description', ROUND((3958.75 *acos(cos(radians($mylat))*cos(radians(p.latitude))*cos(radians(p.longitude)-radians($mylon))+sin(radians($mylat))*sin(radians(p.latitude)))),2) AS distance
								"

								. "FROM listeditem li LEFT JOIN trade t on li.item_number=t.desired_item_num "

								. "JOIN user u ON u.email=li.email "

								. "JOIN postalcode p ON p.postal_code=u.postal_code "

								. "WHERE li.item_number NOT IN (SELECT proposed_item_num from trade where accepted IS NULL OR accepted=1) AND li.item_number NOT IN (SELECT desired_item_num from trade where accepted IS NULL OR accepted=1) AND u.postal_code=$postalCode ORDER BY distance, item_number ASC;";

                                  $result = mysqli_query($db, $sql);
								  if(mysqli_num_rows($result)==0){
									echo ' No results found!';
								  }
								  while($row = mysqli_fetch_array($result)){
									$userEmail=$row[1];
									//Get avg response time
									$rtSql = "SELECT ROUND(AVG(DATEDIFF(t.date_of_response, t.date_of_proposal)),1) AS 'response_time' ".
									"FROM trade t JOIN listedItem li on t.desired_item_num=li.item_number ".
									"WHERE t.date_of_response IS NOT NULL AND li.email='$userEmail'";
									$rtresult = mysqli_query($db, $rtSql);
									$rtrow = mysqli_fetch_array($rtresult);
									$avgTime=$rtrow[0];
									//Get completed trade count
							$countQuery = "SELECT u.email, count.count FROM user u ".
							"LEFT JOIN (".
							"SELECT li.email, COUNT(li.email) as count ".
							"FROM listeditem li ".
							"WHERE li.item_number IN (SELECT DISTINCT desired_item_num as item_number FROM trade WHERE accepted = 1 UNION SELECT DISTINCT proposed_item_num as item_number FROM trade WHERE accepted = 1) ".
							"GROUP BY li.email) count ON ".
							"count.email = '$userEmail'";
							$countResult = mysqli_query($db, $countQuery);
							$countRow=mysqli_fetch_array($countResult);
							$numOfTrade=$countRow[1];

									print "<tr>";
									print "<td>".$row["item_number"]."</td>";
									print "<td>".$row["type"]."</td>";
									 print "<td>".$row["title"]."</td>";
									print "<td>".$row["condition_type"]."</td>";

									print "<td>".$row["description"]."</td>";
									if ($avgTime == 0){
                                        print '<td style="color:#000000">None</td>';
                                      } else if ($avgTime <= 7 && $avgTime>0) {
                                        print '<td style="color:#008000">'.$avgTime.'</td>';
                                      } else if ($avgTime <= 14 && $avgTime>7.1) {
                                        print '<td style="color:#FFFF00">'.$avgTime.'</td>';
                                      } else if ($avgTime < 20.9 && $avgTime>=14.1) {
                                        print '<td style="color:#FFA500">'.$avgTime.'</td>';
                                      } else if ($avgTime <= 27.9 && $avgTime>=21) {
                                        print '<td style="color:#FF0000">'.$avgTime.'</td>';
                                      } else if ($avgTime >= 28) {
                                        print ' <b><td style="color:#FF0000"><b>'.$avgTime.'</b></td>';
                                      }
									if ($numOfTrade == 0.){
										print '<td>None</td>';
									  } else if ($numOfTrade <= 2.) {
										print '<td>Aluminium</td>';
									  } else if ($numOfTrade <= 3.) {
										print '<td>Bronze</td>';
									  } else if ($numOfTrade <= 5.) {
										print '<td>Silver</td>';
									  } else if ($numOfTrade <= 7.) {
										print '<td>Gold</td>';
									  } else if ($numOfTrade <= 9.) {
										print '<td>Platinum</td>';
									  } else {
										print '<td>Alexandium</td>';
									  }
									print "<td>".$row["distance"]."</td>";
									print "<td> <a href=\"item_detail.php?item_number=".$row['item_number']."\">Detail</a> </td>";
									print "</tr>";
								  }
							}

							//Search by x miles
							if(isset($_POST["searchOption"]) && $_POST["searchOption"] == "numofMiles"){
								$mileNum = mysqli_real_escape_string($db, $_POST['mile']);

								print 'Search results: Within '. $mileNum. ' of me';

								$sql = "SELECT DISTINCT item_number, li.email, type, title, condition_type, IF(CHAR_LENGTH(li.description)>100, CONCAT(LEFT(li.description,100),\"...\"),li.description) as 'description', ROUND((3958.75 *acos(cos(radians($mylat))*cos(radians(p.latitude))*cos(radians(p.longitude)-radians($mylon))+sin(radians($mylat))*sin(radians(p.latitude)))),2) AS distance
								"
								. "FROM listeditem li LEFT JOIN trade t on li.item_number=t.desired_item_num "

								. "JOIN user u ON u.email=li.email "

								. "JOIN postalcode p ON p.postal_code=u.postal_code "

								. "WHERE li.item_number NOT IN (SELECT proposed_item_num from trade where accepted IS NULL OR accepted=1) AND li.item_number NOT IN (SELECT desired_item_num from trade where accepted IS NULL OR accepted=1)"
								. "HAVING distance<=$mileNum ORDER BY distance,item_number ASC;";

                                  $result = mysqli_query($db, $sql);
								  if(mysqli_num_rows($result)==0){
									echo ' No results found!';
								  }
								  while($row = mysqli_fetch_array($result)){

									$userEmail=$row[1];
									//Get avg response time
									$rtSql = "SELECT ROUND(AVG(DATEDIFF(t.date_of_response, t.date_of_proposal)),1) AS 'response_time' ".
									"FROM trade t JOIN listedItem li on t.desired_item_num=li.item_number ".
									"WHERE t.date_of_response IS NOT NULL AND li.email='$userEmail'";
									$rtresult = mysqli_query($db, $rtSql);
									$rtrow = mysqli_fetch_array($rtresult);
									$avgTime = $rtrow[0];
									//Get completed trade count
							$countQuery = "SELECT u.email, count.count FROM user u ".
							"LEFT JOIN (".
							"SELECT li.email, COUNT(li.email) as count ".
							"FROM listeditem li ".
							"WHERE li.item_number IN (SELECT DISTINCT desired_item_num as item_number FROM trade WHERE accepted = 1 UNION SELECT DISTINCT proposed_item_num as item_number FROM trade WHERE accepted = 1) ".
							"GROUP BY li.email) count ON ".
							"count.email = '$userEmail'";
							$countResult = mysqli_query($db, $countQuery);
							$countRow=mysqli_fetch_array($countResult);
							$numOfTrade=$countRow[1];

									print "<tr>";
									print "<td>".$row["item_number"]."</td>";
									print "<td>".$row["type"]."</td>";
									 print "<td>".$row["title"]."</td>";
									print "<td>".$row["condition_type"]."</td>";
									print "<td>".$row["description"]."</td>";
									if ($avgTime == 0){
                                        print '<td style="color:#000000">None</td>';
                                      } else if ($avgTime <= 7 && $avgTime>0) {
                                        print '<td style="color:#008000">'.$avgTime.'</td>';
                                      } else if ($avgTime <= 14 && $avgTime>7.1) {
                                        print '<td style="color:#FFFF00">'.$avgTime.'</td>';
                                      } else if ($avgTime < 20.9 && $avgTime>=14.1) {
                                        print '<td style="color:#FFA500">'.$avgTime.'</td>';
                                      } else if ($avgTime <= 27.9 && $avgTime>=21) {
                                        print '<td style="color:#FF0000">'.$avgTime.'</td>';
                                      } else if ($avgTime >= 28) {
                                        print ' <b><td style="color:#FF0000"><b>'.$avgTime.'</b></td>';
                                      }
									if ($numOfTrade == 0.){
										print '<td>None</td>';
									  } else if ($numOfTrade <= 2.) {
										print '<td>Aluminium</td>';
									  } else if ($numOfTrade <= 3.) {
										print '<td>Bronze</td>';
									  } else if ($numOfTrade <= 5.) {
										print '<td>Silver</td>';
									  } else if ($numOfTrade <= 7.) {
										print '<td>Gold</td>';
									  } else if ($numOfTrade <= 9.) {
										print '<td>Platinum</td>';
									  } else {
										print '<td>Alexandium</td>';
									  }
									print "<td>".$row["distance"]."</td>";
									print "<td> <a href=\"item_detail.php?item_number=".$row['item_number']."\">Detail</a> </td>";
									print "</tr>";

								  }
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
