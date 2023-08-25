<?php

session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: login.php');
	exit;
}

	/*-- we included connection files--*/
	include "connection.php";

	/*--- we created a variables to display the error message on design page ------*/
	$error = "";

	if (isset($_POST["btn_upload"]) == "Upload")
	{
		$file_tmp = $_FILES["fileImg"]["tmp_name"];
		$file_name = $_FILES["fileImg"]["name"];

		/*image name variable that you will insert in database ---*/
		$image_name = $_POST["img-name"];

		//image directory where actual image will be store
		$file_path = "photo/".$file_name;	

	/*---------------- php textbox validation checking ------------------*/
	if($image_name == "")
	{
		$error = "Please enter Image name.";
	}

	/*-------- now insertion of image section has start -------------*/
	else
	{	
	

		
		if(file_exists($file_path))
		{
			$error = "Sorry,The <b>".$file_name."</b> image already exist.";
		}
			else
			{
				
				$con = mysqli_connect($host, $uname, $pwd, $db_name);
				$stmt = $con->prepare('SELECT password, email, id, name, address, phone, sex, question, answer FROM user WHERE email = ?');
				// In this case we can use the account ID to get the account info.
				$stmt->bind_param('s', $_SESSION['email']);
				$stmt->execute();
				$stmt->bind_result($password, $email, $id, $name, $address, $phone, $sex, $question, $answer);
				$stmt->fetch();
				$stmt->close();

				$sql_w = "SELECT image_time FROM image_table WHERE email = '" . $email . "' ";
        		$ret_w = mysqli_query($con, $sql_w);
        		$row_w = mysqli_fetch_array($ret_w); 
        		$image_time = $row_w['image_time'];
        		$image_time_n = strtotime($image_time)+ date('Z');
        		$time_now = time();
			
				$result = mysqli_connect($host, $uname, $pwd) or die("Connection error: ". mysqli_error());
				mysqli_select_db($result, $db_name) or die("Could not Connect to Database: ". mysqli_error());
				mysqli_query($result,"INSERT INTO image_table(img_name,img_path,email,image_time)
				VALUES('$image_name','$file_path','$email', NOW())") or die ("image not inserted". mysqli_error());
				move_uploaded_file($file_tmp,$file_path);
				$error = "<p align=center>File ".$_FILES["fileImg"]["name"].""."<br />Image saved into Table.";
				
			}
		
	}
	}
?>