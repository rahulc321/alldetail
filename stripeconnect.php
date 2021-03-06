<?php
 public function updateProfile(request $request)
  {
    if (isset($request->verification_doc)) {
      $userid = Auth::id();
      $payment = UserBillingCard::where(['user_id' => $userid, 'payment_method' => 'bank_account'])->first();
      $kycDetails = KYC::where('user_id', $userid)->first();

      if ($request->verification_doc == 'update_profile') 
      {
        $users = Users::where('id', $userid)->first();
        $users->name = encrypt($request->first_name);
        $users->last_name = encrypt($request->last_name);
        $users->mobile = encrypt($request->phone);
        $users->job_skill = $request->job_skill;
        $users->hire_amount = $request->hire_amount;
        $users->tags = $request->tags;

        $users->address_one = $request->address_one;
        $users->address_two = $request->address_two;
        $users->country = $request->country;
        $users->state = $request->state;
        $users->city = $request->city;
        $users->zipcode = $request->zipcode;
        $users->save();


        $kycDetails->first_name = $request->first_name;
        $kycDetails->last_name = $request->last_name;
        $kycDetails->dob = date('d-m-Y', strtotime($request->dob));
        $kycDetails->ssn_last_four = $request->ssn;

        $kycDetails->phone = $request->phone;
        $kycDetails->address_one = $request->address_one;
        $kycDetails->address_two = $request->address_two;
        $kycDetails->state = state($request->state);
        $kycDetails->city = city($request->city);
        $kycDetails->zip = $request->zipcode;
        $kycDetails->website = $request->website; 

        if(isset($request->identity_front))
        {

          // Use get_headers() function 
        $headers = @get_headers($request->identity_front); 
          
        // Use condition to check the existence of URL 
        if($headers && strpos( $headers[0], '200')) 
        { 
           
        } else { 
            
          $UploadImageArray = $this->Base64toImage($request->identity_front);

          $kycDetails->id_front = $UploadImageArray['image_name'];
  
          // Upload Identity Front


          $destinationPath = public_path('/storage/dashboard/images/kycproofs/');
          \File::put($destinationPath.$UploadImageArray['image_name'], base64_decode($UploadImageArray['image']));
          // $this->storage->put($this->upload_kyc_filepath . $UploadImageArray['image_name'], base64_decode($UploadImageArray['image']));
        }

      }
      if(isset($request->identity_back))
      {

          // Use get_headers() function 
          $headers = @get_headers($request->identity_back); 
          
          // Use condition to check the existence of URL 
          if($headers && strpos( $headers[0], '200')) 
          { 
             
          } else { 
            $UploadImageArray2 = $this->Base64toImage($request->identity_back);

            $kycDetails->id_back = $UploadImageArray2['image_name'];

            // Upload Identity Back
             
            $destinationPath2 = public_path('/storage/dashboard/images/kycproofs/');
          \File::put($destinationPath2.$UploadImageArray2['image_name'], base64_decode($UploadImageArray2['image']));

            // $this->storage->put($this->upload_kyc_filepath . $UploadImageArray2['image_name'], base64_decode($UploadImageArray2['image']));
          }
      }
        $kycDetails->save();

        $payment->bank_name = $request->bank_name;
        $payment->account_no = $request->account_no;
        $payment->branch_code = $request->branch_code;
        $payment->ifsc_code = $request->routing_no;
        $payment->postal_code = $request->postal_code;
        $payment->save();


        return response()->json(['status' => true, 'message' => 'user profile updated successfully', 'data' => $request->all()], 200);
      } else {

        if ($kycDetails) {
          if ($payment) {
            $stripe_account_id = $payment->stripe_account_id;

            $front_id_image_path = public_path() . '/storage/dashboard/images/kycproofs/' . $kycDetails->id_front;
            $back_id_image_path = public_path() . '/storage/dashboard/images/kycproofs/' . $kycDetails->id_back;

            \Stripe\Stripe::setApiKey(env("STRIPE_SECRET"));

            \Stripe\Stripe::setVerifySslCerts(false);

            try {

              $front = \Stripe\File::create(
                [
                  "purpose" => "identity_document",
                  "file" => fopen($front_id_image_path, 'r')
                ],
                ["stripe_account" => $stripe_account_id]
              );

              $back = \Stripe\File::create(
                [
                  "purpose" => "identity_document",
                  "file" => fopen($back_id_image_path, 'r')
                ],
                ["stripe_account" => $stripe_account_id]
              );
              $birthdate = explode('-', $kycDetails->dob);
              // echo $kycDetails->state; die;

              if ($request->verification_doc == 'without_doc') {
                $result = \Stripe\Account::update(
                  $stripe_account_id,
                  [
                    'individual' => [
                      'address' => [
                        'city' => $kycDetails->city,
                        'line1' => $kycDetails->address_one,
                        'line2' => $kycDetails->address_two,
                        'postal_code' => $kycDetails->zip,
                        'state' => 'NY',
                      ],
                      'dob' => [
                        'day' => $birthdate[0],
                        'month' => $birthdate[1],
                        'year' => $birthdate[2],
                      ],

                      'email' => $kycDetails->email,
                      'first_name' => $kycDetails->first_name,
                      'last_name' => $kycDetails->last_name,
                      'phone' => $kycDetails->phone,
                      'id_number' => $kycDetails->ssn_last_four,
                      'ssn_last_4' => substr($kycDetails->ssn_last_four, -4),
                    ],
                    'business_profile' => ['url' => $kycDetails->website, 'mcc' => '7623']
                  ],

                );
                if($result->capabilities->card_payments=='active' && $result->capabilities->transfers=='active' &&  $result->payouts_enabled==1)
               {
                $kycDetails->verification_status = 1;
                $kycDetails->full_response = json_encode($result);
                $kycDetails->save();
                $Vmessage = "Account verification is completed.";

               } else {
                $kycDetails->full_response = json_encode($result);
                $kycDetails->save();
                $Vmessage = "Account verification is not completed. Please try again after 24 hours";

               }
              } else {

                $result = \Stripe\Account::update(
                  $stripe_account_id,
                  [
                    'individual' => [
                      'address' => [
                        'city' => $kycDetails->city,
                        'line1' => $kycDetails->address_one,
                        'line2' => $kycDetails->address_two,
                        'postal_code' => $kycDetails->zip,
                        'state' => 'NY',
                      ],
                      'dob' => [
                        'day' => $birthdate[0],
                        'month' => $birthdate[1],
                        'year' => $birthdate[2],
                      ],
                      'verification' => [
                        'document' => ['front' => $front->id, 'back' => $back->id]
                      ],
                      'email' => $kycDetails->email,
                      'first_name' => $kycDetails->first_name,
                      'last_name' => $kycDetails->last_name,
                      'phone' => $kycDetails->phone,
                      'id_number' => $kycDetails->ssn_last_four,
                      'ssn_last_4' => substr($kycDetails->ssn_last_four, -4),
                    ],
                    'business_profile' => ['url' => $kycDetails->website, 'mcc' => '7623']
                  ],

                );

                if($result->capabilities->card_payments=='active' && $result->capabilities->transfers=='active' &&  $result->payouts_enabled==1)
                {
                 $kycDetails->verification_status = 1;
                 $kycDetails->full_response = json_encode($result);
                 $kycDetails->save();
                 $Vmessage = "Account verification is completed.";
                } else {
                  $kycDetails->full_response = json_encode($result);
                  $kycDetails->save();
                  $Vmessage = "Account verification is not completed. Please try again after 24 hours";

                }
              }

              return response()->json(['status' => true, 'message' =>$Vmessage ,'data'=>$result], 200);
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
          } else {
            return response()->json(['status' => false, 'message' => 'You have not added your bank account details.'], 200);
          }
        } else {
          return response()->json(['status' => false, 'message' => 'You have not submitted your kyc details yet, kindly upload your KYC details.'], 200);
        }
      }
    } else {

      $id = Auth::id();
      $users = Users::where('id', $id)->first();
      if (isset($request->first_name)) {
        $users->name = encrypt($request->first_name);
      }

      $users->last_name = encrypt($request->last_name);

      if (isset($request->business_name)) {
        $users->business_name = $request->business_name;
      }

      if (isset($request->business_address)) {
        $users->business_address = $request->business_address;
      }

      if (isset($request->business_email)) {
        $users->business_email = $request->email;
      }

      if (isset($request->business_phone)) {
        $users->business_phone = $request->phone;
      }

      $users->language = $request->language;

      if (isset($request->job_skill)) {
        $users->job_skill = $request->job_skill;
      }

      $users->tags = $request->tags;

      if (isset($request->mobile)) {
        $users->mobile = encrypt($request->phone);
      }

      if (isset($request->preferred_communication)) {
        $users->preferred_communication = $request->preferred_communication;
      }


      $users->text_number = $request->text_number;

      if (isset($request->address_one)) {
        $users->address_one = $request->address_one;
      }

      $users->address_two = $request->address_two;

      if (isset($request->country)) {
        $users->country = $request->country;
      }

      if (isset($request->state)) {
        $users->state = $request->state;
      }

      if (isset($request->city)) {
        $users->city = $request->city;
      }

      if (isset($request->zipcode)) {
        $users->zipcode = $request->zipcode;
      }

      if (isset($request->hire_amount)) {
        $users->hire_amount = $request->hire_amount;
      }

      $users->updated_at = date('Y-m-d h:i:s');

      if ($request->file('image')) {
        $files = $request->file('image');
        $destinationPath = public_path('dashboard/images/users/'); // upload path
        $profileImage =  uniqid() . "." . $files->getClientOriginalExtension();
        $files->move($destinationPath, $profileImage);
        $users->image = $profileImage;
      }

      if ($users->save()) {
        // update user account details
        $email = decrypt($users->email);
        $countrydetails = Country::where('id', $request->country)->first();
        $countryCode = $countrydetails->sortname;
        //creating stripe customer account for workit
        \Stripe\Stripe::setApiKey(env("STRIPE_SECRET"));
        // echo env("STRIPE_SECRET"); die;
        try {

          $account = \Stripe\Account::create([
            "type" => "custom",
            "country" => $countryCode,
            "email" => $email,
            "requested_capabilities" => ['card_payments', 'transfers'],
            "business_type" => "individual"
          ]);

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
                'account_holder_name' => decrypt($users['name']) . " " . decrypt($users['last_name']),
                'account_holder_type' => 'individual',
                'account_number' => $request->account_no,
                'routing_number' => $request->routing_no,
                'sort_code' => $request->sort_code,
              ],
            ]
          );

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
        } catch (\Stripe\Exception\CardException $e) {
          return response()->json(['status' => false, 'message' => $e->getError()->message], 200);
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


        $bankDetails = new UserBillingCard();
        $bankDetails->user_id = $id;
        $bankDetails->payment_method = 'bank_account';
        $bankDetails->bank_name = $request->bank_name;
        $bankDetails->account_no = $request->account_no;
        $bankDetails->ifsc_code = $request->routing_no;
        $bankDetails->branch_code = $request->branch_code;
        $bankDetails->postal_code = $request->postal_code;
        if ($account->id) {
          $bankDetails->stripe_account_id = $account->id;
        }
        $bankDetails->save();


        // Upload KYC Details
        $kyc = new KYC();
        $kyc->user_id = $id;
        $kyc->first_name = $request->first_name;
        $kyc->last_name = $request->last_name;
        $kyc->dob =  date('d-m-Y', strtotime($request->dob));
        $kyc->ssn_last_four = $request->ssn;

        $UploadImageArray = $this->Base64toImage($request->identity_front);

        $kyc->id_front = $UploadImageArray['image_name'];

        // Upload Identity Front
        $this->storage->put($this->upload_kyc_filepath . $UploadImageArray['image_name'], base64_decode($UploadImageArray['image']));

        $UploadImageArray2 = $this->Base64toImage($request->identity_back);

        $kyc->id_back = $UploadImageArray2['image_name'];

        // Upload Identity Back

        $this->storage->put($this->upload_kyc_filepath . $UploadImageArray2['image_name'], base64_decode($UploadImageArray2['image']));



        $kyc->website = $request->website;
        $kyc->email = $request->email;
        $kyc->phone = $request->phone;
        $kyc->address_one = $request->address_one;
        $kyc->address_two = $request->address_two;
        $kyc->city = city($request->city);
        $kyc->state = state($request->state);
        $kyc->zip = $request->zipcode;
        $kyc->verification_status = 0;

        $ErrorsArray = array();
        if ($kyc->save()) {
        }
        // print_r($ErrorsArray); die;

        $details = array(
          'id' => $users->id,
          'uuid' => $users->uuid,
          'first_name' => decrypt($users->name),
          'last_name' =>  decrypt($users->last_name),
          'email' =>  decrypt($users->email),
        );
        $details['kyc_details'] = $kyc;
        $details['kyc_details']->id_front = url("/") . '/public/storage/dashboard/images/kycproofs/' . $kyc->id_front;
        $details['kyc_details']->id_back = url("/") . '/public/storage/dashboard/images/kycproofs/' . $kyc->id_back;
        if ($users->mobile != null) {
          //$details['mobile'] = decrypt($users->mobile);
        } else {
          //$details['mobile'] = "";
        }
        if ($users->user_type_id == 4) {
          $details['business_name'] = $users->business_name;
          $details['business_address'] = $users->business_address;
          $details['business_email'] = $users->email;
          $details['business_phone'] = $users->phone;
        }
        if ($users->image != null) {
          $details['image'] = url('/') . '/public/dashboard/images/users/' . $users->image;
        } else {
          $details['image'] = url('/') . '/public/dashboard/images/users/default.jpg';
        }

        if ($users->user_type_id == 4) {
          $details['user_type'] = 'Business Owner';
        } elseif ($users->user_type_id == 3) {
          $details['user_type'] = 'Supervisor';
        } elseif ($users->user_type_id == 2) {
          $details['user_type'] = 'Workit';
        }

        $details['language'] = $users->language;
        if ($users->job_skill != null) {
          $skilldata = Jobskill::where('id', $users->job_skill)->get();
          $details['job_skill'] = $users->job_skill;
          $details['job_skillName'] = $skilldata[0]->skill;
        } else {
          $details['job_skill'] = '';
          $details['job_skillName'] = '';
        }
        $details['tags'] = $users->tags;
        $details['preferred_communication'] = $users->preferred_communication;
        $details['text_number'] = $users->text_number;
        $details['address_one'] = $users->address_one;
        $details['address_two'] = $users->address_two;
        $details['country'] = $users->country;
        $details['countryName'] = country($users->country);
        $details['state'] = $users->state;
        $details['stateName'] = state($users->state);
        $details['city'] = $users->city;
        $details['cityName'] = city($users->city);
        $details['zipcode'] = $users->zipcode;
        $details['hire_amount'] = $users->hire_amount;
        //$details['available'] = $users->available;
        //$details['about_us'] = $users->about_us;
        //$details['certification'] = $users->certification;
        //$details['education'] = $users->education;
        //$details['experience'] = $users->experience;
        $details['bank_account_details'] = $bankDetails;

        return response()->json(['status' => true, 'message' => 'user profile updated successfully', 'data' => $details], 200);
      } else {
        return response()->json(['status' => false, 'message' => 'Error while updating user profile'], 200);
      }
    }
  }

?>
