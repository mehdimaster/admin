<?php

namespace App\Http\Controllers;

use App\CancellationLog;
use App\Events\RegisterUser;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResendCodeRequest;
use App\InboxMsg;
use App\Order;
use App\Traveller;
use App\VerificationCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Plugins;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Input;
use Session;
use Date;
use Image;
use App\FlightTraveller;
use App\UserRequestPassword;
use App\User;
use App\FlightItinerary;
use App\Payment;
use App\FlightPassenger;
use App\HotelDetail;
use App\HotelPassenger;
use App\HotelHistory;
use App\CountryWebservice;
use App\City;
use App\Country;
use Hash;
use Redirect;
use Plugin;
use Config;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    private $data;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user) {
                $fullUser = User::whereId($user->id)->with(['person', 'lastTraveller'])->first();
                $this->data['user'] = $fullUser;
            }
            return $next($request);
        });
    }

    protected function userUpdateValidator(array $data, $user_id)
    {
        return Validator::make($data, [
            'mobile' => 'nullable|required_without:email|unique:users,mobile,' . $user_id . ',id,deleted_at,NULL,active,1',
            'email' => 'nullable|required_without:mobile|email|unique:users,email,' . $user_id . ',id,deleted_at,NULL,active,1',
            'name' => 'max:255',
            'en_name' => 'max:255',
            'family' => 'max:255',
            'en_family' => 'max:255',
            'passport_number' => 'max:50',
            'national_code' => 'max:50',
            'dob' => 'required_with:month,day,year',
            'password' => 'nullable'
        ]);
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->action('HomeController@index');
        }
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $data['userName'] = explode('@', $user->email)[0];
        $traveller = FlightTraveller::where('default', 1)->where('user_id', $userId)->first();

        if (!is_null($traveller)) {
            $data['userName'] = $traveller->name;
        }
        $userFlightHistories = DB::table('flight_itineraries')->where('userId', $userId)->get();
//                         print_r($userFlightHistories);die();
        if (!empty($userFlightHistories)) {
            $data['flightHistory'] = [];
            foreach ($userFlightHistories as $userFlight) {
                $data['flightHistories'][$userFlight->uniqueId][] = $userFlight;
                $invoiceNumber = $userFlight->invoiceNumber;
                /////////////////////////////////////////////  For Test ///////////////////////////////////////////
                if ($userFlight->invoiceNumber == '') {
                    $invoiceNumber = '6640051275';
                }
//                 print_r($invoiceNumber);die();
                $Payment = Payment::where('invoice_number', $invoiceNumber)->first();
                $data['checkPay'][$userFlight->uniqueId]['checkPay'] = $Payment->status_id;
//                print_r($Payment->status_id);die();
//                $vaziatPayment =
                $flightPassengers = FlightPassenger::where('user_id', $userId)->where('payment_id', $Payment->id)->get();
//                 print_r($flightPassengers);
                $flightPassengersInfo = [];
                foreach ($flightPassengers as $passenger) {
                    $flightPassengersInfo[] = array(
                        'id' => $passenger->id,
                        'firstName' => $passenger->first_name,
                        'lastName' => $passenger->last_name
                    );
                } /* foreach : get passenger Flight */
                $data['passengers'][$userFlight->uniqueId]['passengers'] = $flightPassengersInfo;
            } /* end First Foreach */
        }/* end if (! empty($userFlightHistories))*/

        /*  Hotel  */

        $hotelPassenger = HotelPassenger::where('userId', $userId)->get();
        $arrPassenger = array();
        foreach ($hotelPassenger as $hotelPass) {
            // print_r($hotelPass);
            array_push($arrPassenger, $hotelPass->firstName . ' ' . $hotelPass->surname);

        }/*print_r($arrPassenger);die();*/
        $data['arrPassenger'] = $arrPassenger;
        $hotelDetail = HotelDetail::where('userId', $userId)->get();
        $groups = HotelDetail::groupBy('paymentId', 'userId')->having('userId', '=', $userId)->get();
        $data['hotelDetail'] = $hotelDetail;
        $data['group'] = $groups;

        $hotelHistory = HotelHistory::where('user_id', $userId)->get();
        $data['hotelHistory'] = $hotelHistory;
        $groupsss = HotelHistory::groupBy('paymentId', 'user_id')->having('user_id', '=', $userId)->get(['paymentId']);
        $statusId = Payment::where('user_id', Auth::user()->id)->get();
        $arrPayment = array();
        $arrStatus = array();
        foreach ($groupsss as $group) {
            // print_r($group->paymentId."     ");
            array_push($arrPayment, $group->paymentId);
        }
        foreach ($statusId as $status) {
            if (in_array($status->id, $arrPayment)) {
                array_push($arrStatus, $status->status_id);
            }
        }
        $data['paymentId'] = $arrPayment;
        $data['statusId'] = $arrStatus;
//        print_r($arrStatus);die();

        // echo "#############3";

        // print_r($groups);die();


        /*
                $hotelDetails = DB::table('hotel_histories')
                    ->join('hotel_details', 'hotel_details.id', '=', 'hotel_histories.hotelId')
                    ->join('hotel_passengers', 'hotel_passengers.paymentId', '=', 'hotel_histories.paymentId')
                    ->join('hotel_passengers', 'hotel_passengers.paymentId', '=', 'hotel_histories.paymentId')
                    ->select(
                        'hotel_histories.*',
                        'hotel_details.hotelName as hotelName',
                        'hotel_details.hotelStar as hotelStar',
                        'hotel_details.hotelAddress as hotelAddress',
                        'hotel_passengers.uniqueId as hotelUnique',
                        'hotel_passengers.firstName as firstName',
                        'hotel_passengers.surname as surname',
                        'hotel_passengers.isAdult as isAdult')->where('user_id',$userId)
                    ->get();
                    print_r($hotelDetails);die();*/
        // $data['hotelDetails'] = $hotelDetails;
        /*die();

        $hotelHistory = HotelHistory::where('user_id',$userId)->get();

        print_r($hotelPassenger);

        echo '####################################';
        print_r($hotelDetail);

        echo '####################################';
        print_r($hotelHistory);

        die();*/


        /*  Hotel  */


        $data['userTravellers'] = FlightTraveller::where('user_id', $userId)->get();
        $countryArr = Country::all();
        $data['countryArr'] = $countryArr;
        return view('frontend.user.profile', compact('data'));
    }

    /*
    * user registeration
    *
    */
//    public function register()
//{
//    if (Session::has('sessionUserId')){
//        return redirect()->action('HomeController@index');
//    }
//    /*when POST data for Register ( submit )*/
//    if ($_SERVER["REQUEST_METHOD"] == "POST") {
//        $data = Input::all();
//        $rules = array(
//            'email' => 'required|unique:users|email|min:7',
//            'password' => 'required|min:7',
//            'CaptchaCode' => 'required|captcha'
//
//        );
//        $validator = Validator::make($data, $rules);
//
//        $response['email'] = false;
//        $response['password'] = false;
//
//        if ($validator->passes()) {
//            $randomConfirmRegister = Plugin::generateRandomString();
//            $user = new User();
//            $user->email = $data['email'];
//            $user->password = Hash::make($data['password']);
//            $user->active = 0;
//            $user->verification_code = $randomConfirmRegister;
//            $result = $user->save();
//            Plugin::sendMail(Input::get('email'),$randomConfirmRegister,'Confirm Registration');
//            if ($result) {
//                $data['status'] = 'registered';
//                /*Call WebService*/
//                $paramAddUser ='	{
//                                    "PASSWORD": "'.$data['password'].'",
//                                    "GENDER": "",
//                                    "IMAGEPROFILE": "Image",
//                                    "PHONENUMBER": "",
//                                    "TGUSER":
//                                            {
//                                            "FIRSTNAME":"",
//                                            "LASTNAME":"",
//                                            "NATIONALCODE":"",
//                                            "PASSPORTNUMBER":"",
//                                            "MOBILE":"",
//                                            "EMAIL":"'.$data['email'].'"
//                                            }
//                                    }
//                                ';
//                $ch = curl_init();
//                $url = env('WEBSERVICE_URL_ADD_USER');
//                curl_setopt($ch, CURLOPT_URL, $url);
//                curl_setopt($ch, CURLOPT_POSTFIELDS, $paramAddUser);
//                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//                $resultAddUser = curl_exec($ch);
//                $userId =json_decode($resultAddUser)->USERID;
//                if($userId != null){
//                    $updateUserIdWebService = User::where('id', $user->id)->update(['userIdWebservice'=>$userId]);
//                    if($updateUserIdWebService){
//                        return view('frontend.user.register', compact('data'));
//                    }else{
//                        /*delete row user from table*/
//                        $data['status'] = 'error';
//                        return view('frontend.user.register', compact('data'));
//                    }
//                }
//                /*Call WebService*/
//                return view('frontend.user.register', compact('data'));
//            }
//        }
//        $data['status'] = 'error';
//        return view('frontend.user.register', compact('data'));
//    }
//    /* IF Request == Get */
//    $data['status'] = 'get';
//
//    return view('frontend.user.register', compact('data'));
//}

    /*
    * user account details
    * update email and password
    */
    public function ajaxUpdateUserAccountDetails()
    {
        $data = Input::all();
        $rules = array(
            'email' => 'required|email|min:7',
            'password' => 'required|min:7'
        );
        $validator = Validator::make($data, $rules);
        $response['email'] = false;
        $response['password'] = false;
        if ($validator->passes()) {
            $user['email'] = $data['email'];
            $user['password'] = Hash::make($data['password']);
            $result = User::where('id', Session::get('sessionUserId'))->update($user);
            $userIdWebservice = User::where('id', Session::get('sessionUserId'))->get(['userIdWebservice']);

            if ($result) {
                $response['status'] = 'registered';
                /*Call WebService*/
                $paramUpdateUser = '	{
                                        "USERID":' . $userIdWebservice . ',
                                        "PASSWORD": "' . $data['password'] . '",
                                        "GENDER": "",
                                        "IMAGEPROFILE": "",
                                        "PHONENUMBER": "",
                                        "TGUSER":
                                            {
                                                "FIRSTNAME":"",
                                                "LASTNAME":"",
                                                "NATIONALCODE":"",
                                                "PASSPORTNUMBER":"",
                                                "MOBILE":"",
                                                "EMAIL":"' . $data['email'] . '"
                                            }
                                    }
                                ';
                $ch = curl_init();
                $url = env('WEBSERVICE_URL_UPDATE_USER');
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $paramUpdateUser);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $resultUpdateUser = curl_exec($ch);
                $userId = json_decode($resultUpdateUser)->USERID;
                if ($userId != 0) {
                    echo json_encode($response);
                    return;
                } else {
                    $response['status'] = 'error';
                    $response['field'][] = 'main';
                    $response['message'] = 'Oops, Registration failed.';
                    echo json_encode($response);
                    return;
                }

                /*Call WebService*/
            }

            $response['status'] = 'error';
            $response['field'][] = 'main';
            $response['message'] = 'Oops, Registration failed.';
            echo json_encode($response);
            return;
        }

        if (@$validator->errors()->getMessages()['email']) {
            $response['status'] = 'error';
            $response['email'] = true;
            $response['message']['email'] = $validator->errors()->getMessages()['email'][0];
        }

        if (@$validator->errors()->getMessages()['password']) {
            $response['status'] = 'error';
            $response['password'] = true;
            $response['message']['password'] = $validator->errors()->getMessages()['password'][0];
        }

        echo json_encode($response);
        return;
    }

    public function ajaxGetUserById()
    {
        $data = Input::all();
        $result = User::where('id', $data['id'])->first();
//        print_r($result);die();
        /*Call WebService*/
        $paramGetUser = '{"Id":' . $result->userIdWebservice . '}';
        $ch = curl_init();
        $url = env('WEBSERVICE_URL_GET_USER');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramGetUser);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $resultGetUser = curl_exec($ch);
//        print_r($paramGetUser);die();
        $userId = json_decode($resultGetUser)->USERID;
        echo json_encode($result);
        return;
        if ($userId != 0) {
            $response['email'] = $result->email;
            $response['password'] = $result->password;
            echo json_encode($response);
        }
        /*Call WebService*/
    }

    public function confirmAccount($code)
    {
        $user = User::where('verification_code', $code)->where('active', 0)->update(['active' => 1]);
        $user = User::where('verification_code', $code)->first();
        if ($user) {
            Auth::login($user, true); /* Login With User Detail */
            if (strlen($code) == 5) { /* IF register account in Page Passenger Detail */
                return 'confirm';
            } else {
                return Redirect::to(url("/"));
            }
        } else {
            return 'wrong';
        }
    }

    public function resetPassword()
    {
        if (Request::Method() == 'POST') {
            $lang = Input::get('lang');
            $validator = Validator::make(Input::all(), [
                "email" => "required"
            ]);
            if ($validator->fails()) {
                echo "not required";
                return;
            }
            $checkMail = User::where('email', Input::get('email'))->first();
            if ($checkMail != null) {
                $randomConfirmResetPass = Plugin::generateRandomString();
                $requestPass = new UserRequestPassword();
                $requestPass->user_id = $checkMail->id;
                $requestPass->resetCode = $randomConfirmResetPass;
                $requestPass->save();
                Plugin::sendMail(Input::get('email'), $randomConfirmResetPass, 'Reset Password');
                echo "send";
                return;
            } else {
                echo "no register";
                return;
            }

        } else {
            return view('frontend.user.resetPassword');
        }
    }

    public function changePassword($lang = null, $code = null)
    {

        $userRequestPassword = UserRequestPassword::where('resetCode', $code)->first();
        $getUser = User::find($userRequestPassword->user_id);
        if (Request::Method() == 'GET') { /* When Click On Link Mail Reset Password */
            if ($userRequestPassword != null) {
                return view('frontend.user.changePassword', compact('code'));
            } else {
                return Redirect::to(url("$lang"));
            }
        } else { /* When Click Submit Form Change Password */
            if ($_POST['re-password'] == $_POST['password']) {
                $repass = $_POST['re-password'];
                $pass = Hash::make($_POST['password']);
                $user = User::where('id', $userRequestPassword->user_id)->update(
                    ['re_password' => $repass,
                        'password' => $pass]);
                if ($user) {
                    $userLogin = User::find($userRequestPassword->user_id);
                    /*Call WebService*/
                    $paramChangePasswordUser = '{"USERID":' . $getUser->userIdWebserivice . ',"OldPassword":"' . $getUser->re_password . '","NewPassword":"' . $pass . '"}';
                    $ch = curl_init();
                    $url = env('WEBSERVICE_URL_CHANGE_PASSWORD_USER');
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $paramChangePasswordUser);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $resultChangePasswordUser = curl_exec($ch);
                    $userId = json_decode($resultChangePasswordUser)->USERID;
                    if ($userId != 0) {
                        Auth::login($userLogin, true);
                        echo "change";
                        return;
                    } else {
                        echo "not match";
                        return;
                    }
                    /*Call WebService*/
                    /*Auth::login($userLogin, true);
                echo "change";
                return;*/
                }
            } else {
                echo "not match";
                return;
            }
        }
    }

    public function checkMail()
    {
        $validator = Validator::make(Input::all(), [
            "email" => "required|unique:users|email"
        ]);
        if ($validator->fails()) {
            echo 'emailExist';
        }
    }

    public function updateAccount()
    {
        if (Input::get('completeDetailFlight') == 'flight') {
            $validator = Validator::make(Input::all(), [
                'user-update-firstname' => 'required',
                'user-update-lastname' => 'required',
                'user-update-bd-day' => 'required',
                'user-update-bd-moon' => 'required',
                'user-update-bd-year' => 'required',
                'phonenumber' => 'required'
            ]);

            if ($validator->fails()) {
                echo 'not required';
                return;
            }
            $userId = Auth::user()->id;
            $user = User::where('id', $userId)->update(['first_name' => Input::get('user-update-firstname'),
                'last_name' => Input::get('user-update-lastname'),
                'phonenumber' => Input::get('phonenumber'),
                'dob' => Input::get('user-update-bd-year') . '-' . Input::get('user-update-bd-moon') . '-' . Input::get('user-update-bd-day')
            ]);
            echo "update";
            return;
//            if($user){
//                /*Call WebService*/
//                $paramUpdateUser ='	{
//                                        "USERID":'.Auth::user()->userIdWebservice.',
//                                        "PASSWORD": "'.Auth::user()->re_password.'",
//                                        "GENDER": "",
//                                        "IMAGEPROFILE": "",
//                                        "PHONENUMBER": "'.Input::get('user-update-phone-number').'",
//                                        "TGUSER":
//                                            {
//                                                "FIRSTNAME":"'.Input::get('user-update-firstname').'",
//                                                "LASTNAME":"'.Input::get('user-update-lastname').'",
//                                                "NATIONALCODE":"",
//                                                "PASSPORTNUMBER":"",
//                                                "MOBILE":"'.Input::get('phonenumber').'",
//                                                "EMAIL":"'.Auth::user()->email.'"
//                                            }
//                                    }
//                                ';
//                $ch = curl_init();
//                $url = env('WEBSERVICE_URL_UPDATE_USER');
//                curl_setopt($ch, CURLOPT_URL, $url);
//                curl_setopt($ch, CURLOPT_POSTFIELDS, $paramUpdateUser);
//                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                $resultUpdateUser = curl_exec($ch);
//                $userId =json_decode($resultUpdateUser)->USERID;
//                if($userId != 0){
//                    echo "update";
//                    return;
//                }else{
//                    echo "wrongs";
//                    return;
//                }
//
//                /*Call WebService*/
//
//            }else{
//                echo "wrong";
//                return;
//            }
        }
        if (Input::get("formConfirm") == 'confirmDetail') {
            // print_r($_POST);die();
            $validator = Validator::make(Input::all(), [
                'firstname' => 'required',
                'lastname' => 'required',
                'address' => 'required',
                'phone' => 'required'
            ]);

            if ($validator->fails()) {
                echo 'notRequired';
                return;
            }
            $userId = Auth::user()->id;
            $user = User::where('id', $userId)->update(['first_name' => Input::get('firstname'),
                'last_name' => Input::get('lastname'),
                'phonenumber' => Input::get('phone'),
                'address' => Input::get('address')
            ]);
            echo 'update';
            return;
//            if($user){
//                /*Call WebService*/
//                $paramUpdateUser ='	{
//                                        "USERID":'.Auth::user()->userIdWebservice.',
//                                        "PASSWORD": "'.Auth::user()->re_password.'",
//                                        "GENDER": "",
//                                        "IMAGEPROFILE": "",
//                                        "PHONENUMBER": "'.Input::get('phone').'",
//                                        "TGUSER":
//                                            {
//                                                "FIRSTNAME":"'.Input::get('firstname').'",
//                                                "LASTNAME":"'.Input::get('lastname').'",
//                                                "NATIONALCODE":"",
//                                                "PASSPORTNUMBER":"",
//                                                "MOBILE":"'.Input::get('phone').'",
//                                                "EMAIL":"'.Auth::user()->email.'"
//                                            }
//                                    }
//                                ';
//                $ch = curl_init();
//                $url = env('WEBSERVICE_URL_UPDATE_USER');
//                curl_setopt($ch, CURLOPT_URL, $url);
//                curl_setopt($ch, CURLOPT_POSTFIELDS, $paramUpdateUser);
//                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//                $resultUpdateUser = curl_exec($ch);
//                $userId =json_decode($resultUpdateUser)->USERID;
//                if($userId != 0){
//                    echo "update";
//                    return;
//                }else{
//                    echo "wrong";
//                    return;
//                }
//
//                /*Call WebService*/
//
//            }else{
//                echo "wrong";
//                return;
//            }


        }
        $validator = Validator::make(Input::all(), [
            'firstname' => 'required',
            'lastname' => 'required',
//            'dob'=>'required',
            'mobile' => 'required'
        ]);

        if ($validator->fails()) {
            echo 'not required';
            return;
        }
        $userId = Auth::user()->id;
        $user = User::where('id', $userId)->update(['first_name' => Input::get('firstname'),
            'last_name' => Input::get('lastname'),
            'mobile' => Input::get('mobile')
        ]);
        if ($user) {
            /*Call WebService*/
            $paramUpdateUser = '	{
                                        "USERID":' . Auth::user()->userIdWebservice . ',
                                        "PASSWORD": "' . Auth::user()->re_password . '",
                                        "GENDER": "",
                                        "IMAGEPROFILE": "",
                                        "PHONENUMBER": "' . Input::get('mobile') . '",
                                        "TGUSER":
                                            {
                                                "FIRSTNAME":"' . Input::get('firstname') . '",
                                                "LASTNAME":"' . Input::get('lastname') . '",
                                                "NATIONALCODE":"",
                                                "PASSPORTNUMBER":"",
                                                "MOBILE":"' . Input::get('mobile') . '",
                                                "EMAIL":"' . Auth::user()->email . '"
                                            }
                                    }
                                ';
            $ch = curl_init();
            $url = env('WEBSERVICE_URL_UPDATE_USER');
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramUpdateUser);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $resultUpdateUser = curl_exec($ch);
            $userId = json_decode($resultUpdateUser)->USERID;
            if ($userId != 0) {
                echo "update";
                return;
            } else {
                echo "wrong";
                return;
            }

            /*Call WebService*/

        } else {
            echo "wrong";
            return;
        }
    }

    public function ajaxGetUserInfo()
    {

        if (Auth::check()) {

            if (Input::get('flag') == 1) {

                $userId = Auth::user()->id;
                $user = array(User::find($userId));
                $countries = CountryWebservice::get();

                $countryCollection = array();

                foreach ($countries as $country) {
                    array_push($countryCollection, array('country_id' => $country->country_id, 'name' => $country->name));
                }

                $data['userinfo'] = $user;
                $data['countries'] = $countryCollection;

                return json_encode($data);

            } elseif (Input::get('flag') == 2) {
                $cities = City::where('country_id', Input::get('country_id'))->get();

                $cityCollection = array();

                foreach ($cities as $city) {
                    array_push($cityCollection, array('city_id' => $city->city_id, 'name' => $city->name));
                }

                $data['cities'] = $cityCollection;

                return json_encode($data);
            }

        } else {
            return 'not login';
        }

    }

    public function googleLogin(Request $request)
    {
        $google_redirect_url = url('oauth2callback');
        $config = Config::get('packageConfig.app');

        $gClient = new \Google_Client();
        $gClient->setApplicationName('panel');
        $gClient->setClientId(Config::get("packageConfig.googleAuth." . $config . ".setClientId"));
        $gClient->setClientSecret(Config::get("packageConfig.googleAuth." . $config . ".setClientSecret"));
        $gClient->setRedirectUri($google_redirect_url);
        $gClient->setDeveloperKey(Config::get("packageConfig.googleAuth." . $config . ".setDeveloperKey"));
        $gClient->setScopes(array(
            'https://www.googleapis.com/auth/plus.me',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ));

        $google_oauthV2 = new \Google_Service_Oauth2($gClient);

        if (Input::get('code')) {

            $gClient->authenticate($request->get('code'));
            Session::put('token', $gClient->getAccessToken());
        }
        if (Session::get('token')) {
            $gClient->setAccessToken($request->session()->get('token'));
        }
        if ($gClient->getAccessToken()) {
            //For logged in user, get details from google using access token
            $guser = $google_oauthV2->userinfo->get();
            // print_r($guser);die();

            Session::put('name', $guser['name']);
            $user = User::where('email', $guser['email'])->first();
            $languageCookie = \Request::cookie('language');
            if (empty($languageCookie)) {
                $lang = 'fa'; // this is default lang
            } else {
                $lang = $languageCookie;
            }
            if ($user != null) {
                $param = array(
                    "email" => $guser['email'],
                    "googleAuth" => "1"
                );
                $url = env('WEBSERVICE_USER_MANAGMENT_LOGIN');
                $resultLoginUser = CURL::init()->execute($url, $param)->response();

                if (json_decode($resultLoginUser)->code == '1010') {
                    Session::put("tokenAPI", json_decode($resultLoginUser)->token);
                    Auth::login($user, true);
                    return Redirect::to(url("$lang/user/contact-detail"));
                }
            } else {
                $randomPassword = Plugin::quickRandomWithoutZero(8);
                $param = array(
                    "email" => $guser['email'],
                    "password" => $randomPassword,
                    "googleAuth" => "1"
                );
                $url = env('WEBSERVICE_USER_MANAGMENT_REGISTER');
                $resultRegisterUser = CURL::init()->execute($url, $param)->response();

                if (json_decode($resultRegisterUser)->code == '1010') {
                    $param = array(
                        "email" => $guser['email'],
                        "password" => $randomPassword,
                    );
                    $url = env('WEBSERVICE_USER_MANAGMENT_LOGIN');
                    $resultLoginUser = CURL::init()->execute($url, $param)->response();
                    $gender = 1;
                    if (json_decode($resultLoginUser)->code == '1010') {
                        Session::put("tokenAPI", json_decode($resultLoginUser)->token);

                        if ($guser['gender'] != '') {
                            if ($guser['gender'] == 'male') {
                                $gender = 1;
                            } else {
                                $gender = 0;
                            }
                        }

                        $userUpdateGoogle = User::where('email', $guser['email'])->update([
                            'first_name' => $guser['givenName'],
                            'last_name' => $guser['familyName'],
                            'gender' => $gender
                        ]);
                        $user = User::where('email', $guser['email'])->first();

                        $travellerDefault = Traveller::where('user_id', $user->id)
                            ->where('defualt_traveller', 1)
                            ->get();

                        if (count($travellerDefault) > 0) {
                            Traveller::where('user_id', $user->id)
                                ->where('defualt_traveller', 1)->update(['defualt_traveller' => 0, 'active' => 0]);

                        }
                        $traveller = new Traveller();
                        $traveller->user_id = $user->id;
                        $traveller->first_name_en = $user->first_name;
                        $traveller->last_name_en = $user->last_name;
                        $traveller->first_name_fa = $user->fa_first_name;
                        $traveller->last_name_fa = $user->fa_last_name;
                        $traveller->gender = $user->gender;
                        $traveller->nationality_code = $user->national_code;
                        $traveller->passport_number = $user->passport_number;
                        $traveller->birth_date = $user->dob;
                        $traveller->active = 1;
                        $traveller->defualt_traveller = 1;
                        $traveller->save();
                        Auth::login($user, true);
                        return Redirect::to(url("$lang/user/contact-detail"));
                    }
                }
            }
        } else {
            //For Guest user, get google login url
            $authUrl = $gClient->createAuthUrl();
            return redirect()->to($authUrl);
            // return Redirect::to(url("/"));
        }
    }

    public function userRegisterValidator(array $data, $registerType)
    {
        if ($registerType == "registerEmail") {
            return Validator::make($data, [
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);
        } elseif ($registerType == "registerMobile") {
            return Validator::make($data, [
                'email' => 'required',
                'password' => 'required|min:6'
            ]);
        } elseif ($registerType == "fastRegisterEmail") {
            return Validator::make($data, [
                'email' => 'required|email',
            ]);
        } elseif ($registerType == "fastRegisterMobile") {
            return Validator::make($data, [
                'email' => 'required',
            ]);
        }
    }

    public function register(RegisterRequest $request, $lang)
    {
        if (Session::has('sessionUserId')) {
            return redirect()->action('HomeController@index');
        }

        $npw = $request->get('npw');
        $email = $request->get('email-mobile-sign-up');
        $mobile = $request->get('mobile');
        $param = [
            "lang" => $lang,
            "email" => $email,
            "mobile" => $mobile,
            "password" => $request->get("password-sign-up")
        ];

        if (isset($npw) && $npw == '1') {
            unset($param['password']);
            $param['npw'] = '1';
        }

        if (!isset($email) && empty($email)) {
            unset($param['email']);
        }


        $url = env('WEBSERVICE_USER_MANAGMENT_REGISTER');
        $resultRegisterUser = CURL::init()->execute($url, $param)->response();

        return $resultRegisterUser;
    }

    public function verifyRegister($lang, Request $request)
    {
        $param = array(
            "lang" => $lang,
            "verifyCode" => $request->get("verification-code")
        );

        $url = env('WEBSERVICE_USER_MANAGMENT_VERIFY');

        $resultVerifyrUser = CURL::init()->execute($url, $param)->response();

        $user = json_decode($resultVerifyrUser);

        if (is_object($user)) {
            if ($user->code == '1010') {
                $userDetail = User::find($user->userId);
                Auth::login($userDetail, true);
            }
        }

        return $resultVerifyrUser;
    }

    public function resendVerify($lang, ResendCodeRequest $request)
    {
        $param = ['lang' => $lang];
        $param = array_merge($param, $request->all());

        $url = env('WEBSERVICE_USER_MANAGMENT_RESEND_VERIFY');
        $resultResendVerify = CURL::init()->execute($url, $param)->response();

        return $resultResendVerify;
    }

    protected function userPasswordValidator($data)
    {
        return Validator::make($data, [
            'current-password' => 'required',
            'new-password' => 'required|same:new-password',
            'new-password-confirmation' => 'required|same:new-password',
        ]);
    }

    public function forgetPasswordValidator($data)
    {
        return Validator::make($data, [
            'password' => 'required|confirmed|min:6',
        ]);
    }

    public function getUserPassword()
    {
        $data = $this->data;
        $data['url'] = 'user/update-password';
        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');
        if ($data['user']->avatar != null && $data['user']->avatar != '') {
            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
            }
        }
        return view('frontend.user.updatePassword', compact('data'));
    }

    public function updateUserPassword(Request $request)
    {
        $request_data = $request->all();

        $this->userPasswordValidator($request->all())->validate();
        $user_id = Auth::User()->id;
        $current_password = User::where('id', $user_id)->first();
        $current_password = $current_password->password;

        if (Hash::check($_POST['current-password'], $current_password)) {

            $user = User::find($user_id);
            $user->password = Hash::make($request_data['new-password']);;
            $user->save();
            $success = true;
        } else {
            $success = false;
        }
        if ($success) {
            return response()->json(['status' => 'success', 'message' => 'success']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'error']);
        }
    }

    public function forgotPassword($lang, Request $request)
    {
        $param = array(
            "lang" => $lang,
            "email" => $request->get('email-forgot-password'),
            "mobile" => $request->get('country-code-mobile-forgot') . str_replace(config('mobileConfig.pattern'), '', $request->get('mobile'))
        );

        if (isset($request->type) && $request->type == 'email') {
            unset($param['mobile']);
        } else {
            unset($param['email']);
        }

        $url = env('WEBSERVICE_USER_MANAGMENT_FORGOT_PASSWORD');
        $result = CURL::init()->execute($url, $param)->response();

        $data = json_decode($result);

        if (isset($data->userId)) {
            $request->session()->put('forgetPassword.userId', encrypt($data->userId));
            unset($data->userId);
        }

        return json_encode($data);
    }

    public function confirmVerifyCode($lang, Request $request)
    {
        $param = [
            'lang' => $lang,
            'verify-code' => $request->get('verify-code'),
            'user-id' => decrypt(session('forgetPassword.userId')),
        ];

        $url = env('WEBSERVICE_USER_MANAGMENT_CONFIRM_VERIFYCODE');
        $result = CURL::init()->execute($url, $param)->response();

        $data = json_decode($result);

        if (isset($data->token)) {
            $request->session()->put('forgetPassword.token', encrypt($data->token));
            unset($data->token);
        }

        return json_encode($data);
    }

    public function setNewPassword($lang, Request $request)
    {
        $this->forgetPasswordValidator($request->all())->validate();

        $param = [
            'lang' => $lang,
            'password' => $request->get('password'),
            'user-id' => decrypt(session('forgetPassword.userId')),
            'token' => decrypt(session('forgetPassword.token')),
        ];

        $url = env('WEBSERVICE_USER_MANAGMENT_SET_NEW_PASSWORD');
        $result = CURL::init()->execute($url, $param)->response();

        $data = json_decode($result);

        if (isset($data->code)) {
            if ($data->code == '1000') {
                return response()->json($data, 422);
            }
        }

        session()->forget('forgetPassword.token');

        return json_encode($data);
    }

    public function contactDetail($lang)
    {
        $data = $this->data;
        $data['countries'] = \App\Country::all();
        $data['url'] = 'user/contact-detail';
        $data['defaultStatus'] = 0;

        $countryName = null;
        if ($data['user']->lastTraveller && $data['user']->lastTraveller->nationality) {
            $country = Country::where('iso3', $data['user']->lastTraveller->nationality)->first();

            if ($country) {
                if ($lang == 'fa') {
                    $countryName = ['id' => $country->id, 'name' => $country->faname, 'iso' => $country->iso3];
                } else {
                    $countryName = ['id' => $country->id, 'name' => $country->nicename, 'iso' => $country->iso3];
                }
            }
        }

        $data['countryName'] = $countryName;

        if (isset($data['user']['lastTraveller'])) {
            $data['defaultStatus'] = $data['user']['lastTraveller']['defualt_traveller'];
        }

        return view('frontend.user.contactDetail', compact('data'));
    }

    public function newFunctionForUpdateUser(Request $request, $lang)
    {
        $user = Auth::user();
        $person = $user->person();

        $dob = null;
        $mobile = null;
        $en_name = $request->input('first-name-en');
        $en_family = $request->input('last-name-en');
        $name = $request->input('first-name-fa');
        $family = $request->input('last-name-fa');
        $gender_id = $request->input('gender');
        $national_code = $request->input('national-code');
        $passport_number = $request->input('passport-number');
        $day = $request->input('day');
        $month = $request->input('month');
        $year = $request->input('year');
        $cc = $request->input('country-code-mobile');
        $bc = $request->input('before-code-mobile');
        $mc = $request->input('middleware-code-mobile');
        $mb = $request->input('mobile-number');
        $dateType = $request->input('dateTypeBirthDay');
        $email = $request->input('email');
        $mobile = $request->input('mobile');
        $countryCode = $request->input('country');
        $default = $request->input('default');
        if ($day && $month && $year && $dateType) {
            if ($dateType == 'miladi') {
                $dob = $year . '-' . $month . '-' . $day;
            } else {
                $date = $year . '-' . $month . '-' . $day;
                $dob = Date::ConvertShamsiToMiladiWithDash($date);
            }
        }

        if ($cc && $bc && $mc && $mb) {
            $mobile = $cc . $bc . $mc . $mb;
        }

        if ($user->email) {
            $email = $user->email;
            $request->merge(['email' => $email]);
        }

        if ($user->mobile) {
            $mobile = $user->mobile;
        }

        $request->merge(['name' => $name, 'en_name' => $en_name, 'dob' => $dob, 'mobile' => $mobile,
            'family' => $family, 'en_family' => $en_family,
            'gender_id' => $gender_id, 'national_code' => $national_code, 'passport_number' => $passport_number
        ]);

        $this->userUpdateValidator($request->all(), $user->id)->validate();

        $personData = $request->only(['name', 'en_name', 'family', 'en_family', 'dob', 'national_code', 'passport_number', 'gender_id']);

        $currentUser = $user;

        DB::beginTransaction();
        try {
            $person->update($personData);

            $userData = [
                'update_user_id' => $currentUser ? $currentUser->id : null
            ];

            if (!$user->email) {
                $userData = array_add($userData, 'email', $request->input('email'));
            } else {
                $email = $user->email;
            }

            if (!$user->mobile) {
                $userData = array_add($userData, 'mobile', $request->input('mobile'));
            } else {
                $mobile = $user->mobile;
            }

            $user->update($userData);
            $countryName = $this->saveTraveller($personData, $currentUser, $countryCode, $default);
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        }

        $finalDOB = '';
        if ($dob) {
            if (\App::getLocale() == 'fa') {
                $finalDOB = str_replace('/', '-', Date::ConvertMiladiToShamsiWithDash($dob));
            } else {
                $finalDOB = $dob;
            }
        }

        if ($success) {

            return json_encode([
                'result' => [
                    'firstNameEn' => $en_name ? $en_name : '',
                    'lastNameEn' => $en_family ? $en_family : '',
                    'firstNameFa' => $name ? $name : '',
                    'lastNameFa' => $family ? $family : '',
                    'nationalCode' => $national_code ? $national_code : '',
                    'passportNumber' => $passport_number ? $passport_number : '',
                    'gender' => $gender_id ? $gender_id : 0,
                    'mobile' => $mobile ? $mobile : '',
                    'email' => $email ? $email : '',
                    'dob' => $finalDOB,
                    'country' => $countryName ? $countryName : ''
                ],
                'status' => 'success',
                'code' => 1
            ]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'error']);
        }
    }

    protected function saveTraveller($personData, $currentUser, $countryCode, $default)
    {

        $countryName = null;
        $travellerDefault = Traveller::where('user_id', $currentUser->id)
            ->where('defualt_traveller', 1)
            ->get();

        if (count($travellerDefault) > 0) {
            Traveller::where('user_id', $currentUser->id)
                ->where('defualt_traveller', 1)->update(['defualt_traveller' => 0, 'active' => 0]);
        }
        $traveller = new Traveller();
        $traveller->user_id = $currentUser->id;
        $traveller->first_name_en = $personData['en_name'];
        $traveller->last_name_en = $personData['en_family'];
        $traveller->first_name_fa = $personData['name'];
        $traveller->last_name_fa = $personData['family'];
        $traveller->gender = $personData['gender_id'];
        if ($countryCode) {
            $lang = \App::getLocale();
            $da = Country::whereId($countryCode)->first();
            if ($da) {
                if ($lang == 'fa') {
                    $countryName = $da->faname;
                } else {
                    $countryName = $da->name;
                }
                $traveller->nationality = $da->iso3;
            }
        }
        $traveller->nationality_code = $personData['national_code'];
        $traveller->passport_number = $personData['passport_number'];
        $traveller->birth_date = $personData['dob'];
        $traveller->active = 1;
        $traveller->defualt_traveller = $default ? 1 : 0;
        $traveller->save();

        return $countryName;
    }

//    public function updateUser()
//    {
//        $user = Auth::user();
//
//
//        $year = Input::get('year');
//        $month = ((int)Input::get('month') < 10) ? '0' . Input::get('month') : Input::get('month');
//        $day = ((int)Input::get('day') < 10) ? '0' . Input::get('day') : Input::get('day');
//        if (!empty($year) && !empty($month) && !empty($day)) {
//            $date = $year . '-' . $month . '-' . $day;
//            if (Input::get('dateTypeBirthDay') == 'jalali') {
//                $date = $year . '/' . $month . '/' . $day;
//                $date = Date::ShamsiToMiladi($date);
//            }
//        } else {
//            $date = '';
//        }
//
//        if (!empty(Input::get('first-name-en'))) {
//            $user->first_name = Input::get('first-name-en');
//        }
//
//        if (!empty(Input::get('last-name-en'))) {
//            $user->last_name = Input::get('last-name-en');
//        }
//
//        if (!empty(Input::get('first-name-en'))) {
//            $user->fa_first_name = Input::get('first_name_fa');
//        }
//
//        if (!empty(Input::get('first-name-en'))) {
//            $user->fa_last_name = Input::get('last-name-fa');
//        }
//
//        if (!empty(Input::get('national_code'))) {
//            $user->national_code = Input::get('national_code');
//        }
//
//        if (!empty(Input::get('passport-number'))) {
//            $user->passport_number = Input::get('passport-number');
//        }
//
//        if (!empty(Input::get('gender'))) {
//            $user->gender = Input::get('gender');
//        }
//
//        if (!empty(Input::get('mobile'))) {
//            if ($user->mobile == 0 || $user->mobile == null || $user->mobile == '') {
//                $user->mobile = Input::get('mobile');
//            }
//        }
//
//        if (!empty(Input::get('email'))) {
//            if ($user->email == null || $user->email == '') {
//                $user->email = Input::get('email');
//            }
//        }
//
//        if (!empty($date)) {
//            if (Plugin::validateDate($date) == 1) {
//                $user->dob = $date;
//            }
//        }
//        if (!empty(Input::get('country'))) {
//            $countryId = 1;
//        } else {
//            $countryId = Input::get('country');
//        }
//
//
//        $country = Country::find((int)$countryId);
//
//        if ($country) {
//            $countryName = (\App::getLocale() == 'fa') ? $country->faname : $country->nicename;
//            $user->country_id = $countryId;
//        }
//
//
//        $user->save();
//
//
//
//
//        $travellerDefault = Traveller::where('user_id', Auth::user()->id)
//            ->where('defualt_traveller', 1)
//            ->get();
//
//        if (count($travellerDefault) > 0) {
//            Traveller::where('user_id', Auth::user()->id)
//                ->where('defualt_traveller', 1)->update(['defualt_traveller' => 0, 'active' => 0]);
//
//        }
//        $traveller = new Traveller();
//        $traveller->user_id = Auth::user()->id;
//        $traveller->first_name_en = $user->first_name;
//        $traveller->last_name_en = $user->last_name;
//        $traveller->first_name_fa = $user->fa_first_name;
//        $traveller->last_name_fa = $user->fa_last_name;
//        $traveller->gender = $user->gender;
//        if (!empty($user->country_id)) {
//            $traveller->nationality = Country::find($user->country_id)->iso3;
//        }
//        $traveller->nationality_code = $user->national_code;
//        $traveller->passport_number = $user->passport_number;
//        $traveller->birth_date = $user->dob;
//        $traveller->active = 1;
//        $traveller->defualt_traveller = 1;
//        $traveller->save();
//
//        return json_encode([
//            'result' => [
//                'firstNameEn' => $user->first_name,
//                'lastNameEn' => $user->last_name,
//                'firstNameFa' => $user->fa_first_name,
//                'lastNameFa' => $user->fa_last_name,
//                'nationalCode' => $user->national_code,
//                'passportNumber' => $user->passport_number,
//                'gender' => $user->gender,
//                'mobile' => $user->mobile,
//                'email' => $user->email,
//                'dob' => (\App::getLocale() == 'fa') ? str_replace('/', '-', Date::ConvertMiladiToShamsiWithDash($date)) : $date,
//                'country' => $countryName
//            ],
//            'status' => 'success',
//            'code' => 1
//        ]);
//    }

    public function userFlightBooking()
    {
        $data = $this->data;
        $userId = Auth::user()->id;
        $serviceTypeId = Order::resolveServiceType(Order::FLIGHT);
        $flightUserOrders = Order::serviceType($serviceTypeId)
            ->where('orders.user_id', $userId)
            ->cancellationLog()
            ->orderBy('orders.created_at', 'desc')->get();
        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');
        if ($data['user']->avatar != null && $data['user']->avatar != '') {
            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
            }
        }
        $data['url'] = 'user/booking/flight';
        return view('frontend.user.flightBooking', compact(
            'data',
            'flightUserOrders'
        ));
    }

    public function userHotelBooking()
    {
        $data = $this->data;
        $userId = Auth::user()->id;
        $serviceTypeId = Order::resolveServiceType(Order::HOTEL);
        $hotelUserOrders = Order::serviceType($serviceTypeId)
            ->where('orders.user_id', $userId)
            ->cancellationLog()
            ->orderBy('orders.created_at', 'desc')->get();
        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');

        if ($data['user']->avatar != null && $data['user']->avatar != '') {
            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
            }
        }
        $config = Config::get('packageConfig.app');
        $params = '{
                    "ClientUniqueCode":"' . Config::get("packageConfig.uniqueClientId." . $config) . '"
                    }';
        $ch = curl_init();
        $url = env('WEBSERVICE_GET_LAST_RATES');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        $rates = json_decode($result);

        $currencyRates = [];

        foreach ($rates as $rate) {
            $currencyRates[$rate->CorrencyCode] = $rate->Rate;
        }

        $data['currancyRates'] = json_encode($currencyRates);

        $data['url'] = 'user/booking/hotel';
        return view('frontend.user.hotelBooking', compact(
            'data',
            'hotelUserOrders'
        ));
    }

    public function userTrainBooking()
    {
        $data = $this->data;

        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');
        $userId = Auth::user()->id;
        $serviceTypeId = Order::resolveServiceType(Order::TRAIN);
        $trainUserOrders = Order::serviceType($serviceTypeId)
            ->where('orders.user_id', $userId)
            ->cancellationLog()
            ->orderBy('orders.created_at', 'desc')->get();

        if ($data['user']->avatar != null && $data['user']->avatar != '') {
            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
            }
        }

        $lang = \App::getLocale();

        $data['booking'] = [];

        $historyCollection = [];

        if (isset($data['user']) && $data['user'] != null && $data['user'] != '') {

            $data['booking'] = \App\TrainHistory::where('user_id', $data['user']['id'])
                ->where('order_id', '!=', 0)->get(); // whereNotNull('previous_route_id')

            $data['ticketCollection'] = [];

            foreach ($data['booking'] as $booking) {
                $tickets = \App\TrainTicket::where('ticket_number', $booking->ticket_number)
                    ->where('order_id', $booking->order_id)->get();

                if (count($tickets)) {
                    foreach ($tickets as $ticket) {
                        $data['ticketCollection'][$ticket->order_id][$ticket->passenger_number][$ticket->route_number] = $ticket;
                    }
                }

                $booking->from_station = \App\RajaaApiCity::where('code', $booking->from_station_id)
                    ->first()->{'name_' . $lang};
                $booking->to_station = \App\RajaaApiCity::where('code', $booking->to_station_id)
                    ->first()->{'name_' . $lang};

                $route = 'go';

                if ($booking->previous_route_id != null || $booking->previous_route_id != '') {
                    $route = 'back';
                }
                $historyCollection[$booking->order_id][$route] = $booking;
            }

        }
        $data['booking'] = $historyCollection;
        $data['url'] = 'user/booking/train';
        return view('frontend.user.trainBooking', compact(
            'data',
            'trainUserOrders'
        ));
    }

    public function userBusBooking()
    {
        $data = $this->data;
        $userId = Auth::user()->id;
        $serviceTypeId = Order::resolveServiceType(Order::BUS);
        $busOrders = Order::serviceType($serviceTypeId)->where('orders.user_id', $userId)
            ->cancellationLog()
            ->orderBy('orders.created_at', 'desc')->get();;

        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');

        if ($data['user']->avatar != null && $data['user']->avatar != '') {
            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
            }
        }

        $data['url'] = 'user/booking/bus';
        return view('frontend.user.busBooking', compact(
            'data',
            'busOrders'
        ));
    }

    public function userCarBooking()
    {
        $data = $this->data;
        $userId = Auth::user()->id;
        $serviceTypeId = Order::resolveServiceType(Order::CAR);
        $carOrders = Order::serviceType($serviceTypeId)->where('orders.user_id', $userId)
            ->cancellationLog()
            ->orderBy('orders.created_at', 'desc')->get();;
        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');

        if ($data['user']->avatar != null && $data['user']->avatar != '') {
            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
            }
        }

        $data['url'] = 'user/booking/car';
        return view('frontend.user.carBooking', compact('data','carOrders'));
    }

    public function userVacationBooking()
    {
        $data = $this->data;

        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');

        if ($data['user']->avatar != null && $data['user']->avatar != '') {
            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
            }
        }

        $data['url'] = 'user/booking/vacation';
        return view('frontend.user.vacationBooking', compact('data'));
    }

    public function userTourBooking()
    {
        $data = $this->data;

        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');

        if ($data['user']->avatar != null && $data['user']->avatar != '') {
            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
            }
        }

        $data['url'] = 'user/booking/tour';
        return view('frontend.user.tourBooking', compact('data'));
    }

    public function userPassengerDetail()
    {
        $data = $this->data;
//        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');
//
//        if ($data['user']->avatar != null && $data['user']->avatar != '') {
//            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
//                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
//            }
//        }
//
//        if (isset($_POST['travellerId'])) {
//            Traveller::where('id', Input::get('travellerId'))->update(['active' => 0]);
//        }
//        if (isset($_POST['first-name-en'])) {
//            $travellerExist = Traveller::where('first_name_en', Input::get('first-name-en'))
//                ->where('last_name_en', Input::get('last-name-en'))
//                ->where('first_name_fa', Input::get('first_name_fa'))
//                ->where('last_name_fa', Input::get('last-name-fa'))
//                ->get();
//            if (count($travellerExist) == 0) {
//                $year = Input::get('year-add');
//                $month = ((int)Input::get('month-add') < 10) ? '0' . (int)Input::get('month-add') : Input::get('month-add');
//                $day = ((int)Input::get('day-add') < 10) ? '0' . (int)Input::get('day-add') : Input::get('day-add');
//                $date = $year . '-' . $month . '-' . $day;
//
//                if (Input::get('dateTypeBirthDay-add') == 'jalali') {
//                    $date = $year . '/' . $month . '/' . $day;
//                    if (Plugin::validateDate($date) == 1) {
//                        $date = Date::ShamsiToMiladi($date);
//                    }
//
//
//                }
//                if (isset($_POST['nationality-add']) && !empty($_POST['nationality-add'])) {
//                    $nationalCode = Input::get('nationality-add');
//                } else {
//                    $nationalCode = 'IRN';
//                }
//                $traveller = new Traveller();
//                $traveller->user_id = Auth::user()->id;
//                $traveller->first_name_en = Input::get('first-name-en');
//                $traveller->last_name_en = Input::get('last-name-en');
//                $traveller->first_name_fa = Input::get('first_name_fa');
//                $traveller->last_name_fa = Input::get('last-name-fa');
//                $traveller->gender = Input::get('gender');
//                $traveller->nationality = $nationalCode;
//                $traveller->nationality_code = Input::get('national_code');
//                $traveller->passport_number = Input::get('passport');
//                if (!empty($date)) {
//
//                    if (Plugin::validateDate($date) == 1) {
//                        $traveller->birth_date = $date;
//                    }
//                }
//                $file = array('imageFile' => Input::file('imageFile'));
//                $rules = array('imageFile' => 'required|mimes:jpeg,jpg,png');
//                $validator = Validator::make($file, $rules);
//                $traveller->active = 1;
//                $traveller->save();
//
//                if (Input::file('imageFile')) {
//                    if (Input::file('imageFile')->isValid() && !$validator->fails()) {
//                        $traveller = Traveller::find($traveller->id);
//                        $destinationPath = Plugin::pathFile('avatar');
//                        $extension = Input::file('imageFile')->getClientOriginalExtension();
//                        $originalName = Input::file('imageFile')->getClientOriginalName();
//                        $fileName = 't' . $traveller->id . '_' . time() . '_' . rand(999, 9999) . '.' . $extension;
//                        $img = Image::make(Input::file('imageFile'))->resize(400, 400)->save($destinationPath . '/' . $fileName);
//                        $user = Auth::user();
//                        $traveller->avatar = $fileName;
//                        $traveller->save();
//                    }
//                }
//            }
//        }
//        if (isset($_POST['traveller-edit-form'])) {
//            // $travellerExist = Traveller::where('first_name_en',Input::get('first-name-en-edit'))
//            //     ->where('last_name_en',Input::get('last-name-en-edit'))
//            //     ->where('first_name_fa',Input::get('first_name_fa-edit'))
//            //     ->where('last_name_fa',Input::get('last-name-fa-edit'))
//            //     ->get();
//
//            // if(count($travellerExist) == 0) {
//            $year = Input::get('year-edit');
//            $month = ((int)Input::get('month-edit') < 10) ? '0' . (int)Input::get('month-edit') : Input::get('month-edit');
//            $day = ((int)Input::get('day-edit') < 10) ? '0' . (int)Input::get('day-edit') : Input::get('day-edit');
//            $date = $year . '-' . $month . '-' . $day;
//
//            if (Input::get('dateTypeBirthDay-edit') == 'jalali') {
//                $date = $year . '/' . $month . '/' . $day;
//
//                if (Plugin::validateDate($date) == 1) {
//                    $date = Date::ShamsiToMiladi($date);
//                }
//            }
//
//
//            if (isset($_POST['nationality-edit'])) {
//                $nationalCode = Input::get('nationality-edit');
//            } else {
//                $nationalCode = 'IRN';
//            }
//
//            Traveller::where('id', Input::get('traveller-edit-form'))->update(['active' => 0]);
//            $traveller = new Traveller();
//            $traveller->user_id = Auth::user()->id;
//            $traveller->first_name_en = Input::get('first-name-en-edit');
//            $traveller->last_name_en = Input::get('last-name-en-edit');
//            $traveller->first_name_fa = Input::get('first_name_fa-edit');
//            $traveller->last_name_fa = Input::get('last-name-fa-edit');
//            $traveller->gender = Input::get('gender-edit');
//            $traveller->nationality = $nationalCode;
//            $traveller->nationality_code = Input::get('national_code_edit');
//            $traveller->passport_number = Input::get('passport-edit');
//            if (!empty($date)) {
//                $traveller->birth_date = $date;
//            }
//            $file = array('imageFile' => Input::file('imageFile'));
//            $rules = array('imageFile' => 'required|mimes:jpeg,jpg,png');
//            $validator = Validator::make($file, $rules);
//            $traveller->active = 1;
//            $traveller->save();
//            if (Input::file('imageFile')) {
//                if (Input::file('imageFile')->isValid() && !$validator->fails()) {
//                    $traveller = Traveller::find($traveller->id);
//                    $destinationPath = Plugin::pathFile('avatar');
//                    $extension = Input::file('imageFile')->getClientOriginalExtension();
//                    $originalName = Input::file('imageFile')->getClientOriginalName();
//                    $fileName = 't' . $traveller->id . '_' . time() . '_' . rand(999, 9999) . '.' . $extension;
//                    $img = Image::make(Input::file('imageFile'))->resize(400, 400)->save($destinationPath . '/' . $fileName);
//                    $user = Auth::user();
//                    $traveller->avatar = $fileName;
//                    $traveller->save();
//                }
//            }
//            // }
//        }
        /*$t = Traveller::where('id',Input::get('travellerId'))->first();

        if ($t AND $t->active != 0) {

        }*/

        $data['travellers'] = \App\Traveller::where('user_id', $data['user']->id)
            ->where('active', 1)->get();
        $data['url'] = 'user/booking/passengerDetail';
        return view('frontend.user.passengerDetail', compact('data'));
    }

    protected function passengerCreateValidator(array $data)
    {
        return Validator::make($data, [
//            'imageFile' => 'nullable|mimes:jpeg,jpg,PNG',
            'first_name_en' => 'required',
            'last_name_en' => 'required',
//            'first_name_fa' => 'max:255|required',
//            'last_name_fa' => 'max:255|required',
//            'gender' => 'required',
//            'nationality-add' => 'required',
//            'national_code' => 'nullable|digits:10',
//            'passport' => 'max:50|required',
//            'date' => 'date_format:"Y-m-d"|required',
        ]);
    }

    protected function passengerUpdateValidator(array $data)
    {
        return Validator::make($data, [
//            'imageFile' => 'nullable|mimes:jpeg,jpg,png',
            'first_name_en_edit' => 'required',
            'last_name_en_edit' => 'required',
//            'first_name_fa_edit' => 'max:255|required',
//            'last_name_fa_edit' => 'max:255|required',
//            'gender-edit' => 'required',
//            'national_code_edit' => 'nullable|digits:10',
//            'nationality-edit' => 'required',
//            'passport-edit' => 'max:50|required',
//            'date-edit' => 'date_format:"Y-m-d"|required',
        ]);
    }

    public function addPassengerDetail(Request $request)
    {
        $success = true;
        $defaultPath = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');
        $fileName = null;
        $nationalCode = null;
        $userId = Auth::user()->id;
        if (isset($_POST['first_name_en'])) {
            $travellerExist = Traveller::where('user_id', $userId)
                ->where('active', '1')
                ->where('first_name_en', Input::get('first_name_en'))
                ->where('last_name_en', $_POST['last_name_en'])
                ->where('first_name_fa', $_POST['first_name_fa'])
                ->where('last_name_fa', $_POST['last_name_fa'])
                ->get();
            if (count($travellerExist) == 0) {
                $year = $_POST['year-add'];
                $month = ((int)$_POST['month-add'] < 10) ? '0' . (int)$_POST['month-add'] : $_POST['month-add'];

                $day = ((int)$_POST['day-add'] < 10) ? '0' . (int)$_POST['day-add'] : $_POST['day-add'];
                $date = $year . '-' . $month . '-' . $day;
                if ($_POST['dateTypeBirthDay-add'] === 'jalali') {
                    $date = $year . '/' . $month . '/' . $day;
                    if (Plugin::validateDate($date) == 1) {
                        $date = Date::ShamsiToMiladi($date);
                    } else {
                        $date = "00-00-00";

                    }
                }
                $request->merge(array('date' => $date));

                $this->passengerCreateValidator($request->all())->validate();

                if (isset($_POST['nationality-add']) && !empty($_POST['nationality-add'])) {
                    $nationalCode = $_POST['nationality-add'];
                } else {
                    $nationalCode = 'IRN';
                }
                $traveller = new Traveller();
                $traveller->user_id = Auth::user()->id;
                $traveller->first_name_en = $_POST['first_name_en'];
                $traveller->last_name_en = $_POST['last_name_en'];
                $traveller->first_name_fa = $_POST['first_name_fa'];
                $traveller->last_name_fa = $_POST['last_name_fa'];
                $traveller->gender = $_POST['gender'];
                $traveller->nationality = $nationalCode;
                $traveller->nationality_code = $_POST['national_code'];
                $traveller->passport_number = $_POST['passport'];
                $traveller->birth_date = $date;
                $traveller->active = 1;
                $traveller->save();
                if ($request->file('imageFile')) {
                    $file = array('imageFile' => $request->file('imageFile'));
                    $rules = array('imageFile' => 'required|mimes:jpeg,jpg,png');
                    $validator = Validator::make($file, $rules);
                    if ($request->file('imageFile')->isValid() && !$validator->fails()) {
                        if ($request->hasfile('imageFile')) {
                            $traveller = Traveller::find($traveller->id);
                            $file = $request->file('imageFile');
                            $destinationPath = Plugin::pathFile('avatar');
                            $extension = $file->getClientOriginalExtension();
                            $fileName = 't' . $traveller->id . '_' . time() . '_' . rand(999, 9999) . '.' . $extension;
                            $img = Image::make($request->file('imageFile'))->resize(400, 400)->save($destinationPath . '/' . $fileName);
//                            $file->move($destinationPath . '/', $fileName);
                        }
                    }
                    $traveller->avatar = $fileName;
                    $traveller->save();
                }
                $id = $traveller->id;

                if ($nationalCode != null && $nationalCode != '') {

                    $country = \App\Country::where('iso3', $nationalCode)->first();

                    if ($country) {

                        $nationalityEn = $country->nicename;
                        $nationalityFa = $country->faname;
                    }

                }
                $data = $request->except('_token', 'imageFile');
                $success = true;
            } else {
                $success = false;

            }

        }
        //ToDo:inset persian nationality

        if ($success) {
            return response()->json([
                'status' => 'success',
                'message' => 'success',
                'data' => $data,
                'passengerId' => $id,
                'imageFile' => $traveller->fullAvatar,
                'nationalityEn' => $nationalityEn,
                'nationalityFa' => $nationalityFa,
                "defaultPath" => $defaultPath
            ]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'error']);
        }

    }

    public function updatePassengerDetail(Request $request)
    {
        $defaultPath = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');
        $nationalCode = '';
        if (isset($_POST['travellerId'])) {
            Traveller::where('id', $_POST['travellerId'])->update(['active' => 0]);
        }
        if (isset($_POST['traveller-edit-form'])) {
            $year = $_POST['year-edit'];
            $month = ((int)$_POST['month-edit'] < 10) ? '0' . (int)$_POST['month-edit'] : $_POST['month-edit'];
            $day = ((int)$_POST['day-edit'] < 10) ? '0' . (int)$_POST['day-edit'] : $_POST['day-edit'];
            $date = $year . '-' . $month . '-' . $day;

            if ($_POST['dateTypeBirthDay-edit'] == 'jalali') {
                $date = $year . '/' . $month . '/' . $day;
                if (Plugin::validateDate($date) == 1) {
                    $date = Date::ShamsiToMiladi($date);
                } else {
                    $date = "00-00-00";
                }
            }
            if (isset($_POST['nationality-edit'])) {
                $nationalCode = $_POST['nationality-edit'];
            } else {
                $nationalCode = 'IRN';
            }
            Traveller::where('id', $_POST['traveller-edit-form'])->update(['active' => 0]);
            $fileName = Traveller::where('id', $_POST['traveller-edit-form'])->first();
            $fileName = $fileName->avatar;
            $request->merge(array('date-edit' => $date));
            $this->passengerUpdateValidator($request->all())->validate();
            $traveller = new Traveller();
            $traveller->user_id = Auth::user()->id;
            $traveller->first_name_en = $_POST['first_name_en_edit'];
            $traveller->last_name_en = $_POST['last_name_en_edit'];
            $traveller->first_name_fa = $_POST['first_name_fa_edit'];
            $traveller->last_name_fa = $_POST['last_name_fa_edit'];
            $traveller->gender = $_POST['gender-edit'];
            $traveller->nationality = $nationalCode;
            $traveller->nationality_code = $_POST['national_code_edit'];
            $traveller->passport_number = $_POST['passport-edit'];
            $traveller->birth_date = $date;
            $file = array('imageFile' => $request->file('imageFile'));
            $rules = array('imageFile' => 'required|mimes:jpeg,jpg,png');
            $validator = Validator::make($file, $rules);
            $traveller->active = 1;
            $traveller->save();
            if ($request->input('removeFile') == "yes") {
                $traveller->avatar = null;
                $traveller->save();
            } else {
                if ($request->file('imageFile')) {
                    $file = array('imageFile' => $request->file('imageFile'));
                    $rules = array('imageFile' => 'required|mimes:jpeg,jpg,png');
                    $validator = Validator::make($file, $rules);
                    if ($request->file('imageFile')->isValid() && !$validator->fails()) {
                        if ($request->hasfile('imageFile')) {
                            $traveller = Traveller::find($traveller->id);
                            $file = $request->file('imageFile');
                            $destinationPath = Plugin::pathFile('avatar');
                            $extension = $file->getClientOriginalExtension();
                            $fileName = 't' . $traveller->id . '_' . time() . '_' . rand(999, 9999) . '.' . $extension;
                            $img = Image::make($request->file('imageFile'))
                                ->resize(400, 400)
                                ->save($destinationPath . '/' . $fileName);
                        }
                    }

                }
                $traveller->avatar = $fileName;
                $traveller->save();
            }
            $nationalityEn = "";
            $nationalityFa = "";
            if ($nationalCode != null && $nationalCode != '') {

                $country = \App\Country::where('iso3', $nationalCode)->first();

                if ($country) {
                    $nationalityEn = $country->nicename;
                    $nationalityFa = $country->faname;
                }
            }
            $id = $traveller->id;
            $data = $request->except('_token', 'imageFile');
//                }
//                $success = true;
//            } else {
//                $success = false;
//
//            }
            return response()->json([
                'status' => 'success',
                'message' => 'success',
                'data' => $data,
                'passengerId' => $id,
                'imageFile' => $traveller->fullAvatar,
                'nationalityFa' => $nationalityFa,
                'nationalityEn' => $nationalityEn,
                'previousId' => $_POST['travellerId'],
                "defaultPath" => $defaultPath
            ]);

            //ToDo:inset persian nationality
        }

    }

    public function removeAvatar()
    {
        if (Auth::user()->avatar != null || Auth::user()->avatar != '') {
            $user = Auth::user();
            $user->avatar = null;
            $user->save();
        }
        return response()->json(['status' => 'success', 200]);
    }

    public function deletePassenger()
    {
        $passengerId = $_POST["index"];
        Traveller::where('id', $passengerId)->update(['active' => 0]);
        return response()->json([
            'status' => 'success',
            'message' => 'success',
            'passengerId' => $passengerId
        ]);
    }

    public function userEwallet()
    {
        $data = $this->data;

        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');

        if ($data['user']->avatar != null && $data['user']->avatar != '') {
            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
            }
        }
        $data['url'] = 'user/booking/eWallet';
        return view('frontend.user.eWallet', compact('data'));
    }

    public function userPoint()
    {
        $data = $this->data;

        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');

        if ($data['user']->avatar != null && $data['user']->avatar != '') {
            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
            }
        }
        $data['url'] = 'user/booking/myPoint';
        return view('frontend.user.myPoint', compact('data'));
    }

    public function userInbox()
    {
        $data = $this->data;

        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');

        if ($data['user']->avatar != null && $data['user']->avatar != '') {
            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
            }
        }

        $data['url'] = 'user/booking/inbox';
        if (isset($_POST['_token'])) {
            InboxMsg::where('user_id', Auth::user()->id)->update(['active' => 0]);
        }
        $data['inbox'] = InboxMsg::where('user_id', Auth::user()->id)->where('active', 1)->first();
        $data['isInbox'] = ($data['inbox']) ? 1 : 0;
        return view('frontend.user.inbox', compact('data'));
    }

    public function userMyBooking()
    {
        $data = $this->data;
        $userId = Auth::user()->id;
        $serviceTypeId = Order::resolveServiceType(Order::FLIGHT);
        $flightOrder=Order::serviceType($serviceTypeId)
            ->where('orders.user_id', $userId)->where('status',1)->orderBy('orders.created_at', 'desc')->first();
        $serviceTypeId = Order::resolveServiceType(Order::HOTEL);
        $hotelOrder=Order::serviceType($serviceTypeId)
            ->where('orders.user_id', $userId)->where('status',1)->orderBy('orders.created_at', 'desc')->first();
        $serviceTypeId = Order::resolveServiceType(Order::BUS);
        $busOrder=Order::serviceType($serviceTypeId)
            ->where('orders.user_id', $userId)->where('status',1)->orderBy('orders.created_at', 'desc')->first();
        $serviceTypeId = Order::resolveServiceType(Order::TRAIN);
        $trainOrder=Order::serviceType($serviceTypeId)
            ->where('orders.user_id', $userId)->where('status',1)->orderBy('orders.created_at', 'desc')->first();
        $serviceTypeId = Order::resolveServiceType(Order::CAR);
        $carOrder=Order::serviceType($serviceTypeId)
            ->where('orders.user_id', $userId)->where('status',1)->orderBy('orders.created_at', 'desc')->first();

        $serviceTypeId = Order::resolveServiceType(Order::TOUR);
        $tourOrder=Order::serviceType($serviceTypeId)
            ->where('orders.user_id', $userId)->where('status',1)->orderBy('orders.created_at', 'desc')->first();
        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');

        if ($data['user']->avatar != null && $data['user']->avatar != '') {
            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
            }
        }
        $data['url'] = 'user/booking/myBooking';
        return view('frontend.user.myBooking', compact('data','flightOrder','hotelOrder','carOrder','tourOrder','busOrder','trainOrder'));
    }

    public function userbookings()
    {
        $data = $this->data;
        $data['userAvatarPath'] = staticAsset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');

        if (isset($data['user']) && $data['user']->avatar != null && $data['user']->avatar != '') {

            if (file_exists('./pictures/avatar/' . $data['user']->avatar)) {
                $data['userAvatarPath'] = staticAsset('/pictures/avatar/' . $data['user']->avatar);
            }
        }

        return view('frontend.user.bookings', compact('data'));
    }

    public function getUserFlightDetails()
    {
        $order_id = $_POST['order_id'];
        $order = Order::find($order_id);
        $orderDetails = [];
        $passengerDetails = [];
        $flightConditions = [];
        foreach ($order->flightDetail as $detail) {
            $temp = array(
                "id" => $detail->id,
                "stream_sequence" => $detail->stream_sequence,
                "segment_sequence" => $detail->segment_sequence,
                "baggage" => $detail->baggage,
                "aireline_logo" => $detail->flightlogo,
                "aireline_name" => $detail->aireline_name,
                "departure_date" => date('Y-m-d H:s', strtotime($detail->departure_date)),
                "departure_date_shamsi" => str_replace("/","-",Date::ConvertMiladiToShamsi(date('Y-m-d', strtotime($detail->departure_date)))),
                "arrival_date" => date('Y-m-d H:s', strtotime($detail->arrival_date)),
                "arrival_date_shamsi" => str_replace("/","-",Date::ConvertMiladiToShamsi(date('Y-m-d', strtotime($detail->arrival_date)))),
                "origin_airport" => $detail->origin_airport,
                "destination_airport" => $detail->destination_airport,
                "duration" => $detail->duration,
                "flight_number" => $detail->flight_number,
                "flight_class" => $detail->flight_class,
                "flight_classified" => $detail->flight_classified,
                "flight_type" => $detail->flight_type
            );
            array_push($orderDetails, $temp);
        }
        foreach ($order->flightPassenger as $passenger) {
            $temp1 = array(
                "invoice_number" => $order->invoice_number,
                "first_name_en" => $passenger->traveller->first_name_en,
                "last_name_en" => $passenger->traveller->last_name_en,
                "gender" => $passenger->traveller->gender,
                "ticket_number" => $passenger->ticket_number,
                "currency" => $passenger->currency,
                "fee" => $passenger->fee, "tax" => $passenger->tax,
                "discount" => $passenger->discount,
                "final_amount" => $passenger->final_amount
            );
            array_push($passengerDetails, $temp1);
        }
        foreach ($order->flightCondition as $condition) {
            $temp2 = array(
                "condition_fa" => $condition->condition_fa,
                "condition_en" => $condition->condition_en
            );
            array_push($flightConditions, $temp2);
        }
        $details = array();
        $details['invoiceNumber'] = $order->invoice_number;
        $details['orderDetails'] = $orderDetails;
        $details['passengerDetails'] = $passengerDetails;
        $details['flightConditions'] = $flightConditions;
        return \GuzzleHttp\json_encode($details);
    }

    public function getUserHotelDetails()
    {
        $orderId = $_POST['order_id'];
        $order = Order::find($orderId);
        $details = array();
        $hotelDetails = [];
        $rooms = isset($order->hotelRoom) ? $order->hotelRoom : null;
        $temp1 = array(
            "orderId" => $order->id,
            "hotelImage" => isset($order->hotelDetail)?$order->hotelDetail->hotelpicture:null,
            "hotelName" => isset($order->hoteldetail->hotel_name) ? $order->hoteldetail->hotel_name : "-",
            "checkInDate" => isset($order->hoteldetail->start_date) ? date("Y-m-d", $order->hoteldetail->start_date) : "-",
            "checkInDateShamsi" => isset($order->hoteldetail->start_date) ? str_replace("/","-",Date::ConvertMiladiToShamsi(date("Y-m-d", $order->hoteldetail->start_date))) : "-",
            "checkOutDate" => isset($order->hoteldetail->end_date) ? date("Y-m-d", $order->hoteldetail->end_date) : "-",
            "checkOutDateShamsi" => isset($order->hoteldetail->end_date) ? str_replace("/","-",Date::ConvertMiladiToShamsi(date("Y-m-d", $order->hoteldetail->end_date))) : "-",
            "reservationNumber" => isset($order->hoteldetail->confirm_code) ? $order->hoteldetail->confirm_code :"-",
            "roomName" => isset($order->hotelRoom[0]) ? $order->hotelRoom[0]->room_name : null,
            "roomType" => isset($order->hotelRoom[0]) ? $order->hotelRoom[0]->room_type : null,
            "roomPrice" => isset($order->hotelRoom[0]) ? $order->hotelRoom[0]->total : null,
            "tax" => isset($order->hotelRoom[0]) ? $order->hotelRoom[0]->tax : null,
            "discount" => isset($order->hotelRoom[0]) ? $order->hotelRoom[0]->discount : null,
            "cancellationPolicy" => isset($order->hoteldetail->hotelInquiry) ? $order->hoteldetail->hotelInquiry->cancelation_policy : null,
            "remark" => isset($order->hoteldetail->hotelInquiry) ? $order->hoteldetail->hotelInquiry->remark : null,
            "currency" => isset($order->currency) ? $order->currency : null
        );
        array_push($hotelDetails, $temp1);
        $passengerDetails = [];
        $roomName = null;
        $roomType = null;
        $pricePerRoom = 0;
        $tax = 0;
        $fee = 0;
        $discount = 0;
        foreach ($order->hotelPassenger as $passenger) {
            if (!empty($rooms)) {
                if ($passenger->room_number == 1) {
                    $roomName = $rooms[0]->room_name;
                    $roomType = $rooms[0]->room_type;
                    $pricePerRoom = $rooms[0]->total;
                    $tax = $rooms[0]->tax;
                    $fee = 0;
                    $discount = $rooms[0]->discount;
                } else if ($passenger->room_number == 2) {
                    $roomName = $rooms[1]->room_name;
                    $roomType = $rooms[1]->room_type;
                    $pricePerRoom = $rooms[1]->total;
                    $tax = $rooms[1]->tax;
                    $fee = 0;
                    $discount = $rooms[1]->discount;
                } else if ($passenger->room_number == 3) {
                    $roomName = $rooms[2]->room_name;
                    $roomType = $rooms[2]->room_type;
                    $pricePerRoom = $rooms[2]->total;
                    $tax = $rooms[2]->tax;
                    $fee = 0;
                    $discount = $rooms[2]->discount;
                }
            }
            $temp = array(
                "firstName" => $passenger->traveller->first_name_en,
                "lastName" => $passenger->traveller->last_name_en,
                "gender" => $passenger->traveller->gender,
                "roomName" => $roomName,
                "roomType" => $roomType,
                "pricePerRoom" => $pricePerRoom,
                "tax" => $tax,
                "fee" => $fee,
                "discount" => $discount);
            array_push($passengerDetails, $temp);
        }
        $details['invoiceNumber'] = $order->invoice_number;
        $details['hotelDetails'] = $hotelDetails;
        $details['passengerDetails'] = $passengerDetails;
        $details['currency'] = isset($order->hotelDetail->currency) ? $order->hotelDetail->currency : "IRR";
        return \GuzzleHttp\json_encode($details);
    }

    public function getUserBusDetails()
    {
        $order_id = $_POST['order_id'];
        $order = Order::find($order_id);
        $busDetails = [];
        $seatNumbers = array();
        $noPassengers = 0;
        foreach ($order->busDetail as $detail) {

            $seats = explode(",", $detail->seats);
            foreach ($seats as $seat) {
                if ($seat != "") {
                    $seat = explode("/", $seat);
                    array_push($seatNumbers, $seat[0]);
                    $noPassengers++;
                }
            }
            $discount = isset($detail->full_price) ? (int)($detail->full_price) - (int)($detail->price) : 0;
//            $noPassengers = (count(explode(",", $detail->seats)));
            if ($order->currency == 'IRR') {
                $price = number_format((int)$detail->price, 0);
                $discount = number_format((int)$discount, 0);
            }
            $temp = array(
                "id" => $detail->id,
                "origin" => $detail->src_city,
                "destination" => $detail->des_city,
                "departureDate" => $detail->departure_date,
                "departureTime" => $detail->departure_time,
                "type" => $detail->type,
                "companyName" => $detail->coname,
                "seat" => $seatNumbers,
                "price" => $price,
                "ticketNumber" => isset($detail->busReserve) ? $detail->busReserve->ticket_no : null,
                "noPassengers" => $noPassengers,
                "discount" => $discount,
                "busLogo" => $detail->busImage
            );
            array_push($busDetails, $temp);
            $noPassengers = 0;
            $seatNumbers = array();

        }
        $Details = array();
        $Details['invoiceNumber'] = $order->invoice_number;
        $Details['busDetails'] = $busDetails;
        return \GuzzleHttp\json_encode($Details);
    }

    public function userFlightTicketCancellation(Request $request)
    {
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        $phoneNumber = $request->phone_Number;
        $description = $request->description;
        $confirmationCode = Plugins::quickRandomAllCharacter(10);
        $cancellationRequest = new CancellationLog;
        $cancellationRequest->user_id = $order->user_id;
        $cancellationRequest->order_id = $order_id;
        $cancellationRequest->phone_number = $phoneNumber;
        $cancellationRequest->description_user = $description;
        $cancellationRequest->request_status = 0;
        $cancellationRequest->codeConfirm = $confirmationCode;
        $cancellationRequest->save();
        $userOrders['confirmationCode'] = $confirmationCode;
        $userOrders['orderCancellationId'] = $cancellationRequest->id;
        $userOrders['orderId'] = $request->order_id;
        return json_encode($userOrders);
    }

    public function userHotelTicketCancellation(Request $request)
    {
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        $phoneNumber = $request->phone_Number;
        $description = $request->description;
        $confirmationCode = Plugins::quickRandomAllCharacter(10);
        $cancellationRequest = new CancellationLog;
        $cancellationRequest->user_id = $order->user_id;
        $cancellationRequest->order_id = $order_id;
        $cancellationRequest->phone_number = $phoneNumber;
        $cancellationRequest->description_user = $description;
        $cancellationRequest->request_status = 0;
        $cancellationRequest->codeConfirm = $confirmationCode;
        $cancellationRequest->save();
        $userOrders = array();
        $userOrders['confirmationCode'] = $confirmationCode;
        $userOrders['orderCancellationId'] = $cancellationRequest->id;
        $userOrders['orderId'] = $request->order_id;
        return json_encode($userOrders);
    }

    public function userBusTicketCancellation(Request $request)
    {
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        $phoneNumber = $request->phone_Number;
        $description = $request->description;
        $confirmationCode = Plugins::quickRandomAllCharacter(10);
        $cancellationRequest = new CancellationLog;
        $cancellationRequest->user_id = $order->user_id;
        $cancellationRequest->order_id = $order_id;
        $cancellationRequest->phone_number = $phoneNumber;
        $cancellationRequest->description_user = $description;
        $cancellationRequest->request_status = 0;
        $cancellationRequest->codeConfirm = $confirmationCode;
        $cancellationRequest->save();
        $userOrders['confirmationCode'] = $confirmationCode;
        $userOrders['orderCancellationId'] = $cancellationRequest->id;
        $userOrders['orderId'] = $request->order_id;
        return json_encode($userOrders);
    }

    public function getUserTicketConfirmCode()
    {
        $order_id = $_POST['order_id'];
        $order = CancellationLog::where("order_id", $order_id)->first();
        $orderDetails = array(
            "id" => $order->id,
            "request_status" => $order->request_status,
            "codeConfirm" => $order->codeConfirm,
            "description_admin" => $order->description_admin
        );
        return json_encode($orderDetails);
    }
}
