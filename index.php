<?php
require 'config.php';

mb_internal_encoding("UTF-8");
date_default_timezone_set('UTC');

require 'vendor/autoload.php';

$date = trim(file_get_contents('date'));

$url = 'https://secure.gaug.es/gauges/'.GAUGE_ID;

$response = get($url);

$data = json_decode($response);

if($data->status == 'fail') {
	echo 'There was some type of failure. Check API key or Gauges ID. Message from Gauges: '.$data->message;
	die();
}

$title = $data->gauge->title;
$yesterday = $data->gauge->yesterday;

// Check that we didn't already send this notification
if($yesterday->date == $date) {
	echo 'We already sent (or attempted to send) this notification.';
	die();
}

// Next time, we'll get all posts after this timestamp
file_put_contents('date', $yesterday->date);

$notification_message = $title.' served '.number_format($yesterday->people).' people with '.number_format($yesterday->views).' pageviews yesterday ('.$yesterday->date.').';

use GorkaLaucirica\HipchatAPIv2Client\Auth\OAuth2;
use GorkaLaucirica\HipchatAPIv2Client\Client;
use GorkaLaucirica\HipchatAPIv2Client\API\RoomAPI;
use GorkaLaucirica\HipchatAPIv2Client\Model\Message;

$auth = new OAuth2(HIPCHAT_TOKEN); // Crowdmap Posts Room
$client = new Client($auth);

$roomAPI = new RoomAPI($client);

foreach($data AS $post) {
	$message = new Message();
	$message->setMessage($notification_message);
	var_dump($message);
	$roomAPI->sendRoomNotification(HIPCHAT_ROOM_ID, $message);
}

echo "Finished. Have a nice day!\n";

function generate_signature($http_method,$url) {
	$date = time();
	return 'A' . CM_PUBLIC . hash_hmac('sha1', "{$http_method}\n{$date}\n{$url}\n", CM_PRIVATE);
}

function get($url) {
	$headers = array('X-Gauges-Token: '.GAUGES_KEY);

	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,15); // Timeout set to 15 seconds. This is somewhat arbitrary and can be changed.
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); //Set curl to store data in variable instead of print
	curl_setopt($ch,CURLOPT_HTTPGET,true);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch,CURLOPT_USERAGENT,'Gauges to HipChat Application v0');
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);

	$buffer = curl_exec($ch);
	curl_close($ch);

	return $buffer;
}



