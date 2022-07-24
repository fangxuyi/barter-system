<?php
include('lib/common.php');
//written by Awang497


//Note: known issue with _POST always empty using PHPStorm built-in web server: Use *AMP server instead
if( $_SERVER['REQUEST_METHOD'] == 'POST') {

	$email = mysqli_real_escape_string($db, $_POST['email']);
	$password = mysqli_real_escape_string($db, $_POST['password']);
  $first_name = mysqli_real_escape_string($db, $_POST['first_name']);
  $last_name = mysqli_real_escape_string($db, $_POST['last_name']);
  $nickname = mysqli_real_escape_string($db, $_POST['nickname']);
  $postal_code = mysqli_real_escape_string($db, $_POST['postal_code']);


  if (empty($email)) {
            array_push($error_msg,  "Please enter an email address.");
    }

	if (empty($password)) {
			array_push($error_msg,  "Please enter a password.");
	}

  if (empty($first_name)) {
      array_push($error_msg,  "Please enter your first name.");
  }

  if (empty($last_name)) {
      array_push($error_msg,  "Please enter your last name.");
  }

  if (empty($nickname)) {
      array_push($error_msg,  "Please enter your nick name.");
  }
  if (empty($postal_code)) {
      array_push($error_msg,  "Please enter your Postal Code.");
  }

  if ( !empty($email) && !empty($password) && !empty($nickname)&& !empty($first_name)&& !empty($last_name) )   {
 $checkEmailQuery = "SELECT email FROM user WHERE email='$email'";
 $checkEmailResult = mysqli_query($db, $checkEmailQuery);
 include('lib/show_queries.php');
        $emailCount = mysqli_num_rows($checkEmailResult);

        $checkNicknameQuery = "SELECT nickname FROM user WHERE email='$nickname'";
        $checkNicknameResult = mysqli_query($db, $checkNicknameQuery);
        include('lib/show_queries.php');
               $nmCount = mysqli_num_rows($checkNicknameResult);

        if (!empty($checkEmailResult) && ($emailCount > 0) ) {
    array_push($error_msg, "Email already existed!");
  } else if (!empty($checkNicknameResult) && ($nmCount > 0)
  ){
    array_push($error_msg, "Nickname already existed!");
  } else{


     $query = "INSERT INTO User (email, nickname, first_name, last_name, postal_code, password) " .
                "VALUES ('$email', '$nickname', '$first_name', '$last_name','$postal_code', '$password')";

     $queryID = mysqli_query($db, $query);
    include('lib/show_queries.php');
                   if ($queryID  == False) {
                        array_push($error_msg, "Error");
                      }
                   if ($queryID  == 1) {
                        array_push($error_msg, "Congrations! You have created an new account.");
                        $_SESSION['email'] = $email;
                        echo '<script>window.location.href="welcome.php"</script>';
                      }
                    }
                   }
}


?>

<?php include("lib/header.php"); ?>
<title>Trade Plaza Registration Form</title>
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
                <form action="register.php" method="post" enctype="multipart/form-data">
                    <div class="title">Trade Plaza Registration</div>
                    <div class="login_form_row">
                        <label class="login_label">Email:</label>
                        <input type="email" name="email"  class="login_input"/>
                    </div>
                    <div class="login_form_row">
                        <label class="login_label">Password:</label>
                        <input type="password" name="password"  class="login_input"/>
                    </div>
                    <div class="login_form_row">
                        <label class="login_label">First Name:</label>
                        <input type="text" name="first_name"  class="login_input"/>
                    </div>
                    <div class="login_form_row">
                        <label class="login_label">Last Name:</label>
                        <input type="text" name="last_name"  class="login_input"/>
                    </div>
                    <div class="login_form_row">
                        <label class="login_label">Nick Name:</label>
                        <input type="text" name="nickname"  class="login_input"/>
                    </div>
                    <div class="login_form_row">
                        <label class="login_label">Postal Code:</label>

                    <select name="postal_code">
       <option value="">--- Select --- </option>
       <?php $query="select postal_code from postalcode";
       $result=mysqli_query($db, $query);
       while($rw=mysqli_fetch_array($result))
       { ?>
       <option value="<?php echo $rw['postal_code']; ?>"<?php if($row['postal_code']==$rw['postal_code']) echo 'selected="selected"'; ?>><?php echo $rw['postal_code']; ?></option>
       <?php } ?>
   </select>

                    </div>
                    <input type="image" src="img/register.gif" class="login"/>
                  </form>
                 <div class="register">
                   <a href ="login.php"><img src="img/login.gif"></a>
                 </div>
                </div>

                <?php include("lib/error.php"); ?>

                <div class="clear"></div>
            </div>
					<?php include("lib/footer.php"); ?>

        </div>
    </body>
</html>
