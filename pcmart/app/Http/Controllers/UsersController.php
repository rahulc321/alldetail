<?php

namespace App\Http\Controllers;
require_once base_path().'/dbf/vendor/autoload.php';
use App\User;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\LoginTime;
use App\Models\UserPermission;
use App\Models\Role;
use App\Models\Product;
use App\Models\Cust;
use App\Models\Ictran;
use App\Models\Info;
use App\Models\CustomerInfo;
use App\Models\Ticket;
use App\Models\AssignTicket;
use App\Models\Feedback;
use XBase\Table;

class UsersController extends Controller
{
  //user List
  public function listUser(){
    $this->data['users']= User::with('getUserRole')->orderBy('id','desc')->get();
    return view('pages.app-users-list',$this->data);
  }
    //user view
    public function viewUser(){
    return view('pages.app-users-view');
  }
   //user edit
   public function editUser(){
    $this->data['loginData']= User::where('id',\Auth::user()->id)->first();
    return view('pages.app-users-edit',$this->data);
  }



    public function addUser(){

        $this->data['allPermision'] = Module::with('getAllModuel')->get()->toArray();
        $this->data['roles'] = Role::where('status',1)->get()->toArray();
         
        return view('pages.add-user',$this->data);
    }
    public function userStore(Request $request){
        $allData= $request->all();
       // dd($allData);
        $checkUser= User::where('email',$request->email)->first();
        if($checkUser){
            return redirect('app/add-user')->withErrors(['Error', 'Email already Exists !!!']);
        }

        $user= new User();
        $user->name= $request->fname;
        //$user->profile_pic= $request->profile_pic;
        $user->email= $request->email;
        $user->phone= $request->phone;
        $user->password= \Hash::make($request->password);
        $user->address= $request->address;
        $user->status= $request->is_active;
        $user->user_type= $request->user_type;
        //$user->user_type= 2;


        if($request->hasFile('file')) {
             

            $logofile = $request->file('file');
            // $destinationPath = public_path('/profile');
            // $name->move($destinationPath, $name);

            $destinationPath = public_path('profile/'); // upload path
            $outputImage =  "profile_".uniqid().".".$logofile->getClientOriginalExtension();
            $logofile->move($destinationPath, $outputImage);

             
            $user->profile_pic= $outputImage;
            //echo $logofile;die;
             
        }


        $user->save();
 
        return redirect('app/users/list')->withErrors(['Success', 'You have successfully added !!!']);
    }


  public function adminUpdate(Request $request){
    $user= User::where('id',\Auth::user()->id)->first();

    if($request->hasFile('file')) {
             

            $logofile = $request->file('file');
            // $destinationPath = public_path('/profile');
            // $name->move($destinationPath, $name);

            $destinationPath = public_path('profile/'); // upload path
            $outputImage =  "logo_".uniqid().".".$logofile->getClientOriginalExtension();
            $logofile->move($destinationPath, $outputImage);

             
            $user->profile_pic= $outputImage;
            //echo $logofile;die;
             
        }


    $user->name= $request->fname;
    //$user->profile_pic= $request->profile_pic;
    $user->email= $request->email;
    $user->phone= $request->phone;
    $user->address= $request->address;
    $user->company_name= $request->company_name;
    $user->update();

    return \Redirect::back()->withErrors(['Success', 'You have successfully updated !!!']);
     
  }


  // Edit user data and update permision
  public function editUserData($id){
    $this->data['edit']= User::where('id',$id)->first();
    $this->data['loginTime']= LoginTime::where('user_id',$id)->get()->toArray();
    //echo '<pre>';print_r($this->data['loginTime']);die;
    $this->data['allPermision'] = Module::with('getAllModuel')->get()->toArray();
    $allPerm = UserPermission::where('user_id',$id)->get();
    $this->data['roles'] = Role::where('status',1)->get()->toArray();
    $userKey=[];
    foreach ($allPerm as $key => $value) {
        $userKey[] = $value['module_key'];
    }



    return view('pages.users-edit',$this->data)->with('userKey',$userKey);
  }

  public function permstore(Request $request,$id){
        $allData= $request->all();
        
        $user= User::where('id',$id)->first();
        $user->name= $request->fname;
        //$user->profile_pic= $request->profile_pic;
        $user->email= $request->email;
        $user->phone= $request->phone;
        $user->address= $request->address;
        $user->status= $request->is_active;
        $user->user_type= @$request->user_type;

        if($request->hasFile('file')) {
             

            $logofile = $request->file('file');
            // $destinationPath = public_path('/profile');
            // $name->move($destinationPath, $name);

            $destinationPath = public_path('profile/'); // upload path
            $outputImage =  "profile_".uniqid().".".$logofile->getClientOriginalExtension();
            $logofile->move($destinationPath, $outputImage);

             
            $user->profile_pic= $outputImage;
            //echo $logofile;die;
             
        }

        $user->save();


        return redirect('app/users/list')->withErrors(['Success', 'You have successfully updated !!!']);
   
    }

    public function deleteUser($id){
        $user= User::where('id',$id)->delete();

        $allPerm = UserPermission::where('user_id',$id)->get();
        $LoginTime = LoginTime::where('user_id',$id)->get();

            if($allPerm){
               foreach ($allPerm as $key => $value) {
                //$allPerm = UserPermission::where('id',\Auth::user()->id)->get();
                $value->delete();
                
                } 
            }

            if($LoginTime){
               foreach ($LoginTime as $key => $value) {
                //$allPerm = UserPermission::where('id',\Auth::user()->id)->get();
                $value->delete();
                
                } 
            }
            return redirect('app/users/list')->withErrors(['Success', 'You have successfully deleted !!!']);
    }

    // list Role
    public function roles(){
        $this->data['roles']= Role::with('checkRole')->orderBy('id','desc')->get();
        //echo '<pre>';print_r($this->data['roles']);die;
        return view('admin.role.list',$this->data);
    }
    // Add role
    public function addRole(){
        $this->data['allPermision'] = Module::with('getAllModuel')->get()->toArray();
        return view('admin.role.add',$this->data);
    }

    public function roleStore(Request $request){
        $allData= $request->all();

        $checkUser= Role::where('role',$request->role)->first();
        if($checkUser){
            return redirect('app/add-role')->withErrors(['Error', 'Role already Exists !!!']);
        }

        $role= new Role();
        $role->role= $request->role;
         
        $role->status= $request->status;
        $role->save();

         if(isset($allData['keyname'])){
            foreach ($allData['keyname'] as $key => $value) {
                $userPermission= new UserPermission();
                $userPermission->user_id= $role->id;
                $userPermission->module_key= $value;
                $userPermission->save();
            }

        }



            foreach ($allData['from_from'] as $key => $value) {
                $logintime= new LoginTime();
                $logintime->user_id= $role->id;;
                $logintime->day_id= $key;
                $logintime->start_time= $value;
                $logintime->end_time= $allData['from_to'][$key];
                $logintime->save();
            }


        


        return redirect('app/role/list')->withErrors(['Success', 'You have successfully added !!!']);
    }

    // For edit role
    public function roleEdit($id){
    $this->data['edit']= Role::where('id',$id)->first();
    $this->data['loginTime']= LoginTime::where('user_id',$id)->get()->toArray();
    //echo '<pre>';print_r($this->data['loginTime']);die;
    $this->data['allPermision'] = Module::with('getAllModuel')->get()->toArray();
    $allPerm = UserPermission::where('user_id',$id)->get();
    $userKey=[];
    foreach ($allPerm as $key => $value) {
        $userKey[] = $value['module_key'];
    }



    return view('admin.role.edit',$this->data)->with('userKey',$userKey);
  }

  public function roleDelete($id){
        $user= Role::where('id',$id)->delete();

        $allPerm = UserPermission::where('user_id',$id)->get();
        $LoginTime = LoginTime::where('user_id',$id)->get();

            if($allPerm){
               foreach ($allPerm as $key => $value) {
                //$allPerm = UserPermission::where('id',\Auth::user()->id)->get();
                $value->delete();
                
                } 
            }

            if($LoginTime){
               foreach ($LoginTime as $key => $value) {
                //$allPerm = UserPermission::where('id',\Auth::user()->id)->get();
                $value->delete();
                
                } 
            }
            return redirect('app/role/list')->withErrors(['Success', 'You have successfully deleted !!!']);
    }
    // Update Role
    public function roleUpdate(Request $request,$id){
        $allData= $request->all();
        //echo '<pre>';print_r($allData['keyname']);die;
        $allPerm = UserPermission::where('user_id',$id)->get();
        $LoginTime = LoginTime::where('user_id',$id)->get();

            if($allPerm){
               foreach ($allPerm as $key => $value) {
                //$allPerm = UserPermission::where('id',\Auth::user()->id)->get();
                $value->delete();
                
                } 
            }

            if($LoginTime){
               foreach ($LoginTime as $key => $value) {
                //$allPerm = UserPermission::where('id',\Auth::user()->id)->get();
                $value->delete();
                
                } 
            }


        if(isset($allData['keyname'])){
            foreach ($allData['keyname'] as $key => $value) {
                $user= new UserPermission();
                $user->user_id= $id;
                $user->module_key= $value;
                $user->save();
            }

        }



            foreach ($allData['from_from'] as $key => $value) {
                $user= new LoginTime();
                $user->user_id= $id;
                $user->day_id= $key;
                $user->start_time= $value;
                $user->end_time= $allData['from_to'][$key];
                $user->save();
            }


        $Role= Role::where('id',$id)->first();
        $Role->role= $request->role;
        
        $Role->status= $request->status;
        $Role->save();


        return redirect('app/role/list')->withErrors(['Success', 'You have successfully updated !!!']);
   
    }

    public function updateTheme(Request $request){

        $allData=  $request->all();
        $user = User::where('id',\Auth::user()->id)->first();
        $user->theme= $allData['theme'];
            if($user->save()){
                return true;
            }
    }

    // for setting module
    
    public function settings(){
        $this->data['products']= Product::get();
        return view('admin.product.list',$this->data) ;
    }
    // for settind related
    public function settingsAdd(){
        
        return view('admin.product.add') ;
    }
    public function settingsStore(Request $request){
        $setting= new Product();
        $setting->title= $request->title;
        $setting->first_user= $request->first_user;
        $setting->add_user= $request->add_user;
        $setting->new= $request->new;
        $setting->renew= $request->renew;
        $setting->description= $request->description;
        $setting->company_name= $request->company_name;
        $setting->tax= $request->tax;
        $setting->save();

        return redirect('app/settings/list')->withErrors(['Success', 'You have successfully added !!!']);
    }


    public function settingsEdit($id){
    $this->data['edit']= Product::where('id',$id)->first();
     
    return view('admin.product.edit',$this->data);
  }

  public function settingsUpdate(Request $request,$id){
        $setting= Product::where('id',$id)->first();
        $setting->title= $request->title;
        $setting->first_user= $request->first_user;
        $setting->add_user= $request->add_user;
        $setting->new= $request->new;
        $setting->renew= $request->renew;
        $setting->description= $request->description;
        $setting->company_name= $request->company_name;
        $setting->tax= $request->tax;
        $setting->save();

        return redirect('app/settings/list')->withErrors(['Success', 'You have successfully updated !!!']);
    }

    public function settingsDelete($id){
        

        $Product = Product::where('id',$id)->delete();
        if($Product){ 
            return redirect('app/settings/list')->withErrors(['Success', 'You have successfully deleted !!!']);
        }
    }

    // For uploads module
    public function uploads(){
        return view('admin.upload.add');
    }
    // For customerList module
    public function customerList(){
        $this->data['customers']= Cust::with('getDeleteCount')->get();
       // dd($this->data['customers']);
        return view('admin.customer.list',$this->data);
    }

    public function cusomer2(Request $request){

         ## Read value
     $draw = $request->get('draw');
     $start = $request->get("start");
     $rowperpage = $request->get("length"); // Rows display per page

     $columnIndex_arr = $request->get('order');
     $columnName_arr = $request->get('columns');
     $order_arr = $request->get('order');
     $search_arr = $request->get('search');

     $columnIndex = $columnIndex_arr[0]['column']; // Column index
     $columnName = $columnName_arr[$columnIndex]['data']; // Column name
     $columnSortOrder = $order_arr[0]['dir']; // asc or desc
     $searchValue = $search_arr['value']; // Search value

      

    $records = Cust::with('getDeleteCount')->where(function ($query) use ($searchValue) {
        $query->where('arcust.Organization_Number', 'like', '%' .$searchValue . '%')
       ->orwhere('arcust.Organization_Name', 'like', '%' .$searchValue . '%')
       ->orwhere('arcust.Attention', 'like', '%' .$searchValue . '%')
       ->orwhere('arcust.Secondary_Phone', 'like', '%' .$searchValue . '%')
       ->orwhere('arcust.Primary_Phone', 'like', '%' .$searchValue . '%');
        });

         
       
    $records= $records->select('arcust.*');
        
   
    $recordr= $records->count();
    $records=   $records->skip($start)
   ->take($rowperpage)
    ->orderBy($columnName,$columnSortOrder)
    ->get()->toArray();

    $totalRecordswithFilter =$recordr;
    $totalRecords =$totalRecordswithFilter;

      
    $data_arr = array();
     foreach($records as $record){
        $deleteCount=0;
        //echo '<pre>';print_r($record['get_delete_count']);
        if(count($record['get_delete_count']) > 0){
        $deleteCount=1;   
        }

        $data_arr[] = array(
          "id" => $record['id'],
          "Organization_Number" => $record['Organization_Number'],
          "Organization_Name" => $record['Organization_Name'],
          "Attention" => $record['Attention'],
          "Primary_Phone" => $record['Primary_Phone'],
          "btn" => '',
          "deleteCount" => $deleteCount,
          "Secondary_Phone" => $record['Secondary_Phone']
        );
     }

     $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordswithFilter,
        "aaData" => $data_arr
     );

     echo json_encode($response);
     exit;

    }

    // For edit customer customerEdit
    public function customerEdit($id){
        $this->data['edit']= Cust::where('id',$id)->first();
       // dd($this->data['edit']['Organization_Number']);
        $this->data['products']= Product::get();
        $this->data['checkInfo'] = CustomerInfo::where('customer_id',$this->data['edit']['Organization_Number'])->get()->toArray();
        //echo '<pre>';print_r($this->data['checkInfo']);die;
        return view('admin.customer.edit',$this->data);
    }
    public function customerUpdate($id,Request $request){
        $all= $request->all();
       // echo '<pre>';print_r($all);

        // die;

        
        $checkInfo = CustomerInfo::where('customer_id',$request->Organization_Number)->get();


        



        foreach($checkInfo as $key=>$ids){
            $ids->delete();
        }
        $updateIds=[];
        foreach($all['id'] as $key=>$ids){

            if($all['expcheck'][$key]==1){
                $updateIds[]=$all['id'][$key];
            }


            $otherInfo = new CustomerInfo();
            $otherInfo->customer_id=$request->Organization_Number;
            $otherInfo->setting_id=@$all['id'][$key];
            $otherInfo->exp_date_checkbox=@$all['expcheck'][$key];
            if($all['exp_date'][$key] != ""){
            $otherInfo->exp_date=@date('Y-m-d',strtotime($all['exp_date'][$key]));
            }
            $otherInfo->sno_number=@$all['sno'][$key];
            $otherInfo->user=@$all['user'][$key];
            if($all['sagecover'][$key] != ""){
            $otherInfo->sage_cover=@date('Y-m-d',strtotime($all['sagecover'][$key]));
            }
            $otherInfo->sage_cover_checkbox=@$all['sagecover_check'][$key];
            $otherInfo->info_type=@$all['title'][$key];
            $otherInfo->save();
          //  echo $ids.'>>>'.$all['title'][$key].'>>'.$all['sno'][$key].'>>'.$all['user'][$key].'>>'.$all['sagecover'][$key];echo '<br>';
        }
        // echo '<pre>';print_r($updateIds);

        // die;


        /*$ictran = Ictran::where('CUSTNO',$request->Organization_Number)->first();
        $ictran->product= implode(',',$updateIds);
        $ictran->save();*/

        $custUpdate= Cust::where('id',$id)->first();
        $custUpdate->Organization_Number= $request->Organization_Number;
        $custUpdate->Organization_Name= $request->Organization_Name;
        $custUpdate->Address1= $request->Address1;
        $custUpdate->Address2= $request->Address2;
        $custUpdate->Address3= $request->Address3;
        $custUpdate->Address4= $request->Address4;
        $custUpdate->Attention= $request->Attention;
        $custUpdate->Contact= $request->Contact;
        $custUpdate->Primary_Phone= $request->Primary_Phone;
        $custUpdate->Secondary_Phone= $request->Secondary_Phone;
        $custUpdate->Fax= $request->Fax;
        $custUpdate->Primary_Email= $request->Primary_Email;
        $custUpdate->Area= $request->Area;
        $custUpdate->Agent= $request->Agent;
        $custUpdate->ROC= $request->ROC;
        $custUpdate->GST= $request->GSTREGNO;
        $custUpdate->Blacklist= $request->Blacklist;
        $custUpdate->save();
        return redirect('app/customer')->withErrors(['Success', 'You have successfully updated !!!']);
    }
    // For customerDelete module
    public function customerDelete($id){
        $deleteCustomer= Cust::where('id',$id)->delete();
        if($deleteCustomer){
            return redirect('app/customer')->withErrors(['Success', 'You have successfully deleted !!!']);
        }
    }

    public function infoList(){
        $this->data['info'] = Info::first();
        return view('admin.info.list',$this->data);
    }
    public function infoEdit(){
        $this->data['edit'] = Info::first();
        return view('admin.info.edit',$this->data);
    }

    public function infoUpdate(Request $request){
        $data= $request->all();
        //echo '<pre>';print_r($data);die;
        // dd($request->all());
        $info = Info::where('id',1)->first();
        $info->company_name= $data['company_name'];
        $info->company_number= $data['company_number'];
        $info->address= $data['address'];
        $info->phone= $data['phone'];
        $info->attention= $data['attention'];
        $info->email= $data['email'];
        $info->website= $data['website'];
        $info->fb= $data['fb'];
        $info->skype= $data['skype'];
        $info->other= $data['other'];
        $info->tax= $data['tax'];
        $info->tax_number= $data['tax_number'];
        $info->save();
        return redirect('app/info')->withErrors(['Success', 'You have successfully updated !!!']);
    }

    // delete records
    public function deleteRecord(Request $request){
        //echo '<pre>';print_r($request->all());die;
        foreach($request->checkOne as $value){
            $deleteCustomer= Cust::where('id',$value)->delete();

        }
        return redirect('app/customer')->withErrors(['Success', 'You have successfully deleted !!!']);
    }


    // For upload file
    public function arcust(Request $request){
        if($request->hasFile('file')) {
             

            $logofile = $request->file('file');
             $fileName= $logofile->getClientOriginalName();


             if($fileName !='arcust.dbf'){
                \Session::flash('error', 'Please choose only arcust.dbf file !!!');
                return redirect('app/uploads');
             }

            $destinationPath = public_path('arcust/'); // upload path
            $outputImage =  "arcust_".uniqid().".".$logofile->getClientOriginalExtension();
            $logofile->move($destinationPath, $outputImage);

             
            $path= public_path().'/arcust/'.$outputImage;


            $table = new Table($path);

            $actual_data = array();
            $keyVal = array();


            $actual_data = array();
            $keyVal = array();

            $i = 0;
            while ($record = $table->nextRecord()) {
                $row = array();
                
                foreach ($table->getColumns() as $i=>$c)
                {   


                    if($c->getType() != 'G'){
                        if($c->getName()=="CREATED_ON"){
                            $row[$c->getName()] = $record->getDateTime($c);
                        }else{
                            $row[$c->getName()] = $record->$c;
                        }
                    }
                }
                $keyI = count($keyVal);
            
                //$keyVal[$keyI] = array($row['custno'],$row['custno']);
                array_push($actual_data, $row);
            }

           // echo '<pre>';print_r($actual_data);die;

            foreach ($actual_data as $key => $value) {
                $checkCustNo = Cust::where('Organization_Number',$value['custno'])->first();

                if($checkCustNo){
                    //echo 'update'; echo '<br>';
                }else{ 

                $values = new Cust;
                if($value['custno'] !=""){
                    $values->Organization_Number    = $value['custno'];
                }
                if($value['name'] !=""){
                    $values->Organization_Name    = $value['name'];
                }
                if($value['add1'] !=""){
                    $values->Address1    = $value['add1'];
                }
                if($value['add2'] !=""){
                    $values->Address2    = $value['add2'];
                }
                if($value['add3'] !=""){
                 
                    $values->Address3    = $value['add3'];
                }
                if($value['add4'] !=""){
                    $values->Address4    = $value['add4'];
                }
                
                if($value['attn'] !=""){
                    $values->Attention    = $value['attn'];
                }
                if($value['contact'] !=""){
                    $values->Contact    = $value['contact'];
                }
                if($value['phone'] !=""){
                   $values->Primary_Phone    = $value['phone'];
                }
                if($value['phonea'] !=""){
                   $values->Secondary_Phone    = $value['phonea'];
                }
                if($value['fax'] !=""){
                     $values->Fax    = $value['fax'];
                }
                if($value['e_mail'] !=""){
                    $values->Primary_Email    = $value['e_mail'];
                }
                if($value['area'] !=""){
                    $values->Area    = $value['area'];
                }
                if($value['agent'] !=""){
                   $values->Agent    = $value['agent'];
                }
                if($value['status'] !=""){
                   $values->Blacklist    = $value['status'];
                }
                if($value['comuen'] !=""){
                   $values->ROC    = $value['comuen'];
                }
                //echo $key.'-'.$value['comuen'];echo '<br>';
                if($value['gstregno'] !=""){
                    $values->GST    = $value['gstregno'];
                }
                if($value['date'] !=""){
                    $values->Created_Time = date('d-m-Y',strtotime($value['date']));
                }
                // if($value['name'] !=""){

                //     $actTime= $value['created_on']->format(\DateTime::ISO8601);
                //     $values->Created_Time = date('d-m-Y',strtotime($actTime));
                // }
                
                $values->save();

            }
        }
             
        }


        \Session::flash('message', 'You have successfully uploaded !!!');
        return redirect('app/uploads');
    }
    public function ictrain(Request $request){
        if($request->hasFile('file')) {
             

            $logofile = $request->file('file');
             $fileName= $logofile->getClientOriginalName();


             if($fileName !='ictran.dbf'){
                \Session::flash('error', 'Please choose only ictran.dbf file !!!');
                return redirect('app/uploads');
             }

            $destinationPath = public_path('ictrain/'); // upload path
            $outputImage =  "arcust_".uniqid().".".$logofile->getClientOriginalExtension();
            $logofile->move($destinationPath, $outputImage);

             
            $path= public_path().'/ictrain/'.$outputImage;


            $table = new Table($path);

            $actual_data = array();
            $keyVal = array();


            $actual_data = array();
            $keyVal = array();

            $i = 0;
            while ($record = $table->nextRecord()) {
                $row = array();
                
                foreach ($table->getColumns() as $i=>$c)
                {   


                    if($c->getType() != 'G'){
                        if($c->getName()=="CREATED_ON"){
                            $row[$c->getName()] = $record->getDateTime($c);
                        }else{
                            $row[$c->getName()] = $record->$c;
                        }
                    }
                }
                $keyI = count($keyVal);
            
                //$keyVal[$keyI] = array($row['custno'],$row['custno']);
                array_push($actual_data, $row);
            }

            // echo '<pre>';print_r($actual_data);die;

            foreach ($actual_data as $key => $value) {
                $checkCustNo = Ictran::where('Contract_Number',$value['refno'])->first();

                if($checkCustNo){
                   // echo 'update'; echo '<br>';
                }else{ 

               // echo $value['itemno'];echo '<br>'; 




                $action=0;
                if($request->start !="" && $request->to !="" && $request->start <= $value['itemno'] && $request->to >= $value['itemno']){


                     
                $values = new Ictran;
                if($value['custno'] !=""){
                    $values->CUSTNO = $value['custno'];
                }

                if($value['name'] !=""){
                    $values->Organization_Name = $value['name'];
                }

                if($value['desp'] !=""){
                    $values->Subject = $value['desp'];
                }

                $new=Product::where('new',$request->start)->first();
                $reNew=Product::where('renew',$request->start)->first();
                //echo '<pre>';print_r($product);
                //echo $value['itemno']; echo '<br>';

                /********************************************************/
                if($new){
                     //echo 1;
                    if($value['date'] !=""){
                        $values->Start_Date = date('Y-m-d',strtotime($value['date']));
                    }
                    if($value['date'] !=""){
                        $values->invoice_date = date('Y-m-d',strtotime($value['date']));
                    }
                    if($value['date'] !=""){
                        $values->Due_date = date('Y-m-d', strtotime('+ 1 year', strtotime($value['date'])));
                    // $values->search_due_date = date('Y-m-d', strtotime('+ 1 year', strtotime($value['date'])));
                    }

                }

                if($reNew){
                    $date=CustomerInfo::where('customer_id',$value['custno'])->where('exp_date_checkbox',1)->get();
                    //echo '>>'.$value['custno'];echo '<br>';
                    $Ids=[];
                    foreach ($date as $key => $value1) {
                       /// echo '<pre>';print_r($value);
                        $Ids[]=$value1->setting_id;
                    }
                    //die;
                    if($value['date'] !=""){
                        $values->invoice_date = date('Y-m-d',strtotime($value['date']));
                    }
                   // echo '<pre>';print_r($date[0]->exp_date);die;
                    $values->product = implode(',',$Ids);
                    

                    if($value['date'] !=""){
                    $values->Start_Date = date('Y-m-d', strtotime('+ 1 day', strtotime($date[0]->exp_date)));
                    }
                    
                    if($value['date'] !=""){
                        $values->Due_date = date('Y-m-d', strtotime('+ 1 year', strtotime($date[0]->exp_date)));
                        /*$values->search_due_date = date('Y-m-d', strtotime('+ 1 year', strtotime(@$date[0])));*/
                    }
                }





                /********************************************************/





             //    $date=CustomerInfo::where('customer_id',$value['custno'])->where('exp_date_checkbox',1)->get()->pluck('exp_date')->toArray();
             //    if(empty($date)){
               	 


             //    if($value['date'] !=""){
             //        $values->Start_Date = date('Y-m-d',strtotime($value['date']));
             //    }
             //    if($value['date'] !=""){
             //        $values->invoice_date = date('Y-m-d',strtotime($value['date']));
             //    }
             //    if($value['date'] !=""){
             //        $values->Due_date = date('Y-m-d', strtotime('+ 1 year', strtotime($value['date'])));
             //        // $values->search_due_date = date('Y-m-d', strtotime('+ 1 year', strtotime($value['date'])));
             //    }

            	// }else{
            	// 	$date=CustomerInfo::where('customer_id',$value['custno'])->where('exp_date_checkbox',1)->get()->pluck('exp_date')->toArray();
            	// 	//echo '>>'.$value['custno'];echo '<br>';
            	// 	if($value['date'] !=""){
	            //         $values->invoice_date = date('Y-m-d',strtotime($value['date']));
	            //     }

            	// 	if($value['date'] !=""){
             //        $values->Start_Date = date('Y-m-d', strtotime('+ 1 day', strtotime(@$date[0])));
	            //     }
	                
	            //     if($value['date'] !=""){
	            //         $values->Due_date = date('Y-m-d', strtotime('+ 1 year', strtotime(@$date[0])));
             //            /*$values->search_due_date = date('Y-m-d', strtotime('+ 1 year', strtotime(@$date[0])));*/
	            //     }
            	// }


                if($value['refno'] !=""){
                    $values->Contract_Number = $value['refno'];
                }
                if($value['itemno'] !=""){
                    $values->Support_Type = $value['itemno'];
                }
                if($value['amt'] !=""){
                    $values->Price_RM = $value['amt'];
                }




                if($value['refno'] !=""){

                    $actTime= $value['created_on']->format(\DateTime::ISO8601);
                    $values->Created_Time = date('Y-m-d',strtotime($actTime));
                }
                $values->save();

                $action=1;
                } 

                //echo $action;die;
                if($request->start =="" && $request->to ==""){
                    $values = new Ictran;
                if($value['custno'] !=""){
                    $values->CUSTNO = $value['custno'];
                }

                if($value['name'] !=""){
                    $values->Organization_Name = $value['name'];
                }

                if($value['desp'] !=""){
                    $values->Subject = $value['desp'];
                }

             //    $date=CustomerInfo::where('customer_id',$value['custno'])->where('exp_date_checkbox',1)->get()->pluck('exp_date')->toArray();

             //    if(empty($date)){
               	 


             //    if($value['date'] !=""){
             //        $values->Start_Date = date('Y-m-d',strtotime($value['date']));
             //    }
             //    if($value['date'] !=""){
             //        $values->invoice_date = date('Y-m-d',strtotime($value['date']));
             //    }
             //    if($value['date'] !=""){
             //        $values->Due_date = date('Y-m-d', strtotime('+ 1 year', strtotime($value['date'])));
             //        /*$values->search_due_date = date('Y-m-d', strtotime('+ 1 year', strtotime($value['date'])));*/
             //    }

            	// }else{
            	// 	$date=CustomerInfo::where('customer_id',$value['custno'])->where('exp_date_checkbox',1)->get()->pluck('exp_date')->toArray();
            	// 	//dd($date);
            	// 	if($value['date'] !=""){
	            //         $values->invoice_date = date('Y-m-d',strtotime($value['date']));
	            //     }

            	// 	if($value['date'] !=""){
             //        $values->Start_Date = date('Y-m-d', strtotime('+ 1 day', strtotime(@$date[0])));
	            //     }
	                
	            //     if($value['date'] !=""){
	            //         $values->Due_date = date('Y-m-d', strtotime('+ 1 year', strtotime(@$date[0])));
             //            /*$values->search_due_date = date('Y-m-d', strtotime('+ 1 year', strtotime(@$date[0])));*/
	            //     }
            	// }


                $new=Product::where('new',$value['itemno'])->first();
                $reNew=Product::where('renew',$value['itemno'])->first();
                //echo '<pre>';print_r($product);
                //echo $value['itemno']; echo '<br>';

                /********************************************************/
                if($new){
                     //echo 1;
                    if($value['date'] !=""){
                        $values->Start_Date = date('Y-m-d',strtotime($value['date']));
                    }
                    if($value['date'] !=""){
                        $values->invoice_date = date('Y-m-d',strtotime($value['date']));
                    }
                    if($value['date'] !=""){
                        $values->Due_date = date('Y-m-d', strtotime('+ 1 year', strtotime($value['date'])));
                    // $values->search_due_date = date('Y-m-d', strtotime('+ 1 year', strtotime($value['date'])));
                    }

                }

                if($reNew){
                    $date=CustomerInfo::where('customer_id',$value['custno'])->where('exp_date_checkbox',1)->get();
                    //echo '>>'.$value['custno'];echo '<br>';
                    $Ids=[];
                    foreach ($date as $key => $value1) {
                       /// echo '<pre>';print_r($value);
                        $Ids[]=$value1->setting_id;
                    }
                    //die;
                    if($value['date'] !=""){
                        $values->invoice_date = date('Y-m-d',strtotime($value['date']));
                    }
                   // echo '<pre>';print_r($date[0]->exp_date);die;
                    $values->product = implode(',',$Ids);
                    

                    if($value['date'] !=""){
                    $values->Start_Date = date('Y-m-d', strtotime('+ 1 day', strtotime($date[0]->exp_date)));
                    }
                    
                    if($value['date'] !=""){
                        $values->Due_date = date('Y-m-d', strtotime('+ 1 year', strtotime($date[0]->exp_date)));
                        /*$values->search_due_date = date('Y-m-d', strtotime('+ 1 year', strtotime(@$date[0])));*/
                    }
                }





                /********************************************************/
                 
                if($value['refno'] !=""){
                    $values->Contract_Number = $value['refno'];
                }
                if($value['itemno'] !=""){
                    $values->Support_Type = $value['itemno'];
                }
                if($value['amt'] !=""){
                    $values->Price_RM = $value['amt'];
                }




                if($value['refno'] !=""){

                    $actTime= $value['created_on']->format(\DateTime::ISO8601);
                    $values->Created_Time = date('Y-m-d',strtotime($actTime));
                }
               $values->save();
                }

                

            }
        }
             
        }

        	//die; 
        \Session::flash('message', 'You have successfully uploaded !!!');
        return redirect('app/uploads');
    }

     public function search(Request $request){
        $ictran = Ictran::orderBy('CUSTNO','asc')
        ->orWhere('CUSTNO', 'like', '%' . $request->seacrh . '%')
        ->orWhere('Organization_Name', 'like', '%' . $request->seacrh . '%')
        ->orWhere('Support_Type', 'like', '%' . $request->seacrh . '%')

        ->Paginate(10);
        return view('admin.ictran.list')->with('ictran',$ictran);
     }

    
    public function ictranDelete($id){
        
         $delete= Ictran::where('id',$id)->delete();
        return redirect('app/service-contract')->withErrors(['Success', 'You have successfully deleted !!!']);
     
    }
    public function renew($id){
        
        $renew= Ictran::where('id',$id)->first();
        $renew->renew_status= 1;
        $renew->save();
        return redirect('app/service-contract')->withErrors(['Success', 'You have successfully renew !!!']);
     
    }
    public function agree($id){
        
        $renew= Ictran::where('id',$id)->first();
        $renew->renew_status= 2;
        $renew->save();
        return redirect('app/service-contract')->withErrors(['Success', 'You have successfully agree !!!']);
     
    }
    public function cancelled($id){
        
        $renew= Ictran::where('id',$id)->first();
        $renew->renew_status= 3;
        $renew->save();
        return redirect('app/service-contract')->withErrors(['Success', 'You have successfully cancelled !!!']);
     
    }
    public function ictranEdit($id){
        
        $this->data['edit']= Ictran::where('id',$id)->first();
        $this->data['products']= Product::get();
        //dd(explode(',',$this->data['edit']['product']));
        /*if($this->data['edit']['product']==""){
        $this->data['CustomerInfo']=CustomerInfo::where('customer_id',$this->data['edit']['CUSTNO'])->where('exp_date_checkbox',1)->get()->pluck('setting_id')->toArray();
        }else{
            $this->data['CustomerInfo']=Product::whereIn('id',explode(',',$this->data['edit']['product']))->get()->pluck('id')->toArray();
        }*/

         $this->data['CustomerInfo']=Product::whereIn('id',explode(',',$this->data['edit']['product']))->get()->pluck('id')->toArray();
        //dd($this->data['date']);
        return view('admin.ictran.edit',$this->data); 
         
     
    }
    public function ictranUpdate($id, Request $request){
        
        $update= Ictran::where('id',$id)->first();
        $update->CUSTNO=$request->CUSTNO;
        $update->Organization_Name=$request->Organization_Name;
        $update->Start_Date=date('Y-m-d',strtotime($request->Start_Date));
        $update->Due_date=date('Y-m-d',strtotime($request->Due_date));
        /*$update->search_due_date=date('Y-m-d',strtotime($request->Due_date));*/
        $update->invoice_date=date('Y-m-d',strtotime($request->invoice_date));
        if(!empty($request->product)){
        $update->product=implode(',',$request->product);
    	}else{
    		$update->product="";
    	}
        $update->Support_Type=$request->Support_Type;
        $update->Price_RM=$request->Price_RM;
        $update->save();


        // if(!empty($request->product)){
        //     foreach ($request->product as $key => $value) {
                
        //         $update->exp_date_checkbox=1;
        //         $update->save();
        //     }
        // }

        
        return redirect('app/service-contract')->withErrors(['Success', 'You have successfully updated !!!']); 
         
     
    }

    public function convertDate(){
        $keyword=':';
        $Ictran= Ictran::where('invoice_date', 'like', '%' . $keyword . '%')->get();
         
        foreach ($Ictran as $key => $value) {

            //echo date('d-m-Y', strtotime($value->invoice_date));echo '<pre>';
            $actDtae= explode('+',$value->invoice_date)[0];

            if($value->Price_RM != ""){
            $tert= number_format(str_replace(',','',$value->Price_RM), 2);
            $convert= str_replace('.00','',@$tert);
            }
            $update= Ictran::where('id',$value->id)->first();
            $update->invoice_date= date('Y-m-d', strtotime($actDtae));
            //$update->Start_Date= date('d-m-Y', strtotime('- 1 year', strtotime($value->invoice_date)));
            $update->Start_Date=date('Y-m-d', strtotime($actDtae));
            $update->Due_date=date('Y-m-d', strtotime('+ 1 year', strtotime($actDtae)));
            /*$update->search_due_date=date('Y-m-d', strtotime('+ 1 year', strtotime($actDtae)));*/
             
            $update->Price_RM=@$convert;
            $update->save();
            //echo '<pre>';print_r($value);
            # code...
        }


        $keyword=',';
        $Ictran= Ictran::where('invoice_date', 'like', '%' . $keyword . '%')->get();
         
        foreach ($Ictran as $key => $value) {
            $invDate= date('Y-m-d', strtotime(str_replace(',','',$value->invoice_date)));
            $dueDate= date('Y-m-d', strtotime(str_replace(',','',$value->Due_date)));
            //echo $value->invoice_date;
            if($value->Price_RM != ""){
            $tert= number_format(str_replace(',','',$value->Price_RM), 2);
            $convert= str_replace('.00','',@$tert);
            }
            $update= Ictran::where('id',$value->id)->first();
            $update->invoice_date= $invDate;
            $update->Start_Date= date('Y-m-d', strtotime('- 365 day', strtotime($dueDate)));
            //$update->Start_Date=date('d-m-Y', strtotime($actDtae));
            $update->Due_date=date('Y-m-d', strtotime($dueDate));
            /*$update->search_due_date=date('Y-m-d', strtotime($dueDate));*/
             
            $update->Price_RM=@$convert;
            $update->save();
            //echo '<pre>';print_r($value);
            # code...
        }
        return redirect('/dashboard');
    }

    public function serviceContract (){

        /*$update = Ictran::where('id',1)->first();
        $update->search_due_date ='2020-12-12';
        $update->save();*/


        $this->data['invoice']= Ictran::get();
        $this->data['prodcucts']= Product::get();
        $this->data['customers']= Ictran::groupby('Organization_Name')->get();
        $this->data['Support_Type']= Ictran::groupby('Support_Type')->get();
        return view('admin.ictran.list',$this->data);
     
    }

         function date_compare($a, $b)
        {
            $t1 = strtotime($a['Due_date']);
            $t2 = strtotime($b['Due_date']);
            return $t1 - $t2;
        }  

    public function serviceContract1(Request $request){

     ## Read value
     $draw = $request->get('draw');
     $start = $request->get("start");
     $rowperpage = $request->get("length"); // Rows display per page

     $columnIndex_arr = $request->get('order');
     $columnName_arr = $request->get('columns');
     $order_arr = $request->get('order');
     $search_arr = $request->get('search');

     $columnIndex = $columnIndex_arr[0]['column']; // Column index
     $columnName = $columnName_arr[$columnIndex]['data']; // Column name
     $columnSortOrder = $order_arr[0]['dir']; // asc or desc
     $searchValue = $search_arr['value']; // Search value

     // Total records
     /*$totalRecords = Ictran::select('count(*) as allcount')->count();
     $totalRecordswithFilter = Ictran::select('count(*) as allcount')
     ->where('Organization_Name', 'like', '%' .$searchValue . '%')
     ->orwhere('Contract_Number', 'like', '%' .$searchValue . '%')
     ->orwhere('Support_Type', 'like', '%' .$searchValue . '%')
     ->orwhere('Price_RM', 'like', '%' .$searchValue . '%')
     ->count();*/

     // Fetch records
     /*$records = Ictran::orderBy($columnName,$columnSortOrder)
       ->where('ictran.Organization_Name', 'like', '%' .$searchValue . '%')
       ->orwhere('ictran.Contract_Number', 'like', '%' .$searchValue . '%')
       ->orwhere('ictran.Support_Type', 'like', '%' .$searchValue . '%')
       ->orwhere('ictran.Price_RM', 'like', '%' .$searchValue . '%')
       ->select('ictran.*')
       ->skip($start)
       ->take($rowperpage)
       ->get()->toArray();*/

    $records = Ictran::where(function ($query) use ($searchValue) {
        $query->where('ictran.Organization_Name', 'like', '%' .$searchValue . '%')
       ->orwhere('ictran.Contract_Number', 'like', '%' .$searchValue . '%')
       ->orwhere('ictran.Support_Type', 'like', '%' .$searchValue . '%')
       ->orwhere('ictran.Price_RM', 'like', '%' .$searchValue . '%');
        });

    if($_GET['startDate'] != "" && $_GET['endDate']){

        $startDate= date('Y-m-d',strtotime($_GET['startDate']));
        $endDate= date('Y-m-d',strtotime($_GET['endDate']));
        $records= $records->whereBetween('ictran.Due_date', [$startDate, $endDate]);
       }
    //user for status not invoice name
    if($_GET['invoice'] != ""){

            $records= $records->where('ictran.renew_status',$_GET['invoice']);
        }
    if($_GET['customer'] != ""){

            $records= $records->where('ictran.product', 'like', '%' .$_GET['customer'] . '%');
        }
    if($_GET['type'] != ""){

            $records= $records->where('ictran.Support_Type',$_GET['type']);
        } 

    if($_GET['value'] != ""){
            if($_GET['value']==1000){
                $records->where('ictran.Price_RM','>=', 1)
                    ->where('ictran.Price_RM','<=', (int)$_GET['value']);
            }else{
               $records->where('ictran.Price_RM','>', 1000);
                   // ->where('ictran.Price_RM','<=', (int)$_GET['value']);
            }
        }    
       
    $records= $records->select('ictran.*');
        
   
    $recordr= $records->count();
    $records=   $records->skip($start)
   ->take($rowperpage)
    ->orderBy($columnName,$columnSortOrder)
    ->get()->toArray();

    $totalRecordswithFilter =$recordr;
    $totalRecords =$totalRecordswithFilter;

     

  
        usort($records, array($this,'date_compare'));

        // echo '<pre>';print_r($records);

        // die;
     $data_arr = array();
     foreach($records as $record){
        
        // $username = $record->username;
        // $name = $record->name;
        // $email = $record->email;

        /*if($record['product']==""){
        $product=CustomerInfo::where('customer_id',$record['CUSTNO'])->where('exp_date_checkbox',1)->get()->pluck('info_type')->toArray();
        }else{
        $product=Product::whereIn('id',explode(',',$record['product']))->get()->pluck('title')->toArray();
        }*/

        $product=Product::whereIn('id',explode(',',$record['product']))->get()->pluck('title')->toArray();

        $dueDate= strtotime($record['Due_date']);
        $toDayDate= strtotime(date('d-m-Y'));

        $dueDateColor=0;
        if($toDayDate > $dueDate){
        $dueDateColor=1;
        }




        $data_arr[] = array(
          "id" => $record['id'],
          "Contract_Number" => $record['Contract_Number'],
          "Organization_Name" => $record['Organization_Name'],
          "Support_Type" => $record['Support_Type'],
          "product" => implode(',',$product),
          "Price_RM" => $record['Price_RM'],
          "button" => 'efsd',
          "dueDateColor" => $dueDateColor,
          "renew_status" => $record['renew_status'],
          "due_date" => date('d-m-Y',strtotime($record['Due_date']))
        );
     }

     $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordswithFilter,
        "aaData" => $data_arr
     );

     echo json_encode($response);
     exit;
   }

   // For assign ticket
   public function ictranTicket($id){
        $this->data['adminUser'] =User::with('getUserCount')->get();
        //echo '<pre>';print_r($this->data['adminUser']);die;
        $this->data['id'] =$id;
        $getInfo1= Ictran::where('id',$id)->first();
        $this->data['custInfo'] =Cust::where('Organization_Number',$getInfo1->CUSTNO)->first();
        return view('admin.ictran.ticket',$this->data);
   }
   public function ticketStore($id, Request $request){
        $ticket= new Ticket();
        $ticket->ictran_id=$id;
        $ticket->status=0;
        $ticket->save();

        $assignTicket= new AssignTicket();
        $assignTicket->ticket_id= $ticket->id;
        $assignTicket->user_id= $request->user_id;
        $assignTicket->description= $request->description;
        $assignTicket->status= 0;
        $assignTicket->phone= $request->phone;
        $assignTicket->contact_person= $request->contact_person;
        $assignTicket->assigned_by= \Auth::user()->id;
        $assignTicket->updated_by= \Auth::user()->id;
        $assignTicket->save();

        return redirect('app/service-contract')->withErrors(['Success', 'You have successfully assign ticket !!!']); 
   }


   public function ticket (){

        $data= Ticket::get()->pluck('ictran_id');
        $userIdArray= AssignTicket::get()->pluck('user_id');
        $this->data['users']= User::whereIn('id',$userIdArray)->get()->toArray();
        $this->data['customers']= Ictran::whereIn('id',$data)->get()->toArray();

         
        return view('admin.ticket.list',$this->data);
     
    }

    public function ticket2(Request $request){

     ## Read value
     $draw = $request->get('draw');
     $start = $request->get("start");
     $rowperpage = $request->get("length"); // Rows display per page

     $columnIndex_arr = $request->get('order');
     $columnName_arr = $request->get('columns');
     $order_arr = $request->get('order');
     $search_arr = $request->get('search');

     $columnIndex = $columnIndex_arr[0]['column']; // Column index
     $columnName = $columnName_arr[$columnIndex]['data']; // Column name
     $columnSortOrder = $order_arr[0]['dir']; // asc or desc
     $searchValue = $search_arr['value']; // Search value
     \Session::put('key',$searchValue);

      
     // for user only filter

     $status=0;
     if($_GET['status'] != ""){
        $status=$_GET['status'];
     }
     
     // Fetch records
    $records =\DB::table('ticket')
        ->join('ticket_assign', 'ticket_assign.ticket_id', '=', 'ticket.id')
        ->join('ictran', 'ictran.id', '=', 'ticket.ictran_id')
        ->join('users', 'users.id', '=', 'ticket_assign.user_id')
        ->leftjoin('feedback', 'feedback.ticket_id', '=', 'ticket.id')
                    
        
       ->select('ticket.*','ticket_assign.*','ictran.Organization_Name as oname','users.name as user','ticket.created_at as cdate','ticket.status as tstatus','feedback.*')
        ->where(function ($query) use ($searchValue) {
        $query->orwhere('ticket_assign.phone', 'like', '%' .$searchValue . '%')
        ->orwhere('ticket.ictran_id', 'like', '%' .$searchValue . '%')
        ->orwhere('ticket_assign.contact_person', 'like', '%' .$searchValue . '%')
        ->orwhere('ticket.created_at', 'like', '%' .$searchValue . '%')
        ->orwhere('users.name', 'like', '%' .$searchValue . '%')
        ->orwhere('ictran.Organization_Name', 'like', '%' .$searchValue . '%');
        //->orWhere('ticket.status', 'like', '%' .$searchValue . '%');
        });
        
       if($_GET['startDate'] != "" && $_GET['endDate']){

        $startDate= date('Y-m-d',strtotime($_GET['startDate']));
        $endDate= date('Y-m-d',strtotime($_GET['endDate']));
        $records= $records->whereBetween('ticket.created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
       }
       if($_GET['customer'] != ""){

            $records= $records->where('ictran.id',$_GET['customer']);
        }
        if($_GET['user'] != ""){

            $records= $records->where('users.id',$_GET['user']);
        }
        if($_GET['rating'] != ""){

            $records= $records->where('feedback.rate',$_GET['rating']);
        }

       $records = $records
       ->Where('ticket.status', '=',$status)
       ->skip($start)
       ->take($rowperpage)
       ->get();
       $recordsr= $records->count();

    $totalRecordswithFilter =$recordsr;
    $totalRecords =$totalRecordswithFilter;
    //echo count($records);die;
     $data_arr = array();
    //echo '<pre>';print_r($records);die;
     foreach($records as $record){
        
       //echo '<pre>';print_r(date('H:i:s'));
        $assignBy= User::where('id',$record->assigned_by)->first();
       //$time= $this->getDateAndTime(date('d-m-Y H:i:s',strtotime($record->created_at)));
        $previousDate = $record->cdate;
        $startdate = new \DateTime($previousDate);
        $endDate   = new \DateTime('now');
        $interval  = $endDate->diff($startdate);
        $time= $interval->format('%H:%i:%s');
      // echo $time;
        //$time= '2:10:0';
        if($time < '00:30:0'){
        $timeStatus=1;

        }elseif($time > '00:30:00' && $time <= '02:00:0 0'){
         
        $timeStatus=2;
        }else{

        $timeStatus=3;
        }

      
        $data_arr[] = array(
          "id" => $record->id,
           
          "customer" => @$record->oname,
          "user" => $record->user,
          "phone" => @$record->phone,
          "contact" => @$record->contact_person,
          "description" => @$record->description,
          "assign" => $assignBy->name,
          "time" => $time,
          "ticketstatus" => $record->tstatus,
          "timeStatus" => $timeStatus,
          "created_at" => date('d-m-Y',strtotime($record->cdate))
        );
     }

     $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordswithFilter,
        "aaData" => $data_arr
     );

     echo json_encode($response);
     exit;
   }


   public function getDateAndTime($date1){
    // Declare and define two dates 
        $date1 = strtotime($date1);  
        $date2 = strtotime(date('d-m-Y H:i:s'));  
          
        // Formulate the Difference between two dates 
        $diff = abs($date2 - $date1);  
          
          
        // To get the year divide the resultant date into 
        // total seconds in a year (365*60*60*24) 
        $years = floor($diff / (365*60*60*24));  
          
          
        // To get the month, subtract it with years and 
        // divide the resultant date into 
        // total seconds in a month (30*60*60*24) 
        $months = floor(($diff - $years * 365*60*60*24) 
                                       / (30*60*60*24));  
          
          
        // To get the day, subtract it with years and  
        // months and divide the resultant date into 
        // total seconds in a days (60*60*24) 
        $days = floor(($diff - $years * 365*60*60*24 -  
                     $months*30*60*60*24)/ (60*60*24)); 
          
          
        // To get the hour, subtract it with years,  
        // months & seconds and divide the resultant 
        // date into total seconds in a hours (60*60) 
        $hours = floor(($diff - $years * 365*60*60*24  
               - $months*30*60*60*24 - $days*60*60*24) 
                                           / (60*60));  
          
          
        // To get the minutes, subtract it with years, 
        // months, seconds and hours and divide the  
        // resultant date into total seconds i.e. 60 
        $minutes = floor(($diff - $years * 365*60*60*24  
                 - $months*30*60*60*24 - $days*60*60*24  
                                  - $hours*60*60)/ 60);  
          
          
        // To get the minutes, subtract it with years, 
        // months, seconds, hours and minutes  
        $seconds = floor(($diff - $years * 365*60*60*24  
                 - $months*30*60*60*24 - $days*60*60*24 
                        - $hours*60*60 - $minutes*60));  
          
        // Print the result 
        return $hours.':'.$minutes.':'.$seconds; 
   }


   // for edit ticket
   public function ticketEdit($id){
    $this->data['edit']= AssignTicket::where('ticket_id',$id)->first();
    return view('admin.ticket.ticket',$this->data);
   }
   // for ticketReassign
   public function ticketReassign($id){
    $this->data['ticket']= Ticket::where('id',$id)->first();
    $this->data['assign']= AssignTicket::where('ticket_id',$id)->first();
    $this->data['adminUser'] =User::with('getUserCount')->get();
    return view('admin.ticket.ticket-assign',$this->data);
   }
   // For update ticket
   public function ticketUpdate($id, Request $request){
    $update= AssignTicket::where('id',$id)->first();
    $update->description=$request->description;
    $update->phone=$request->phone;
    $update->contact_person=$request->contact_person;
    $update->save();

    return redirect('app/ticket')->withErrors(['Success', 'You have successfully updated !!!']);
   }
   //  Ticket resaasign
   public function assignUpdate($id, Request $request){

    /*$ticketCheck= AssignTicket::where('id',$id)->where('status',1)->first();
    if($ticketCheck){
        return redirect('app/ticket')->withErrors(['Success', 'Ticket Already Assign !!!']);
    }else{*/
    $update= AssignTicket::where('id',$id)->first();
    $update->user_id=$request->user_id;
    $update->description=$request->description;
    $update->phone=$request->phone;
    $update->status=1;
    $update->contact_person=$request->contact_person;
    $update->save();

    return redirect('app/ticket')->withErrors(['Success', 'You have successfully updated !!!']);
    // }
   }

   // for ticket delete
   
   public function ticketDelete($id){
    $ticketDelete= Ticket::where('id',$id)->delete();
    if($ticketDelete){
        AssignTicket::where('ticket_id',$id)->delete();
        return redirect('app/ticket')->withErrors(['Success', 'You have successfully deleted !!!']);
    }
    }

// For close ticket

 public function ticketClose($id){
     $this->data['webInfo'] = Info::first();
     $ictranId = Ticket::where('id',$id)->first()->ictran_id;
     $this->data['ictran'] = Ictran::where('id',$ictranId)->first();
     $this->data['cust'] = Cust::where('Organization_Number',$this->data['ictran']->CUSTNO)->first();
     $this->data['ticket_number'] = $id;
     $email = $this->data['cust']->Primary_Email;
     // echo '<pre>';print_r($this->data['cust']->Primary_Email);die;
      //echo '<pre>';print_r($this->data['cust']->Attention);die;

    \Mail::send('emails.close-ticket', $this->data, function($message) use ($email){
    $message->to($email)->subject
    ('Ticket Feedback');
    $message->from('sales@pcmart.com.my','Ticket Feedback');
    });

    $tk = Ticket::where('id',$id)->first();
    $tk->status= 1;
    $tk->save();
    $tka= AssignTicket::where('id',$id)->first();
    $tka->status= 2;
    $tka->save();
    
    return redirect('app/ticket')->withErrors(['Success', 'You have successfully closed ticket !!!']);
    }

    public function feedback($id){
      $this->data['id']=$id;
    
     return view('admin.ticket.feedback',$this->data);
    }

    public function feedbackSubmit($id,Request $request){
      $data= base64_decode($id);
       
      $feedback= new Feedback();
      $feedback->ticket_id = explode('_', $data)[1];       
      $feedback->CUST_NO= explode('_', $data)[0];
      $feedback->rate= $request->rating;
      $feedback->save();

     return redirect('/thankyou');
    }

    // email marketing
    public function emailMarketing(){

     
    return view('admin.market.list');
    }
    public function emailMarket(Request $request){


    $info= Info::first();
    $id=1;
    \DB::update('update filter_Setting set month = '.$_GET['month'].', year='.$_GET['year'].' where id = '.$id);
    
    ## Read value
     $draw = $request->get('draw');
     $start = $request->get("start");
     $rowperpage = $request->get("length"); // Rows display per page

     $columnIndex_arr = $request->get('order');
     $columnName_arr = $request->get('columns');
     $order_arr = $request->get('order');
     $search_arr = $request->get('search');

     $columnIndex = $columnIndex_arr[0]['column']; // Column index
     $columnName = $columnName_arr[$columnIndex]['data']; // Column name
     $columnSortOrder = $order_arr[0]['dir']; // asc or desc
     $searchValue = $search_arr['value']; // Search value

     // Total records
     /*$totalRecords = Ictran::select('count(*) as allcount')->count();
     $totalRecordswithFilter = Ictran::select('count(*) as allcount')
     ->where('Organization_Name', 'like', '%' .$searchValue . '%')
     ->orwhere('Contract_Number', 'like', '%' .$searchValue . '%')
     ->orwhere('Support_Type', 'like', '%' .$searchValue . '%')
     ->orwhere('Price_RM', 'like', '%' .$searchValue . '%')
     ->count();*/

     // Fetch records
     /*$records = Ictran::orderBy($columnName,$columnSortOrder)
       ->where('ictran.Organization_Name', 'like', '%' .$searchValue . '%')
       ->orwhere('ictran.Contract_Number', 'like', '%' .$searchValue . '%')
       ->orwhere('ictran.Support_Type', 'like', '%' .$searchValue . '%')
       ->orwhere('ictran.Price_RM', 'like', '%' .$searchValue . '%')
       ->select('ictran.*')
       ->skip($start)
       ->take($rowperpage)
       ->get()->toArray();*/

    $records = Ictran::where(function ($query) use ($searchValue) {
        $query->where('ictran.Organization_Name', 'like', '%' .$searchValue . '%')
       ->orwhere('ictran.Contract_Number', 'like', '%' .$searchValue . '%')
       ->orwhere('ictran.Support_Type', 'like', '%' .$searchValue . '%')
       ->orwhere('ictran.Price_RM', 'like', '%' .$searchValue . '%');
        });

    if($_GET['month'] != "" && $_GET['year']){

        $records= $records->whereYear('ictran.Due_date', $_GET['year'])
        ->whereMonth('ictran.Due_date', $_GET['month']);
       }
    //user for status not invoice name
    /*if($_GET['invoice'] != ""){

            $records= $records->where('ictran.renew_status',$_GET['invoice']);
        }
    if($_GET['customer'] != ""){

            $records= $records->where('ictran.Organization_Name',$_GET['customer']);
        }
    if($_GET['type'] != ""){

            $records= $records->where('ictran.Support_Type',$_GET['type']);
        } 

    if($_GET['value'] != ""){
            if($_GET['value']==1000){
                $records->where('ictran.Price_RM','>=', 1)
                    ->where('ictran.Price_RM','<=', (int)$_GET['value']);
            }else{
               $records->where('ictran.Price_RM','>', 1000);
                   // ->where('ictran.Price_RM','<=', (int)$_GET['value']);
            }
        }*/    
       
    $records= $records->select('ictran.*');
        
   
    $recordr= $records->count();
    $records=   $records->skip($start)
   ->take($rowperpage)
    ->orderBy($columnName,$columnSortOrder)
    ->get()->toArray();

    $totalRecordswithFilter =$recordr;
    $totalRecords =$totalRecordswithFilter;

     

  
        usort($records, array($this,'date_compare'));

        // echo '<pre>';print_r($records);

        // die;
     $data_arr = array();
     foreach($records as $record){



        /******************************************************/
        $Product= Product::whereIn('id',explode(',',$record['product']))->get();
        $custInfo= CustomerInfo::where('customer_id',$record['CUSTNO'])->whereIn('setting_id',explode(',',$record['product']))->get();

        $sum=0;
        foreach($Product as $key=>$prod){
          
        $getMonth= date('m',strtotime($custInfo[$key]['exp_date']));
        $getYear= date('Y',strtotime($custInfo[$key]['exp_date']));
         
        $price=$prod->first_user;
        $add_user=$prod->add_user;
        $tax=$prod->tax;

        $custUser= $custInfo[$key]->user;
        $actUsr=$custUser-1;
        $actPrice=0;
        if($actUsr > 0){
            $actPrice=$actUsr*$add_user;
        } 

        $realPrice= $actPrice+$price;

        if($tax==1){
            $tax=($realPrice*$info->tax)/100;
            $realPrice=$tax+$realPrice;
        }

        $sum+=$realPrice;

        }




        /**************************************************/



        $custInfo= Cust::where('Organization_Number',$record['CUSTNO'])->first();
        $email="";
        if($custInfo){
            $email= $custInfo->Primary_Email;
        }
        // $username = $record->username;
        // $name = $record->name;
        // $email = $record->email;

        $tax= ($record['Price_RM']*$info->tax)/100;

        $valueAfterTax= $tax+$record['Price_RM'];

        if($record['product']==""){
        $product=CustomerInfo::where('customer_id',$record['CUSTNO'])->where('exp_date_checkbox',1)->get()->pluck('info_type')->toArray();
        }else{
        $product=Product::whereIn('id',explode(',',$record['product']))->get()->pluck('title')->toArray();
        }

        $dueDate= strtotime($record['Due_date']);
        $toDayDate= strtotime(date('d-m-Y'));

        $dueDateColor=0;
        if($toDayDate > $dueDate){
        $dueDateColor=1;
        }




        $data_arr[] = array(
          "id" => $record['id'],
          "Subject" => $record['Subject'],
          "Organization_Name" => $record['Organization_Name'],
          "Support_Type" => $record['Support_Type'],
          "product" => implode(',',$product),
          "Price_RM" => $record['Price_RM'],
          "valueAfterTax" => $sum,
          "email" => $email,
          "button" => 'efsd',
          "dueDateColor" => $dueDateColor,
          "renew_status" => $record['renew_status'],
          "due_date" => date('d-m-Y',strtotime($record['Due_date']))
        );
     }

     $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordswithFilter,
        "aaData" => $data_arr
     );

     echo json_encode($response);
     exit; 
    
    }

    // update filter settings
   // updateSetting

public function sendEmail(Request $request){

        $all= $request->all();


        $this->data['info']= Info::first();
        $this->data['month']= $request->month;
        $this->data['year']= $request->year;
        $records = Ictran::whereYear('ictran.Due_date', $request->year)
        ->whereMonth('ictran.Due_date', $request->month);
        $records=$records->get()->toArray();

        $productIds="";
        foreach ($records as $key => $value) {
            $this->data['ictran']=$value;

            $cust= Cust::where('Organization_Number',$value['CUSTNO'])->first();

            if($request->testmode==1){
                $email= $request->email;
            }else{
            $email= $cust['Primary_Email'];
            }

            $Product= Product::whereIn('id',explode(',',$value['product']))->get();
            $custInfo= CustomerInfo::where('customer_id',$value['CUSTNO'])->whereIn('setting_id',explode(',',$value['product']))->get();
            foreach($Product as $key=>$prod){
                             
            $getMonth= date('m',strtotime($custInfo[$key]['exp_date']));
            $getYear= date('Y',strtotime($custInfo[$key]['exp_date']));


             if($request->month==$getMonth && $request->year==$getYear){
           // return view('emails.marketing',$this->data);
            \Mail::send('emails.marketing', $this->data, function($message) use ($email){
            $message->to($email)->subject
            ('UBS Software Support - Quotation');
            $message->from('sales@pcmart.com.my','UBS Software Support - Quotation');
            });

            }
            }

            //return view('emails.marketing',$this->data);
        }
        // $product= Product::whereIn('id',explode(',',$value['product']))->get()->toArray();
        // echo '<pre>';print_r($product);
       

 

       return redirect('app/email-marketing')->withErrors(['Success', 'You have successfully sent email !!!']); 
    }


}