<?php

include('lib/common.php');
// written by xluo99

if (!isset($_SESSION['email'])) {
	header('Location: login.php');
	exit();}

    $pendingTradequery = "SELECT COUNT(li.item_number) AS PendingTradesasCounterpart " .
                        "FROM trade t JOIN listedItem li on t.desired_item_num=li.item_number ".
                        "WHERE t.accepted IS NULL AND li.email='{$_SESSION['email']}'".
                        "GROUP BY li.email";

$pendingTradeResult = mysqli_query($db, $pendingTradequery);
$pendingTrade = mysqli_fetch_array($pendingTradeResult)['PendingTradesasCounterpart'];

$query = "SELECT p.postal_code, latitude, longitude FROM postalcode p JOIN user u on p.postal_code=u.postal_code WHERE u.email='{$_SESSION['email']}'";
$georesult = mysqli_query($db, $query);
if (!empty($georesult) && (mysqli_num_rows($georesult) > 0) ){
    $georow = mysqli_fetch_array($georesult);
      $myPostalCode=$georow[0];
      $mylat=$georow[1];
      $mylon=$georow[2];


      $item_number=mysqli_real_escape_string($db, $_REQUEST['item_number']);
      $sql = "SELECT li.email, item_number, type, title, condition_type, platform, media, card_count,description,ROUND((3958.75 *acos(cos(radians($mylat))*cos(radians(p.latitude))*cos(radians(p.longitude)-radians($mylon))+sin(radians($mylat))*sin(radians(p.latitude)))),2) AS distance, "
          . "u.nickname, p.city, p.state, p.postal_code "
          . "FROM listeditem li JOIN user u on li.email=u.email "
          . "JOIN postalcode p ON p.postal_code=u.postal_code "
          . "WHERE li.item_number=$item_number";

      $result = mysqli_query($db, $sql);
      include('lib/show_queries.php');
      if (!is_bool($result) && (mysqli_num_rows($result) > 0) ) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    } else {
        array_push($error_msg,  "SELECT ERROR: Failed to get item details.". $item_number."<br>".  __FILE__ ." line:". __LINE__ );
    }

}

?>

<?php include("lib/header.php"); ?>
<?php   
$userEmail=$row['email'];

?>
<title>Item Detail</title>

<body>	
    <div id="main_container">
    <?php include("lib/menu.php"); ?>
    <div class="center_content">
    <div class="center_left">
    <div class="features">
    <div class="profile_section">
<div class="subtitle">Item Details</div>
<table>
     <tr>
         <td class="item_label">Item #</td>
         <td>
            <?php print $row['item_number'];?>
         </td>
     </tr>
     <tr>
         <td class="item_label">Title</td>
         <td>
             <?php print $row['title'];?>
         </td>
         <tr>
         <?php if(!empty($row['description'])):?>
        <tr>
          <td class="item_label">Description</td>
          <td>
             <?php print $row['description'];?>
          </td>
      </tr>
    <?php endif ?>
        <tr>
            <td class="item_label">Game type</td>
            <td>
                <?php print $row['type'];?>
            </td>
        </tr>
        <?php if(!empty($row['platform'])):?>
        <tr>
            <td class="item_label">Platform</td>
            <td>
                <?php print $row['platform'];?>
            </td>
        </tr>
    <?php endif ?>
    <?php if(!empty($row['media'])):?>
    <tr>
        <td class="item_label">Media</td>
        <td>
            <?php print $row['media'];?>
        </td>
    </tr>
    <?php endif ?>

    <?php if(!empty($row['card_count'])):?>
    <tr>
        <td class="item_label">Card count</td>
        <td>
            <?php print $row['card_count'];?>
        </td>
        <?php endif ?>
    </tr>
    <tr>
         <td class="item_label">Condition</td>
         <td>
             <?php print $row['condition_type'];?>
         </td>
    </tr>
</table>
</div>


               

   
                     <?php if($userEmail!==$_SESSION['email']): ?>
    <div class="center_right">

    <table>
    <tr>
                                 <td class="item_label">Offered by</td>
                                 <td>
                                     <?php print $row['nickname'];?>
                                 </td>
                             </tr>

                             <tr>
                                 <td class="item_label">Location</td>
                                 <td>
                                     <?php print $row['city'] . ', ' . $row['state'] . ' ' . $row['postal_code'];?>
                                 </td>
                             </tr>

                             <tr>
                                 <td class="item_label">Response Time</td>
                                 <?php
                                 //Get avg response time
									$rtSql = "SELECT ROUND(AVG(DATEDIFF(t.date_of_response, t.date_of_proposal)),1) AS 'response_time' ".
									"FROM trade t JOIN listedItem li on t.desired_item_num=li.item_number ".
									"WHERE t.date_of_response IS NOT NULL AND li.email='$userEmail'";
									$rtresult = mysqli_query($db, $rtSql);
									$rtrow = mysqli_fetch_array($rtresult);
                                    $avgTime =$rtrow[0];
                                    if ($avgTime == 0){
                                        print '<td style="color:#000000">None</td>';
                                      } else if ($avgTime <= 7 && $avgTime>0) {
                                        print '<td style="color:#008000">'.$avgTime.' days</td>';
                                      } else if ($avgTime <= 14 && $avgTime>7.1) {
                                        print '<td style="color:#FFFF00">'.$avgTime.' days</td>';
                                      } else if ($avgTime < 20.9 && $avgTime>=14.1) {
                                        print '<td style="color:#FFA500">'.$avgTime.' days</td>';
                                      } else if ($avgTime <= 27.9 && $avgTime>=21) {
                                        print '<td style="color:#FF0000">'.$avgTime.' days</td>';
                                      } else if ($avgTime >= 28) {
                                        print ' <td style="color:#FF0000"><span style="font-weight:bold">'.$avgTime.'</span></td>';
                                      } 

                                 ?>
                             </tr>
                             <tr>
                                 <td class="item_label">Rank</td>
                                 <?php



                $countQuery = "SELECT u.email, count.count FROM user u ".
                "LEFT JOIN (".
                "SELECT li.email, COUNT(li.email) as count ".
                "FROM listeditem li ".
                "WHERE li.item_number IN (SELECT DISTINCT desired_item_num as item_number FROM trade WHERE accepted = 1 UNION SELECT DISTINCT proposed_item_num as item_number FROM trade WHERE accepted = 1) ".
                "GROUP BY li.email) count ON ".
                "count.email = '$userEmail'";

                $countResult = mysqli_query($db, $countQuery);
                $countRow = mysqli_fetch_array($countResult);
                $numOfTrade=$countRow[1];
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
                }?>
            </tr>
                            
            
                            <tr>
                            <?php if($row['distance'] >= 100): ?>
                              <td class="item_label"  style="background-color:#FF0000;">Distance</td>
                              <td style="background-color:#FF0000;">
                                 <?php print $row['distance'];?>
                                 <?php endif; ?>
                              </td>
                              <?php if($row['distance'] <= 25): ?>
                              <td class="item_label"  style="background-color:#008000;" >Distance</td>
                              <td style="background-color:#008000;">
                                 <?php print $row['distance'];?>
                                 <?php endif; ?>
                              </td>
                              <?php if((25<$row['distance']) && ($row['distance']<= 50)): ?>
                              <td class="item_label"  style="background-color:#FFFF00;" >Distance</td>
                              <td style="background-color:#FFFF00;">
                                 <?php print $row['distance'];?>
                                 <?php endif; ?>
                              </td>
                              <?php if((50<$row['distance']) && ($row['distance']< 100)): ?>
                              <td class="item_label"  style="background-color:#FFA500;" >Distance</td>
                              <td style="background-color:#FFA500;">
                                 <?php print $row['distance'];?>
                                 <?php endif; ?>
                              </td>
                           
                          </tr>
                          
                        <tr>
                            <?php 
                            $tradableSql = "SELECT item_number from listeditem \n"

                            . "WHERE item_number NOT IN (SELECT proposed_item_num FROM trade WHERE accepted is null OR accepted=1 UNION SELECT desired_item_num FROM trade WHERE accepted is null OR accepted=1) AND item_number=$item_number;";
                            $tradableResult = mysqli_query($db, $tradableSql);

                            
                            ?>
                        <?php if(($pendingTrade<=2) || (mysqli_num_rows($tradableResult)>0)): ?>
                          <td>  <a href ="propose_trade.php?desired_item_num=<?php echo $item_number; ?>"><img src="img/propose_trade.png"></a></td>
                        <?php endif; ?>
                              </tr>
                         
                          

                               </table>
                               

    </div>
                               
    <?php endif; ?>               
   




<?php include("lib/footer.php"); ?>
</body>
</html>
