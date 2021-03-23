<?php
// delete account from stripe
  public function stripeDeleteAccount()
  {
    $userId= Auth::id();
    $kycDetail= KYC::where('user_id',$userId)->first();
    //echo '<pre>';print_r(json_decode($kycDetail->full_response)->individual->account);die;

    if($kycDetail){
      $chrgeId= json_decode($kycDetail->full_response)->individual->account;
      //$chrgeId= 'acct_1IWK4DQdznujFHNK';
   
      $stripe = new \Stripe\StripeClient(env("STRIPE_SECRET"));
      $delete = $stripe->accounts->delete(
      $chrgeId,
      []
      );
       
      if($delete->deleted==1){
        UserBillingCard::where('user_id',$userId)->delete();
        KYC::where('user_id',$userId)->delete();
        return response()->json(['status' => true, 'message' =>'Your account is successfully deleted.' ,'data'=>[]], 200);
      }else{
        return response()->json(['status' => false, 'message' =>'Please Try again.' ,'data'=>[]], 200);
      }

    }else{
      return response()->json(['status' => false, 'message' =>'Customer not found over stripe.' ,'data'=>[]], 200);
    }

     
            
  }

  // Account connected over stripe
  public function accountConnected(Request $request){
     // dd(3523);
    //echo Auth::id();die;
    $userDetail= Users::where('id',Auth::id())->first();
    //echo state($userDetail->state);die;
      $email = decrypt($userDetail->email);
      $countrydetails = Country::where('id', $userDetail->country)->first();
      $countryCode = $countrydetails->sortname;
      try{
      // Create account over stripe
      \Stripe\Stripe::setApiKey(env("STRIPE_SECRET"));
      $account = \Stripe\Account::create([
            "type" => "custom",
            "country" => $countryCode,
            "email" => $email,
            "requested_capabilities" => ['card_payments', 'transfers'],
            "business_type" => "individual"
          ]);

      // Update Account over stripe
      $result = \Stripe\Account::update(
            $account->id,
            [
              'default_currency' => 'usd',
              'email' => $email,
              ['metadata' => ['order_id' => uniqid()]],
              'external_account' => [
                'object' => 'bank_account',
                'country' => $countryCode,
                'currency' => 'usd',
                'account_holder_name' => decrypt($userDetail->name) . " " . decrypt($userDetail->last_name),
                'account_holder_type' => 'individual',
                'account_number' => $request->account_no,
                'routing_number' => $request->routing_no,
                'sort_code' => $request->branch_code,
              ],
            ]
          );

      // Reterive and update account
      $acct = \Stripe\Account::retrieve($account->id);

          $tos_acceptance = \Stripe\Account::update(
            $account->id,
            [
              'tos_acceptance' => [
                'date' => time(),
                'ip' => $_SERVER['REMOTE_ADDR'], // Assumes you're not using a proxy
              ],
            ]
          );


      // Image upload in our directory
      $fronImageName="";
      if(isset($request->identity_front)){
      $headers = @get_headers($request->identity_front); 
      if($headers && strpos( $headers[0], '200')){ 

      }else{ 
      $UploadImageArray = $this->Base64toImage($request->identity_front); 
      //echo '<pre>';print_r($UploadImageArray);
      //$kyc->id_front = $UploadImageArray['image_name'];
      $destinationPath = public_path('/storage/dashboard/images/kycproofs/');
      \File::put($destinationPath.$UploadImageArray['image_name'], base64_decode($UploadImageArray['image']));
      $fronImageName=$UploadImageArray['image_name'];
      }

      }
      $backImageName= "";
      if(isset($request->identity_back)){
          $headers = @get_headers($request->identity_back); 
          if($headers && strpos( $headers[0], '200')) { 
             
          }else { 
            $UploadImageArray2 = $this->Base64toImage($request->identity_back);
           // $kyc->id_back = $UploadImageArray2['image_name'];
            $destinationPath2 = public_path('/storage/dashboard/images/kycproofs/');
          \File::put($destinationPath2.$UploadImageArray2['image_name'], base64_decode($UploadImageArray2['image']));

          $backImageName= $UploadImageArray2['image_name'];
          }
      }
     
      /*$front_id_image_path= url("/") . '/public/storage/dashboard/images/kycproofs/'.$fronImageName; 
      $back_id_image_path= url("/") . '/public/storage/dashboard/images/kycproofs/'.$backImageName;*/
       

      $front_id_image_path = public_path() . '/storage/dashboard/images/kycproofs/'.$fronImageName;
      $back_id_image_path = public_path() . '/storage/dashboard/images/kycproofs/'.$backImageName;
      $chargeId= $account->id;

      \Stripe\Stripe::setApiKey(env("STRIPE_SECRET"));

      \Stripe\Stripe::setVerifySslCerts(false);
      
      $front = \Stripe\File::create(
      [
      "purpose" => "identity_document",
      "file" => fopen($front_id_image_path, 'r')
      ],
      ["stripe_account" => $chargeId]
      );

      $back = \Stripe\File::create(
      [
      "purpose" => "identity_document",
      "file" => fopen($back_id_image_path, 'r')
      ],
      ["stripe_account" => $chargeId]
      );
      $birthdate = explode('-', '22-10-1990');


      $result = \Stripe\Account::update(
                  $chargeId,
                  [
                    'individual' => [
                      'address' => [
                        'city' => city($userDetail->city),
                        'line1' => $userDetail->address_one,
                        'line2' => $userDetail->address_two,
                        'postal_code' => $userDetail->zipcode,
                        'state' => state($userDetail->state),
                      ],
                      'dob' => [
                        'day' => $birthdate[0],
                        'month' => $birthdate[1],
                        'year' => $birthdate[2],
                      ],
                      'verification' => [
                        'document' => ['front' => $front->id, 'back' => $back->id]
                      ],
                      'email' => decrypt($userDetail->email),
                      'first_name' => decrypt($userDetail->name),
                      'last_name' => decrypt($userDetail->last_name),
                      'phone' => decrypt($userDetail->mobile),
                      'id_number' => $request->ssn,
                      'ssn_last_4' => substr($request->ssn, -4),
                    ],
                    'business_profile' => ['url' => 'http://check.com', 'mcc' => '7623']
                  ],

                );

      sleep(2);
      $result = \Stripe\Account::update(
                  $chargeId,
                  [
                    'individual' => [
                      'address' => [
                        'city' => city($userDetail->city),
                        'line1' => $userDetail->address_one,
                        'line2' => $userDetail->address_two,
                        'postal_code' => $userDetail->zipcode,
                        'state' => state($userDetail->state),
                      ],
                      'dob' => [
                        'day' => $birthdate[0],
                        'month' => $birthdate[1],
                        'year' => $birthdate[2],
                      ],
                      // 'verification' => [
                      //   'document' => ['front' => $front->id, 'back' => $back->id]
                      // ],
                      'email' => decrypt($userDetail->email),
                      'first_name' => decrypt($userDetail->name),
                      'last_name' => decrypt($userDetail->last_name),
                      'phone' => decrypt($userDetail->mobile),
                      'id_number' => $request->ssn,
                      'ssn_last_4' => substr($request->ssn, -4),
                    ],
                    'business_profile' => ['url' => 'http://check.com', 'mcc' => '7623']
                  ],

                );
      //echo '<pre>';print_r($result->individual->verification->status=='verified'); 
              $details=[];
                if($result->capabilities->card_payments=='active' && $result->capabilities->transfers=='active' &&  $result->payouts_enabled==1)
               {

                  $bankDetails = new UserBillingCard();
                  $bankDetails->user_id = Auth::id();
                  $bankDetails->payment_method = 'bank_account';
                  $bankDetails->bank_name = $request->bank_name;
                  $bankDetails->account_no = $request->account_no;
                  $bankDetails->ifsc_code = $request->routing_no;
                  $bankDetails->branch_code = $request->branch_code;
                  $bankDetails->postal_code = $userDetail->zipcode;
                  $bankDetails->stripe_account_id = $account->id;
                  $bankDetails->save();

                  $kyc = new KYC();
                  $kyc->user_id = Auth::id();
                  $kyc->first_name = decrypt($userDetail->name);
                  $kyc->last_name = decrypt($userDetail->last_name);
                  $kyc->dob =  '';
                  $kyc->ssn_last_four = $request->ssn;
                  $kyc->id_front = $fronImageName;
                  $kyc->id_back = $backImageName;

                  $kyc->website = 'http://check.com';
                  $kyc->email = decrypt($userDetail->email);
                  $kyc->phone = decrypt($userDetail->mobile);
                  $kyc->address_one = $userDetail->address_one;
                  $kyc->address_two = $userDetail->address_two;
                  $kyc->city = city($userDetail->city);
                  $kyc->state = state($userDetail->state);
                  $kyc->zip = $userDetail->zipcode;
                  $kyc->full_response = json_encode($result);
                  $kyc->verification_status = 1;
                  $kyc->save();


                 $details['account_no'] = $request->account_no; 
                 $details['routing_no'] = $request->routing_no; 
                 $details['branch_code'] = $request->branch_code; 
                 $details['ssn'] = $request->ssn; 
                 $details['bank_name'] = $request->bank_name; 
                 $details['id_front'] = url("/") . '/public/storage/dashboard/images/kycproofs/' . $kyc->id_front;
                $details['id_back'] = url("/") . '/public/storage/dashboard/images/kycproofs/' . $kyc->id_back;
                 $details['verification_status'] = ($kyc->verification_status==1) ? 'Verified' : 'Pending'; 
                  
                $Vmessage = "Account verification is completed.";
                return response()->json(['status' => true, 'message' =>$Vmessage ,'data'=>$details], 200);
                } else {
                    
                $Vmessage = "Account verification is not completed. Please try again after 24 hours";
                return response()->json(['status' => false, 'message' =>$Vmessage ,'data'=>$details], 200);

                }
                
              } catch (\Stripe\Exception\RateLimitException $e) {
              // Too many requests made to the API too quickly
              return response()->json(['status' => false, 'message' => $e->getError()->message], 200);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
              // Invalid parameters were supplied to Stripe's API
              return response()->json(['status' => false, 'message' => $e->getError()->message], 200);
            } catch (\Stripe\Exception\AuthenticationException $e) {
              // Authentication with Stripe's API failed
              // (maybe you changed API keys recently)
              return response()->json(['status' => false, 'message' => $e->getError()->message], 200);
            } catch (\Stripe\Exception\ApiConnectionException $e) {
              // Network communication with Stripe failed
              return response()->json(['status' => false, 'message' => $e->getError()->message], 200);
            } catch (\Stripe\Exception\ApiErrorException $e) {
              // Display a very generic error to the user, and maybe send
              // yourself an email
              return response()->json(['status' => false, 'message' => $e->getError()->message], 200);
            } catch (Exception $e) {
              // Something else happened, completely unrelated to Stripe
              return response()->json(['status' => false, 'message' => $e->getError()->message], 200);
            }
         

  }


  // Get stripe bank 
  
  public function getStripeDetail()
  {

    $userId= Auth::id();
    $kyc= KYC::where('user_id',$userId)->first();

    if($kyc){
    $UserBillingCard= UserBillingCard::where('user_id',$userId)->first();

    $front_id = url("/") . '/public/storage/dashboard/images/kycproofs/' . $kyc->id_front;
    $back_id = url("/") . '/public/storage/dashboard/images/kycproofs/' . $kyc->id_back;

    $detail=array(
        'identity_front'=>$front_id,
        'identity_back'=>$back_id,
        'bank_name'=>$UserBillingCard->bank_name,
        'routing_no'=>$UserBillingCard->ifsc_code,
        'branch_code'=>$UserBillingCard->branch_code,
        'ssn'=>$kyc->ssn_last_four,
        'account_no'=>$UserBillingCard->account_no,
        'verification_status'=>($kyc->verification_status ==1) ? 'Verified' : 'Pending',
      );
    return response()->json(['status' => true, 'data' => $detail,'message' =>'List kyc details.'], 200);
  }else{
    $detail=array();
    return response()->json(['status' => true, 'data' => (object)$detail,'message' =>'Please Update profile'], 200);
  }
  } 

  ?>