<?php
use Illuminate\Support\ServiceProvider;
use App\Permission;
use App\NationalityHotel;
use Illuminate\Support\Facades\Session;


Route::get("load-resource", 'HomeController@resources');
Route::get("testuser", function () {


    $salt = "EniacT3ch";
    $token = crypt("29518476",$salt);
    dd($token);
    $persons = \App\USRPersons::all();
    foreach ($persons as $person) {
        dd($person->user()->get());
        die();
    }

});

Route::get("api/getGeneralSettings", "AgentController@getGeneralSettings");
Route::get("api/getCountryList", "AgentController@getCountriesList");
Route::post("api/flight/flightSearch", "AgentController@flightSearch");
Route::post("api/flight/flightSearchAgain", "AgentController@flightSearchAgain");
Route::post("api/flight/getOffer", "AgentController@getOffer");
Route::post("api/flight/sentVerifyPayment", "AgentController@sentCodeVerifyUserPayment");
Route::post("api/flight/reserves", "AgentController@reserveFlight");
Route::post("api/flight/issue", "AgentController@issuedFlight");
Route::post("api/flight/standAlonePdfCreate", "AgentController@standAlonePdfCreationForFlight");
Route::post("api/checkPdfExistance", "AgentController@checkThePdfExist");

Route::get('api/getTravellers', 'AgentController@getTravellers');
Route::post("api/hotel/hotelSearch","AgentController@hotelSearch");
Route::post("api/hotel/hotelFilter","AgentController@hotelFilter");
Route::post("api/hotel/cancellation","AgentController@getHotelCancellationRulesCounter");
Route::post("api/hotel/hotelContent","AgentController@hotelContent");
Route::post("api/hotel/reserves","AgentController@reserveHotel");
Route::post("api/hotel/confirm","AgentController@confirmHotel");
Route::post("api/hotel/standAlonePdfCreate", "AgentController@standAlonePdfCreationForHotel");


Route::post("api/basket/massPaymentOfBasket","AgentController@massPaymentOfBasket");
Route::post("api/basket/eachReserveOfBasket","AgentController@eachReserveOfBasket");
Route::post("api/basket/eachPDFOfBasket","AgentController@eachPDFOfBasket");


Route::post("api/payment","AgentController@payment");


Route::post("api/payment/ivr","AgentController@payIVR");
Route::post("api/send-caller-detail","AgentController@sendCallerDetail");

Route::post("api/getUserInfo","AgentController@getUserInfo");
Route::post("api/updateUserInfo","AgentController@updateUserInfo");
Route::post("api/createNewUserInfo","AgentController@createNewUserInfo");
Route::post("api/userSearch","AgentController@userSearch");

Route::post("api/send-detail-user-by-id","AgentController@sendDetailUserById");

Route::get("search-data", function () {
//    dd( date("Y-m-d",strtotime(date("Y-m-d") . ' -1 day')));
    $currentTime = date("Y-m-d", strtotime(date("Y-m-d") . ' -1 day'));
    $search = \App\UrlGeneratorSite::where("created_at", 'like', "%" . $currentTime . "%")->get();

    foreach ($search as $value) {
        echo "KeyURL : " . $value->keyurl . "</br>";
        echo "ParamSearch : " . $value->param . "</br>";
        echo "Time : " . $value->created_at . "</br>";
        echo "_________________________________" . "</br>";
    }
//    print_r($search);
});

Route::get('init-event', function () {

    $data = [
        'topic_id' => 'onNewData',
        'data' => 'someData: ' . rand(1, 100)
    ];
    \App\Classes\Socket\Pusher::sentDataToServer($data);
    var_dump($data);
    // \App\Classes\Socket\ChatSocket::onMesasge();
});


Route::POST('callback/ivr', 'UserManagmentController@ivrCallback');
Route::POST('check-ivr', 'HomeController@checkIVR');
Route::get('wait-for-pay', 'HomeController@waitForPay');
Route::post('payment-ivr', 'HomeController@paymentIVR');

Route::get("tour_detail", function () {
    $tour = App\Tour::with(['units' => function ($sql) {
        return $sql->with(['routes' => function ($sql) {
            return $sql->with(['accommodations', 'transports', 'attractions', 'extraServices', 'transfers']);
        }]);
    }])->get();
    return response()->json($tour);
});

Route::post('flight/userFlightTicketDetails', 'UserController@getUserFlightDetails');
Route::post('flight/userFlightTicketCancellation', 'UserController@userFlightTicketCancellation');
Route::post('flight/getUserFlightTicketConfirmCode', 'UserController@getUserTicketConfirmCode');

Route::post('hotel/userHotelTicketDetails', 'UserController@getUserHotelDetails');
Route::post('hotel/userHotelTicketCancellation', 'UserController@userHotelTicketCancellation');
Route::post('hotel/getUserHotelTicketConfirmCode', 'UserController@getUserTicketConfirmCode');

Route::post('bus/userBusTicketDetails', 'UserController@getUserBusDetails');
Route::post('bus/userBusTicketCancellation', 'UserController@userBusTicketCancellation');
Route::post('bus/getUserBusTicketConfirmCode', 'UserController@getUserTicketConfirmCode');


/*
 *
|--------------------------------------------------------------------------
| AloBelit.Com Route Collection                E-Niac
|--------------------------------------------------------------------------
*/

Route::get('fav-city', function () {
    $favs = \App\FavCity::all();
    foreach ($favs as $fav) {
        $hotel = \App\Hotel::where('name', strtolower($fav->capital))->first();
        if (!empty($hotel)) {
            \App\FavCity::where('id', $fav->id)->update(['hotel_id' => $hotel->id]);
        }
    }
    die('ok');
});

Route::get('gethotel', function () {
    set_time_limit(0);
    /*$ch = curl_init();

    $url = 'http://192.168.20.242/MainService.svc/restHotel/GetHotelLocations';

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, false);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $results = curl_exec($ch);
    file_put_contents("city.json",$results);
    die('ok');*/

//    print_r(glob('./HotelData/*.json'));die();
    foreach (glob('./HotelData/*.json') as $file) {
        $results = '[' . file_get_contents($file);
        $results = substr($results, 0, -1);
        $results = $results . ']';
        foreach (json_decode($results) as $country) {
            foreach ($country->Childs as $region) {
                foreach ($region->Childs as $city) {
                    $store = new App\Hotel();
                    $store->service_id = $city->ID;
                    $store->city_id = $city->ID;
                    $store->name = $city->Name;
                    $store->name_fa = $city->PersianTitle;
                    $store->country_id = $country->ID;
                    $store->country_name = $country->Name;
                    $store->country_name_fa = $country->PersianTitle;
                    $store->region_id = $region->ID;
                    $store->region = $region->Name;
                    $store->region_fa = $region->PersianTitle;
                    if ($city->AllLanguageType != null) {
                        $explodeCity = explode(",", $city->AllLanguageType);
                        $cityAll = '';
                        foreach ($explodeCity as $cities) {
                            $cityAll .= $cities . '#';
                        }
                        if ($city->PersianTitle != null) {
                            $store->value = $city->PersianTitle . '#' . $city->Name . '#' . $cityAll . $country->Name;
                        } else {
                            $store->value = $city->Name . '#' . $cityAll . $country->Name;
                        }
                    } else {
                        if ($city->PersianTitle != null) {
                            $store->value = $city->PersianTitle . '#' . $city->Name . '#' . $country->Name;
                        } else {
                            $store->value = $city->Name . '#' . $country->Name;
                        }
                    }
                    $store->hasArea = (count($city->Childs)) ? 1 : 0;
                    $store->save();

                    if (count($city->Childs)) {
                        foreach ($city->Childs as $area) {
                            $store1 = new App\Hotel();
                            $store1->service_id = $area->ID;
                            $store1->city_id = $city->ID;
                            $store1->name = $area->Name;
                            $store1->name_fa = $area->PersianTitle;
                            $store1->country_id = $country->ID;
                            $store1->country_name = $country->Name;
                            $store1->country_name_fa = $country->PersianTitle;
                            $store1->region_id = $region->ID;
                            $store1->region = $region->Name;
                            $store1->region_fa = $region->PersianTitle;
                            $store1->area_id = $area->ID;
                            $store1->parent = $store->id;
                            if ($area->AllLanguageType != null) {
                                $explodeCity = explode(",", $area->AllLanguageType);
                                $cityAll = '';
                                foreach ($explodeCity as $cities) {
                                    $cityAll .= $cities . '#';
                                }
                                if ($area->PersianTitle != null) {
                                    $store1->value = $area->PersianTitle . '#' . $area->Name . '#' . $cityAll . $country->Name;
                                } else {
                                    $store1->value = $area->Name . '#' . $cityAll . $country->Name;
                                }
                            } else {
                                if ($area->PersianTitle != null) {
                                    $store1->value = $area->PersianTitle . '#' . $area->Name . '#' . $country->Name;
                                } else {
                                    $store1->value = $area->Name . '#' . $country->Name;
                                }
                            }
                            $store1->save();
                        }
                    }
                }
            }
        }

    }


});


Route::get('createSearchTerminal', function () {

    $terminals = App\BusTerminal::all();

    $stateId = null;

    foreach ($terminals as $terminal) {

        $type = 't';
        $city = null;
        $state = null;

        if ($terminal->city_id != null && $terminal->city_id != '') {

            $city = App\BusCity::find($terminal->city_id);

            if ($city) {

                if ($city->state_id) {

                    $state = App\BusState::find($city->state_id);

                }

            }

        } else {

            $city = App\BusCity::where('code', $terminal->code)->first();

            if ($city) {

                $type = 'c';
                $terminal = null;

                if ($city->state_id) {

                    $state = App\BusState::find($city->state_id);

                }

            } else {

                $state = App\BusState::where('code', $terminal->code)->first();

                if ($state) {

                    $type = 's';
                    $terminal = null;

                }

            }

        }

        $cityId = null;
        $value = '';

        if ($state) {

            if ($state->code == '98000000') {

                $type = 'f';

                $state = null;

            } else {

                if (!App\SearchTerminal::where('code', $state->code)->count()) {

                    $t = App\BusTerminal::where('code', $state->code)->first();

                    $stateNameFa = $state->state_name;
                    $stateNameEn = null;

                    if ($t) {

                        $stateNameFa = $t->title_fa;
                        $stateNameEn = $t->title_en;

                        $value = $t->title_fa;

                        if ($t->title_en) {

                            $value .= '#' . $t->title_en;

                        }

                    } else {

                        $value = $state->state_name;

                    }

                    $state = App\SearchTerminal::create([
                        'code' => $state->code,
                        'name_fa' => $stateNameFa,
                        'name_en' => strtolower($stateNameEn),
                        'type' => 's',
                        'parent' => null,
                        'value' => $value
                    ]);

                    $stateId = $state->id;

                }

            }

        }

        if ($city) {

            if ($terminal != null) {

                if ($city->code && $terminal->code) {

                    if ($city->code == $terminal->code) {

                        $terminal = null;

                    }

                }

            }

        }

        $allTerminals = null;

        if (!$terminal && $city) {

            if ($city->id) {

                $allTerminals = App\BusTerminal::where('city_id', $city->id)->where('code', '!=', $city->code)->get();

                if ($allTerminals) {

                    $terminal = null;

                }

            }

        }


        if ($city) {

            if ($city->state_id) {

                $cities = App\BusCity::where('state_id', $city->state_id)->get();

                if ($cities) {

                    foreach ($cities as $c) {

                        if (!App\SearchTerminal::where('code', $c->code)->count()) {

                            $t = App\BusTerminal::where('code', $c->code)->first();

                            $cityNameFa = $c->city_name;
                            $cityNameEn = $c->city_name_en;

                            if ($t) {
                                $cityNameFa = $t->title_fa;
                                $cityNameEn = $t->title_en;

                                $value = $t->title_fa;

                                if ($t->title_en) {

                                    $value .= '#' . $t->title_en;

                                }

                            } else {

                                $value = $c->city_name;

                                if ($c->city_name_en) {

                                    $value .= '#' . $c->city_name_en;

                                }

                            }

                            $city = App\SearchTerminal::create([
                                'code' => $c->code,
                                'name_en' => strtolower($cityNameEn),
                                'name_fa' => $cityNameFa,
                                'type' => 'c',
                                'parent' => $stateId,
                                'value' => $value
                            ]);

                            $at = App\BusTerminal::where('city_id', $c->id)->where('code', '!=', $c->code)->get();

                            foreach ($at as $t) {

                                if (!App\SearchTerminal::where('code', $t->code)->count()) {

                                    $value = $t->title_fa;

                                    if ($t->title_en) {

                                        $value .= '#' . $t->title_en;

                                    }

                                    App\SearchTerminal::create([
                                        'code' => $t->code,
                                        'name_en' => strtolower($t->title_en),
                                        'name_fa' => $t->title_fa,
                                        'type' => 't',
                                        'parent' => $city->id,
                                        'value' => $value
                                    ]);

                                }

                            }

                        }

                    }

                } else {

                    if (!App\SearchTerminal::where('code', $city->code)->count()) {

                        $cityNameFa = $city->city_name;
                        $cityNameEn = $city->city_name_en;

                        $t = App\BusTerminal::where('code', $city->code)->first();

                        if ($t) {
                            $cityNameFa = $t->title_fa;
                            $cityNameEn = $t->title_en;

                            $value .= $t->title_fa;

                            if ($t->title_en) {

                                $value .= '#' . $t->title_en;

                            }

                        } else {

                            $value .= $city->city_name;

                            if ($city->city_name_en) {

                                $value .= '#' . $city->city_name_en;

                            }

                        }

                        $city = App\SearchTerminal::create([
                            'code' => $city->code,
                            'name_en' => strtolower($cityNameEn),
                            'name_fa' => $cityNameFa,
                            'type' => 'c',
                            'parent' => $stateId,
                            'value' => $value
                        ]);

                        $stateId = null;

                        $cityId = $city->id;

                    }

                }

                $stateId = null;

            }

        }

        if ($allTerminals) {

            foreach ($allTerminals as $terminal) {

                if (!App\SearchTerminal::where('code', $terminal->code)->count()) {

                    // if ( strlen( $value ) > 0 ) {
                    //
                    //     $value .= '#';
                    //
                    // }
                    //
                    // $value .= $terminal->title_fa;

                    $value = $terminal->title_fa;

                    if ($terminal->title_en) {

                        $value .= '#' . $terminal->title_en;

                    }

                    App\SearchTerminal::create([
                        'code' => $terminal->code,
                        'name_en' => strtolower($terminal->title_en),
                        'name_fa' => $terminal->title_fa,
                        'type' => 't',
                        'parent' => $cityId,
                        'value' => $value
                    ]);

                }

            }

        } else if (!$allTerminals && $terminal) {

            if (!App\SearchTerminal::where('code', $terminal->code)->count()) {

                $value = '';

                $terminalNameFa = $terminal->city_name;
                $terminalNameEn = $terminal->city_name_en;

                $t = App\BusTerminal::where('code', $terminal->code)->first();

                if ($t) {
                    $terminalNameFa = $t->title_fa;
                    $terminalNameEn = $t->title_en;

                    $value = $t->title_fa;

                    if ($t->title_en) {

                        $value .= '#' . $t->title_en;

                    }

                } else {

                    if ($terminal->city_name) {

                        $value .= $terminal->city_name;

                    }

                    if ($terminal->title_en) {

                        if (strlen($value) > 0) {

                            $value .= '#';

                        }

                        $value .= $terminal->city_name_en;

                    }

                }

                App\SearchTerminal::create([
                    'code' => $terminal->code,
                    'name_en' => strtolower($terminalNameEn),
                    'name_fa' => $terminalNameFa,
                    'type' => $type,
                    'parent' => null,
                    'value' => $value
                ]);

            }

        }

    }

});
/*Route::get('gethotelnationality',function (){
    set_time_limit(0);
    $ch = curl_init();

    $url = 'http://192.168.50.42:1036/MainService.svc/restHotel/GetAllNationalities';

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, false);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $results = curl_exec($ch);
    foreach (json_decode($results) as $country){
        $store = new NationalityHotel();
        $store->country_name = $country->CountryName;
        $store->iso_code = $country->ISOCode;
        $store->nationality_id = $country->NationalityID;
        $store->save();

    }
});
Route::get('gethotel',function (){
    set_time_limit(0);
    $ch = curl_init();

    $url = 'http://192.168.50.42:1036/MainService.svc/restHotel/GetHotelLocations';

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, false);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $results = curl_exec($ch);
    foreach (json_decode($results) as $country){
        foreach ($country->Childs as $region){
            foreach ($region->Childs as $city){
                $store = new Hotel();
                $store->city_id = $city->ID;


                $store->name = $city->Name;
                $store->name_fa = $city->PersianTitle;
                $store->country_id = $country->ID;
                $store->country_name = $country->Name;
                if($city->PersianTitle != null){
                    $store->value = $city->Name.'#'.$country->Name.'#'.$city->PersianTitle.'#'.;
                }else{
                    $store->value = $city->Name.'#'.$country->Name;
                }
                $store->save();
            }
        }
    }
});*/
/*Route::get('upbus',function(){
    $busnew = DB::table('bus_terminals')->get();
    $busold = DB::table('bus_terminals_old')->get();
    foreach ($busnew as $new){
        foreach ($busold as $old){
            if($new->code == $old->code){
                DB::table('bus_terminals')
                    ->where('id', $new->id)
                    ->update(['city_id' => $old->city_id]);
            }
        }
    }
});*/
Route::get('busnew', function () {
    $bus = \App\BusNew::all();
    foreach ($bus as $value) {
        $str = str_replace("?", "ی", $value->name_fa);
        \App\BusNew::where('id', $value->id)->update(['name_fa' => $str]);
    }
//    print_r($bus);die();
});

Route::get('getEuropcar', function () {

    $get = \App\Http\Controllers\CURL::init()->execute('http://192.168.50.42:1036/MainService.svc/restCar/getAllStations', [], [], 'GET')->response();
    $resultLoginDecode = json_decode($get);
    foreach ($resultLoginDecode->ObjectResponse as $value) {
        $web = new \App\WebserviceStation();
        $web->station_id = $value->StationID;
        $web->station_code = $value->StationCode;
        $web->station_name = $value->StationName;
        $web->country_code = $value->CountryCode;
        $web->country_name = $value->CountryName;
        $web->area_type = $value->AreaType;
        $web->city_name = $value->CityName;
        $web->address = $value->Address;
        $web->postal_code = $value->PostalCode;
        $web->phone_country_code = $value->PhoneCountryCode;
        $web->phone_area_code = $value->PhoneAreaCode;
        $web->phone_number = $value->PhoneNumber;
        $web->fax_number = $value->FaxNumber;
        $web->longitude = $value->StationLongitude;
        $web->latitude = $value->StationLatitude;
        $web->delivery = $value->StationDelivery;
        $web->collection = $value->StationCollection;
        $web->flag_prestige = $value->Prestige;
        $web->is_truck_available = $value->IsTruckAvailable;
        $web->value = $value->StationCode . '%' . $value->CountryName . '%' . $value->CityName . '%' . $value->Address . '%' . $value->PostalCode;
        $web->save();
    }
    die();
});

Route::group(["prefix" => "api", "namespace" => "Api"], function () {
    Route::get('searchTerminals', 'ApiController@searchTerminals');
    Route::get('tour/fetchCities', 'ApiController@fetchCities');
    Route::post('profile/getTravellerData', 'ApiController@getTravellerData');
    Route::post('profile/remove-photo', 'ApiController@removePassengerPhoto');
    Route::post('currency/change', 'ApiController@changeCurrency');
    Route::post('user-profile-currency/change', 'ApiController@changeUserProfileCurrency');
    Route::post('user-profile-language/change', 'ApiController@changeUserProfileLanguage');
    Route::post('hotel/detail', 'ApiController@getHotelDetail');
    Route::post('hotel/getCancellationRules', 'ApiController@getHotelCancellationRules');
    Route::post('booking/checkReference', 'ApiController@checkBookingReference');
    Route::post('workwithus', 'ApiController@workWithUs');
    Route::post('job', 'ApiController@job');
    Route::any('user/uploadAvatar', 'ApiController@uploadUserAvatar');
    Route::any('saveContactDetails', 'ApiController@saveContactDetails');
    Route::post('flight/getOfferCondition', 'ApiController@getOfferCondition');
    Route::post('send-caller-detail', 'ApiController@sendCallerDetail');
    Route::post('update-hit-point', 'ApiController@updateHitPoint');


});

Route::get('curltest', 'PaymentController@curltest');
Route::get('update-password', 'UserController@updateUserPassword');

Route::get('oauth2callback', 'UserController@googleLogin');
Route::post('hotel/filter', 'HotelController@filterMemcached');
Route::group(['prefix' => 'api'], function () {
    Route::post('user/register', 'UserManagmentController@register');
    Route::post('user/registerWithoutPw', 'UserManagmentController@registerWithOutPassword');
    Route::post('user/verifyRegister', 'UserManagmentController@verifyRegister');
    Route::post('user/resendVerify', 'UserManagmentController@resendVerify');
    Route::post('user/login', 'UserManagmentController@login');
    Route::post('user/forgotPassword', 'UserManagmentController@forgotPassword');
    Route::post('user/confirmVerifyCode', 'UserManagmentController@confirmVerifyCode');
    Route::post('user/setNewPassword', 'UserManagmentController@setNewPassword');
//    Route::post('hotel/filter','HotelController@wbsFilter');
    Route::get('bus/searchSourceTerminals', 'Bus\BusController@ajaxSearchSourceTerminals');
    Route::get('bus/searchDestinationTerminals', 'Bus\BusController@ajaxSearchDestinationTerminals');

    Route::post('hotel/compare', 'HotelController@ajaxHotelsCompare');
});
Route::group(['prefix' => 'api', 'middleware' => 'jwt.auth'], function () {


});


Route::get("hello", function () {
    $sessionId = \App\RajaaAPI::init()->wbsLogin();
    $res = \App\RajaaAPI::init()->execute(\App\RajaaAPI::STATION_TIMELINE, [
        "pTrainNumber" => 704,
        "pSCP" => 960401,
        "pSCPs" => 350,
        'serviceSessionId' => $sessionId
    ])->response();
    var_dump($res);
});
Route::group(['middleware' => ['web']], function () {
    Route::get('sessionTest', function () {
        //Session::put('mehdii' ,'shakki');
//     Session::set('mehdi','shakki');
        // Session::save();
//    session_start();
        //dd(Session::get('mehdi'));
        var_dump(Session::has('mehdii'));
    });
    Route::get('after-session', function () {
        echo Session::get('mehdi');
//     Session::set('mehdi','shakki');
        // Session::save();
//    session_start();
        //dd(Session::get('mehdi'));
    });
});
Route::get('t-invoice', function (Illuminate\Http\Request $request) {
    echo App\TPay::visible($request);
});
Route::get('file-portal', 'Portal\PortalController@file');
Route::post('file-portal', 'Portal\PortalController@file');
Route::get('adminportal', 'Portal\PortalController@index');
Route::post('adminportal', 'Portal\PortalController@index');
Route::post('totalprice', 'Portal\PortalController@totalPrice');
//    Route::post('filter-portal','Portal\PortalController@filter');


Route::get('glogin', array('as' => 'glogin', 'uses' => 'UserController@googleLogin'));
Route::get('google-user', array('as' => 'user.glist', 'uses' => 'UserController@listGoogleUser'));
Route::post('filterClassified', 'ClassifiedController@filterClassified');
Route::get('session', function () {
    echo Session::getId();
});
Route::post('getpictureshotel', 'HotelController@getPicturesHotel');
Route::get('test', function () {
    return view('frontend.hotel/result');
});

Route::post('checkHappy', 'HotelController@checkCodeHappy');
Route::post('passengerurl', 'FlightController@passengerUrl');
Route::post('hotelpassengerurl', 'HotelController@hotelPassengerUrl');
Route::post('hotelurl', 'HotelController@passengerUrl');
Route::get('cronflighturl', 'CronController@cronFlightUrl');


/*
|--------------------------------------------------------------------------
| CRON
|--------------------------------------------------------------------------
*/

Route::get('jsonEuropCar', 'CronController@jsonEuropCar');
Route::get('cron/getCityAndCountry', 'CronController@getCitiesAndCountriesFromWebService');

/*
|--------------------------------------------------------------------------
| Currencies
|--------------------------------------------------------------------------
*/
Route::get('exchange/royal', "ExchangeController@royal");


/*
|--------------------------------------------------------------------------
| WebService
|--------------------------------------------------------------------------
*/
Route::post('rest/getLatestHotelUpdates', "WebserviceController@getLatestHotelUpdates");

/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/


Route::get('fill', 'HomeController@fill');
Route::get('insertcity', 'HomeController@insertCity');
Route::get('insertcountry', 'HomeController@insertCountry');
Route::get('insertairport', 'HomeController@insertAirport');
Route::get('resultairport', 'HomeController@resultAirport');

Route::get('login', 'Auth\LoginController@login');
Route::post('login', 'Auth\LoginController@login');

Route::get('logout', 'Auth\LoginController@logout');
Route::post('updateaccount', 'UserController@updateAccount');

Route::post('filter', 'FlightController@filter');
//Route::post('hotel/filter','HotelController@filterHotel');
Route::post('vacation/filter', 'VacationController@filter');
Route::post('searchCityVacation', 'VacationController@ajaxSearchCity');

// Route::get('Applications' , 'FooterController@Applications');
// Route::get('errorpage' , 'HomeController@errorPage');
Route::get('app', 'FlightController@application');
Route::get('sms', 'FlightController@sendSms');
Route::post('justPassengerValid', 'FlightController@justPassengerValid');


/*
|--------------------------------------------------------------------------
| Ajax Route
|--------------------------------------------------------------------------
*/

Route::get('checktimeout', 'FlightController@checkTimeOut');
Route::get('refresh_captcha', 'NewsLetterSubscriberController@refreshCaptcha');
Route::get('checktimeoutHotel', 'HotelController@checkTimeout');
Route::get('user/confirmRegistration/{code}', 'UserController@confirmAccount');
Route::get('getcookie', 'HomeController@getcookie');
//Route::post('searchAirports' , 'FlightController@ajaxSearchAirports');
Route::get('searchAirports', 'FlightController@ajaxSearchAirport');
Route::get('checkLogin', 'Auth\LoginController@checkLogin');
Route::post('checkMail', 'UserController@checkMail');
Route::post('autocomplete', 'HomeController@autocomplete');
Route::post('userregistration', 'FlightController@userRegistration');
Route::get('finalProcedure', 'AloBelit\FlightController@finalProcedure');
Route::post('subscription/ajaxRegister', 'NewsLetterSubscriberController@ajaxRegister');
Route::post('user/ajaxGetUserById', 'UserController@ajaxGetUserById');
Route::post('user/ajaxUpdateUserAccountDetails', 'UserController@ajaxUpdateUserAccountDetails');
Route::post('traveller/ajaxCreateTraveller', 'FlightTravellerController@ajaxCreateTraveller');
Route::post('traveller/ajaxUpdateTraveller', 'FlightTravellerController@ajaxUpdateTraveller');
Route::post('traveller/ajaxDeleteTraveller', 'FlightTravellerController@ajaxDeleteTraveller');
Route::post('traveller/ajaxGetTravellersByUserId', 'FlightTravellerController@ajaxGetTravellersByUserId');
Route::post('traveller/ajaxGetTravellerById', 'FlightTravellerController@ajaxGetTravellerById');
Route::post('gettraveller', 'FlightController@getTraveller');
Route::post('stationSearch', 'CarController@ajaxStationSearch');
Route::post('getCitiesByCountryCode', 'CarController@ajaxGetCitiesByCountryCode');
Route::post('getStationsByCityName', 'CarController@ajaxGetStationsByCityName');
Route::post('getCountries', 'CarController@ajaxGetCountries');
Route::post('truckSearch', 'CarController@ajaxTruckSearch');
Route::post('registerhotel', 'HotelController@registerHotel');
//Route::post('searchHotelAndCity' , 'HotelController@ajaxSearchHotels');
Route::get('searchHotelAndCity', 'HotelController@ajaxSearchHotels');
Route::post('searchCities', 'HotelController@ajaxSearchCityByCountryId');
Route::post('searchHotels', 'HotelController@ajaxSearchHotelByCityId');
Route::post('searchCityAndCountry', 'VacationController@ajaxSearchCityAndCountry');

Route::post('getimagegallery', 'Admin\AdminHotelController@getImageGallery');
Route::post('imageremovelist', 'Admin\AdminHotelController@sendImageForRemoveList');
Route::post('getUserInfo', 'UserController@ajaxGetUserInfo');
Route::post('searchCar', 'CarController@ajaxSearchCar');
Route::post('getCarSearchDetails', 'CarController@ajaxGetCarSearchDetails');
Route::post('searchHotelByFilter', 'HotelController@ajaxSearchHotelByFilter');
Route::post('registerAgency', 'Admin\AdminAgencyController@registerAgency');
Route::post('expireLoadTime', 'HomeController@ajaxExpireLoadTime');

Route::post('generateKey', 'HomeController@ajaxGenerateKey');
Route::get('traveller', 'HomeController@getTraveller');
Route::post('get-all-data-traveller', 'HomeController@getAllDataTraveller');


/*
|--------------------------------------------------------------------------
| Payment
|--------------------------------------------------------------------------
*/
Route::get('payment', 'PaymentController@checkPaymentParsian');
//Route::post('payment/gotoparsian' , 'PaymentController@gotoPersian');
Route::get('payment/checkparsian', 'PaymentController@checkPaymentParsian');
Route::get('payment/failure', 'PaymentController@failedPayment');
Route::post('payment/checkhappy', 'PaymentController@checkPaymentHappy');
Route::get('payment/dixipay', 'PaymentController@dixiPay');
//Route::get('payment/dixipay' , 'PaymentController@gotoSaman');
Route::post('payment/checkpaymentsaman', 'PaymentController@checkPaymentSaman');

/*IFrame*/
Route::post('searchcondition', 'IFrameController@searchCondition');
Route::get('iframe', 'IFrameController@indexIFrame');
Route::get('reportiframe', 'IFrameController@reportIframe');

Route::group(['prefix' => '{locale?}', 'middleware' => ['beforeLoad']], function () {
    Route::get("404", function () {
        return view("errors.404");
    });

    Route::get("tour/تور-کانادا" , "FooterController@canada");
    Route::get("tour/تور-سوئيس" , "FooterController@switzerland");
    Route::get("tour/تور-ژاپن" , "FooterController@japan");
    Route::get("tour/تور-اتریش-فرانسه" , "FooterController@france");
    Route::get("tour/تور-سوئيس-آلمان-اتريش" , "FooterController@german");
    Route::get("tour/تور-اسپانیا" , "FooterController@spain");


    Route::get("user/inbox", 'AgentController@inbox');
    Route::post("account-turnover", 'AgentController@getUserAccountTurnover');
    Route::post("user/orderFilter", 'AgentController@orderFilter');
    Route::post("user/flightTicketDetails", 'AgentController@getFlightTicketDetail');
    Route::post("user/hotelTicketDetails", 'AgentController@getHotelTicketDetail');
    Route::post("user/busTicketDetails", 'AgentController@getBusTicketDetail');
    Route::get("user/show-msg", 'AgentController@showMsg');
    Route::get("user-detail-agent", 'Housers/dataTablesmeController@detailUserAgent');
    Route::post('subscription/ajaxRegister', 'NewsLetterSubscriberController@ajaxRegister');
    Route::post('subscription/ajax_register', 'NewsLetterSubscriberController@ajaxRegisterHermes');

    Route::group(['middleware' => ['guest']], function () {
        Route::get('portal/login', "AdminPortal\AdminPortalController@login");
        Route::post('portal/login', "AdminPortal\AdminPortalController@loginAdmin");
    });

    Route::post('portal/check-login', "AdminPortal\AdminPortalController@loginAdmin");


    Route::group(['prefix' => 'portal', 'middleware' => ['portalAuthCheck']], function () {

        Route::get('unAuthorizedAccess', "AdminPortal\AdminPortalController@unAuthorizedAccess");
        Route::get('logoutPortal', "AdminPortal\AdminPortalController@logoutPortal");
        Route::post('sale-reports/getFlightTable', "AdminPortal\saleReports\saleReportsController@getFlight");
        Route::post('bought-tickets/getFlightTable', 'AdminPortal\boughtTickets\boughtTicketsController@getFlight');
//        Route::get('dashboard', "AdminPortal\AdminPortalController@dashboard");


//        Route::post('manage-pages/edit-common-questions/update-question', 'AdminPortal\managePages\managePagesController@updateQuestion');
//        Route::post('manage-pages/edit-common-questions/delete', 'AdminPortal\managePages\managePagesController@deleteQuestion');
//        Route::get('manage-pages/edit-common-questions/{id}', 'AdminPortal\managePages\managePagesController@editQuestion');
//        Route::post('manage-pages/edit-common-questions/dataTables', 'AdminPortal\managePages\managePagesController@getCommonQuestionsData');
//        Route::post('manage-pages/common-questions/update-flight-common-questions', 'AdminPortal\managePages\managePagesController@updateFlightCommonQuestions');
//        Route::post('manage-pages/common-questions/update-hotel-common-questions', 'AdminPortal\managePages\managePagesController@updateHotelCommonQuestions');
//        Route::post('manage-pages/common-questions/update-train-common-questions', 'AdminPortal\managePages\managePagesController@updateTrainCommonQuestions');
//        Route::post('manage-pages/common-questions/update-bus-common-questions', 'AdminPortal\managePages\managePagesController@updateBusCommonQuestions');
//        Route::post('manage-pages/common-questions/update-tour-common-questions', 'AdminPortal\managePages\managePagesController@updateTourCommonQuestions');
//        Route::post('manage-pages/common-questions/update-car-common-questions', 'AdminPortal\managePages\managePagesController@updateCarCommonQuestions');
//        Route::get('manage-pages/edit-footer-flight', 'AdminPortal\termsConditionManage\termsConditionManageController@flight');
//        Route::post('manage-pages/edit-footer-flight-charter-terms', 'AdminPortal\termsConditionManage\termsConditionManageController@updateCharterFlightTerms');
//        Route::post('manage-pages/edit-footer-flight-domestic-terms', 'AdminPortal\termsConditionManage\termsConditionManageController@updateDomesticFlightTerms');
//        Route::post('manage-pages/edit-footer-flight-international-terms', 'AdminPortal\termsConditionManage\termsConditionManageController@updateInternationalFlightTerms');
//        Route::post('manage-pages/edit-footer-flight-refund-terms', 'AdminPortal\termsConditionManage\termsConditionManageController@updateRefundFlightTerms');
//        Route::get('manage-pages/edit-footer-hotel', 'AdminPortal\termsConditionManage\termsConditionManageController@hotel');
//        Route::post('manage-pages/edit-footer-hotel-terms', 'AdminPortal\termsConditionManage\termsConditionManageController@updateHotelTerms');
//        Route::get('manage-pages/edit-footer-bus', 'AdminPortal\termsConditionManage\termsConditionManageController@bus');
//        Route::post('manage-pages/edit-footer-bus-terms', 'AdminPortal\termsConditionManage\termsConditionManageController@updateBusTerms');
//        Route::get('manage-pages/edit-footer-rentACar', 'AdminPortal\termsConditionManage\termsConditionManageController@rentACar');
//        Route::post('manage-pages/edit-footer-car-terms', 'AdminPortal\termsConditionManage\termsConditionManageController@updateRentACarTerms');
        Route::get('manage-pages/edit-footer-related-services', 'AdminPortal\termsConditionManage\termsConditionManageController@relatedServices');
        Route::post('manage-pages/edit-footer-FAQ', 'AdminPortal\termsConditionManage\termsConditionManageController@updateFAQService');
//        Route::post('manage-pages/edit-footer-guide', 'AdminPortal\termsConditionManage\termsConditionManageController@updateGuideService');
//        Route::get('manage-pages/edit-footer-train', 'AdminPortal\termsConditionManage\termsConditionManageController@train');
//        Route::post('manage-pages/edit-footer-train-terms', 'AdminPortal\termsConditionManage\termsConditionManageController@updateTrainTerms');
//        Route::get('manage-pages/edit-footer-general', 'AdminPortal\termsConditionManage\termsConditionManageController@general');
//        Route::post('manage-pages/edit-footer-general-terms', 'AdminPortal\termsConditionManage\termsConditionManageController@updateGeneralTerms');
//        Route::get('manage-pages/edit-footer-guide-flight', 'AdminPortal\termsConditionManage\termsConditionManageController@flightGuide');
//        Route::get('manage-pages/edit-footer-guide-hotel', 'AdminPortal\termsConditionManage\termsConditionManageController@hotelGuide');
//        Route::get('manage-pages/edit-footer-guide-bus', 'AdminPortal\termsConditionManage\termsConditionManageController@busGuide');
//        Route::get('manage-pages/edit-footer-guide-train', 'AdminPortal\termsConditionManage\termsConditionManageController@trainGuide');
//        Route::get('manage-pages/edit-footer-guide-rentACar', 'AdminPortal\termsConditionManage\termsConditionManageController@carGuide');
//        Route::post('manage-pages/edit-footer-guide-flight', 'AdminPortal\termsConditionManage\termsConditionManageController@updateFlightGuide');
//        Route::post('manage-pages/edit-footer-guide-hotel', 'AdminPortal\termsConditionManage\termsConditionManageController@updateHotelGuide');
//        Route::post('manage-pages/edit-footer-guide-bus', 'AdminPortal\termsConditionManage\termsConditionManageController@updateBusGuide');
//        Route::post('manage-pages/edit-footer-guide-train', 'AdminPortal\termsConditionManage\termsConditionManageController@updateTrainGuide');
//        Route::post('manage-pages/edit-footer-guide-car', 'AdminPortal\termsConditionManage\termsConditionManageController@updateCarGuide');
////
//        Route::get('manage-pages/add-visa', 'AdminPortal\termsConditionManage\termsConditionManageController@visa');
//        Route::post('manage-pages/add-visa', 'AdminPortal\termsConditionManage\termsConditionManageController@addVisa');
//        Route::get('manage-pages/visas', 'AdminPortal\termsConditionManage\termsConditionManageController@visas');
//        Route::get('manage-pages/visas/{id}','AdminPortal\termsConditionManage\termsConditionManageController@editVisa');
//        Route::post('manage-pages/update-visa','AdminPortal\termsConditionManage\termsConditionManageController@updateVisa');
//        Route::post('manage-page/removeVisa','AdminPortal\termsConditionManage\termsConditionManageController@deleteVisa');


//        Route::group(['prefix' => 'finance', 'namespace' => 'AdminPortal\finance'], function () {
////            Route::get('sale-reports/flight', "financeController@getFlightSaleReport");
////            Route::post('sale-report/flightTicketDetails', "financeController@getFlightTicketDetail");
////            Route::post('sale-reports/flight', "financeController@getFlightSaleReportFilter");
//        });

        Route::group(['prefix' => 'markup', 'namespace' => 'AdminPortal\markup'], function () {
//            Route::get('group', "groupController@index");
//            Route::get('users', "groupController@users");
//            Route::post('group/dataTables', "groupController@groupsDataTable");
//            Route::post('users/dataTables', "groupController@usersDataTable");
//            Route::post('group/create', "groupController@createGroup");
//            Route::post('group/delete', "groupController@deleteGroup");
//            Route::get('group/markup-service/{id}', "groupController@markupService")->name('markup-service');
//            Route::post('group/flight/add-markup-service', "groupController@flightMarkupServiceStore");
//            Route::post('group/flight/add-markup-airline', "groupController@flightMarkupAirlineStore");
//            Route::post('add-user-group', "groupController@userGroupStore");
//            Route::post('group/flight-service/dataTables', "groupController@getFlightMarkupServiceData");
//            Route::post('group/flight-airline/dataTables', "groupController@getFlightMarkupAirlineData");
//            Route::post('group/flight-service/delete', "groupController@deleteFlightMarkupService");
//            Route::post('group/flight-airline/delete', "groupController@deleteFlightMarkupAirline");
//            Route::post('group/user-group/delete', "groupController@userGroupDelete");
//            Route::post('user-group/update', "groupController@userGroupUpdate");
//            Route::post('group/getServiceMarkupInfo', "groupController@getServiceMarkupInfo");
//            Route::post('group/flight-service/update', "groupController@updateServiceMarkup");


//            Route::post('sale-report/flightTicketDetails', "financeController@getFlightTicketDetail");
//            Route::post('sale-reports/flight', "financeController@getFlightSaleReportFilter");
        });


//        Route::post('blog/createPost','AdminPortal\blog\blogController@createPost');
//        Route::get('blog/createPost','AdminPortal\blog\blogController@index');
//        Route::get('blog/createTag','AdminPortal\blog\blogController@createTag');
//        Route::post('blog/createTag','AdminPortal\blog\blogController@storeTag');
//        Route::post('blog/removePost','AdminPortal\blog\blogController@removePost');
//        Route::post('blog/removeTag','AdminPortal\blog\blogController@removeTag');
//        Route::get('blog/posts','AdminPortal\blog\blogController@posts');
//        Route::get('blog/posts/{id}','AdminPortal\blog\blogController@editPost');
//        Route::post('blog/posts/updatePost','AdminPortal\blog\blogController@updatePost');
//        Route::post('blog/updateTag','AdminPortal\blog\blogController@updateTag');
//        Route::post('blog/editTag','AdminPortal\blog\blogController@editTag'); // This route has been removed from code
        // add post
//        Route::post('blog/saveImage','AdminPortal\blog\blogController@saveImage');
//        Route::post('blog/removeImage','AdminPortal\blog\blogController@removeImage');

        //edit post
//        Route::post('blog/updateRemoveImage','AdminPortal\blog\blogController@removeImage');
//        Route::post('blog/updateImage','AdminPortal\blog\blogController@saveImage');

        $permissions = \App\USRPermission::all();
        foreach ($permissions as $permission) {
            switch ($permission->type) {
                case 'GET' :
                    Route::get($permission->url, $permission->action)->middleware("permission:$permission->name");
                    break;
                case 'POST' :
                    Route::post($permission->url, $permission->action)->middleware("permission:$permission->name");
                    break;
                default :
                    Route::get($permission->url, $permission->action)->middleware("permission:$permission->name");
                    break;
            }
        }


    });
    /*
        Route::group(['prefix' => 'portal/tour', 'namespace' => 'AdminPortal\tour'], function () {
            Route::get('create', "tourController@create");
            Route::get('tours', "tourController@tours");
        });*/



//    Route::get('portal/blog/createPost','blogController@createPost');
    // need to move to DB -------------------------------------------

//    Route::post('flight/filter', 'AdminPortal\boughtTickets\boughtTicketsController@flightFilter');
//    Route::post('flight/flightTicketDetails', 'AdminPortal\boughtTickets\boughtTicketsController@getFlightTicketDetail');

//    Route::post('api/admin/hotelFilter', 'AdminPortal\boughtTickets\boughtTicketsController@hotelFilter');
//    Route::post('hotel/hotelTicketDetails', 'AdminPortal\boughtTickets\boughtTicketsController@getHotelTicketDetail');

//    Route::post('bus/filter', 'AdminPortal\boughtTickets\boughtTicketsController@busFilter');
//    Route::post('bus/busTicketDetails', 'AdminPortal\boughtTickets\boughtTicketsController@getBusTicketDetail');

    Route::group(['prefix' => 'portal', 'namespace' => 'AdminPortal', 'middleware' => ['authPortal']], function () {
//        Route::group(['prefix' => 'users', 'namespace' => 'users'], function () {
//            Route::get('users', 'UsersController@users');
//            Route::post('/dataTables', 'UsersController@anyData');
//            Route::post('/userCreate', 'UsersController@userCreate');
//            Route::post('/userUpdate', 'UsersController@userUpdate');
//            Route::post('/delete', 'UsersController@userDelete');
//            Route::post('getUserInfoById', 'usersController@getUserInfoById');
//            Route::post('uploadUserAvatar', 'usersController@uploadUserAvatar');
//
//            Route::get('group', "RolePermissionController@group");
//            Route::post('group/dataTables', "RolePermissionController@rolesAnyData");
//            Route::post('group/create', "RolePermissionController@createRole");
//            Route::post('group/update', "RolePermissionController@updateRole");
//            Route::post('group/delete', "RolePermissionController@deleteRole");
//            Route::post('group/getRoleInfo', "RolePermissionController@getRoleInfo");
//
//            Route::get('permission', "RolePermissionController@permissionIndex");
//            Route::get('permission/dataTables', "RolePermissionController@permissionAnyData");
//            Route::post('permission/permissionSave', "RolePermissionController@permissionSave");
//
//            Route::post('permission/getUserRolePerInfo', "RolePermissionController@getUserRolePerInfo");
//            Route::post('permission/getNewPermissionOnRoleChange', "RolePermissionController@getNewPermissionOnRoleChange");
//
//        });
        Route::post('flight/userFilter', 'boughtTickets\boughtTicketsController@userFilter');

    });


    //        Route::get('/', "AdminPortalController@dashboard");

//        Route::group(['prefix' => 'bought-tickets', 'namespace' => 'boughtTickets'], function () {
//            Route::get('flight', "boughtTicketsController@flight");
//            Route::get('hotel', "boughtTicketsController@hotel");
//            Route::get('bus', "boughtTicketsController@bus");
//            Route::get('train', "boughtTicketsController@train");
//        });

//        Route::group(['prefix' => 'sale-reports', 'namespace' => 'saleReports'], function () {
//            Route::get('flight', "saleReportsController@flight");
//            Route::post('flight', "saleReportsController@flightFilter");
//            Route::post('flightTicketDetails', "saleReportsController@getFlightTicketDetail");
//            Route::get('hotel', "saleReportsController@hotel");
//            Route::post('hotel', "saleReportsController@hotelFilter");
//            Route::post('hotelTicketDetails', "saleReportsController@getHotelTicketDetail");
//            Route::get('bus', "saleReportsController@bus");
//            Route::post('bus', "saleReportsController@busFilter");
//            Route::post('busTicketDetails', "saleReportsController@getBusTicketDetail");
//            Route::get('train', "saleReportsController@train");
//        });


//        Route::group(['prefix' => 'manage-pages', 'namespace' => 'managePages'], function () {
//            Route::get('edit-home-page', "managePagesController@editHomePage");

//            Route::get('edit-about-us', "managePagesController@editAboutUs");
//            Route::post('editAboutUs', 'managePagesController@updateAboutUs');

//            Route::get('edit-contact-us', "managePagesController@editContactUs");
//            Route::post('editContactUs', 'managePagesController@updateContactUs');


//            Route::get('edit-social-network', "managePagesController@editSocialNetwork");
//            Route::post('editSocialNetworks', 'managePagesController@updateSocialNetwork');
//        });


//        Route::group(['prefix' => 'inbox', 'namespace' => 'inbox'], function () {
//            Route::get('inbox', "inboxController@inbox");
//            //20
//
//        });

//        Route::group(['prefix' => 'support', 'namespace' => 'support'], function () {
//            Route::get('ticket', "supportController@ticket");
//        });

//        Route::group(['prefix' => 'sms', 'namespace' => 'sms'], function () {
//            Route::get('send-sms-to-customer', "smsController@sendSMSToCustomer");
//            Route::get('sent-messages', "smsController@sentMessages");
//            Route::get('increase-credit', "smsController@increaseCredit");

    //10

//        });


//        Route::group(['prefix' => 'track-order', 'namespace' => 'trackOrder'], function () {
//            Route::get('cancellations', "trackOrderController@cancellations");
//            Route::post('cancellations/filter', "trackOrderController@cancellationsOrderFilter");
//            Route::post('cancellations/busTicketDetails', "trackOrderController@getBusTicketDetail");
//            Route::post('cancellations/hotelTicketDetails', "trackOrderController@getHotelTicketDetail");
//            Route::post('cancellations/flightTicketDetails', "trackOrderController@getFlightTicketDetail");
//            Route::post('orderCancellation', "trackOrderController@orderCancellationResponse");
//            Route::post('changeOrderStatus', "trackOrderController@changeOrderStatus");


//            Route::get('/', "trackOrderController@index");
    //5

//        });

//        Route::group(['prefix' => 'finance', 'namespace' => 'finance'], function () {
//            Route::get('buy-manage-reports', "financeController@buyManageReports");
//            Route::get('income', "financeController@income");
//            Route::get('reverse-cash-reports', "financeController@reverseCashReports");
//            Route::get('reverse-cash-save', "financeController@reverseCashSave");

    //20

//        });


//        Route::group(['prefix' => 'terms', 'namespace' => 'termsConditionManage'], function () {
//            Route::get('flight', "termsConditionManageController@flight");
//            Route::get('hotel', "termsConditionManageController@hotel");
//            Route::get('train', "termsConditionManageController@train");
//            Route::get('bus', "termsConditionManageController@bus");
//            Route::get('rent-a-car', "termsConditionManageController@rentACar");

    //10
//        });


//        Route::group(['prefix' => 'settings', 'namespace' => 'settings'], function () {
//            Route::get('disable-service', "settingsController@disableService");
//            Route::post('disable-flightService', "settingsController@updateServiceStatus");
//            Route::post('disable-hotelService', "settingsController@updateServiceStatus");
//            Route::post('disable-busService', "settingsController@updateServiceStatus");
//            Route::post('disable-trainService', "settingsController@updateServiceStatus");
//            Route::post('disable-carService', "settingsController@updateServiceStatus");

//            Route::get('seo', "settingsController@seo");
//            Route::post('seo', "settingsController@updateSeo");
//            Route::post('seo/metaDescription', "settingsController@updateMetaDescriptions");
//            Route::post('seo/searchEngineDescription', "settingsController@updateSearchEngineDescriptions");
//            Route::get('payment-gatway', "settingsController@paymentGatway");
//            Route::get('markup-service', "settingsController@markupService");
//            Route::get('script-manage', "settingsController@scriptManage");
//            Route::get('edit-list-autocomplete', "settingsController@editListAutocomplete");

    //10
//        });


//    });
    // need to move to DB ---------------------------------


    Route::get('flight_new', "HomeController@flightNew");
    Route::group(['prefix' => 'tour-packages', 'namespace' => 'TourPackage'], function () {
        Route::get('/', "TourController@index");
        Route::get('offers', "TourController@offers");
        Route::get("view/{id}", "TourController@view");
        Route::get("purchase/{id}", "TourController@purchase");
        Route::get("choose/{touId}", "TourController@choose");
        Route::post("search", "TourController@search");
        Route::post("fetch-unit", "TourController@fetchUnit");
        Route::post("update-price", "TourController@updatePrice");
        Route::get("invoice", "TourController@invoice");

        Route::get("passenger", "TourController@passenger");

    });
    Route::get('email/register', function () {
        $config = \Illuminate\Support\Facades\Config::get('packageConfig.app');
        return view("frontend.email.$config.register");
    });
    Route::get('email/forgetpass', function () {
        $config = \Illuminate\Support\Facades\Config::get('packageConfig.app');
        return view("frontend.email.$config.forgetpass");
    });
    Route::get('email/verification', function () {
        $config = \Illuminate\Support\Facades\Config::get('packageConfig.app');
        return view("frontend.email.$config.verification");
    });

    Route::get('email/email', function () {
        $config = \Illuminate\Support\Facades\Config::get('packageConfig.app');
        return view("frontend.email.$config.email");


    });

    Route::get('email/forgetpass', function () {
        $config = \Illuminate\Support\Facades\Config::get('packageConfig.app');
        return view("frontend.email.$config.forgetpass");


    });


//Route::get('portal/blog/post',function(){
//        return view('frontend.adminportal.blog.createPost');
//    });


    Route::group(["prefix" => "order"], function () {
        Route::post("bus/create", "OrderController@create");
        Route::post("flight/create", "OrderController@create");
        Route::post("hotel/create", "OrderController@create");
        Route::post("train/create", "OrderController@create");
        Route::post("tour-packages/create", "OrderController@create");

    });
    Route::group(["prefix" => "tour-package", "namespace" => "TourPackage"], function () {
        Route::get("detail", "TourController@detail");
    });
    Route::get('flight/invoice', 'FlightController@invoice');
    Route::get('hotel/invoice', 'HotelController@invoice');
    Route::group(["prefix" => "bus", "namespace" => "Bus"], function () {
        Route::post('loadsearch', 'BusController@loadSearch');
        Route::get("search", "BusController@search");
        Route::post("seats", "BusController@seats");
        Route::post("reserve", "BusController@reserve");
        Route::get("invoice", "BusController@invoice");
        Route::post("update-dates", "BusController@updateDates");
    });
    Route::get('ticket-hotel', function () {
        return view('frontend.hotel.ticket');
    });
    Route::get('rent-car', function () {
        return view('frontend.hotel.rentcar');
    });
    Route::get('hoteladmin', function () {
        return view('frontend/extras/hotelAdminLogin');
    });

    // home
    Route::get('/', 'HomeController@index');
    Route::get('advertise', 'HomeController@advertise');
    Route::get('hotels', 'HomeController@hotels');
    Route::get('flights', 'HomeController@flights');
    Route::get('vacations', 'HomeController@vacations');
    Route::get('PackageTours', 'HomeController@PackageTours');
    Route::get('Trains', 'HomeController@Trains');
    Route::get('Bus', 'HomeController@Bus');
    Route::get('Rentacar', 'HomeController@Rentacar');
    Route::get('Classifieds', 'HomeController@Classifieds');

    /*
    |--------------------------------------------------------------------------
    | User Profile
    |--------------------------------------------------------------------------
    */
    Route::get('user/register', 'UserController@register');
    Route::post('user/register', 'UserController@register');
    Route::get('user/verifyRegister', 'UserController@verifyRegister');
    Route::post('user/verifyRegister', 'UserController@verifyRegister');
    Route::post('user/resendVerify', 'UserController@resendVerify');
    Route::post('user/forgotPassword', 'UserController@forgotPassword');
    Route::post('user/confirmVerifyCode', 'UserController@confirmVerifyCode');
    Route::post('user/setNewPassword', 'UserController@setNewPassword');
    Route::get('user/changepassword/{code}', 'UserController@changePassword');
    Route::post('user/changepassword/{code}', 'UserController@changePassword');
    Route::get('user/booking/bookings', 'UserController@userbookings');
    Route::group(['middleware' => ['auth']], function () {
        Route::post('user/update-password', 'UserController@updateUserPassword');
        Route::get('user/update-password', 'UserController@getUserPassword');
        Route::get('user/contact-detail', 'UserController@contactDetail');
//        Route::post('user/update-user', 'UserController@updateUser'); OLD
        Route::post('user/update-user', 'UserController@newFunctionForUpdateUser');
        Route::post('user/remove-avatar', 'UserController@removeAvatar');
        Route::get('user', 'UserController@index');
        Route::get('user/booking/flight', 'UserController@userFlightBooking');
        Route::get('user/booking/hotel', 'UserController@userHotelBooking');
        Route::get('user/booking/train', 'UserController@userTrainBooking');
        Route::get('user/booking/bus', 'UserController@userBusBooking');
        Route::get('user/booking/car', 'UserController@userCarBooking');
        Route::get('user/booking/vacation', 'UserController@userVacationBooking');
        Route::get('user/booking/tour', 'UserController@userTourBooking');

        Route::get('user/booking/passengerDetail', 'UserController@userPassengerDetail');
        Route::post('user/booking/passengerDetail', 'UserController@addPassengerDetail');
        Route::post('user/booking/updatePassengerDetail', 'UserController@updatePassengerDetail');
        Route::post('user/booking/deletePassenger', 'UserController@deletePassenger');
        Route::get('user/booking/ewallet', 'UserController@userEwallet');
        Route::get('user/booking/myPoint', 'UserController@userPoint');
        Route::get('user/booking/inbox', 'UserController@userInbox');
        Route::post('user/booking/inbox', 'UserController@userInbox');

        Route::get('user/booking/myBooking', 'UserController@userMyBooking');

    });


    /*
    |--------------------------------------------------------------------------
    | Flight
    |--------------------------------------------------------------------------
    */
    Route::post('flight/loadsearch', 'FlightController@loadSearch');
    Route::get('flight/loadsearch', 'FlightController@loadSearch');
    Route::post('flight/search', 'FlightController@index');
    Route::get('flight/search', 'FlightController@index');
    Route::post('flight/loadpassenger', 'FlightController@loadPassenger');
    Route::get('flight/passenger', 'FlightController@passengersInfo');
    Route::post('flight/passenger', 'FlightController@passengersInfo');
    Route::post('flight/result', 'FlightController@flightDetailValidation');
    Route::get('passengersvalidation', 'FlightController@passengersValidation');
    Route::get('showticket/{paymentId}', 'FlightController@showTicket');
    Route::post('ticket', 'FlightController@ticket');
    Route::get('rollback', 'PaymentController@rollBackView');


    /*
    |--------------------------------------------------------------------------
    | Hotel
    |--------------------------------------------------------------------------
    */
//    Route::post('hotel/selectroom', 'HotelController@selectRoom');
    Route::get('searchhotelnew', 'HotelController@searchHotelNew');


    Route::get('hotel/selectroom', 'HotelController@selectRoom');
    Route::post('hotel/roominfo', 'HotelController@roomInfo');
//    Route::post('hotel/passengerinfo','HotelController@passengerInfo');
    Route::get('hotel/passengerinfo', 'HotelController@passengerInfo');
    Route::post('hotel/loadsearch', 'HotelController@loadSearch');
    Route::post('hotel/savedetails', 'HotelController@hotelDetailValidation');

    Route::get('hotel/showticket/{paymentId}', 'HotelController@showTicket');
    Route::get('hotel/result', 'HotelController@result');

//    Route::post('hotel/searchhotel', 'HotelController@searchHotel');
    Route::get('hotel/searchhotel', 'HotelController@searchHotel');
    Route::post('hotel/searchhotel', 'HotelController@searchHotel');
    Route::post('hotel/content', 'HotelController@contentHotel');
    Route::post('hotel/getAllPriceHotel', 'HotelController@getAllPriceHotel');
    Route::post('hotel/ajaxGetCityByCountryName', 'HotelController@ajaxGetCityByCountryName');
    Route::post('hotel/ajaxGetHotelByStateId', 'HotelController@ajaxGetHotelByStateId');
    Route::post('hotel/ajaxGetHotelDetailsByStateId', 'HotelController@ajaxGetHotelDetailsByStateId');


    /*
    |--------------------------------------------------------------------------
    | Car
    |--------------------------------------------------------------------------
    */

    Route::post('car/search', 'CarController@carSearch');
    Route::get('car/search', 'CarController@carSearch');
    Route::post('car/extra', 'CarController@carExtra');
    Route::post('car/review', 'CarController@review');
    Route::get('car/result', 'CarController@result');
    Route::post('car/savecardetail', 'CarController@saveCarDetail');
    Route::get('car/showticket/{paymentId}', 'CarController@showTicket');


    /*
    |--------------------------------------------------------------------------
    | Classified
    |--------------------------------------------------------------------------
    */


    Route::get('classified', 'ClassifiedController@index');
    Route::post('classified', 'ClassifiedController@index');
    Route::get('classified/detail/{id}', 'ClassifiedController@tourDetail');
    Route::post('classified/search', 'ClassifiedController@classifiedSearch');
    Route::get('classified/agancydetail/{id}', 'ClassifiedController@agancyDetail');


    /*
    |--------------------------------------------------------------------------
    | Classified
    |--------------------------------------------------------------------------
    */
    Route::post('vacation/search', 'VacationController@search');
    Route::get('vacation/passenger', 'VacationController@passenger');


    /*
    |--------------------------------------------------------------------------
    | Package Tour
    |--------------------------------------------------------------------------
    */

    Route::get('packagetours/home', 'PackageTourController@home');


    /*
    |--------------------------------------------------------------------------
    | Train
    |--------------------------------------------------------------------------
    */

    Route::group(['prefix' => 'train', 'namespace' => 'Train'], function () {
        Route::post('loadsearch', 'TrainController@loadSearch');
        Route::get('search', 'TrainController@search');
        Route::get('passengersInfo', 'TrainController@passengersInfo');
        Route::get('passengersInfofood', 'TrainController@passengersInfoFood');
        Route::get('passengers-validation', 'TrainController@passengersValidation');
        Route::get('getStations', 'TrainController@getStationsList');
        Route::get('stationList', 'TrainController@stationList');
        Route::post('updateStations', 'TrainController@updateStations');
        Route::post('purchase', 'OrderController@purchase');
        Route::get('invoice', 'TrainController@invoice');
    });

    Route::group(['prefix' => 'order'], function () {
        Route::post('purchase', 'OrderController@create');
    });

    /*
    |--------------------------------------------------------------------------
    | BUS
    |--------------------------------------------------------------------------
    */


    /*    Route::get('bus/search','Bus\BusController@searchView');
        Route::get('bus/showticket','Bus\BusController@showTicketView');*/


    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */
    Route::group(['middleware' => ['checkRole']], function () {
        $permissions = Permission::All();
        foreach ($permissions as $permission) {
            Route::get($permission->path, [
                'uses' => $permission->controller . '@' . $permission->function,
                'roles' => $permission->code
            ]);
        }
        foreach ($permissions as $permission) {
            Route::post($permission->path, [
                'uses' => $permission->controller . '@' . $permission->function,
                'roles' => $permission->code
            ]);
        }

//        Route::get('admin/dashboard', ['uses'=>'Admin\AdminClassifiedController@dashboard' , 'roles'=>'ACCESS_DASHBOARD']);

    });


    Route::post('admin/login', 'Auth\LoginController@loginAdmin');
    Route::get('admin/login', 'Auth\LoginController@loginAdmin');

    Route::get('admin/updateagency', 'Admin\AdminHotelController@updateAgencyDetail');
    Route::post('admin/updateagency', 'Admin\AdminHotelController@updateAgencyDetail');


    /*Classified*/
    /*Route::group(['middleware'=>['classified'] ] , function(){
        Route::get('admin/classified/add', 'Admin\AdminClassifiedController@addTour');
        Route::post('admin/classified/add', 'Admin\AdminClassifiedController@addTour');
    });*/


    /*
    |--------------------------------------------------------------------------
    | Footer Link Page
    |--------------------------------------------------------------------------
    */
// Route::resource('companyInfo','FooterController');


    Route::group(['prefix' => 'companyInfo'], function () {
        Route::get("visa", "FooterController@visa");
        Route::get("contactus", "FooterController@contactUs");
        Route::get("workwithus", "FooterController@workWithUs");
        Route::post("workwithus", "FooterController@workWithUs");
        Route::get("aboutus", "FooterController@aboutUs");
        Route::get("job", "FooterController@job");
        Route::post("job", "FooterController@job");
        Route::get("rulespaymentcharter", "FooterController@rulesPaymentCharter");
        Route::get("regulationspaymentsystem", "FooterController@regulationsPaymentSystem");
        Route::get("regulationspaymentrail", "FooterController@regulationsPaymentRail");
        Route::get("regulationspaymentbus", "FooterController@regulationsPaymentBus");
        Route::get("regulationspaymenthotel", "FooterController@regulationsPaymentHotel");
        Route::get("articles", "FooterController@articles");
        Route::get("question", "FooterController@question");
        Route::get("guide", "FooterController@guide");
        Route::get("touristinformation", "FooterController@touristInformation");
        Route::get("classtypesflying", "FooterController@classTypesFlying");
        Route::get("healthregulations", "FooterController@healthRegulations");
        Route::get("importantpoints", "FooterController@importantPoints");
        Route::get("listofvirtualobjects", "FooterController@listOfVirtualObjects");
        Route::get("passengerrights", "FooterController@passengerRights");
        Route::get("typesofflights", "FooterController@typesOfFlights");
        Route::get("generaltermsandconditions", "FooterController@generalTermsAndConditions");
        Route::get("applications", "FooterController@applications");
        Route::get("ios", "FooterController@ios");
        Route::get("rulesandregulationseuropcar", "FooterController@rulesAndRegulationsEuropcar");
        Route::get("recommendationseuropcar", "FooterController@recommendationsEuropcar");
        Route::get("ticketcheckin", "FooterController@ticketCheckin");
        Route::get("tickettypes", "FooterController@ticketTypes");
        Route::get("flightterm", "FooterController@flightTerm");
        Route::get("charterflights", "FooterController@charterFlights");
        Route::get("tophotels", "FooterController@topHotels");
        Route::get("hotelterms", "FooterController@hotelTerms");
        Route::get("vacationterms", "FooterController@vacationTerms");
        Route::get("faqvacation", "FooterController@faqVacation");
        Route::get("carrental", "FooterController@carRental");
        Route::get("caragencies", "FooterController@carAgencies");
        Route::get("carterm", "FooterController@carTerm");
        Route::get("generalcriteria", "FooterController@generalCriteria");
        Route::get("faqrentacar", "FooterController@faqRentacar");
        Route::get("trainterm", "FooterController@trainTerm");
        Route::get("busterm", "FooterController@busTerm");
        Route::get("buscompanies", "FooterController@busCompanies");
        Route::get("faq", "FooterController@faq");
        Route::get("policy", "FooterController@policy");
    });


    /*	Route::get('aboutus' , 'FooterController@aboutUs');
            Route::get('contactus' , 'FooterController@contactUs');
            Route::get('rolespayticket' , 'FooterController@rulesPaymentCharter');
            Route::get('regulationspayticket' , 'FooterController@regulationsPaymentSystem');
            Route::get('regulationspayticketrail' , 'FooterController@regulationsPaymentRail');
            Route::get('regulationspayticketbus' , 'FooterController@regulationsPaymentBus');
            Route::get('regulationspaytickethotel' , 'FooterController@regulationsPaymentHotel');
            Route::get('articles' , 'FooterController@articles');
            Route::get('question' , 'FooterController@question');
            Route::get('guide' , 'FooterController@guide');
            Route::get('Touristinformation' , 'FooterController@Touristinformation');
            Route::get('Classtypesflying' , 'FooterController@Classtypesflying');
            Route::get('HealthRegulations' , 'FooterController@HealthRegulations');
            Route::get('importantpoints' , 'FooterController@importantpoints');
            Route::get('ListOfVirtualObjects' , 'FooterController@ListOfVirtualObjects');
            Route::get('Passengerrights' , 'FooterController@Passengerrights');
            Route::get('TypesOfFlights' , 'FooterController@TypesOfFlights');
            Route::get('GeneralTermsandConditions' , 'FooterController@GeneralTermsandConditions');
            Route::get('workWithUs' , 'FooterController@workWithUs');
            Route::post('workWithUs' , 'FooterController@workWithUs');*/

    /*
    |--------------------------------------------------------------------------
    | Error Page
    |--------------------------------------------------------------------------
    */
    Route::get('errorpage', 'HomeController@errorPage');
    Route::get('errorpage404', 'HomeController@errorPage404');


//Route::get('google-user',array('as'=>'user.glist','uses'=>'UserController@listGoogleUser')) ;


});

Route::group(['prefix' => 'api'], function () {
    Route::post('train/search', 'Train\TrainWebserviceController@search');
    Route::post('train/lockSeat', 'Train\TrainWebserviceController@doLockSeat');
    Route::post('train/issueTicket', 'Train\TrainWebserviceController@issueTicket');
    Route::post('train/getPrice', 'Train\TrainWebserviceController@getPrice');
    Route::post('train/getOptionalServices', 'Train\TrainWebserviceController@getOptionalServices');
    Route::post('train/ticketReport', 'Train\TrainWebserviceController@ticketReport');
    Route::get('train/getTicketReport', 'Train\TrainWebserviceController@getTicketReport');
    Route::post('train/getErrorMessage', 'Train\TrainWebserviceController@getErrorMessage');
//    Route::get('portal/blog/createPost','blog\blogController@createPost');
});

Route::post('irankish-gateway', function (Illuminate\Http\Request $request) {

    $data['status'] = $request->status;
    $data['token'] = $request->token;
    $data['merchantId'] = $request->merchantId;
    $data['amount'] = $request->amount;
    $data['paymentId'] = $request->paymentId;
    $data['referenceId'] = $request->referenceId;
    $data['resultCode'] = $request->resultCode;

    return redirect('callback-irankish');

    // return redirect(Request::get('redirectaddress'), $data);
});

Route::get('payment/happy', function () {

    $order = App\Order::where('invoice_number', '54112493')->first();

    $id = App\TrainOrder::finish($order->invoice_number);

    if ($id === false) {
        return Redirect::to(url("en/errorpage"))->with('errorMsg', trans('payment.error'));
    }

    return redirect(\App::getLocale() . "/train/invoice?id=$id");


    // $order = App\Order::where('invoice_number', '1001706096')->first();

    // dd( $order );

});

Route::get('verify/busTicket', function () {

    $order = App\Order::where('invoice_number', '16992364')->first();

    $id = App\BusOrder::finish($order->invoice_number);
    dd($id);
    if ($id === false) {
        return Redirect::to(url("en/errorpage"))->with('errorMsg', trans('payment.error'));
    }

    return redirect(\App::getLocale() . "/train/invoice?id=$id");

});

Route::any('ipgurl/callback', 'OrderController@bankCallback');

/*Route::get('printticket/{id}' , 'FlightController@printTicket');
Route::post('send', 'EmailController@send');*/


Route::get('bus/api/getPathTest', function () {

    $terminalsPath = json_decode(file_get_contents('terminalsPath.json'));
    $busPath = $terminalsPath->path;

    $sourceCities = json_decode(file_get_contents('sourceCities.json'));
    $destinationCities = json_decode(file_get_contents('destinationCities.json'));

    foreach ($busPath as $path) {

        $error = false;

        if (isset($sourceCities->{$path->source})) {

            $sourceStationName = $sourceCities->{$path->source};

            $sourceStationName = str_replace('(', ' ', $sourceStationName);
            $sourceStationName = str_replace(')', ' ', $sourceStationName);
            $sourceStationName = str_replace('   ', ' ', $sourceStationName);
            $sourceStationName = str_replace('  ', ' ', $sourceStationName);
            $sourceStationName = trim($sourceStationName);

        } else {

            $error = true;

        }

        if (isset($destinationCities->{$path->distin})) {

            $destinationStationName = $destinationCities->{$path->distin};

            $destinationStationName = str_replace('(', ' ', $destinationStationName);
            $destinationStationName = str_replace(')', ' ', $destinationStationName);
            $destinationStationName = str_replace('   ', ' ', $destinationStationName);
            $destinationStationName = str_replace('  ', ' ', $destinationStationName);
            $destinationStationName = trim($destinationStationName);

        } else {

            $error = true;

        }


        if (!$error) {

            $sourceTerminal = (object)App\BusTerminal::where('code', $path->source)->first();

            if (!isset($sourceTerminal->id)) {

                $terminalCityId = -1;

            } else {

                $terminalCityId = (int)$sourceTerminal->city_id;

            }

            $sourceCity = (object)App\BusCities::find($terminalCityId);

            if (isset($sourceCity->city_name)) {

                $sourceCityName = $sourceCity->city_name;

                $sourceCityName = str_replace('(', '', $sourceCityName);
                $sourceCityName = str_replace(')', '', $sourceCityName);
                $sourceCityName = str_replace('   ', ' ', $sourceCityName);
                $sourceCityName = str_replace('  ', ' ', $sourceCityName);
                $sourceCityName = trim($sourceCityName);

            } else {

                $sourceCityName = null;

            }

            $destinationTerminal = (object)App\BusTerminal::where('code', $path->distin)->first();

            if (!isset($destinationTerminal->id)) {

                $terminalCityId = -1;

            } else {

                $terminalCityId = (int)$destinationTerminal->city_id;

            }

            $destinationCity = (object)App\BusCities::find($terminalCityId);

            if (isset($destinationCity->city_name)) {

                $destinationCityName = $destinationCity->city_name;

                $destinationCityName = str_replace('(', '', $destinationCityName);
                $destinationCityName = str_replace(')', '', $destinationCityName);
                $destinationCityName = str_replace('   ', ' ', $destinationCityName);
                $destinationCityName = str_replace('  ', ' ', $destinationCityName);
                $destinationCityName = trim($destinationCityName);

            } else {

                $destinationCityName = null;

            }

            $destinationCity = (object)App\BusCities::find($terminalCityId);

            $bPath = new App\BusPath();
            $bPath->origin_station_id = (isset($sourceTerminal->code)) ? $sourceTerminal->code : null;
            $bPath->origin_station_name_fa = $sourceStationName;
            $bPath->origin_station_name_en = null;
            $bPath->origin_station_city_id = (isset($sourceCity->code)) ? $sourceCity->code : null;
            $bPath->origin_station_city_name_fa = $sourceCityName;
            $bPath->origin_station_city_name_en = null;
            $bPath->destination_station_id = (isset($destinationTerminal->code)) ? $destinationTerminal->code : null;
            $bPath->destination_station_name_fa = $destinationStationName;
            $bPath->destination_station_name_en = null;
            $bPath->destination_station_city_id = (isset($destinationCity->code)) ? $destinationCity->code : null;
            $bPath->destination_station_city_name_fa = $destinationCityName;
            $bPath->destination_station_city_name_en = null;
            $bPath->origin_value = $sourceStationName;
            $bPath->destination_value = $destinationStationName;
            $bPath->save();

            // dd( $bPath->id );

        }

    }

});
