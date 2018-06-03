<?php
require($_SERVER['DOCUMENT_ROOT'].'/angular/includes/connect.php');

//this is one of the most important when sending a json using PHP, so you don't have to parse it in your js script.
header("Content-Type: application/json; charset=UTF-8");

//get the data from the form and convert it to PHP object, 
//this is for regular http request the file will be $form_data->firstname. in this case, im using Upload file library.
//$form_data = json_decode(file_get_contents("php://input"));

//declaration of empty object using PHP.
$output = new stdClass();

//run only if there's a file.
if(empty($_FILES["file"]["name"])){
	$output->response = "No file.";
}
else{

	$target_dir = $_SERVER['DOCUMENT_ROOT']."/angular/people_photos/";
	$target_file = $target_dir . basename($_FILES["file"]["name"]);
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	$temp = explode(".", $_FILES["file"]["name"]);
	$newFileName = md5($_POST['firstname']).'-' .date('mdYHis').'.'.end($temp);

	//checking for a valid input
	if(empty($_POST['firstname'])){
		$output->response = "Firstname is required";
	}
	elseif(empty($_POST['lastname'])){
		$output->response = "Lastname is required";
	}
	elseif(empty($_POST['email'])){
		$output->response = "Email is required";
	}
	elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
		$output->response = "Please put a valid email";
	}
	elseif(getimagesize($_FILES["file"]["tmp_name"]) == false){
		$output->response = "File is not an image";
	}
	elseif(file_exists($target_dir.$newFileName)){
		$output->response = "File already exist";
	}
	elseif($_FILES["file"]["size"] > 2097152){
		$output->response = "Maximum file size is 2MB";
	}
	elseif($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif"){
		$output->response = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
	}
	else{

		//if the inputs are valid, filter all the data.
		$first_name = mysqli_real_escape_string($conn, $_POST['firstname']);
		$last_name = mysqli_real_escape_string($conn, $_POST['lastname']);
		$email = mysqli_real_escape_string($conn, $_POST['email']);

		//check if email exist
		$exist = "SELECT firstname FROM customers WHERE email='".$email."'";
		$run_exist = $conn->query($exist);

		if($run_exist->num_rows > 0){
			$output->response = "Email already exist!";
		}
		else{
			//now insert the data
			$insert = "INSERT INTO customers (firstname, lastname, email, photo) 
					VALUES('".$first_name."','".$last_name."','".$email."','".$newFileName."')";
			$run_insert = $conn->query($insert);

			if($run_insert && move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir.$newFileName)){
				$output->response = "Data inserted";
				$output->digit = 1;
			}
			else{
	  			$output->response = "There was an error uploading your file";
	  		}
		}
	}
}



//echo all the details output. this will show as object already even though its a JSON (string) format because of header.
echo json_encode($output);
?>