<?php

include('lib/common.php');


if($showQueries){
  array_push($query_msg, "showQueries currently turned ON, to disable change to 'false' in lib/common.php");
}

//Note: known issue with _POST always empty using PHPStorm built-in web server: Use *AMP server instead
if( $_SERVER['REQUEST_METHOD'] == 'POST') {

	//$enteredEmail = mysqli_real_escape_string($db, $_POST['email']);
    $enteredInput = mysqli_real_escape_string($db, $_POST['email']);
	$enteredPassword = mysqli_real_escape_string($db, $_POST['password']);

    if (empty($enteredInput)) {
            array_push($error_msg,  "Please enter an email address or nickname.");
    }

	if (empty($enteredPassword)) {
			array_push($error_msg,  "Please enter a password.");
	}

    if ( !empty($enteredInput) && !empty($enteredPassword) )   {

        $ifEmail =  (strpos($enteredInput, '@') !== false && strpos($enteredInput, '.') !== false);

        if ($ifEmail){
            $enteredEmail = $enteredInput;
            $query = "SELECT password FROM User WHERE email='$enteredEmail'";
        }else {
            $enteredNickname = $enteredInput;
            $query = "SELECT email, password FROM User WHERE nickname='$enteredNickname'";
        }

        $result = mysqli_query($db, $query);
        include('lib/show_queries.php');
        $count = mysqli_num_rows($result);

        if (!empty($result) && ($count > 0) ) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $storedPassword = $row['password'];

            $options = [
                'cost' => 8,
            ];
             //convert the plaintext passwords to their respective hashses
             // 'michael123' = $2y$08$kr5P80A7RyA0FDPUa8cB2eaf0EqbUay0nYspuajgHRRXM9SgzNgZO
            $storedHash = password_hash($storedPassword, PASSWORD_DEFAULT , $options);   //may not want this if $storedPassword are stored as hashes (don't rehash a hash)
            $enteredHash = password_hash($enteredPassword, PASSWORD_DEFAULT , $options);

            if($showQueries){
                array_push($query_msg, "Plaintext entered password: ". $enteredPassword);
                //Note: because of salt, the entered and stored password hashes will appear different each time
                array_push($query_msg, "Entered Hash:". $enteredHash);
                array_push($query_msg, "Stored Hash:  ". $storedHash . NEWLINE);  //note: change to storedHash if tables store the plaintext password value
                //unsafe, but left as a learning tool uncomment if you want to log passwords with hash values
                //error_log('email: '. $enteredEmail  . ' password: '. $enteredPassword . ' hash:'. $enteredHash);
            }

            //depends on if you are storing the hash $storedHash or plaintext $storedPassword
            if (password_verify($enteredPassword, $storedHash) ) {
                array_push($query_msg, "Password is Valid! ");
                if (!$ifEmail) {
                    $enteredEmail = $row['email'];
                }
                $_SESSION['email'] = $enteredEmail;

                array_push($query_msg, "logging in... ");
                header(REFRESH_TIME . 'url=welcome.php');		//to view the password hashes and login success/failure

            } else {
                array_push($error_msg, "Login failed: " . $enteredInput . NEWLINE);
                //array_push($error_msg, "To demo enter: ". NEWLINE . "sheng@gmail.com". NEWLINE ."123");
            }

        } else {
                array_push($error_msg, "The username or nickname entered does not exist: " . $enteredInput);
            }
    }
}
?>

<?php include("lib/header.php"); ?>
<title>Trade Plaza Online Login</title>
</head>
<body>
    <div id="main_container">
        <div id="header">
            <div class="logo">
                <img src="img/gtonline_logo.png" style="opacity:1;background-color:FEFFFF;" border="0" alt="" title="Trade Plaza Logo"/>
            </div>
        </div>

        <div class="center_content">
            <div class="text_box">
                <form action="login.php" method="post" enctype="multipart/form-data">
                    <div class="title">Trade Plaza Online Login</div>
                    <div class="login_form_row">
                        <label class="login_label">Email/Nickname:</label>
                        <input type="text" name="email" value="" class="login_input"/>
                    </div>
                    <div class="login_form_row">
                        <label class="login_label">Password:</label>
                        <input type="password" name="password" value="" class="login_input"/>
                    </div>
                    <input type="image" src="img/login.gif" class="login"/>
                  </form>
                 <div class="register">
                   <a href ="Register.php"><img src="img/Register.gif"></a>
                 </div>
                </div>

                <?php include("lib/error.php"); ?>

                <div class="clear"></div>
            </div>
					<?php include("lib/footer.php"); ?>

        </div>
    </body>
</html>
