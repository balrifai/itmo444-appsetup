<html>
<head><title>Gallery</title>
</head>
<body>

<?php
session_start();
$email = $_POST["email"];
echo $email;
require 'vendor/autoload.php';

$client = new Aws\Rds\RdsClient([
	'version' => 'latest',
	'region' => 'us-east-1'
]);

$result = $client->describeDBInstances(array(
    'DBInstanceIdentifier' => 'itmo444-mp1',
));

$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
    // Do something with the message
    echo "============". $endpoint . "================";
    echo "Begin database";
$link = mysqli_connect($endpoint,"balrifai","ilovebunnies","balrifai") or die("Error " . mysqli_error($link));

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
else {
echo "Connection successful!";
}
//below line is unsafe - $email is not checked for SQL injection -- don't do this in real life or use an ORM instead
$link->real_query("SELECT * FROM User WHERE email = '$email'");
echo "Result set order...\n";

if ($result = $link->use_result()) {
    while ($row = $result->fetch_assoc()) {
	echo "<img src=\" " . $row['raws3url'] . "\" height='200' width='200' />";
    }
	$result->close();
}
$link->close();
session_destroy();
?>
</body>
</html>

