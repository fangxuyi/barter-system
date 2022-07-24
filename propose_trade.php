
<?php

include('lib/common.php');
// written by famiri6

if (!isset($_SESSION['email'])) {
	header('Location: login.php');
	exit();
}
	 $desired_item_num = mysqli_real_escape_string($db, $_REQUEST['desired_item_num']);


    $query = "SELECT first_name, last_name, nickname " .
		 "FROM User " .
		 "WHERE User.email='{$_SESSION['email']}'";

    $result = mysqli_query($db, $query);
    include('lib/show_queries.php');



    if ( !is_bool($result) && (mysqli_num_rows($result) > 0) ) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$user_name = $row['first_name'] . " " . $row['last_name'];
    } else {
        array_push($error_msg,  "Query ERROR: Failed to get User profile...<br>" . __FILE__ ." line:". __LINE__ );
    }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['confirm'])) {
        $proposed_item_num = mysqli_real_escape_string($db, $_POST['Item_#']);

        $checkRejectQuery = "SELECT * FROM trade WHERE proposed_item_num=$proposed_item_num AND desired_item_num=$desired_item_num AND accepted=0";
        $rejectedResult = mysqli_query($db, $checkRejectQuery);
        $count=$row["trade_count"];

	 if (empty($proposed_item_num)) {
      array_push($error_msg,  "Please choose an item.");
  } 
  else if(mysqli_num_rows($rejectedResult)>0){
    echo '<script type="text/javascript">alert("You have a rejected trade for the same item!")</script>';
            echo '<script>window.location.href="welcome.php"</script>';
  }

	 else if ( !empty($proposed_item_num) )   {
        $query = "INSERT INTO Trade (proposed_item_num, desired_item_num, date_of_proposal, date_of_response, accepted)
	VALUES ('$proposed_item_num', '$desired_item_num', '". date("Y-m-d") ."', NULL, NULL)";

        $result_confirm = mysqli_query($db, $query);
	include('lib/show_queries.php');
        if ($result_confirm == 1) {
            echo '<script type="text/javascript">alert("Your request has been submitted successfully!")</script>';
            echo '<script>window.location.href="welcome.php"</script>';
        } else {
            echo '<script type="text/javascript">alert("failed!")</script>';
        }
    }
}
}

?>
<?php include("lib/header.php"); ?>
<title>Trade Plaza Proposal</title>
</head>

<body>
		<div id="main_container">
    <?php include("lib/menu.php"); ?>
<?php
	$query = "SELECT PostalCode.postal_code, PostalCode.latitude, PostalCode.longitude
 	FROM PostalCode, User
 	WHERE User.email='{$_SESSION['email']}' AND PostalCode.postal_code= User.postal_code;";
        $result = mysqli_query($db, $query);
        include('lib/show_queries.php');
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$postal_code=$row['postal_code'];
	$latitudeFrom=$row['latitude'];
	$longitudeFrom=$row['longitude'];


?>

<?php
    $query = "SELECT title AS desired_item, email AS counterparty_email
		 FROM ListedItem
		 WHERE item_number=$desired_item_num;";
        $result = mysqli_query($db, $query);
        include('lib/show_queries.php');
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$desired_item=$row['desired_item'];
	$counterparty_email=$row['counterparty_email'];
?>

<?php

	$query = "SELECT PostalCode.postal_code, PostalCode.latitude AS lat, PostalCode.longitude AS lng
 	FROM PostalCode, User
 	WHERE PostalCode.postal_code= User.postal_code AND User.email='$counterparty_email';";
        $result = mysqli_query($db, $query);
        include('lib/show_queries.php');
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$latitudeTo=$row['lat'];
	$longitudeTo=$row['lng'];
?>

<?php

//Calculate distance from latitude and longitude
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $lonDelta = $lonTo - $lonFrom;
  $latDelta = $latTo - $latFrom;
  $a1=pow(sin($latDelta/2), 2)+cos($latFrom)*cos($latTo)*pow(sin($lonDelta/2), 2);
  $b1=1-$a1;
  $angle1 = 2*atan2(sqrt($a1), sqrt($b1));
  $distance=round($angle1*3958.75, 2);
?>
<?php $distance; ?>
<?php
	
	if ( $distance>= 100 ){
    	$sentence = '<span style="background-color: red">The other user is '.$distance.' away!</span>';
	
	} else {
		$sentence = '<span style="color: green">The other user is '.$distance.' away!</span>';
	}
?>


    <div class="center_content">

        <div class="center_left">
            <div class="title_name">
                <?php print $user_name; ?>, you are proposing a trade for:<?php print $desired_item;?>
							</div><br><br>
            <div class="title_name">
                <?php print $sentence; ?></div><br><br>


	    <div class="subtitle">Please choose the item you want to propose:</div>
	<form method="post" enctype="multipart/form-data">
       <select name="Item #">
       <option value="">--- Select --- </option>
       <?php 
					$query = "SELECT item_number as 'Item #'
                                              FROM ListedItem
                                              WHERE email='{$_SESSION['email']}'
                                              AND item_number NOT IN (SELECT proposed_item_num FROM Trade WHERE accepted IS NULL OR accepted=1)
                                              AND item_number NOT IN (SELECT desired_item_num FROM Trade WHERE accepted IS NULL OR accepted=1)
                                              ORDER BY item_number ASC;";

       $result=mysqli_query($db, $query);
 	if (!empty($result) && (mysqli_num_rows($result) == 0) ) {
                                         array_push($error_msg,  "SELECT ERROR: NO item <br>" . __FILE__ ." line:". __LINE__ );
                                    }

       while($rw=mysqli_fetch_array($result))
       { ?>
       <option value="<?php echo $rw['Item #']; ?>"<?php if($row['Item #']==$rw['Item #']) echo 'selected="selected"'; ?>><?php echo $rw['Item #']; ?></option>
	
	 
       <?php } 
	//$proposed_item_num=$rw['Item #'];
?>

   </select>

                        
<div class="features">   	


						<div class='profile_section'>
            <div class="subtitle">Your items not been traded yet:</div>
							<table>
								<tr>
									<td class="heading">Item #</td>
                                    					<td class="heading">Game type</td>
									<td class="heading">Title</td>
                                   					 <td class="heading">Condition</td>
									
								</tr>

								<?php
                                    $query = "SELECT item_number as 'Item #',
                                              type AS 'Game type', title as 'Title', condition_type as 'Condition'
                                              FROM ListedItem
                                              WHERE email='{$_SESSION['email']}'
                                              AND item_number NOT IN (SELECT proposed_item_num FROM Trade WHERE accepted IS NULL OR accepted=1)
                                              AND item_number NOT IN (SELECT desired_item_num FROM Trade WHERE accepted IS NULL OR accepted=1)
                                              ORDER BY item_number ASC;";



                                    $result = mysqli_query($db, $query);
                                     if (!empty($result) && (mysqli_num_rows($result) == 0) ) {
                                         array_push($error_msg,  "SELECT ERROR: No Item found <br>" . __FILE__ ." line:". __LINE__ );
                                    }

                                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                                       
                                      

                                        print "<tr>";
                                        print "<td>{$row['Item #']} </td>";
                                        print "<td>{$row['Game type']}</td>";
                                        print "<td>{$row['Title']} </td>";
                                        print "<td>{$row['Condition']}</td>";
                                    
                                        print "</tr>";
                                    }
                                ?>
							</table>						
						</div>	
					 </div> 
				</div>


               <button name="confirm" value=$proposed_item_num>Confirm</>
                        
                        </form>
			
	
			

                <div class="clear"></div>
				 

				
				
				
	</body>
</html>
