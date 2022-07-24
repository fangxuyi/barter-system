<?php

include('lib/common.php');
//written by Awang497

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


$proposed_item_number = mysqli_real_escape_string($db, $_REQUEST['proposed_item_num']);
$desired_item_number = mysqli_real_escape_string($db, $_REQUEST['desired_item_num']);



    $query = "SELECT Trade.proposed_item_num AS proposed_num, Trade.desired_item_num AS desired_num , Trade.date_of_proposal AS date_proposal, Trade.date_of_response AS date_response, l1.title AS p_title, l2.title AS d_title, l1.type AS p_gt, l2.type AS d_gt,l1.condition_type AS p_con, l2.condition_type AS d_con, l1.description AS p_des, l2.description AS d_des, CASE WHEN u1.email = '{$_SESSION['email']}' THEN 'Proposer' ELSE 'Counterparty' END AS my_role, CASE WHEN Trade.accepted = 1 THEN 'Accepted' ELSE 'Rejected' END AS trade_status, DATEDIFF(Trade.date_of_response,Trade.date_of_proposal) AS response_time, CASE WHEN u1.email = '{$_SESSION['email']}' THEN u2.email ELSE u1.email END AS c_email, CASE WHEN u1.email = '{$_SESSION['email']}' THEN u2.first_name ELSE u1.first_name END AS c_first_name, CASE WHEN u1.email = '{$_SESSION['email']}' THEN u2.last_name ELSE u1.last_name END AS c_last_name, CASE WHEN u1.email = '{$_SESSION['email']}' THEN u2.nickname ELSE u1.nickname END AS c_nickname , l1.card_count AS p_card_count, l1.platform AS p_platform, l1.media AS p_media, l2.card_count AS d_card_count, l2.platform AS d_platform, l2.media AS d_media, ROUND((3958.75 * acos( cos( radians( p1.latitude ) ) * cos( radians( p2.latitude ) ) * cos( radians(p2.longitude) - radians( p1.longitude ) ) +sin( radians( p1.latitude ) ) * sin( radians(  p2.latitude  ) ) ) ),2) AS Distance ".
						 "FROM Trade " .
						 "INNER JOIN ListedItem l1 ON l1.item_number = Trade.proposed_item_num " .
						 "INNER JOIN ListedItem l2 ON l2.item_number = Trade.desired_item_num " .
						 "INNER JOIN User u1 ON u1.email = l1.email " .
						 "INNER JOIN User u2 ON u2.email = l2.email " .
             "JOIN PostalCode p1 on p1.postal_code = u1.postal_code ".
             "JOIN PostalCode p2 on p2.postal_code = u2.postal_code ".
             "WHERE Trade.proposed_item_num = '$proposed_item_number' AND Trade.desired_item_num = '$desired_item_number' " .
						 "AND Trade.date_of_proposal IS NOT NULL " ;


$result = mysqli_query($db, $query);
include('lib/show_queries.php');

if (!is_bool($result) && (mysqli_num_rows($result) > 0) ) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

} else {
    array_push($error_msg,  "SELECT ERROR: Failed to get trade information.". $proposed_item_number."<br>".  __FILE__ ." line:". __LINE__ );
}


?>

<?php include("lib/header.php"); ?>
<title>Trade History</title>
</head>

<body>
		<div id="main_container">
    <?php include("lib/menu.php"); ?>

    <div class="center_content">
        <div class="center_left">
          <div class="title_name"><?php print $user_name; ?></div>
					<div class="features">

                 <div class="profile_section">
                     <div class="subtitle">Trade Details</div>
                     <table>
                         <tr>
                             <td class="item_label">Proposed Date</td>
                             <td>
                                <?php print $row['date_proposal'];?>
                             </td>
                         </tr>
                         <tr>
                             <td class="item_label">Accepted/Rejected</td>
                             <td>
                                 <?php print $row['date_response'];?>
                             </td>
                             <tr>
                                 <td class="item_label">Status</td>
                                 <td>
                                     <?php print $row['trade_status'];?>
                                 </td>
                             </tr>
                             <tr>
                                 <td class="item_label">My role</td>
                                 <td>
                                     <?php print $row['my_role'];?>
                                 </td>
                             </tr>
                             <tr>
                                 <td class="item_label">Response Time</td>
                                 <td>
                                     <?php print $row['response_time'];?> days
                                 </td>
                             </tr>
 											</table>
 	                </div>
                  <div class="profile_section">
  									<div class="subtitle">Proposed Item</div>
                        <table>
  													<tr>
                              <td class="item_label">Item #</td>
  														<td>
                                  <?php print $row['proposed_num'];?>
                              </td>
                          </tr>
  												<tr>
                              <td class="item_label">Title</td>
  														<td>
                                  <?php print $row['p_title'];?>
                              </td>
                          </tr>
  												<tr>
                              <td class="item_label">Game Type</td>
  														<td>
                                  <?php print $row['p_gt'];?>
                              </td>
                          </tr>
  												<tr>
                              <td class="item_label">Condition</td>
  														<td>
                                  <?php print $row['p_con'];?>
                              </td>
                          </tr>
                          <?php if(!empty($row['p_des'])):?>
                            <tr>
                              <td class="item_label">Description</td>
                              <td>
                                 <?php print $row['p_des'];?>
                              </td>
                          </tr>
                        <?php endif ?>
                        <?php if(!empty($row['p_card_count'])):?>
                          <tr>
                            <td class="item_label">Card Count</td>
                            <td>
                               <?php print $row['p_card_count'];?>
                            </td>
                        </tr>
                      <?php endif ?>
                      <?php if(!empty($row['p_platform'])):?>
                        <tr>
                          <td class="item_label">Platform</td>
                          <td>
                             <?php print $row['p_platform'];?>
                          </td>
                      </tr>
                    <?php endif ?>
                    <?php if(!empty($row['p_media'])):?>
                      <tr>
                        <td class="item_label">Media</td>
                        <td>
                           <?php print $row['p_media'];?>
                        </td>
                    </tr>
                  <?php endif ?>

  											</table>
  	                </div>



                  <div class="profile_section">
  										<div class="subtitle">User Details</div>
  												<table>
  													 <tr>
  		                            <td class="item_label">Nickname</td>
  																<td>
  		                                <?php print $row['c_nickname'];?>
  		                            </td>
  		                        </tr>
                              <tr>
                                   <td class="item_label">Distance</td>
                                   <td>
  		                                <?php print $row['Distance'];?> miles
                                   </td>
                               </tr>
									<?php if($row['trade_status'] == "Accepted"):?>
                              <tr>
   		                            <td class="item_label">Name</td>
   																<td>
   		                                <?php print $row['c_first_name'];?>
                                       <?php print $row['c_last_name'];?>
   		                            </td>
   		                        </tr>
  														<tr>
  		                            <td class="item_label">Email</td>
  																<td>
  		                                <?php print $row['c_email'];?>
  		                            </td>
  		                        </tr>
                  <?php endif ?>
  													</table>
  												</div>


  		                <div class="profile_section">
  														<div class="subtitle">Desired Item</div>
  												    <table>
  				                        <tr>
  				                            <td class="item_label">Item #</td>
  																		<td>
  				                                <?php print $row['desired_num'];?>
  				                            </td>
  				                        </tr>
  																<tr>
  				                            <td class="item_label">Title</td>
  																		<td>
  				                                <?php print $row['d_title'];?>
  				                            </td>
  				                        </tr>
  																<tr>
  				                            <td class="item_label">Game Type</td>
  																		<td>
  				                                <?php print $row['d_gt'];?>
  				                            </td>
  				                        </tr>
  																<tr>
  				                            <td class="item_label">Condition</td>
  																		<td>
  				                                <?php print $row['d_con'];?>
  				                            </td>
  				                        </tr>
                                  <?php if(!empty($row['d_des'])):?>
                                    <tr>
                                      <td class="item_label">Description</td>
                                      <td>
                                         <?php print $row['d_des'];?>
                                      </td>
                                  </tr>
                              <?php endif ?>
                              <?php if(!empty($row['d_card_count'])):?>
                                <tr>
                                  <td class="item_label">Card Count</td>
                                  <td>
                                     <?php print $row['d_card_count'];?>
                                  </td>
                              </tr>
                            <?php endif ?>
                            <?php if(!empty($row['d_platform'])):?>
                              <tr>
                                <td class="item_label">Platform</td>
                                <td>
                                   <?php print $row['d_platform'];?>
                                </td>
                            </tr>
                          <?php endif ?>
                          <?php if(!empty($row['d_media'])):?>
                            <tr>
                              <td class="item_label">Media</td>
                              <td>
                                 <?php print $row['d_media'];?>
                              </td>
                          </tr>
                        <?php endif ?>
                    		</table>
  										</div>
      				</div>

                      <?php include("lib/error.php"); ?>

      				<div class="clear"></div>
      			</div>

                     <?php include("lib/footer.php"); ?>

      		</div>
      	</body>
      </html>
