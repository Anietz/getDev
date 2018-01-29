<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Session;
use DB;
use Response;
use Auth;
use Input;
use Redirect;
use Mail;
use Validator;
use App\User;
use App\Visitor;
use App\Visit;
use View;
use Hash;
use Pusher;

class HomeCtrl extends Controller
{
   /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    //$this->middleware('confirm_phone',['except' => ["resend_code","activate_phone","check_status"] ] );      
    $userTimezone = "Africa/Lagos";
    date_default_timezone_set($userTimezone);
    \Config::set('app.timezone', $userTimezone);
    }


    public function demo(Request $request){

      return Response::json(array("name"=>$request->all(),"token"=>rand(9,999999)));
    }

     public function index()
    {   
      /*USers that gave help, but not yet matched completely*/	
        Session::put("visit_id","");

       DB::beginTransaction();
          try {                        	   
               $data["staff_count"] = User::count();
               $data["pending"] = Visit::with("visitor","recep","doctor")->where("status",0)->take(5)->get();                
               $data["past"] = Visit::with("visitor","recep","doctor")->where("status","!=",0)->take(5)->get();                                
               $data["pending_count"] = Visit::with("visitor","recep","doctor")->where("status",0)->count();                
               $data["past_count"] = Visit::with("visitor","recep","doctor")->where("status","!=",0)->count();          
               $data["visitor_count"] = Visitor::count();

              DB::commit();
              $success = true;
          } catch (\Exception $e) {
              $success = false;
              DB::rollback();
          }

          if ($success) {
              return view('main',$data);
              // the transaction worked ...
          }
    }

     public function pending()
    {   
      /*USers that gave help, but not yet matched completely*/
        /*Clear current visit*/
        Session::put("visit_id","");

       DB::beginTransaction();
          try {              
               $data["staff_count"] = User::count();
               $data["pending_count"] = Visit::with("visitor","recep","doctor")->where("status",0)->count();                
               $data["past_count"] = Visit::with("visitor","recep","doctor")->where("status","!=",0)->count();          
               $data["visitor_count"] = Visitor::count();

               $data["pending"] = Visit::with("visitor","recep","doctor")->where("status",0)->orderBy("id","DESC")->paginate(6);                                                             
              DB::commit();
              $success = true;
          } catch (\Exception $e) {
              $success = false;
              DB::rollback();
          }

          if ($success) {
              return view('pending',$data);
              // the transaction worked ...
          }
    }

      public function past()
    {   
      /*Clear current visit*/
        Session::put("visit_id","");

       DB::beginTransaction();
          try {              
               $data["staff_count"] = User::count();
               $data["pending_count"] = Visit::with("visitor","recep","doctor")->where("status",0)->count();                
               $data["past_count"] = Visit::with("visitor","recep","doctor")->where("status","!=",0)->count();          
               $data["visitor_count"] = Visitor::count();

               $data["pending"] = Visitor::orderBy("id","DESC")->paginate(6);

              /* $data["pending"] = DB::table('visitors as a')
				          ->select(DB::raw('a.*'))
				          ->Join('visits as b','b.visitor_id', '=', 'a.id')				         
				          ->where("b.status","!=",0)  
				          ->orderBy("b.id","DESC")   
				          ->paginate(6);*/

              DB::commit();
              $success = true;
          } catch (\Exception $e) {
              $success = false;
              DB::rollback();
          }

          if ($success) {
              return view('past',$data);
              // the transaction worked ...
          }
    }

    public function get_visitors(Request $request){

    	$validator = Validator::make($request->all(), [            
	    		  'search_data' => 'required',	        	
	        ]);

         if($validator->fails()){

    			return Response::json(["status"=>"3","msg"=>$validator->errors()->first()]);
    	  }

    	  $data = Visitor::
    	  where("first_name","like",  '%'.$request->search_data.'%')
    	  ->orWhere("last_name","like",'%'.$request->search_data.'%')
    	  ->orWhere("phone","like",'%'.$request->search_data.'%')    	  
    	  ->take(5)->get();    	

    	return  Response::json(["status"=>"1","msg"=>"Updated successfully","view"=>$this->search_view($data,$request->search_data)]);
    }

    private function search_view($data,$search_data){
    	$view = '';
    	if (count($data) > 0) {

    		foreach ($data as $k ) {
    			
    			$view .= '<div class="sl-item p-b-md">
                            <div class="sl-avatar avatar avatar-sm avatar-circle">
                              <img class="img-responsive" src="'.asset('theme/assets/images/'.$k->image).'" alt="avatar"/>
                            </div>
                            <div class="sl-content m-l-xl">
                              <h5 class="m-t-0"><a href="javascript:void(0)" data-phone="'.$k->phone.'" data-name="'.$k->first_name .' '.$k->last_name.'" data-id="'.$k->id.'" class="m-r-xs theme-color start_visit">'.$k->last_name.' '.$k->first_name.'</a>
                               - <small class="text-muted fz-sm">'.$k->phone.'</small></h5>
                              <p>'.$k->sex.'</p>
                            </div>
                          </div>';
    		}
    		
    	}else{

    		$view = ' <div class="text-center">                  
		                  <h1><i class="fa fa-paperclip"></i></h1>
		                  <p>No match "'.$search_data.'"</p>
		              </div>';
    	}
    		return $view;
    }


    public function get_doctors(Request $request){

    	$validator = Validator::make($request->all(), [            
	    		  'search_data' => 'required',	        	
	        ]);

         if($validator->fails()){

    			return Response::json(["status"=>"3","msg"=>$validator->errors()->first()]);
    	  }

    	  $data = User::
    	  where("name","like",  '%'.$request->search_data.'%')    	 
    	  ->orWhere("phone","like",'%'.$request->search_data.'%')    	  
    	  ->where("type",1)
    	  ->take(3)->get();    	

    	return  Response::json(["status"=>"1","msg"=>"Updated successfully","view"=>$this->search_view2($data,$request->search_data)]);
    }

     private function search_view2($data,$search_data){
    	$view = '';
    	if (count($data) > 0) {

    		foreach ($data as $k ) {
    			
    			$view .= '<div class="sl-item p-b-md">
                            <div class="sl-avatar avatar avatar-sm avatar-circle">
                              <img class="img-responsive" src="'.asset('theme/assets/images/'.$k->image).'" alt="avatar"/>
                            </div>
                            <div class="sl-content m-l-xl">
                              <h5 class="m-t-0"><a href="javascript:void(0)" data-phone="'.$k->phone.'" data-name="'.$k->name.'" 
                              data-id="'.$k->id.'" class="m-r-xs theme-color add_doctor">'.$k->name.'</a>
                               - <small class="text-muted fz-sm">'.$k->phone.'</small></h5>
                            </div>
                          </div>';
    		}
    		
    	}else{

    		$view = ' <div class="text-center">                  
		                  <h1><i class="fa fa-paperclip"></i></h1>
		                  <p>No match "'.$search_data.'"</p>
		              </div>';
    	}
    		return $view;
    }


    public function add_visit(Request $request){

    	########
    	   ######## check if the visitor is registered or new
    	#######
    	if ($request->has_account == "0"){

	    	$validator = Validator::make($request->all(), [            
	    		  'first_name' => 'required',

	        	  'last_name' => 'required',

	              'sex' => 'required',

	              'phone' => 'required|numeric|digits:11',

             	  'doctor_id' => 'required',             

             	  'reason_for_visit' => 'required|min:5',

             	  'time_of_arrival' => 'required',

	        ]);

	         if($validator->fails()){

        			return Response::json(["status"=>"3","msg"=>$validator->errors()->first()]);

        	  }    	

        	//$request->only('email', 'password')
    		$new_visitor = Visitor::create($request->all()); 

    		###### merge the visitor id to the request
    		$request->merge(["visitor_id" => $new_visitor->id]);
    		
    	}else{

	    		$validator = Validator::make($request->all(), [            
	    		 'visitor_id' => 'required',

	             'doctor_id' => 'required',             

	             'reason_for_visit' => 'required|min:5',

	             'time_of_arrival' => 'required',

	        	]);

    	}    	

        if($validator->fails()){
        	return Response::json(["status"=>"3","msg"=>$validator->errors()->first()]);
        }    	

        $request->merge(["receptionist_id" => Auth::user()->id]);

        $request->merge(["time_of_arrival" => Date("Y-m-d H:i:s",strtotime($request->time_of_arrival)) ]);

        
        Visit::create($request->all());   

          /*Notfiy Users using Pusher*/ 
       

      $pusher = new Pusher(env("PUSHER_KEY_NEW"),env("PUSHER_SECRET"), '396571', array( 'encrypted' => true, 'cluster' => 'eu' ) );                   
       $visitor_data = Visitor::find($request->visitor_id);
       $doctor_data = User::find($request->doctor_id);       

       $view = $this->pending_view();

       $pusher->trigger('app-channel', 'doctor-event', array("message"=> $visitor_data->last_name." ".$visitor_data->last_name." wants to meet with ".$doctor_data->name,"view"=>$view ));

       return  Response::json(["status"=>"1","msg"=>"Successfully added","view"=>$view]);

    }

    public function test_pusher(){
		    $options = array(
		    'cluster' => 'eu',
		    'encrypted' => true
		  );
		  $pusher = new Pusher(
		    '1de4adb8b3d38ec68d59',
		    '38489aadb905e4294426',
		    '396571',
		    $options
		  );

		  $data['message'] = 'hello world';
		  $res = $pusher->trigger('app-channel', 'doctor-event', $data);



		    //$pusher = new Pusher("1de4adb8b3d38ec68d59","38489aadb905e4294426", "396571", array( 'encrypted' => true ) );                        

		    //$pusher->trigger( 'app-channel', 'doctor-event', array("message"=> "Hello world testing"));

		  	var_dump($res);

    }

    private function pending_view(){
    	 	  
            $data["pending"] = Visit::with("visitor","recep","doctor")->where("status",0)->orderBy("id","DESC")->paginate(6);

            $data["show_delete"]   = 1;                         	                                              
            return  View::make("visitor_list",$data)->render();
    }

      public function update_visit(Request $request)
    {
       
	    $validator = Validator::make($request->all(), [            
	    		 'visit_id' => 'required',	             
	        	]);      

         if($validator->fails()){
        	return Response::json(["status"=>"3","msg"=>$validator->errors()->first()]);
        }   

        $data = Visit::find($request->visit_id);

        $data->status = 1;

          /*Notfiy Users using Pusher*/ 
      $pusher = new Pusher(env("PUSHER_KEY_NEW"),env("PUSHER_SECRET"), '396571', array( 'encrypted' => true, 'cluster' => 'eu' ) );             

       $visitor_data = Visit::with("visitor","doctor")->where("id",$request->visit_id)->first();      

       $pusher->trigger( 'app-channel', 'recep-event', array("message"=> $visitor_data->visitor->last_name." ".$visitor_data->visitor->last_name." Can come in to see ".$visitor_data->doctor->name." now"));


        $data->save();

        Session::put("reason",$data->reason_for_visit);

        Session::put("visit_id",$data->id);

        return  Response::json(["status"=>"1","msg"=>"Successfully updated"]);      

    }

       public function delete_visit(Request $request)
    {
       
	    $validator = Validator::make($request->all(), [            
	    		 'visit_id' => 'required',	             
	        	]);      

         if($validator->fails()){
        	return Response::json(["status"=>"3","msg"=>$validator->errors()->first()]);
        }   

       	Visit::find($request->visit_id)->delete();

        return  Response::json(["status"=>"1","msg"=>"Successfully deleted","view"=>$this->pending_view()]);      

    }

      public function add_comment(Request $request)
    {
       
	    $validator = Validator::make($request->all(), [            
	    		 'visit_id' => 'required',	  
	    		 'comment'  => 'required'         
	        	]);      

         if($validator->fails()){
        	return Response::json(["status"=>"3","msg"=>$validator->errors()->first()]);
        }   

        $data = Visit::find($request->visit_id);

        $data->status = 2;

        $data->comment = $request->comment;

        $data->save();

        return  Response::json(["status"=>"1","msg"=>"Successfully added the comment"]);      

    }

    public function get_past_visit(Request $request){

    	$validator = Validator::make($request->all(), [            
	    		 'visitor_id' => 'required',	             
	        	]);      

         if($validator->fails()){
        	return Response::json(["status"=>"3","msg"=>$validator->errors()->first()]);
        }   
    	
    	$data = Visit::with("visitor","recep","doctor")->where("status","!=", 0)->where("visitor_id",$request->visitor_id)
    	->orderBy("id","DESC")->take(5)->get();


    	if (count($data) > 0) {    		
			#######
				###### Adding roles right
			#######
				if(Auth::user()->type == 1) {
					$view = '<table class="table">
						<tbody><tr><th>Reason</th><th>Doctors Name</th><th>Doctors Comment</th><th>Date</th></tr>';

					}else{
					$view = '<table class="table">
						<tbody><tr><th>Reason</th><th>Doctors Name</th><th>Date</th></tr>';
				}				

    		foreach ($data as $k) {    			
    			$view .= '<tr><td>'.$k->reason_for_visit.'</td><td>'.$k->doctor->name.'</td>';

    			if(Auth::user()->type == 1) {
    				$view .=  '<th>'.$k->comment.'</th>';
    			}

    			$view .= '<td>'.$k->created_at.'</td></tr>';
    		}

    		$view .= '</tbody></table>';
    		
    	}else{

    		$view = ' <div class="col-md-12 text-center">                  
                  		<h1><i class="fa fa-paperclip"></i></h1>
                  		<p>No previous visit record</p>
          			</div>';

    	}

        return  Response::json(["status"=>"1","msg"=>"Successfully updated","view"=>$view]);      

    }

    public function visitor_profile($visitor_id){
    	  DB::beginTransaction();
          try {              
               $data["staff_count"] = User::count();
               $data["pending_count"] = Visit::with("visitor","recep","doctor")->where("status",0)->count();                
               $data["past_count"] = Visit::with("visitor","recep","doctor")->where("status","!=",0)->count();          
               $data["visitor_count"] = Visitor::count();

               $data["visitor"] = Visitor::find($visitor_id);
               $data["past"] = Visit::with("visitor","recep","doctor")->where("status","!=", 0)->where("visitor_id",$visitor_id)
    				->orderBy("id","DESC")->take(10)->get();                                                             

              DB::commit();
              $success = true;
          } catch (\Exception $e) {
              $success = false;
              DB::rollback();
          }

          if ($success) {
              return view('profile',$data);
              // the transaction worked ...
          }
    }

    public function visitor_profile_new($visitor_id,$name){
    	/*Clear current visit*/
        Session::put("visit_id","");

    	  DB::beginTransaction();
          try {              
               $data["staff_count"] = User::count();
               $data["pending_count"] = Visit::with("visitor","recep","doctor")->where("status",0)->count();                
               $data["past_count"] = Visit::with("visitor","recep","doctor")->where("status","!=",0)->count();          
               $data["visitor_count"] = Visitor::count();

               $data["visitor"] = Visitor::find($visitor_id);
               $data["past"] = Visit::with("visitor","recep","doctor")->where("status","!=", 0)->where("visitor_id",$visitor_id)
    				->orderBy("id","DESC")->take(10)->get();                                                             

              DB::commit();
              $success = true;
          } catch (\Exception $e) {
              $success = false;
              DB::rollback();
          }

          if ($success) {
              return view('profile',$data);
              // the transaction worked ...
          }
    }

    


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'video_title' => 'required',
            'video_url' => 'required',
        ]);

        Video::find($id)->update($request->all());

        return redirect()->route('video.index')

                        ->with('success','Video updated successfully');

    }

    public function store(Request $request)
    {

      $this->validate($request, [

            'video_title' => 'required',

            'video_url' => 'required',

        ]);


        Video::create($request->all());

        return redirect()->route('video.index')

                        ->with('success','Video created successfully');
    }
    

     public function destroy($id)
    {
        Video::find($id)->delete();

        return redirect()->route('video.index')

                        ->with('success','Video deleted successfully');
    }



     private function add_log($description,$client_id,$booking_setup_id=0,$type){
      $input["description"] = $description;
      $input["user_id"] = $client_id;
      $input["booking_setup_id"] = $booking_setup_id;
      $input["type"] = $type;
      Log::create($input);
    }

  function update_profile(Request $request){

    #### check if phone or email has changed and ensure it is unique
    $email_unique = "";
    $phone_unique = "";
      if(strtolower(Auth::user()->email) !== strtolower($request->email) ){
         $email_unique =  "|unique:users";
      }

      if (strtolower(Auth::user()->phone) != strtolower($request->phone) ){
          $phone_unique = "|unique:users";
      }


     $validator = Validator::make($request->all(), [            
            'name' => 'required|max:255',
            'email' => 'required|email|max:255'.$email_unique,           
            'nok_phone_user' => 'required|numeric',
            'nok_name_user' => 'required',
            'gender' => 'required',
            'phone' =>'required|numeric|digits:11'.$phone_unique
        ]);

        if($validator->fails()){
        return Response::json(["status"=>"3","msg"=>$validator->errors()->first()]);
        }

      $input = User::find(Auth::user()->id);
      Auth::user()->nok_phone = $input->nok_phone = $request->nok_phone_user;
      Auth::user()->nok_name = $input->nok_name = $request->nok_name_user;
      Auth::user()->name = $input->name = $request->name;

      if (Auth::user()->email != $request->email ){
          Auth::user()->email = $input->email = $request->email;
      }

       if (Auth::user()->phone != $request->phone ){
          Auth::user()->phone = $input->phone = $request->phone;      
      }

      Auth::user()->sex = $input->sex = $request->gender;      


      $input->save();

      return Response::json(["status"=>"1","msg"=>"Updated successfully"]);
      
    }


  function update_password(Request $request){
     $validator = Validator::make($request->all(), [            
            'old_password' => 'required',
            'new_password' => 'required|min:4',           
            'confirm_password' => 'required|min:4',           
        ]);

        if($validator->fails()){
        return Response::json(["status"=>"3","msg"=>$validator->errors()->first()]);
        }

        if ($request->confirm_password != $request->new_password){
          return Response::json(["status"=>"3","msg"=>"New password must match confirm password"]);          
        }


      $input = User::find(Auth::user()->id);

      if (!Hash::check($request->old_password, $input->password)) {
          // The password does not match...
          return Response::json(["status"=>"3","msg"=>"Password does not match"]);          
      }

      $input->password = Hash::make($request->new_password);

      $input->save();

      return Response::json(["status"=>"1","msg"=>"Updated successfully"]);
      
    }

    public function check_status(Request $request){
         $validator = Validator::make($request->all(), [            
            //'name' => 'required|max:255',
            'ref_code' => 'required|numeric',           
        ]);

        if($validator->fails()){
        return Response::json(["status"=>"3","msg"=>$validator->errors()->first()]);
        }



           $res =  DB::table('user_bookings as a')
          ->select(DB::raw('a.*,d.name as branch_from,c.name as branch_to,e.name as state_to, f.name as state_from'))
          ->leftJoin('booking_setups as b','b.id', '=', 'a.booking_setup_id')
          ->leftJoin('branches as c','c.id', '=', 'b.travelling_to_branch_id')
          ->leftJoin('branches as d','d.id', '=', 'b.travelling_from_branch_id')
          ->leftJoin('states as e','e.id', '=', 'c.state_id')
          ->leftJoin('states as f','f.id', '=', 'd.state_id')      
          ->where("a.unique_reference",$request->ref_code)  
          ->orderBy("a.id","DESC")   
          ->get();

        /*Valid*/
        return Response::json(["status"=>"1","msg"=>"Your booking status","view"=> View::make("status_view",["trips"=>$res] )->render()  ]);


    }
   

    public function pay_now(Request $request){         
         $validator = Validator::make($request->all(), [            
            //'name' => 'required|max:255',
            'token' => 'required',
            'amount' => 'required',
            'final_obj' => 'required',
           /* 'sex' => [
                    'required',
                    Rule::in(['male', 'female']),
                ]*/
        ]);

        if($validator->fails()){
        return Response::json(["status"=>"3","msg"=>$validator->errors()->first()]);
        }        


      if(Session::get("token") == $request->token){
         ####Compare the token given

          /*check the code first b4 giving the guy sub*/
           
            //The parameter after verify/ is the transaction reference to be verified
            $url = 'https://api.paystack.co/transaction/verify/'.$request->token;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt(
              $ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer sk_test_93dc7593451b627d7bce910142dc6a6220b609d7']
            );
            $req = curl_exec($ch);
            curl_close($ch);
              #### check if validation was successful
            if ($req){
              $result_ = json_decode($req);

               #### checking the amount he paid for
              if ((int)$result_->data->amount ==  (int)$request->amount){

                $booking_setup_id = Session::get("search_result")->id;
 
                $seats_selected = Session::get("seats_selected");                  

                
                $final_obj = json_decode($request->final_obj);

                $unique_reference = $this->generateRandomString(7);

                foreach ($final_obj as $k){
                     $input["token"] = $request->token;
                      $input["booking_setup_id"]  = $booking_setup_id; 
                      $input["user_id"] = Auth::user()->id;
                      $input["has_paid"] = 1;
                      $input["payment_date"] = Date("Y-m-d H:i:s"); 


                      $input["unique_reference"] = $unique_reference;
                      $input["booking_type"] = count($final_obj) > 1 ? 1 : 0;
                      $input["seat_number"]   =  $k->seat_number;       
                      $input["booked_person_phone"]  =  $k->phone; 
                      $input["booked_person_name"]  =  $k->name;
                      $input["next_of_kin_phone"]  =  $k->next_of_kin_phone; 
                      $input["next_of_kin_name"]  =  $k->next_of_kin_name; 

                    User_booking::Create($input);                     
                  }


                  /*Update User NOK if not set*/
                  if (Auth::user()->nok_phone == "" || Auth::user()->nok_name == "" || Auth::user()->name == "" ){
                      
                   $dd = User::find(Auth::user()->id);
                    Auth::user()->nok_phone = $dd->nok_phone = $final_obj[0]->next_of_kin_phone;
                    Auth::user()->nok_name = $dd->nok_name = $final_obj[0]->next_of_kin_name;
                    Auth::user()->name = $dd->name = $final_obj[0]->name;                  

                    $dd->save();                    
                  }

                  /*Add to Log*/
                  /*Updating the Booking Setup seats remaining*/
                 $d =  Booking_setup::find($booking_setup_id);
                 $d->seats_available = $d->seats_available - count($seats_selected);
                 $d->save();                  


                   /*Notfiy Users using Pusher*/
                /*Add to pusher*/     
              $pusher = new Pusher(env("PUSHER_KEY"),env("PUSHER_SECRET"), 312719, array( 'encrypted' => true ) );

              $show_view = $this->seats_view($booking_setup_id); 

              $pusher->trigger( 'booking_channel', 'my_event'.$booking_setup_id, array("seats_data"=>json_encode($show_view),"left"=>$d->seats_available ,"view"=>View::make("seats_setup",["seats_booked"=>$show_view] )->render() ));

              $this->add_log("Booked ".Session::get("adult")." Adult seat(s) and ".Session::get("children")." child(s) seat  paid total of &#8358; ".($request->amount/100)." for ".count($seats_selected)." seat(s)" ,Auth::user()->id,$booking_setup_id,1);

                /*Show him the seats he booked for*/
                $show_view = $this->seats_view($booking_setup_id);
                Session::put("search_result","");
                /*Send SMS/Email */
                return Response::json(["status"=>"1","msg"=>"Congratulation!!! Your Booking was successful","view"=> View::make("seats_setup",["seats_booked"=>$show_view] )->render() ]);                

              }else{
                /*the amouunt is not equal*/
                $booking_setup_id = Session::get("search_result")->id;

                $seats_selected = Session::get("seats_selected");
                
                $final_obj = json_decode($request->final_obj);

                foreach ($final_obj as $k){
                     $input["token"] = $request->token;
                      $input["booking_setup_id"]  = $booking_setup_id; 
                      $input["user_id"] = Auth::user()->id;
                      $input["has_paid"] = 0;
                      $input["payment_date"] = Date("Y-m-d H:i:s"); 

                      $input["unique_reference"] = $unique_reference;                      
                      $input["booking_type"] = count($final_obj) > 1 ? 1 : 0;
                      $input["seat_number"]   =  $k->seat_number;       
                      $input["booked_person_phone"]  =  $k->phone; 
                      $input["booked_person_name"]  =  $k->name;
                      $input["next_of_kin_phone"]  =  $k->next_of_kin_phone; 
                      $input["next_of_kin_name"]  =  $k->next_of_kin_name; 

                    User_booking::Create($input);                     
                  }

                  /*Update User NOK if not set*/
                  if (Auth::user()->nok_phone == "" || Auth::user()->nok_name == "" || Auth::user()->name == "" ){
                      
                   $dd = User::find(Auth::user()->id);
                    Auth::user()->nok_phone = $dd->nok_phone = $final_obj[0]->next_of_kin_phone;
                    Auth::user()->nok_name = $dd->nok_name = $final_obj[0]->next_of_kin_name;
                    Auth::user()->name = $dd->name = $final_obj[0]->name;                  

                    $dd->save();                    
                  }

                $this->add_log("Booked ".Session::get("adult")." Adult seat(s) and ".Session::get("children")." child(s)  paid total of &#8358; ".($result_->data->amount/100)." for ".count($seats_selected)." seat(s) instead of &#8358; ".($request->amount/100),Auth::user()->id,$booking_setup_id,1);

                /*Add to log*/                
                return Response::json(["status"=>"-2","msg"=>"Your booking was unsuccessful, please contact admin"]);                
              }               

              }else{           
               /*Error making request*/ 
           return Response::json(["status"=>"0","msg"=>"Error processing request, please try again"]);             
              }

        }else{
          /*Invalid token used*/
           return Response::json(["status"=>"-1","msg"=>"Invalid request, please try again"]); 
        }

    }

    public function start_booking(Request $request){
      $message = ["number_of_seats_data.required"=>"You have not selected the number of seats"];
       $validator = Validator::make($request->all(), [
            'number_of_seats_data' => 'required',                   
        ],$message);

        if($validator->fails()){
        return Response::json(["status"=>"-1","msg"=>$validator->errors()->first()]);
        }  

      /*Check if the seats are still available*/
      $seats_selected = $request->number_of_seats_data;      
      $seats_selected = implode(",", $seats_selected);
      $checker = User_booking::whereIn("seat_number",explode(",", $seats_selected))->where("has_paid","=",1)->where("booking_setup_id","=",Session::get("search_result")->id)->count();


      if ($checker > 0){
        /*send the update view along side and empty the ones he selected before*/
            $res = $this->seats_view(Session::get("search_result")->id);
            return Response::json(["status"=>"0","msg"=>"One of your selected seats have been booked, \n please kindly book and pay again on time",
              "view"=> View::make("seats_setup",["seats_booked"=>$res] )->render() ]);           
      }else{
           /*Seats are availbale*/
           /*Check if he has filled is next of kin information*/

           Session::put("seats_selected",$request->number_of_seats_data);
           return Response::json(["status"=>"1","msg"=>"Continue with booking"]);                     
      } 

      //$data = Booking_setup::where("")
    }

    public function get_seats_view(Request $request){
      $data =  Booking_setup::find($request->id);
      Session::put("search_result",$data);
      $show_view =   $this->seats_view($request->id);
       return Response::json(["status"=>"1","data"=> View::make("seats_setup",["seats_booked"=>$show_view] )->render(),"arr" =>$data ]); 
    }

    private function seats_view($id){
      $result = User_booking::select(["seat_number"])->where("has_paid","=",1)->where("booking_setup_id","=",$id)->get();       

        $final = array();
        if (count($result) > 0){
          foreach ($result as $k) {
              array_push($final, $k->seat_number);
          }
        }        

        return  $final;   
    }

    public function trips(){                  

       $data["branches"] = Branch::with('state')->get();
      $data["trips"] =  DB::table('user_bookings as a')
      ->select(DB::raw('a.*,d.name as branch_from,c.name as branch_to,e.name as state_to, f.name as state_from'))
      ->leftJoin('booking_setups as b','b.id', '=', 'a.booking_setup_id')
      ->leftJoin('branches as c','c.id', '=', 'b.travelling_to_branch_id')
      ->leftJoin('branches as d','d.id', '=', 'b.travelling_from_branch_id')
      ->leftJoin('states as e','e.id', '=', 'c.state_id')
      ->leftJoin('states as f','f.id', '=', 'd.state_id')      
      ->where("a.user_id",Auth::user()->id)  
      ->orderBy("a.id","DESC")   
      ->paginate(5);
                        //User_booking::with("booking_setup")->where('user_id',Auth::user()->id)->paginate(1);      
      return view('trips',$data);
    }

    public function guest(){
      Session::put("type",1);
      $data['title'] = "Oval Transport Services";
      $data["branches"] = Branch::with('state')->get();      
      return view('landing',$data);
    }

    public function get_branch_destination(Request $request){
        $destination_branch = $request->destination_branch;
        $data = Branch::with('state')->whereIn("id",explode(",", $destination_branch))->get();
        return $this->generate_view($data);
    }

    private function generate_view($data){
      $view = "<select class='selectize_select2 required' title='required' placeholder='Select your destination' id='travelling_to'>";
      if (count($data) > 0){
        foreach ($data as $k){
          $view .= "<option value=".$k->id.">".$k->state->name." - ".$k->name."</option>";
        }
      }
      $view .= "</select>";
      return $view;
    }

    public function search_now(Request $request){
      Session::put("from",$request->from);
      Session::put("to",$request->to);
      Session::put("adult",$request->adult);
      Session::put("children",$request->children);
      Session::put("date_to_live",$request->date_to_live);      
      Session::put("token",str_random(64));
      return $this->search_for_booking();
    }



    public function search_for_booking(){


      $from = Session::get("from");
      $from = explode("&&&", $from);

      $res = DB::select("SELECT a.*,b.image,b.no_of_seats,b.plate_no,b.name as bus_name,c.name AS travelling_from_branch, d.name AS travelling_from_state,
                        e.name AS travelling_to_branch, f.name AS travelling_to_state
                        FROM booking_setups AS a
                        LEFT JOIN buses AS b 
                        ON b.id = a.bus_id
                        LEFT JOIN branches AS c
                        ON c.id = a.travelling_from_branch_id
                        LEFT JOIN states AS d
                        ON d.id = c.state_id
                        LEFT JOIN branches AS e
                        ON e.id = a.travelling_to_branch_id
                        LEFT JOIN states AS f
                        ON f.id = e.state_id
                        
                        WHERE a.travelling_to_branch_id = '".Session::get("to")."' AND a.travelling_from_branch_id = '".$from[0]."'
                        AND Date(a.date_time_available) LIKE '%".Session::get("date_to_live")."%' 
                        AND a.status = 0 AND a.seats_available > 0
                        ");
      if (count($res) > 0){
        $data["ress"] = $res;
       /* $result = User_booking::select(["seat_number"])->where("booking_setup_id","=",$res[0]->id)->get();  
       

        $final = array();
        if (count($result) > 0){
          foreach ($result as $k) {
              array_push($final, $k->seat_number);
          }
        }*/
        $data["seats_booked"] = array();       
      }else{
        $data["ress"] = array();
        $data["seats_booked"] = array();        
      }

      //Session::put("search_result",$data["ress"]);
      $data["branches"] = Branch::with('state')->get(); 

      return view("search_result_new",$data);

    }

     /*public function search_for_booking(){


      $from = Session::get("from");
      $from = explode("&&&", $from);

      $res = DB::select("SELECT a.*,b.no_of_seats,b.plate_no,b.name as bus_name,c.name AS travelling_from_branch, d.name AS travelling_from_state,
                        e.name AS travelling_to_branch, f.name AS travelling_to_state
                        FROM booking_setups AS a
                        LEFT JOIN buses AS b 
                        ON b.id = a.bus_id
                        LEFT JOIN branches AS c
                        ON c.id = a.travelling_from_branch_id
                        LEFT JOIN states AS d
                        ON d.id = c.state_id
                        LEFT JOIN branches AS e
                        ON e.id = a.travelling_to_branch_id
                        LEFT JOIN states AS f
                        ON f.id = e.state_id
                        WHERE a.travelling_to_branch_id = '".Session::get("to")."' AND a.travelling_from_branch_id = '".$from[0]."'
                        AND Date(a.date_time_available) LIKE '%".Session::get("date_to_live")."%' 
                        AND a.status = 0 AND a.seats_available > 0 ");
      if (count($res) > 0){
        $data["res"] = $res[0];
        $result = User_booking::select(["seat_number"])->where("booking_setup_id","=",$res[0]->id)->get();  
       

        $final = array();
        if (count($result) > 0){
          foreach ($result as $k) {
              array_push($final, $k->seat_number);
          }
        }
        $data["seats_booked"] = $final;       
      }else{
        $data["res"] = array();
        $data["seats_booked"] = array();        
      }

      Session::put("search_result",$data["res"]);
      $data["branches"] = Branch::with('state')->get(); 
      
      return view("search_result",$data);
    }
*/
    function generateRandomString($length) {
            $characters = '0123456789';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

    public function activate_phone(Request $request){
      if (Auth::user()->confirm_phone == "1"){
        abort(401,"Account already activated");
      }
      $code = $request->code;
      $user = User::findorfail(Auth::user()->id);
      if (strtolower($code) == strtolower($user->sms_code)){
       $user->confirm_phone = 1;
       $user->save();
       return Response::json(["status"=>"1","msg"=>"Account activated successfully"]);      
      }
       return Response::json(["status"=>"0","msg"=>"Code does not match, please try again or re-send code to phone"]);      

      }

    public function resend_code(Request $request){  
       if (Auth::user()->confirm_phone == "1"){
        abort(401,"Account already activated");
        }
       $code = $this->generateRandomString(6);
       $user = User::findorfail(Auth::user()->id);       
       $user->sms_code = $code;
       $user->save();
       $this->send_sms(Auth::user()->name,Auth::user()->phone,$code);
       return array("status"=>"1","msg" =>"We have sent you a new code, please check your phone to activate your account");
    }

     private function send_sms($name,$phone,$code){
            $sms_message = "Dear ".ucfirst($name).", activate your account with the code: ".$code." to continue using our service";
            $curl = curl_init();

            $sender = "Transport";

           curl_setopt_array($curl, array(         
              CURLOPT_URL => "http://smsmobile24.com/components/com_spc/smsapi.php?username=anietz&password=P@zzw0rd&sender=".UrlEncode($sender)."&recipient=".UrlEncode($phone)."&message=".UrlEncode($sms_message),
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache"        
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
              return  "cURL Error #:" . $err;
            } else {
              return $response;
            }
    }

     public function send_email($code){
      //Mail::to('reubenanietz4unical@yahoo.com')->send("Testing email");
      $data["name"] = Auth::user()->name; // Empty array
      $data["code"] = $code;
       $res = Mail::send('errors.email', $data, function($message)
        {
            $message->to(Auth::user()->email, Auth::user()->name)->subject('Activate your Account');
        });

    }

    public function test(){
      return First_comment::with("user","second_comment")->whereTestimonyHelpId(1)->get();
    }

    public function welcome(Request $request){
      $data["testimony_types"] = Testimony_type::all();
      $data["help_types"] = Help_type::all();
      $data["testimonies"] = Testimony::with("user","type")->orderBy("id","DESC")->take(5)->get();
      $data["helps"] = Help::with("user","type")->orderBy("id","DESC")->take(5)->get();
      $data["total"] = Testimony::count();
      $data["type"] = empty($request->type)?"All":$request->type;
      return view('landing_',$data);
    }

    public function testimonies(Request $request){
        $data["help_types"] = Help_type::all();
        $data["testimony_types"] = Testimony_type::all();
        $type = $request->type;
        if (!empty($type)){
        $testimony_type_id = $request->id;
        $data["testimonies"] = Testimony::with("user","type")->where("testimony_type_id",'=',$testimony_type_id)->orderBy("id","DESC")->simplePaginate(10);          
        }else{          
        $data["testimonies"] = Testimony::with("user","type")->orderBy("id","DESC")->simplePaginate(10);
        }
        $data["helps"] = Help::with("user","type")->orderBy("id","DESC")->take(5)->get();
        $data["total"] = Testimony::count();
        $data["count"] = -5;
         $data["type"] = 2;
         if ($request->ajax()){
            return view('ajax_list',$data)->render();  
        }
        return view('testimonies',$data);
    }

     public function help(Request $request){
        $data["help_types"] = Help_type::all();
        $data["testimony_types"] = Testimony_type::all();
        $data["testimonies"] = Testimony::with("user","type")->orderBy("id","DESC")->take(5)->get();

         $type = $request->type;
        if (!empty($type)){
        $testimony_type_id = $request->id;                  
        $data["helps"] = Help::with("user","type")->where("help_type_id",'=',$testimony_type_id)->where("make_private_to_admin",'=',0)->orderBy("id","DESC")->simplePaginate(10);        
        }else{          
        $data["helps"] = Help::with("user","type")->where("make_private_to_admin",'=',0)->orderBy("id","DESC")->simplePaginate(10);
        }


        $data["total"] = Help::count();
        $data["count"] = -5;
        $data["type"] = 2;
         if ($request->ajax()){
            return view('ajax_list',$data)->render();  
        }
        return view('help',$data);
    }

     public function help_details(Request $request){
      $help_id = $request->id;
      $data["help_types"] = Help_type::all();
      $data["testimony_types"] = Testimony_type::all();
      $data["t"] = Help::with("user","type")->whereId($help_id)->first();
      $data["testimonies"] = Testimony::with("user","type")->orderBy("id","DESC")->take(5)->get();
      $data["helps"] = Help::with("user","type")->orderBy("id","DESC")->where("make_private_to_admin",'=',0)->take(5)->get();
      $data["first_comment"] = []; //First_comment::with("user","second_comment")->whereTestimonyHelpId($help_id)->take(10)->get();
      return view('help_details',$data);
    }

    public function testimony_details(Request $request){
      $testimony_id = $request->id;
      $data["help_types"] = Help_type::all();
      $data["testimony_types"] = Testimony_type::all();
      $data["t"] = Testimony::with("user","type")->whereId($testimony_id)->first();
      $data["testimonies"] = Testimony::with("user","type")->orderBy("id","DESC")->take(10)->get();
      $data["helps"] = Help::with("user","type")->orderBy("id","DESC")->take(5)->get();
      $data["first_comment"] = First_comment::with("user","second_comment")->whereTestimonyHelpId($testimony_id)->take(10)->get();
      return view('testimony_details',$data);
    }

    public function add_testimony(Request $request){
       $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'type' => 'required'          
        ]);

        if($validator->fails()){
        return Response::json(["status"=>"3","msg"=>$validator->errors()]);
        }

     $input["title"] = $request->title;
     $input["description"] = $request->description;
     $input["user_id"] = Auth::user()->id;
     $input["testimony_type_id"] = $request->type;

     $res =  Testimony::create($input);
     return ["status"=>"1","msg"=>"success","data"=>$res];

    }

     public function add_help(Request $request){
       $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'type' => 'required',
            'is_private' => 'required'          
        ]);

        if($validator->fails()){
        return Response::json(["status"=>"3","msg"=>$validator->errors()]);
        }

     $input["title"] = $request->title;
     $input["description"] = $request->description;
     $input["user_id"] = Auth::user()->id;
     $input["help_type_id"] = $request->type;
     $input["make_private_to_admin"] = $request->is_private;

     $res =  Help::create($input);
     return ["status"=>"1","msg"=>"success","data"=>$res];

    }
   

  

    public function get_chats(){
      $chats =  Admin_log::where("client_id",Auth::user()->id)->where("type",50)->get();
      $html = "";
      if (sizeof($chats) > 0) {
        foreach ($chats as $value) {
          $who = "";
                                if ($value->client_id == $value->performed_by) {
                                 $html .= '<li class="right clearfix"><span class="chat-img pull-right">
                                <img src="http://placehold.it/50/FA6F57/fff&text=ME" alt="User Avatar" class="img-circle" />
                            </span>
                                <div class="chat-body clearfix">
                                    <div class="header">
                                        <small class=" text-muted"><span class="glyphicon glyphicon-time"></span>'.$value->created_at.'</small>
                                        <strong class="pull-right primary-font">'.Auth::user()->name.'</strong>
                                    </div>
                                    <p>'.$value->description.'</p>
                                </div>
                            </li>'; 
                                }else{
                               
                              $html .= ' <li class="left clearfix"><span class="chat-img pull-left">
                                <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-circle" />
                                  </span>
                                <div class="chat-body clearfix">
                                    <div class="header">
                                        <strong class="primary-font">Admin</strong> <small class="pull-right text-muted">
                                            <span class="glyphicon glyphicon-time"></span>'.$value->created_at.'</small>
                                    </div>
                                    <p>'.$value->description.'</p>
                                </div>
                            </li>';
                                }

        }
      }else{
        $html = '<li><p><i class="fa fa-exclamation-o"></i></p><p>No message to show</p></li>';
      }

      return $html;
    }

    public function add_chat(Request $request){
      $this->validate($request, [
            'message' => 'required|string|max:500'
        ]);
      $this->add_log($request->message,Auth::user()->id,Auth::user()->id,50,$get_help_id=0);
      return $this->get_chats();
    }

        public function referal()
      {   
      
        $data = $this->client_repeated_function();

        $data["side_3"] = 5;

        $data["match_list"] = Referrals_web::with("new_ref")->where("old_user_id",Auth::user()->id)->get();

        return view('client.referals',$data);
    }

       public function logs()
      {   
      
        $data = $this->client_repeated_function();

        $data["side_4"] = 5;

        $data["match_list"] = Admin_log::where("client_id",Auth::user()->id)->orWhere("performed_by",Auth::user()->id)->get();

        return view('client.logs',$data);
    }

     public function transaction_history()
      {   
      
        $data = $this->client_repeated_function();
        
        $data["side_2"] = 5;

        $data["match_list"] = Provide_help_web::with("package")->where("user_id",Auth::user()->id)->orderBy("created_at","DESC")->get();

        return view('client.transaction_history',$data);
    }


    private function client_repeated_function(){
      $data["user"] = User::find(Auth::user()->id);

        $data["total_referals"] = Referrals_web::where("old_user_id",Auth::user()->id)->count();
        $data["total_help_received"] = Get_help_web::where("has_received_full_payment",1)->where("user_id",Auth::user()->id)->count();
        $data["total_investment"] = Provide_help_web::where("has_paid",1)->where("user_id",Auth::user()->id)->count();

        $data["subs"] = Package::orderBy("price","ASC")->get();
        $get_help_status = $this->check_if_user_can_withdraw($data["subs"],$data["user"]);
        if (sizeof($get_help_status) > 0){
        $data["show_withdraw_btn"] = 1;
        $data["withdrawal_subs"] = $get_help_status;
        }else{
        $data["show_withdraw_btn"] = 0;
        }

      return $data;
    }



    public function no_action()
    {   
      $val_a = Session::get("is_giving_help");
      $val_b = Session::get("is_getting_help");
      /*USers that gave help, but not yet matched completely*/
       //$data = $this->status_dash()
        DB::beginTransaction();
    try {
          $data["user"] = User::find(Auth::user()->id);
        $data["total_referals"] = Referrals_web::where("old_user_id",Auth::user()->id)->count();
        $data["total_help_received"] = Get_help_web::where("has_received_full_payment",1)->where("user_id",Auth::user()->id)->count();
        $data["total_investment"] = Provide_help_web::where("has_paid",1)->where("user_id",Auth::user()->id)->count();


       $data["time_to_pay"] = Settings::first()->hour_to_pay;


        if($data["user"]->is_giving_help == "1" && $data["user"]->is_getting_help == "1"){


          $data["ph_data1"] = Provide_help_web::with("package")->where("user_id",Auth::user()->id)
        ->orderBy("id","DESC")->first();

        if (empty($data["ph_data"])){
        $data["msg1"] = "will be matched shortly";
        $data["gh_data1"] = array();
        }else{
        $data["msg1"] = "You have been matched";
        $data["gh_data1"] = Get_help_web::with("user")->where("id",$data["ph_data"]->get_help_id)->first();
        }


        /*Get the latest GH list*/
        $data["gh_data2"] = Get_help_web::with("user","package")
        ->where("user_id",Auth::user()->id)
        ->orderBy("id","DESC")
        ->first();

        $data["match_list2"] = Provide_help_web::with("user","package")
        ->where("get_help_id",$data["gh_data2"]->id)
        ->orderBy("id","DESC")
        ->get();



        }elseif($data["user"]->is_giving_help == "1"){ 


          $data["ph_data"] = Provide_help_web::with("package")->where("user_id","=",Auth::user()->id)
          ->orderBy("id","DESC")->first();        

          if (empty($data["ph_data"])){
          $data["msg"] = "will be matched shortly";
          $data["gh_data"] = array();
          }else{
          $data["msg"] = "You have been matched";
          $data["gh_data"] = Get_help_web::with("user")->where("id",$data["ph_data"]->get_help_id)->first();
          }

       }elseif($data["user"]->is_getting_help == "1") {

        /*Get is last PH*/
        $vvv = DB::select("SELECT a.user_id,b.has_received_full_payment,b.is_paid_count FROM provide_help_web AS a
        LEFT JOIN get_help_web AS b
        ON a.get_help_id = b.id
        WHERE a.user_id = '".Auth::user()->id."' ORDER BY a.id DESC LIMIT 1");

        if (!empty($vvv)){
        $data["last_ph"]   = $vvv[0];          
        }else{
        $data["last_ph"]   = array();                    
        }

        /*Get the latest GH list*/
        $data["gh_data"] = Get_help_web::with("user","package")
        ->where("user_id",Auth::user()->id)
        ->orderBy("created_at","DESC")
        ->first();


        $data["match_list"] = Provide_help_web::with("user","package")
        ->where("get_help_id","=",$data["gh_data"]->id)
        ->orderBy("matched_on","DESC")
        ->take(2)->get();


       }
      $data["subs"] = Package::orderBy("price","ASC")->get();
        DB::commit();
        $success = true;
    } catch (\Exception $e) {
        $success = false;
        DB::rollback();
    }



      if ($success) {
        $data["show_withdraw_btn"] = 0;
        return view('client.home',$data);        
      }
      
      
    }



    private function check_if_user_can_withdraw($subs,$user){
      $possible_plans = array();
      if (sizeof($subs) > 0) {
        foreach ($subs as $sub){
        if ($user->current_balance >= ($sub->price*$sub->no_of_people_to_match)) {
        array_push($possible_plans, $sub);
        }
        }
      }
      return $possible_plans;
    }
    private function get_umnatched_gh_user($id){
       return Get_help_web::where("is_matched_count","<","2")
       ->where("package_type_id","=",$id)
       ->where("user_id","!=", Auth::user()->id)
       ->orderBy("id","ASC")->first();
    }

    public function provide_help(Request $request){
         DB::beginTransaction();
    try {
          /*Client provides help*/
    $package = base64_decode($request->package_type_id);
    $input["user_id"] = Auth::user()->id;
    $input["package_type_id"] = $package;

    $sub = Package::find($package);
    if (empty($sub)){
    return array("status"=>false,"message"=>"Invalid package selected");
    }

    /*get the a GH user and match him now*/
    $gh_data = $this->get_umnatched_gh_user($sub->id);
    if (!empty($gh_data)) {
    $input["get_help_id"] = $gh_data->id;

    /*Make payment in 5 hours time*/

    $settings = Settings::first();
    $input["date_to_make_payment"] = Date("Y-m-d H:i:s" , strtotime("+ ".$settings->hour_to_pay." hours"));
    /*Update the count status*/
    $gh_data->is_matched_count = $gh_data->is_matched_count + 1;
    $gh_data->save();

    $this->add_log("You have been match with a user, to pay you &#8358;".$sub->price." immediately or before ".$sub->duration." days",$gh_data->user_id,$gh_data->user_id,19);

    /*Update is getting help status*/
    $gher = User::find($gh_data->user_id);
    $gher->is_getting_help = 1;
    $gher->save();
    $input["matched_on"] = Date("Y-m-d H:i:s");
    }
    $data =  Provide_help_web::create($input);
    Session::put("is_giving_help","1");
    $dat = User::find(Auth::user()->id);
    $dat->is_giving_help = 1;
    $dat->save();
    //Session::put("is_getting_help","1");
    if (empty($gh_data)){
    $this->add_log("You have shown interest to PH, you will be matched in few minutes,
     after which you should make payment immediately or before ".$sub->duration." days to avoid suspension of account",Auth::user()->id,Auth::user()->id,18);
    }else{ 
     $this->add_log("You have been match with a user please pay immediately or before ".$settings->hour_to_pay." hour(s) time to avoid suspension",Auth::user()->id,Auth::user()->id,20);
     }
        DB::commit();
        $success = true;
    } catch (\Exception $e) {
        $success = false;
        DB::rollback();
    }

    if ($success) {
    return array("status"=>true,"message"=>"Your request was successful");
        // the transaction worked ...
    }
   
    }
    public function submit_fraud(Request $request){
       DB::beginTransaction();
          try {
              $data = Provide_help_web::findorfail(base64_decode($request->id));
            $data->has_paid = 0;
            $data->has_decline = 1;
            $data->reason_for_decline = $request->reason;
            $data->save();
            
            $this->add_log("Reported for fraud case, currently in review",$data->user_id,Auth::user()->id,31,$data->get_help_id);  
              DB::commit();
              $success = true;
          } catch (\Exception $e) {
              $success = false;
              DB::rollback();
          }

          if ($success) {
            return array("status"=>true,"message"=>"Action successfully performed");
              // the transaction worked ...
          }
    }

     public function has_declined(Request $request){
       DB::beginTransaction();
    try {
      $data = Provide_help_web::findorfail($request->provider_help_id);
      $data->has_paid = 0;
      $data->has_decline = 1;
      $data->save();

      $gher = Get_help_web::find($data->get_help_id);
      $gher->is_matched_count =  $gher->is_matched_count -1;
      $gher->save();
      /*Remember to rematch the GHer*/

      /*Delete the user account*/
      User::findorfail(Auth::user()->id)->delete();
      Provide_help_web::where("user_id",Auth::user()->id)->delete();
      Get_help_web::where("user_id",Auth::user()->id)->delete();
      Admin_log::where("client_id",Auth::user()->id)->delete();
      Admin_log::where("performed_by",Auth::user()->id)->delete();
      //Admin_log::where("user_id",Auth::user()->id)->delete();
      
      //$this->add_log("User has refused to make payment",$data->user_id,Auth::user()->id,21,$data->get_help_id);
      $this->add_log("User has been deleted Name: ".Auth::user()->name. " Amount left: ".Auth::user()->current_balance."",1,1,60);  
        DB::commit();
        $success = true;
    } catch (\Exception $e) {
        $success = false;
        DB::rollback();
    }

    if ($success) {
        // the transaction worked ...
      Auth::logout();
      return array("status"=>true,"message"=>"Your account will be deactivated soon");
    }

    }


    private function get_umnatched_gh_user_($id,$unserious_user_id){
       return Get_help_web::where("is_matched_count","<","2")
       ->where("package_type_id","=",$id)
       ->where("user_id","!=", Auth::user()->id)
       ->where("user_id","!=", $unserious_user_id) 
       ->orderBy("id","ASC")->first();
    }

     public function dont_want_to_pay(Request $request){
       DB::beginTransaction();
    try {       
      $data = Provide_help_web::where("id","=",base64_decode($request->id))
      ->where("user_id","=",Auth::user()->id)->first();
     
      $gher = Get_help_web::find($data->get_help_id);
      $gher->is_matched_count =  $gher->is_matched_count -1;
      $gher->save();
      /*Remember to rematch the GHer*/

      /*Delete the user account*/
      User::findorfail(Auth::user()->id)->delete();
      Provide_help_web::where("user_id","=",Auth::user()->id)->delete();
      Get_help_web::where("user_id","=",Auth::user()->id)->delete();
      Admin_log::where("client_id","=",Auth::user()->id)->delete();
      Admin_log::where("performed_by","=",Auth::user()->id)->delete();
      //Admin_log::where("user_id",Auth::user()->id)->delete();
      
      //$this->add_log("User has refused to make payment",$data->user_id,Auth::user()->id,21,$data->get_help_id);
      $this->add_log("User has been deleted Name: ".Auth::user()->name. " Amount left: ".Auth::user()->current_balance."",1,1,60);
        DB::commit();
        $success = true;
    } catch (\Exception $e) {
        $success = false;
        DB::rollback();
    }

    if ($success) {
        // the transaction worked ...
      return array("status"=>true,"message"=>"Your account will be deactivated soon");
      Auth::logout();
    }


    }



    public function provide_help_rematch(Request $request){
       DB::beginTransaction();
    try {
         if (Auth::user()->is_giving_help != "1"){
            abort(401, 'Error');
          }

         $data = Provide_help_web::with("package")->where("id","=",base64_decode($request->id))
    ->where("user_id","=",Auth::user()->id)->first();

     /*Reduce the unserious GHer count*/
    $unserios_gher = Get_help_web::where("id",$data->get_help_id)->first();
    $unserios_gher->is_matched_count =  (int)$unserios_gher->is_matched_count - 1;
    $unserios_gher->save();

    /*get a new GH user and match him now*/
    $gh_data = $this->get_umnatched_gh_user_($data->package->id,$unserios_gher->user_id);
    if (!empty($gh_data)){
    $data->get_help_id = $gh_data->id;

    /*Make payment in 5 hours time*/
    $settings = Settings::first();
    $data->date_to_make_payment = Date("Y-m-d H:i:s" , strtotime("+ ".$settings->hour_to_pay." hours"));
    /*Update the count status*/
    $gh_data->is_matched_count = $gh_data->is_matched_count + 1;
    $gh_data->save();


    /*Update is getting help status*/
    $gher = User::find($gh_data->user_id);
    $gher->is_getting_help = 1;
    $gher->save();
    $data->matched_on = Date("Y-m-d H:i:s");

    $this->add_log("You have been match with a new user, to pay you &#8358;".$data->package->price." immediately or before ".$data->package->duration." days",$gh_data->user_id,$gh_data->user_id,19);
    }else{
    $data->get_help_id  = NULL; 
    }
    $data->has_rematched = 1;
    $data->save();

    

    if (!empty($gh_data)){
    $this->add_log("You have Been rematched with a new gher, make payment immediately or before ".$data->package->duration." days to avoid suspension of account",Auth::user()->id,Auth::user()->id,18);
    }else{ 
    $this->add_log("You have will be rematched shortly user please pay immediately or before ".$settings->hour_to_pay." hour(s) time to avoid suspension",Auth::user()->id,Auth::user()->id,20);
    }
        DB::commit();
        $success = true;
    } catch (\Exception $e) {
        $success = false;
        DB::rollback();
    }

    if ($success) {
    // the transaction worked ...
    return array("status"=>true,"message"=>"Your request was successful");
    }

    }

    private function trans(){
       DB::beginTransaction();
    try {
        $project = Project::find($id);
        $project->users()->detach();
        $project->delete();
        DB::commit();
        $success = true;
    } catch (\Exception $e) {
        $success = false;
        DB::rollback();
    }

    if ($success) {
        // the transaction worked ...
    }
    }



    public function delete_and_rematch(Request $request){
       DB::beginTransaction();
    try {
       if (Auth::user()->is_getting_help != "1"){
        abort(401, 'Error');
      }

      $provider_help_id = base64_decode($request->id);

      $data = Provide_help_web::with("user")->where("id","=",$provider_help_id)->first();

      if (!empty($data->date_of_upload)){
        return array("status"=>true,"message"=> "The Pher has uploaded a verification, please confirm pop or report for fraud");
      }
     

      $gher_data = Get_help_web::with("package")->where("id","=",$data->get_help_id)->first();    
      
      //$this->add_log("User has refused to make payment",$data->user_id,Auth::user()->id,21,$data->get_help_id);
      $this->add_log("You have deleted the user from the System you will be rematched soon>>>> Name: ".$data->user->name. " Amount left: ".$data->user->current_balance."",Auth::user()->id,Auth::user()->id,60);

      /*Match user now*/

    $settings = Settings::first();

    $count = (int)$gher_data->is_matched_count - 1;
    $limit = $count == 0 ? 2 : 0;
    $phers =  $this->unmatched_phers($gher_data->package->id,$limit);
    /*Remember to rematch the GHer*/
    if (sizeof($phers) > 0) {
      foreach ($phers as $v){
          $pher_data = User::find($v->user_id);
          $pher_data->is_giving_help = 1;
          $pher_data->save();

          $v->get_help_id = $gher_data->id;
          $v->matched_on = Date("Y-m-d H:i:s");
          $v->date_to_make_payment = Date("Y-m-d H:i:s" , strtotime("+ ".$settings->hour_to_pay." hours"));     
          $v->save();
          $this->add_log("You have been matched with a person to pay &#8358;".$gher_data->package->price."please pay immediately or before ".$settings->hour_to_pay." hour(s)",$v->user_id,Auth::user()->id,22,$v->id);  
          $count++;
      }
    }

    /*Subtract the first guy from the gher match_count and update the current count*/
   $gher_data->is_matched_count =  $count;
   $gher_data->save();


    if ($count > 0){
    $this->add_log("Your request was successful, you have been re-matched with ".$count." people(s) to pay &#8358;".$gher_data->package->price." each ",Auth::user()->id,Auth::user()->id,1);
    }else{
    $this->add_log("Your request was successful, you will be match shortly",Auth::user()->id,Auth::user()->id,23,$gher_data->id);
    }

     /*Delete the user account*/
      User::where("id",$data->user_id)->delete();
      Provide_help_web::where("user_id","=",$data->user_id)->delete();
      Get_help_web::where("user_id","=",$data->user_id)->delete();
      Admin_log::where("client_id","=",$data->user_id)->delete();
      Admin_log::where("performed_by","=",$data->user_id)->delete();
       
        DB::commit();
        $success = true;
    } catch (\Exception $e) {
        $success = false;
        DB::rollback();
    }

    if ($success) {
    return array("status"=>true,"message"=>"Your successfully deleted the user, you will be rematched soon");
    // the transaction worked ...
    }

    }

  
    public function get_help(Request $request){
       DB::beginTransaction();
    try {
           /*Client gets help*/
    $sub = Package::find($request->package_type_id);
    if (empty($sub)){
    return array("status"=>false,"message"=>"Invalid package selected");
    }
    $package_amount = $sub->price;
    $gher = User::find(Auth::user()->id);
    $current_amount = $gher->current_balance;
    if ($current_amount >= $package_amount*$sub->no_of_people_to_match){
    $input["user_id"] = Auth::user()->id;
    
    $gher_data = $this->add_new_gh($sub->id,Auth::user()->id);

    /*Update the User current amount*/
    $gher->current_balance = $gher->current_balance - $package_amount*$sub->no_of_people_to_match;
    $gher->is_getting_help = 1;
    Session::put("is_getting_help",1);
    $gher->save();

    /*Match user now*/

    $settings = Settings::first();

    $phers =  $this->unmatched_phers($sub->id);
    $count = 0;
    if (sizeof($phers) > 0) {
      foreach ($phers as $v){
          $v->get_help_id = $gher_data->id;

          $pher_data = User::find($v->user_id);
          $pher_data->is_giving_help = 1;
          $pher_data->save();
          /**/
          $v->matched_on = Date("Y-m-d H:i:s");
          $v->date_to_make_payment = Date("Y-m-d H:i:s" , strtotime("+ ".$settings->hour_to_pay." hours"));     
          $v->save();
          $this->add_log("You have been matched with a person to pay &#8358;".$package_amount."please pay immediately or before ".$settings->hour_to_pay." hour(s)",$v->user_id,Auth::user()->id,22,$v->id);  
      $count++;
      }
    }

    $gher_data->is_matched_count = $count;

    if ($count > 0){
    $this->add_log("Your request was successful, you have been matched with ".$count." people(s) to pay &#8358;".$package_amount." each ",Auth::user()->id,Auth::user()->id,1);
    }else{
    $this->add_log("Your request was successful, you will be match shortly",Auth::user()->id,Auth::user()->id,23,$gher_data->id);
    }
    return array("status"=>true,"message"=>"Your request was successful");
    }else{
      /*Insuffiecient balance*/
    return array("status"=>false,"message"=>"Insuccifient balance, please Provide/Invest Now");
    }
        DB::commit();
        $success = true;
    } catch (\Exception $e) {
        $success = false;
        DB::rollback();
    }

    if ($success) {
        // the transaction worked ...
    }
  
   }

    private function add_referal_bonus($referal_user_id,$package_amount){
    $ref = Referrals_web::where("newly_registered_user_id",$referal_user_id)->first();
        if (!empty($ref)){
          /*Update the Referal Current Balance*/
          if($ref->has_provided_help == 0){
          $referal_data = User::find($ref->old_user_id);
          $referal_data->current_balance = $referal_data->current_balance + ($package_amount*0.1);

          $referal_data->save();
          $ref->amount_paid = $package_amount*0.1;
          $ref->has_provided_help = 1;
          $ref->save();
          }
        }
    }

    public function confirm_payment_by_user(Request $request){
       DB::beginTransaction();
    try {
      $usd = User::find(Auth::user()->id);

      if ($usd->is_getting_help != "1"){
        abort(401, 'Error');
      }

      $data = Provide_help_web::findorfail(base64_decode($request->id));
      $data->has_paid = 1;
      $data->date_of_payment = Date("Y-m-d H:i:s");
      $data->has_decline = 0;
      $data->is_moved_to_gh_list = 1;
      $data->moved_on = Date("Y-m-d H:i:s");
      $data->save();

      /*Check the status of transaction*/
      $gher = Get_help_web::with("package")->where("id",$data->get_help_id)->first();

      /*Update the Pher Balance*/
      $uss = User::find($data->user_id);
      $uss->is_giving_help = 0;
      $uss->is_getting_help = 1;
      $uss->current_balance = $uss->current_balance + $gher->package->price*$gher->package->no_of_people_to_match;
      $uss->save();


      /*Match the ph user if  PHers exist*/

    /*Match user now*/
    $gher_data = $this->add_new_gh($gher->package->id,$data->user_id);
    $this->add_log("Your payment have been confirmed, You will be matched Shortly to receive &#8358;".$gher->package->price*$gher->package->no_of_people_to_match,$data->user_id,$data->user_id,30);

    $settings = Settings::first();

    $phers =  $this->unmatched_phers($gher->package->id);
    $count = 0;
    if (sizeof($phers) > 0) {
      foreach ($phers as $v){
          $v->get_help_id = $gher_data->id;
          $pher_data = User::find($v->user_id);
          $pher_data->is_giving_help = 1;
          $pher_data->save();
          /**/
          $v->matched_on = Date("Y-m-d H:i:s");
          $v->date_to_make_payment = Date("Y-m-d H:i:s" , strtotime("+ ".$settings->hour_to_pay." hours"));     
          $v->save();
          $this->add_log("You have been matched with a person to pay &#8358;".$gher->package->price."please pay immediately or before ".$settings->hour_to_pay." hour(s)",$v->user_id,Auth::user()->id,22,$v->id);  
      $count++;
      }
    }

    if ($count > 0){
    $this->add_log("Your request was successful, you have been re-matched with ".$count." people(s) to pay &#8358;".$gher->package->price." each ",Auth::user()->id,Auth::user()->id,1);
    }else{
    $this->add_log("Your request was successful, you will be match shortly",Auth::user()->id,Auth::user()->id,23,$gher_data->id);
    }


   $gher_data->is_matched_count = $count;
   $gher_data->save();

      /*Add referal bonus if exist*/
    $this->add_referal_bonus($data->user_id,$gher->package->price);


      /*Update the User Balance*/      
      $usd->current_balance = $usd->current_balance - $gher->package->price;

      $count_status = $gher->is_paid_count + 1;
      if($count_status == $gher->package->no_of_people_to_match){
      $usd->is_getting_help = 0;
      $gher->has_received_full_payment = 1;
      $this->add_log("You have confirm payment and received full payment",$data->user_id,Auth::user()->id,1,$data->get_help_id);
      }else{
      $this->add_log("User has Confirm payment",$data->user_id,Auth::user()->id,1,$data->get_help_id);
      }
      $gher->is_paid_count = $gher->is_paid_count + 1;
      $gher->save();
      $usd->save();
  
        DB::commit();
        $success = true;
    } catch (\Exception $e) {
        $success = false;
        DB::rollback();
    }

    if ($success){
      return  array("status"=>true,"message"=>"You have successfully confirm payment");
        // the transaction worked ...
    }

    }

     private function unmatched_phers($package_type_id,$limit = 2){
       return Provide_help_web::with("user")
        ->where("get_help_id","=", NULL)->where("package_type_id",$package_type_id)
        ->orderBy("id","ASC")->take($limit)->get();
    }


    private function add_new_gh($package,$user_id){
    $input['package_type_id'] = $package;
    $input["user_id"] = $user_id;
    return Get_help_web::create($input);
    }

    public function confirm_payment(Request $request){
      $data = Provide_help_web::findorfail(base64_decode($request->id));
      $data->has_paid = 1;
      $data->date_of_payment = Date("Y-m-d H:i:s");
      $data->save();
      $this->add_log("You have confirmed payment for the user",$data->user_id,Auth::user()->id,1,$data->get_help_id);
      return $this->get_hp_for_ph($data->get_help_id);
    }


    public function add_receipts(Request $request){
        $this->validate($request, [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:1000KB',
            "provider_help_id" => 'required|numeric'
        ]);
        $data = Provide_help_web::findorfail($request->provider_help_id);
        if (empty($data)) {    
        return array("status"=>false,"message"=>"Operation failed, please try again","data"=>"");
        }else{

          /*// resizing an uploaded file
          Image::make(Input::file('photo'))->resize(300, 200)->save('foo.jpg');*/

        $imageName = 'file_'.time().'.'.$request->file->getClientOriginalExtension();
        $request->file->move(public_path('receipts/'), $imageName);
        $data->date_of_payment = Date("Y-m-d H:i:s");
        $data->date_of_upload = Date("Y-m-d H:i:s");
        $data->payment_receipt_image = $imageName;
        $data->save();
        $sub = Package::find($data->package_type_id);
        $view = '<a href="'.asset('public/receipts/'.$imageName).'" data-lightbox="imag'.base64_encode($imageName).'" data-title="Payment receipt uploaded on ('.Date("Y-m-d H:i:s").') ">
                                                      View receipts
                                                    </a> <p> You receipt is been reviewed for fraud, you will be match to get &#8358;'.($sub->price*$sub->no_of_people_to_match).'  if successful </p>';
         return array("status"=>true,"message"=>"Successlly uploaded","data"=>$view);
       }

    }

     public function edit_profile_picture(Request $request){
        $this->validate($request, [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:100KB',
        ]);
        $imageName = 'file_'.time().'.'.$request->file->getClientOriginalExtension();
        $request->file->move(public_path('receipts/'), $imageName);
        return $imageName;
    }

    public function edit_profile(Request $request){
    $data = User::findorfail(base64_decode($request->user_id));
    if (empty($data)){
     return array("status"=>false,"message"=>"You must login to continue");
    }
    //'name', 'email', 'password','is_admin','sex','current_balance','alias','account_number','payment_date','bank_name'
    $data->name = $request->name;
    $data->account_number = $request->account_number;
    $data->bank_name = $request->bank_name;
    $data->save();
    }
    
}
