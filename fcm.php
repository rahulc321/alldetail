<?php  
			$url = "https://fcm.googleapis.com/fcm/send";
			$token = 'djQw0NBX3kB7qjy1EXI71d:APA91bGh72S3x0xSbRhAk0rAuxzn4kKmrjMQOyGUrOI4Vw0YuaJvK0V6AHHDuNkMZXmGnJGPxubCDnZJ9eB0RCsa9t2gEL-QTx4UL5TohtqPWtz3YvOb5VHPM-bUg3dmSNPuVuM5fv0c';
			//$token = 'f_dC0e99JEWhrXzOZRVyzw:APA91bEoA5-A54u1iBB0Vff6jbU-j746mKmsCm_X2L3-VjzY0H_71QtWvYgYMekxrR3ayWQRHBS32SEYWsFwAkgrhowP1qhewPm5ie4LYWXJMmtYPp_jbpLi79WxGjN-n6-bVDZbhqK6';
			$serverKey = 'AAAAJPp4m0U:APA91bF0mUFIMOeDyQ8BlfyXnx1Q-k_6_GN5AJbeKU21gsz4DwuAk_y9BwIp59edvUiEAO-MU_D6mYsuH7MCBOpZkIcY5KKXpi1DPHtPks0C-epWiwqTXnAzBb9Z23wC06NZ5Tte2qVC';
			$title = 'test';
			$description ='test';
			$booking_type = 1;
			$order_id = 2412;

			$notification = array('title' =>$title , 'description' => $description, 'booking_type' =>$booking_type,"order_id"=>$order_id, 'sound' => 'default', 'badge' => '2','alert'=>$description);


			// // print_r($notification); die;
			// $notification['aps']['sound'] = 'default';
			// $notification['aps']['alert'] = $description;
			// $notification['aps']['badge'] = 2;
			// $notification['aps']['notification_type'] = $booking_type;
			// $notification['aps']['title'] = $title;
			// $notification['aps']['booking_type'] = $booking_type;
			// $notification['aps']['order_id'] = $order_id;


			$arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');


			$json = json_encode($arrayToSend);

			$headers = array();

			$headers[] = 'Content-Type: application/json';
			$headers[] = 'Authorization: key='. $serverKey;
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			$response = curl_exec($ch);
			echo '<pre>';
			print_r(json_encode($response)); die;	

	
		die;
	

		$url = "https://fcm.googleapis.com/fcm/send";
        $token = 'djQw0NBX3kB7qjy1EXI71d:APA91bGh72S3x0xSbRhAk0rAuxzn4kKmrjMQOyGUrOI4Vw0YuaJvK0V6AHHDuNkMZXmGnJGPxubCDnZJ9eB0RCsa9t2gEL-QTx4UL5TohtqPWtz3YvOb5VHPM-bUg3dmSNPuVuM5fv0c';
        $serverKey = 'AAAAJPp4m0U:APA91bF0mUFIMOeDyQ8BlfyXnx1Q-k_6_GN5AJbeKU21gsz4DwuAk_y9BwIp59edvUiEAO-MU_D6mYsuH7MCBOpZkIcY5KKXpi1DPHtPks0C-epWiwqTXnAzBb9Z23wC06NZ5Tte2qVC';
        $notification = array(
            'title' => 'test',
            'text' => 'abc',
            'sound' => 'default',
            'badge' => 1,
            'category' => 'kkk',
            'content-available' => 1
        );
        $arrayToSend = array(
            'to' => $token,
            'notification' => $notification,
            'custom_perms' => 'w322323',
            'priority' => 'high'
        );
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . $serverKey;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //Send the request
        echo $response = curl_exec($ch);
        //Close request
        if ($response === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);


die;
    
		$url = "https://fcm.googleapis.com/fcm/send";
		$token = 'djQw0NBX3kB7qjy1EXI71d:APA91bGh72S3x0xSbRhAk0rAuxzn4kKmrjMQOyGUrOI4Vw0YuaJvK0V6AHHDuNkMZXmGnJGPxubCDnZJ9eB0RCsa9t2gEL-QTx4UL5TohtqPWtz3YvOb5VHPM-bUg3dmSNPuVuM5fv0c';
		//$token = 'f_dC0e99JEWhrXzOZRVyzw:APA91bEoA5-A54u1iBB0Vff6jbU-j746mKmsCm_X2L3-VjzY0H_71QtWvYgYMekxrR3ayWQRHBS32SEYWsFwAkgrhowP1qhewPm5ie4LYWXJMmtYPp_jbpLi79WxGjN-n6-bVDZbhqK6';
		$serverKey = 'AAAAJPp4m0U:APA91bF0mUFIMOeDyQ8BlfyXnx1Q-k_6_GN5AJbeKU21gsz4DwuAk_y9BwIp59edvUiEAO-MU_D6mYsuH7MCBOpZkIcY5KKXpi1DPHtPks0C-epWiwqTXnAzBb9Z23wC06NZ5Tte2qVC';
		$title = 'test';
		$description ='test';
		$booking_type = 1;
		$order_id = 2412;
		//json_encode(array('booking_type'=>1,'order_id'=>45,'title'=>'Hello'))
		$notification = array('title' =>$title ,'category'=>json_encode(array('booking_type'=>1,'order_id'=>45,'title'=>'Hello')), 'description' => $description, 'booking_type' =>$booking_type,"order_id"=>$order_id, 'sound' => 'default', 'badge' => '2','alert'=>$description);


		// // print_r($notification); die;
		// $notification['aps']['sound'] = 'default';
		// $notification['aps']['alert'] = $description;
		// $notification['aps']['badge'] = 2;
		// $notification['aps']['notification_type'] = $booking_type;
		// $notification['aps']['title'] = $title;
		// $notification['aps']['booking_type'] = $booking_type;
		// $notification['aps']['order_id'] = $order_id;


		$arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');


		$json = json_encode($arrayToSend);

		$headers = array();

		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Authorization: key='. $serverKey;
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($ch);
		echo '<pre>';
		print_r(json_encode($notification)); die;
    
    


    die;
    

    
    

 		define('API_ACCESS_KEY','AAAAJPp4m0U:APA91bF0mUFIMOeDyQ8BlfyXnx1Q-k_6_GN5AJbeKU21gsz4DwuAk_y9BwIp59edvUiEAO-MU_D6mYsuH7MCBOpZkIcY5KKXpi1DPHtPks0C-epWiwqTXnAzBb9Z23wC06NZ5Tte2qVC');
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $token = 'cGydjw1lvEKdskWs_Hvk8U:APA91bHmFu_139DwDgYtOy_EFjNT8mfbHpdaIgEhJUAp7O0NqSelSC2hvuJ-0HZmVOo3sx_3Z2FptgJK4GYsnStKSr1o12eh6_r58X4YCyDU8YbnZAWieYDXXqwlwhavc76s_ltseiws';
        //$token='dmeobwX9QWQ:APA91bGdKJ2J8gZVyGfyZAnqvvg1kGXtW1Wu60VFVnwmDQ0769Rwyw8P3jkBETuWhLwJeYH_4ZVH7nx28CtoDqLBeV2N7w5_XpnPfW93RLHX61brrNIWxY6KmM2X0l4T8aeC9ZktBplC';
            $notification = [
                'title' =>'test',
                'body' => 'test',
                'icon' =>'myIcon', 
                'sound' => 'mySound',
                'notification_type'=>''   
            ];

           // $extraNotificationData = [$notification];

            $fcmNotification = [
                //'registration_ids' => $tokenList, //multple token array
                'to'        => $token, //single token
                'notification' => $notification,
                'data' => $notification
            ];

            $headers = [
                'Authorization: key=' .API_ACCESS_KEY,
                'Content-Type: application/json'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$fcmUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
            $result = curl_exec($ch);
            echo "<pre>";print_r($result);exit;
            curl_close($ch);
            return true;

?>