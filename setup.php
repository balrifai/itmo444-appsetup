<?php
// Start the session
require 'var/www/html/vendor/autoload.php';
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'itmo444-mp1',
]);
$rds->waitUntil('DBInstanceAvailable',['DBInstanceIdentifier' => 'itmo444-mp1',]);

$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
print "============\n". $endpoint . "================\n";
$link = mysqli_connect($endpoint,"balrifai","ilovebunnies","balrifai",3306) or die("Error " . mysqli_error($link)); 
echo "Here is the result: " . $link;

$sql = "CREATE TABLE IF NOT EXISTS User 
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(20),
username VARCHAR(20),
telephone VARCHAR(20),
raws3url VARCHAR(256),
finished3url VARCHCAR(256),
filename VARCHAR(256),
state TINYINT(3),
datetime VARCHAR

)";
$link->query($sql);
shell-exec("chmod 600 setup.php");

?>
