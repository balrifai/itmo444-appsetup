<?php
// Start the session
echo "Session starting";
session_start();
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.
//get info from form
var_dump($_POST);
	if(!empty($_POST)){
	echo $_POST['username'];
	echo $_POST['telephone'];
	echo $_POST['email'];
	}
	else {
	echo "No information found in form";
	}

date_default_timezone('America/Chicago');

$uploaddir = '/tmp/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}
echo 'Here is some more debugging info:';
print_r($_FILES);
print "</pre>";
require 'vendor/autoload.php';
#use Aws\S3\S3Client;
$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$bucket = uniqid("balrifai-php",false);

# AWS PHP SDK version 3 create bucket
$result = $s3->createBucket([
'ACL' => 'public-read',
    'Bucket' => $bucket
]);

$s3->waitUntil('BucketExists',[
	'Bucket' => $bucket
]);

# PHP version 3
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
   'Key' => $uploadfile
]);
$url = $result['ObjectURL'];
echo $url;
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

$result = $rds->describeDBInstances(array(
    'DBInstanceIdentifier' => 'itmo444-mp1'
));
$endpoint = $result['DBInstances'][0]['Endpoint']['Address']
    echo "============\n". $endpoint . "================";^M
//Begin database
$link = mysqli_connect($endpoint,"balrifai","ilovebunnies","balrifai") or die("Error " . mysqli_error($link));
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
} else {
	echo "Connect succeeded";
	}
/* Prepared statement, stage 1: prepare */
if (!($stmt = $link->prepare("INSERT INTO User (username,email,telephone,filename,raws3url,finisheds3url,state,datetime) VALUES (NULL,?,?,?,?,?,?,?)"))) {
    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
}

$username=$_POST['username'];
$email = $_POST['email'];
$_SESSION["email"]=$email;
$telephone = $_POST['telephone'];
$raws3url = $url; //  $result['ObjectURL']; from above
$filename = basename($_FILES['userfile']['name']);
$finisheds3url = "none";
$state=0;
$datetime = date("d M Y - h:i:s A");

$stmt->bind_param("ssssssis",$username,$email,$telephone,$filename,$raws3url,$finisheds3url,$state,$datetime);
if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

printf("%d Row inserted.\n", $stmt->affected_rows);
/* explicit close recommended */
$stmt->close();

$publish = $result->publish(array(
	'TopicArn' => $topicARN,
	'Subject' => 'ITMO444-MP2',
	'Message' => 'Testing MP2 message fxn',
));

$link->real_query("SELECT * FROM User");
$res = $link->use_result();

echo "Result set order...\n";

while ($row = $res->fetch_assoc()) {
    echo $row['username'] . " " . $row['email']. " " . $row['telephone'];
}
$link->close();
header("Location: gallery.php");

?>
