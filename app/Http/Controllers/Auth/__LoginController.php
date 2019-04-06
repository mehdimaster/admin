<?php

namespace App\Http\Controllers\Auth;

use App\City;
use App\Country;
use Validator;
use Request;
use Input;
use Auth;
use Cookie;
use Redirect;
use Session;
use App;
use URL;
use Plugin;
use \App\User;
use App\FlightTraveller;

class LoginController extends \App\Http\Controllers\Controller
{

    public function login()
    {
//        print_r($_POST);die();
        if(isset($_POST['lang'])){
            $lang = Input::get('lang');
        }else{
            $lang = 'fa';
        }
        if(isset($_POST['type']) && $_POST['type'] =='email'){
            $param =array(
                "lang"=>$lang,
                "email"=>Input::get("emailLogin"),
                "password"=>Input::get("passwordLogin")
            );
        }else{
            $mobileNumber = Input::get('country-code-mobile-login'). str_replace(config('mobileConfig.pattern'), '', Input::get('mobile'));
            $param =array(
                "lang"=>$lang,
                "email"=>$mobileNumber,
                "password"=>Input::get("passwordLogin")
            );
        }
        /*if(isset($_POST['emailLogin']) && !empty($_POST['emailLogin'])){
            $param =array(
                "lang"=>$lang,
                "email"=>Input::get("emailLogin"),
                "password"=>Input::get("passwordLogin")
            );
        }else{
            $mobileNumber = Input::get('country-code-mobile-login').Input::get('before-code-mobile-login').Input::get('middleware-code-mobile-login').Input::get('mobile-number-login');
            $param =array(
                "lang"=>$lang,
                "email"=>$mobileNumber,
                "password"=>Input::get("passwordLogin")
            );
        }*/


        /*$param =array(
            "email"=>Input::get("emailLogin"),
            "password"=>Input::get("passwordLogin")
        );*/

        $url = env('WEBSERVICE_USER_MANAGMENT_LOGIN');

        $resultLoginUser = App\Http\Controllers\CURL::init()->execute($url,$param)->response();

        if(json_decode($resultLoginUser)->code == '1010'){
            Session::put("tokenAPI" , json_decode($resultLoginUser)->token);
            if(!Plugin::isDigits(json_decode($resultLoginUser)->data->email)){
                $user = User::where('email',json_decode($resultLoginUser)->data->email)->first();
            }else{
                $user = User::where('mobile',json_decode($resultLoginUser)->data->email)->first();
            }

            Auth::login($user, true);
            //remember me
            if(Input::get('keepMe') == '1'){
                $cookie = (Cookie::forever('rememberMe', Input::all()));
                Cookie::queue($cookie);
            }
            $cookie = (Cookie::forever('userLogedin', true));
            Cookie::queue($cookie);

        }
        return $resultLoginUser;
    }

    public function loginx()
    {

        if (Request::isMethod('GET')) {
            return view('auth.login');
        }
       // var_dump($_POST);die();
        $rules = array(
            'emailLogin' => 'required',
            'passwordLogin' => 'required'
        );

        $data = Input::all();

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {

            echo 'not-valid';
            return;
        }
        $userData = array(
            'email' => Input::get('emailLogin'),
            'password' => Input::get('passwordLogin')
        );

        //var_dump($validator->errors()->getMessages());

        if (Auth::attempt($userData)) {
            $userId = Auth::user()->id;
            $user = User::find($userId);
            
            if (Input::get('type') == 'car') {
                return json_encode(array(
                        "user" => $user,
                        "type" => 'car'
                    ));
            }
            
            $cityName = '';
            $countryName = '';
            $countryCode = '';
            if($user->city_id != null || $user->city_id !=''){
                $city = City::find($user->city_id);
                if($city!= null) {
                    $cityName = $city->name;
                    if ($city->country_id != null || $city->country_id != '') {
                        $country = Country::find($city->country_id);
                        if($country->nicename != null || $country->nicename == '') {
                            $countryName = $country->nicename;
                            $countryCode = $country->phonecode;
                        }
                    }
                }
            }
            echo json_encode(array(
                    "userid" => $user->id,
                    "email" => $user->email,
                    "address" => $user->address,
                    "city" => $cityName,
                    "country" => $countryName,
                    "countryCode" =>'+'. $countryCode,
                    "phonenumber" => $user->phonenumber,
                    "firstName"=>$user->first_name,
                    "lastName"=>$user->last_name,
                    "login"=>'true',
                    "type"=>Input::get('type')
                ));


            /*if(Input::get('type') == 'hotel'){
                $userId = Auth::user()->id;
                $user = User::find($userId);
                echo json_encode(array(
                    "userid" => $user->id,
                    "login" => 'true',
                    "email" => $user->email,
                    "postCode" => $user->postal_code,
                    "phonenumber" => $user->phonenumber
                ));
                // echo '{"login":"true" ,"email":"'.Auth::user()->email.'"}';
            }*/
            /*if (Input::get('type') == 'website') {
                $userId = Auth::user()->id;
                $user = User::find($userId);
                $userName = $user->email;
                $traveller = FlightTraveller::where('default', 1)->where('user_id', $userId)->first();*/

                //remember me
                if(Input::get('keepMe') == 'true'){
                    $cookie = (Cookie::forever('rememberMe', Input::all()));
                    Cookie::queue($cookie);
                }
                

                /*if (! is_null($traveller)) {
                    $userName = $traveller->name;

                }*/

                $cookie = (Cookie::forever('userLogedin', true));
                Cookie::queue($cookie);
                
                Session::put('sessionUserId' , $userId);
                // Session::put('sessionUserName' , $userName);

                /*echo json_encode(array(
                    'type' => 'website',
                    'userName' => $userName
                ));*/

            /*} else if (Input::get('type') == 'userForm') {
                $userId = Auth::user()->id;
                $user = User::find($userId);
                $city = City::find($user->city_id);
                $country = Country::find($city->country_id);
                $userName = $user->email;
                $traveller = FlightTraveller::where('default', 1)->where('user_id', $userId)->first();

                if (! is_null($traveller)) {
                    $userName = $traveller->name;
                }
*/
                // Session::put('sessionUserName' , $userName);

                /*$travellersDrop = FlightTraveller::where('user_id',$userId)->get();*/
//                $data['travellers'] = $travellersDrop;
               // $data['travellers'] = $travellers;
                // Session::put('sessionUserId' , $userId);
                //echo $travellersDrop;
                /*echo json_encode(array(
                    "userid" => $user->id,
                    "email" => $user->email,
                    "address" => $user->address,
                    "city" => $city->name,
                    "country" => $country->nicename,
                    "countryCode" =>'+'. $country->phonecode,
                    "postCode" => $user->postal_code,
                    "phonenumber" => $user->phonenumber
                ));*/
                // return $json;
                //echo '<span style="color: green;">Successfuly logged in<span>';
                //return Redirect::back();
            
        } else {
            echo 'not-login';
        }

        //return Redirect::back();

    }


    public function checkLogin(){
        if(Auth::check()){
            $response = '{"login":"true" ,"email":"'.Auth::user()->email.'"}';
        }else{
            $response = '{"login":"false"}';
        }
        echo $response;
    }

    public function loginAdmin()
    {
        if(Auth::check()){
            if (strpos(app('Illuminate\Routing\UrlGenerator')->previous(), '/fa/') !== false) {
                return Redirect::to(url("fa/admin/dashboard"));
            }
            if (strpos(app('Illuminate\Routing\UrlGenerator')->previous(), '/en/') !== false) {
                return Redirect::to(url("en/admin/dashboard"));
            }

        }
        if(Request::Method() == 'GET'){

            return view('frontend.admin.login.login');
        }
        $lang = Input::get('lang');
        $rules = array(
            'userAdmin' => 'required',
            'passwordAdmin' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {

            return Redirect::to(url("$lang/admin/login"))->withErrors($validator)->withInput(Input::all())->with(['not valid'=>'Username or password is required!!!']);
        }
        $userData = array(
            'email' => Input::get('userAdmin'),
            'password' => Input::get('passwordAdmin')
        );

        if (Auth::attempt($userData)) {
            return Redirect::to(url("$lang/admin/dashboard"));
        }else{
            return Redirect::to(url("$lang/admin/login"))->withErrors($validator)->withInput(Input::all())->with(['not correct'=>'Username or password not correct!!!']);
        }
    }

    public function logout()
    {
        Cookie::forget('userLogedin');
        
        Auth::logout(); // log the user out of our application
//        Session::flush();
//        print_r();
        return Redirect::back();
//        return Redirect::to('/'); // redirect the user to the login screen
    }
}