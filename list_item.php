<?php

include('lib/common.php');
// written by fyi33

if (!isset($_SESSION['email'])) {
	header('Location: login.php');
	exit();
}

$query = "SELECT COUNT(li.item_number) AS PendingTradesasCounterpart " .
"FROM trade t JOIN listedItem li on t.desired_item_num=li.item_number ".
"WHERE t.accepted IS NULL AND li.email='{$_SESSION['email']}'".
"GROUP BY li.email";

$result = mysqli_query($db, $query);
$pendingTrade = mysqli_fetch_array($result)['PendingTradesasCounterpart'];

if ($pendingTrade>2){
  echo '<script type="text/javascript">alert("You have more than 2 pending trades! Please decide on them first.")</script>';
  echo '<script>window.location.href="welcome.php"</script>';
}


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (isset($_POST['game_type'])) {
            $game_type = $_POST['game_type'];
            $_SESSION['game_type'] = $game_type;
        }

        $title = mysqli_real_escape_string($db, $_POST['title']);
        $description = mysqli_real_escape_string($db, $_POST['description']);
        #$game_type = mysqli_real_escape_string($db, $_POST['game_type_store']);
        $condition_type = mysqli_real_escape_string($db, $_POST['condition_type']);
        $no_of_cards = mysqli_real_escape_string($db, $_POST['no_of_cards']);
        $video_game_platform_type = mysqli_real_escape_string($db, $_POST['video_game_platform_type']);
        $media_name = mysqli_real_escape_string($db, $_POST['media_name']);
        $computer_game_platform_type = mysqli_real_escape_string($db, $_POST['computer_game_platform_type']);

        if (empty($title)) {
                array_push($error_msg,  "Please enter a title.");
        }

         if (empty($_SESSION['game_type'])) {
            array_push($error_msg,  "Please enter a game_type.");
        }

        if (empty($condition_type)) {
                array_push($error_msg,  "Please enter an condition_type.");
        }

        if ($game_type == "Card Collecting Game" && empty($no_of_cards)) {
                array_push($error_msg,  "Please enter no of cards for card collecting games.");
        }

        if (($game_type == "Video Game") && (empty($video_game_platform_type))) {
                array_push($error_msg,  "Please enter video game platform type for video game.");
        }

        if (($game_type == "Computer Game") && (empty($computer_game_platform_type))) {
                array_push($error_msg,  "Please enter computer game platform type for computer game.");
        }

        if (($game_type == "Video Game") && (empty($media_name))) {
                array_push($error_msg,  "Please enter media name for video game.");
        }

         if ( !empty($title) && !empty($_SESSION['game_type']) && !empty($condition_type) )   {

                if (!empty($computer_game_platform_type)) {
                    $platform = $computer_game_platform_type;
                } else {
                    $platform = $video_game_platform_type;
                }

                $query_main = "INSERT INTO listeditem (title, description, condition_type, email, type, card_count, platform, media) ".
                "VALUES ('$title', '$description', '$condition_type', '{$_SESSION['email']}', '{$_SESSION['game_type']}', NULLIF('$no_of_cards',''), '$platform', NULLIF('$media_name',''))";
                $query1 = mysqli_query($db, $query_main);

                $query_insertnumber = "SELECT max(item_number) as item_number FROM listeditem WHERE email='{$_SESSION['email']}'";
                $query2 = mysqli_query($db, $query_insertnumber);
                $item_number = mysqli_fetch_array($query2)['item_number'];

               if ($query1  == False) {
                    array_push($error_msg, "Item creation error. Please contact support.");
                  }
               if ($query1  == 1) {
                    unset($_SESSION['game_type']);
                    echo '<script type="text/javascript">alert("You item has been listed! Item number is '.$item_number.'")</script>';
                  }

         }

    }
?>


<?php include("lib/header.php"); ?>
		<title>Trade Plaza List Item</title>
	</head>

	<body>

    	<div id="main_container">
        <?php include("lib/menu.php"); ?>

			<div class="center_content">
				<div class="title_name">
					<?php print $row['first_name'] . ' ' . $row['last_name']; ?>
				</div>
					<div class="features">

            <div class="profile_section">
							<div class="subtitle">List Item</div>
                            <form name="item_label_form" action="list_item.php" method="post">
                            <table>
                                <tr>
                                    <td class="item_label">Game Type</td>
                                    <td>
                                        <select name="game_type" onchange="item_label_form.submit();">
                                            <option value="" <?php if($game_type == ""){echo("selected");} ?>>--- Select --- </option>
                                            <option value="Board Game" <?php if($game_type == "Board Game"){echo("selected");} ?>>Board Game</option>
                                            <option value="Playing Card Game" <?php if($game_type == "Playing Card Game"){echo("selected");} ?>>Playing card Game</option>
                                            <option value="Video Game" <?php if($game_type == "Video Game"){echo("selected");} ?>>Video Game</option>
                                            <option value="Collectible Card Game" <?php if($game_type == "Collectible Card Game"){echo("selected");} ?>>Collectible Card Game</option>
                                            <option value="Computer Game" <?php if($game_type == "Computer Game"){echo("selected");} ?>>Computer Game</option>
                                        </select>
                                    </td>
                                </tr>
                                </table>
                            </form>
                            <?php
                                if ($_SESSION['game_type']=="Collectible Card Game"){ ?>
                                <table>
                                <tr>
                                  <td class="item_label">No Of Cards</td>
                                  <td>
                                    <input type="text" name="no_of_cards"/>
                                  </td>
                                </tr>
                                </table>
                                <?php }
                                else if ($_SESSION['game_type']=="Video Game") { ?>
                                <table>
                                <tr>
                                  <td class="item_label">Platform Type</td>
                                  <td>
                                  <select name="video_game_platform">
                                    <option value="">--- Select --- </option>
                                    <option value="Nintendo">Nintendo</option>
                                    <option value="Xbox">Xbox</option>
                                    <option value="PlayStation">PlayStation</option>
                                 </td>
                                 </tr>
                                 <tr>
                                   <td class="item_label">Media Type</td>
                                   <td>
                                   <select name="computer_game_media">
                                   <option value="">--- Select --- </option>
                                  <?php $query="select distinct media from listeditem where media IS NOT NULL";
                                  $result=mysqli_query($db, $query);
                                  while($rw=mysqli_fetch_array($result))
                                  { ?>
                                  <option value="<?php echo $rw['media']; ?>"<?php if($row['media']==$rw['media']) echo 'selected="selected"'; ?>><?php echo $rw['media']; ?></option>
                                  <?php } ?>
                                  </td>
                                 </tr>
                                 </table>
                                <?php } else if ($_SESSION['game_type']=="Computer Game") { ?>
                                <table>
                                <tr>
                                  <td class="item_label">Platform Type</td>
                                  <td>
                                  <select name="computer_game_platform">
                                    <option value="">--- Select --- </option>
                                    <option value="">macOS</option>
                                    <option value="">Linux</option>
                                    <option value="">Windows</option>
                                 </td>
                                 </tr>
                                 </table>
                                <?php } ?>
							<form name="profileform" action="list_item.php" method="post" enctype="multipart/form-data">
							<table>
								<tr>
										<td class="item_label">Item Title</td>
										<td>
											<input type="text" name="title"/>
										</td>
									</tr>
										<tr>
											<td class="item_label">Condition</td>
											<td>
												<select name="condition_type">
												<option value="">--- Select --- </option>
												<option value="Unopened" >Unopened</option>
												<option value="Like New" >Like New</option>
												<option value="Lightly Used" >Lightly Used</option>
												<option value="Moderately Used" >Moderately Used</option>
												<option value="Heavily Used" >Heavily Used</option>
												<option value="Damaged/Missing parts" >Damaged/Missing parts</option>
											</td>
										</tr>
										<tr>
											<td class="item_label">Item Description</td>
											<td>
												<textarea cols="20" rows="10" name="description" ></textarea>
											</td>
										</tr>
									    <tr>
									    <td>
                                            <input type="image" src="img/save.gif" class="register"/>
                                        </td>
										</tr>
                            </table>
							</form>
						</div>

				</div>

                <?php include("lib/error.php"); ?>

				<div class="clear"></div>
			</div>

               <?php include("lib/footer.php"); ?>

		</div>
	</body>
</html>
