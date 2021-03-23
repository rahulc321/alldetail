<?php
private function send($message,$driver_contact)
{

$source = "2349040000067";
$destination = $driver_contact ;
$text = $message;

$curl = curl_init();

curl_setopt_array($curl, array(
CURLOPT_URL => "https://api.gupshup.io/sm/api/v1/msg",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => "",
CURLOPT_MAXREDIRS => 10,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => "POST",
CURLOPT_POSTFIELDS => "channel=whatsapp" .
"&source={$source}" .
"&destination={$destination}" .
"&message={$text}",
CURLOPT_HTTPHEADER => array(
"Cache-Control: no-cache",
"Content-Type: application/x-www-form-urlencoded",
"apikey: 987eec7ae6024a5cca4d1087671678e7",
"cache-control: no-cache"

),
));
$response = curl_exec($curl);
\Log::info($response);

$err = curl_error($curl);
curl_close($curl);

if ($err) {
} else {
}

?>