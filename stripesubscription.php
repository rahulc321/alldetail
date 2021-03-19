<?php
public function createSubscriptionApi(Request $request){
$user = Auth::user();

$plans = array(
'prod_J7A0FFfw7VhuxT' => array(
'name' => 'Executive Monthly Subscription',
'price' => number_format(22.00, 2, '.', ''),
'interval' => 'month',
'plan_id' => 'prod_J7A0FFfw7VhuxT'
)
);

if(isset($user->stripe_cus_id) && !empty($request->subscription_plan)) {
// Plan info
$planID = $request->subscription_plan;
$planInfo = $plans[$planID];
$planName = $planInfo['name'];
$planPrice = $planInfo['price'];
$planInterval = $planInfo['interval'];

// Convert price to cents
$priceCents = round($planPrice*100);
$currency = "usd";
// Set API key
Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
// Create a plan
try {
$plan = \Stripe\Plan::create(array(
'product' => $planInfo['plan_id'],
// "product" => [
// "name" => $planName
// ],
"amount" => $priceCents,
"currency" => $currency,
"interval" => $planInterval,
"interval_count" => 1
));
}catch(Exception $e) {
$api_error = $e->getMessage();
}
if(empty($api_error) && $plan){

// Creates a new subscription
try {
$subscription = \Stripe\Subscription::create(array(
"customer" => $user->stripe_cus_id,
"items" => array(
array(
"plan" => $plan->id,
),
),
));
}catch(Exception $e) {
$api_error = $e->getMessage();
}

if(empty($api_error) && $subscription){
// Retrieve subscription data
$subsData = $subscription->jsonSerialize();

// Check whether the subscription activation is successful
if($subsData['status'] == 'active'){
// Subscription info
$subscrID = $subsData['id'];
$custID = $subsData['customer'];
$planID = $subsData['plan']['id'];
$planAmount = ($subsData['plan']['amount']/100);
$planCurrency = $subsData['plan']['currency'];
$planinterval = $subsData['plan']['interval'];
$planIntervalCount = $subsData['plan']['interval_count'];
$created = date("Y-m-d H:i:s", $subsData['created']);
$current_period_start = date("Y-m-d H:i:s", $subsData['current_period_start']);
$current_period_end = date("Y-m-d H:i:s", $subsData['current_period_end']);
$status = $subsData['status'];

// Include database connection file
// include_once 'dbConnect.php';

// Insert transaction data into the database
// $sql = "INSERT INTO user_subscriptions(user_id,stripe_subscription_id,stripe_customer_id,stripe_plan_id,plan_amount,plan_amount_currency,plan_interval,plan_interval_count,payer_email,created,plan_period_start,plan_period_end,status) VALUES('".$userID."','".$subscrID."','".$custID."','".$planID."','".$planAmount."','".$planCurrency."','".$planinterval."','".$planIntervalCount."','".$email."','".$created."','".$current_period_start."','".$current_period_end."','".$status."')";
// $insert = $db->query($sql);

// Update subscription id in the users table
// if($insert && !empty($userID)){
// $subscription_id = $db->insert_id;
// $update = $db->query("UPDATE users SET subscription_id = {$subscription_id} WHERE id = {$userID}");
// }
// if(!empty($user_id)){
// $date1 = date("y-m-d");
// $update = mysqli_query($conn,"UPDATE sp_user SET `plan_id`='".$plan_id."', `plan_start_date`='".$current_period_start."', `plan_end_date`='".$current_period_end."' WHERE user_id='".$user_id."'" );
// }

$responseData['code'] = __('statuscodes.success');
$responseData['message'] = __('messages.success');
$responseData['data'] = $subsData;

}else{
$responseData['code'] = 400;
$responseData['message'] = "Subscription activation failed!";
}
}else{
$responseData['code'] = 400;
$responseData['message'] = "Subscription creation failed! ".$api_error;
}
}else{
$statusMsg = "Plan creation failed! ".$api_error;
$responseData['code'] = 400;
$responseData['message'] = $statusMsg;
}

// dd($responseData);
return response()->json($responseData);

}else{
$responseData['code'] = 400;
$responseData['message'] = "Either stripe customer id or subscription plan id is missing";
return response()->json($responseData);
}

}
?>