<?php
require($_SERVER['DOCUMENT_ROOT'].'/angular/includes/connect.php');

//this is one of the most important when sending a json using PHP, so you don't have to parse it in your js script.
header("Content-Type: application/json; charset=UTF-8");

//get the data from the form and convert it to PHP object, 
//this is for regular http request the file will be $form_data->firstname. in this case, im using Upload file library.
//$form_data = json_decode(file_get_contents("php://input"));

//declaration of empty object using PHP.
//$output = new stdClass();

$data = "";
$output = "";

$sql_data = "SELECT * FROM customers";
$run_data = $conn->query($sql_data);

if($run_data->num_rows > 0){
	while($row_data = $run_data->fetch_assoc()){

		//this will add comma after array index.
		if ($data != "") {$data .= ",";}

		$data .= '{"id":"'.$row_data['id'].'",';
		$data .= '"firstname":"'.$row_data['firstname'].'",';
		$data .= '"lastname":"'.$row_data['lastname'].'",';
		$data .= '"email":"'.$row_data['email'].'",';
		$data .= '"photo":"'."/angular/people_photos/".$row_data['photo'].'"}';
	}

	$output = '{"records":['.$data.']}';
}
else{
	$output = "No entry found!";
}

//echo all the details output. this will show as object already even though its a JSON (string) format because of header.
echo $output;
?>