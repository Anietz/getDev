<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

use App\Http\Requests;
use App\Questions;
use GuzzleHttp;


class UserController extends Controller
{


    public $successStatus = 200;



     /**
     * Authenticate a user
     *
     * This endpoint is used to authenticate a registered user. This request is not authenticated.
     *
     * @param Requests\LoginRequest $request
     * @return App/User
     *
     */
    public function login(Requests\LoginRequest $request){       

        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            return response()->json(['data'=>$user,'token'=>$success["token"]], $this->successStatus);
        }
        else{
            return response()->json(['error'=>'Email and Pasword does not exist'], 401);
        }
    }


   
     /**
     * Register a user
     *
     * This endpoint is used to register a user. This request is not authenticated.
     *
     * @param Requests\RegisterRequest $request
     * @return App/User    
    */
    public function register(Requests\RegisterRequest $request)
    {
                
        $input['password'] = bcrypt($request['password']);
        $input['name'] = $request['name'];
        $input['email'] = $request['email'];


        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;

        Auth::login($user, true); 

        return response()->json(['data'=>$user,'token'=>$success["token"]], $this->successStatus);
    }

     /**
     * Get facebook auth token
     *
     * This endpoint is used to get the token from facebook auth endpoint.
     *
     * 
     *     
    */
  
     public function getFacebookAuth(Request $request){
      $access_token_url = 'https://graph.facebook.com/v2.3/oauth/access_token';
      $params = [
        'client_id'=> $request->clientId,
        'redirect_uri'=> $request->redirectUri,
        'client_secret'=> '5ded4ec1a24eaf69a001ab822495edff',
        'code'=> $request->code
      ];

        // Send an asynchronous request.
      $client = new GuzzleHttp\Client();
      $request = new \GuzzleHttp\Psr7\Request('GET', $access_token_url.'?code='.$request->code.'&client_id='.$request->clientId.'&client_secret=5ded4ec1a24eaf69a001ab822495edff&redirect_uri='.$request->redirectUri);
      $promise = $client->sendAsync($request)->then(function ($response) {
      $this->res =  $response->getBody();
      });
      $promise->wait();

      $reccc = json_decode($this->res,true);

      $access_token= $reccc['access_token'];
      echo json_encode(array('token'=>$access_token)); //Returns a token
  
    }


     /**
     * Get a user or register
     *
     * This endpoint is used to get a user using his social id or register the user if the social id is new. This request is authenticated.
     *
     * 
     * @return App/User
     *
    */

    public function get_user_data(Request $request){
     
            $authUser = User::where('social_id', $request->id)->first();          

           if($authUser){
                Auth::login($authUser, true);                
                $success['token'] =  $authUser->createToken('MyApp')->accessToken;
                return response()->json(['data'=>$authUser,'token'=>$success["token"]], $this->successStatus);
            }else{
              $picture = json_decode($request->picture,true);

              $user  = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'social_id' => $request->id,
                'password' => bcrypt('test'),
                 'social_name' => $request->name,
                'profile_image' => $picture['data']['url'],
                'registration_type'=>1

            ]);

              $success['token'] =  $user->createToken('MyApp')->accessToken;
              return response()->json(['data'=>$user,'token'=>$success["token"]], $this->successStatus);             
            }
        }



    
     /**
     * Get an authenticated user
     *
     * This endpoint is used to get a current logged in user. This request is authenticated.
     *
     * 
     * @return App/User
     *
    */
    public function userData()
    {
        $user = Auth::user();
        return response()->json(['message' => $user], $this->successStatus);
    }


    /**
     * Get practice question
     *
     * This endpoint is used to get questions. This request is authenticated.
     *
     * 
     * @return App/Questions
     *
     */
    public function getQuestion(){
        $question = Questions::with("instruction")->get();

        return response()->json(['data' => $question,"message"=>"successful"], $this->successStatus);
    }


    /**
     * Update Profile
     *
     * This endpoint is used to update a user profile. This request is authenticated.
     *
     * 
     * @return App/User
     *
     */

    public function updateProfile(Requests\ProfileRequest $request){

        $user = User::where('id', $request->user_id)->first();     

        $user->name = $request->name;
        $user->save();

        return response()->json(["data"=>$user,"message"=>"successful"], $this->successStatus);

    }


    public function edit_profile_picture(Requests $request){
       
        $imageName = 'file_'.time().'.'.$request->file->getClientOriginalExtension();
        $request->file->move(public_path('receipts/'), $imageName);

        $user = User::where('id', $request->user_id)->first(); 
        $user->profile_image = $imageName;
        $user->save();
        
        return response()->json(["data"=>$user,"message"=>"uploadeded successful"], $this->successStatus);

    }   

}