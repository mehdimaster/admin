<?php

namespace App\Http\Controllers\Admin\Users;

use App;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Plugins;
use App\USRPersons;
use App\USRRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Date;
use Plugin;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Rules\validNationalCode;
use App\Helpers\Helper;


class UsersController extends Controller
{
    protected function userCreateValidator(array $data)
    {
        return Validator::make($data, [
            'email' => 'nullable|email|unique:users,email,NULL,id,deleted_at,NULL',
            'mobile' => 'required|numeric|digits:11|unique:users,mobile,NULL,id,deleted_at,NULL',
            'name' => 'max:255',
            'family' => 'max:255',
            'national_code' => ['nullable', 'digits:10', new validNationalCode],
            'password' => 'required'
        ]);
    }

    protected function userUpdateValidator(array $data, $user_id)
    {
        return Validator::make($data, [
            'mobile' => 'required|unique:users,mobile,' . $user_id . ',id,deleted_at,NULL',
            'email' => 'nullable|email|unique:users,email,' . $user_id . ',id,deleted_at,NULL',
            'name' => 'max:255',
            'family' => 'max:255',
            'national_code' => ['nullable', 'digits:10', new validNationalCode],
            'dob' => 'required_with:month,day,year',
            'password' => 'nullable'
        ]);
    }

    public function users()
    {
        $data = [];
        $data['user'] = [];
        $data['isAjancy'] = 1;
        $data['currentYear'] = Carbon::today()->format('Y');
        $data['roles'] = USRRole::select('name', 'id')->get();
        $data['placeHolder'] = asset('/assets/gfx/' . Config::get('packageConfig.app') . '/userprofile/user-pic.png');

        return view('admin.users.users', compact('data'));
    }

    public function anyData(Request $request)
    {
        $nameQString = $request->input('search_name');
        $familyQString = $request->input('search_family');
        $emailQString = $request->input('search_email');
        $mobileQString = $request->input('search_mobile');
        $nationalQString = $request->input('search_national');
        $roleQString = $request->input('search_role');
        $rolesArray = Helper::stringArrayConvertToIntArray($roleQString);

        return Datatables::of(USRPersons::select(['*'])->with('oneUser'))
            ->editColumn('name', function ($person) {
                return $person->name ? $person->name : '--';
            })
            ->editColumn('family', function ($person) {
                return $person->family ? $person->family : '--';
            })
            ->editColumn('email', function ($person) {
                if ($person->oneUser && $person->oneUser->email) {
                    return $person->oneUser->email;
                } else {
                    return '--';
                }
            })
            ->editColumn('mobile', function ($person) {
                if ($person->oneUser && $person->oneUser->mobile) {
                    return '<div class="ltr">' . $person->oneUser->mobile . '</div>';
                } else {
                    return '--';
                }
            })
            ->editColumn('dob', function ($person) {
                return $person->dob ? $person->dob : '--';
            })
            ->editColumn('national_code', function ($person) {
                $final = '';

                if ($person->national_code) {
                    $final .= $person->national_code;
                }

                if ($person->passport_number) {
                    $person->national_code ? $final .= '/' : '';

                    $final .= $person->passport_number;
                }

                return $final ? $final : '--';
            })
            ->addColumn('roles', function ($person) {
                $final = null;
                $roles = $person->oneUser->getRoleNames();
                return $this->renderRoles($roles, $person->oneUser->user_status);
            })
            ->addColumn('action', function ($user) {
                return $this->render($user);
            })
            ->filter(function ($query) use ( $nameQString, $familyQString, $emailQString, $mobileQString, $nationalQString, $rolesArray) {

                if ($nameQString) {
                    $query->where('name', 'like', "%" . $nameQString . "%");
                }

                if ($familyQString) {
                    $query->where('family', 'like', "%" . $emailQString . "%");
                }

                if ($nationalQString) {
                    $query->where('national_code', 'like', "%" . $nationalQString . "%")
                        ->orWhere('passport_number', 'like', "%" . $nationalQString . "%");
                }

                if ($emailQString) {
                    $query->whereHas('oneUser', function ($q) use ($emailQString) {
                        $q->where('users.email', 'like', "%" . $emailQString . "%");
                    });
                }

                if ($mobileQString) {
                    $query->whereHas('oneUser', function ($q) use ($mobileQString) {
                        $q->where('users.mobile', 'like', "%" . $mobileQString . "%");
                    });
                }

                if ($rolesArray) {
                    $query->whereHas('oneUser.getRoles', function ($q) use ($rolesArray) {
                        $q->whereIn('usr_roles.id', $rolesArray);
                    });
                }

            })
            ->rawColumns(['mobile', 'action'])
            ->make(true);
    }

    public function render($person)
    {
        $final = null;
        $final .= '<button type="button" class="infobtn edit-btn" id="edit-btn" data-toggle="modal" title="ویرایش"
                    data-target="#modal-edit-user"
                    data-id="' . $person->id . '">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                   </button>';

        $final .= '<button type="button" class="infobtn delete-btn" data-id="' . $person->id . '"  title="حذف">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                   </button>';
        return $final;
    }

    protected function renderRoles($roles, $agancyType)
    {
        $final = null;
        if ($agancyType == 1) {
            $final .= ' آژانس ';
        }

        $total = $roles->count();

        if ($agancyType == 1 &  $total > 0) {
            $final .= ' | ';
        }

        foreach ($roles as $key => $role) {
            $final .= $role;
            $final .= ($key <= $total - 2) ? ' | ' : '';
        }
        return $final;
    }

    public function userCreate(Request $request)
    {

        if(!$request->exists('password')){
            $password = Helper::quickRandomNumber(8);
            $request->request->add([
                'password' => $password
            ]);
        }

        $avatar = $request->input('avatarName');
        $request->merge(['mobile' => str_replace("-","",$request->input('mobile'))]);
        $request->merge(['national_code' => str_replace("-","",$request->input('national_code'))]);
//        $request->merge(['dob' => str_replace("-","",$request->input('dob'))]);
        $this->userCreateValidator($request->all())->validate();
        $personData = $request->only(['name', 'family', 'dob', 'national_code', 'gender_id' , 'address']);
        $currentUser = Auth::user();

        DB::beginTransaction();
        try {
            $person = USRPersons::create($personData);
            $userData = [
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => $request->input('password') ? bcrypt($request->input('password')) : null,
                'create_user_id' => $currentUser ? $currentUser->id : null,
                'active' => 1,
            ];
            $person->user()->create($userData);

            if ($avatar) {
                $tempAvatar = base_path('public\pictures\tempAvatar\\' . $avatar);
                $path_info = pathinfo($tempAvatar);

                $user = $person->oneUser;
                $newName = 'u' . $user->id . '_' . time() . '_' . rand(999, 9999) . '.' . $path_info['extension'];

                $mainAvatar = base_path('public\pictures\avatar\\' . $newName);

                if (file_exists($tempAvatar)) {
                    $success = \File::copy($tempAvatar, $mainAvatar);
                    if ($success) {
                        $user->avatar = $newName;
                        $user->update();
                    }
                    @unlink($tempAvatar);
                }
            }

            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
            return $e;
        }

        if ($success) {
            return response()->json(['status' => 'success', 'message' => 'success']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Transaction Error']);
        }
    }

    public function userUpdate(Request $request)
    {
        $personId = $request->input('edit_identifier');
        $person = USRPersons::whereId($personId)
            ->with(['oneUser' => function ($q) {
                $q->select(['id', 'person_id', 'email', 'mobile']);
            }])
            ->first();
        $user = $person->oneUser;
        if ($user->email) {
            $request->merge(array('email' => $user->email));
        } else {
            $request->merge(array('email' => $request->input('email')));
        }

        if ($user->mobile) {
            $request->merge(array('mobile' => $user->mobile));
        } else {
            $request->merge(array('mobile' => str_replace("-","",$request->input('email'))));
        }
        $request->merge(array('national_code' => str_replace("-","",$request->input('national_code'))));
        $this->userUpdateValidator($request->all(), $person->oneUser->id)->validate();

        $personData = $request->only(['name', 'family', 'dob', 'national_code', 'gender_id' , 'address']);
        $currentUser = Auth::user();

        DB::beginTransaction();
        try {
            $person->update($personData);

            $userData = [
                'update_user_id' => $currentUser ? $currentUser->id : null
            ];

            if ($request->input('password')) {
                $userData = array_add($userData, 'password', bcrypt($request->input('password')));
            }

            if (!$user->email) {
                $userData = array_add($userData, 'email', $request->input('email'));
            }

            if (!$user->mobile) {
                $userData = array_add($userData, 'mobile', $request->input('mobile'));
            }

            $user->update($userData);

            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        }

        if ($success) {
            return response()->json(['status' => 'success', 'message' => 'success']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'error']);
        }
    }

    public function userDelete(Request $request)
    {
        $id = $request->input('identifier');
        $person = USRPersons::whereId($id)->with('oneUser')->first();

        DB::beginTransaction();
        try {
            $person->oneUser->syncRoles([]);
            $person->oneUser->syncPermissions([]);
            if ($person->oneUser->mockAccess) {
                $person->oneUser->mockAccess->forceDelete();

            }
            $person->delete();
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        }


        if ($success)
            return response()->json(['status' => 'success', 'message' => $id]);
        else
            return response()->json(['status' => 'error', 'message' => $id]);
    }

    public function getUserInfoById(Request $request)
    {
        $personId = $request->input('identifier');
        $person = USRPersons::whereId($personId)
            ->with(['oneUser' => function ($q) {
                $q->select(['id', 'person_id', 'email', 'mobile', 'avatar']);
            }])
            ->first();

        $mobile = $person->oneUser ? $person->oneUser->mobile : null;

        return response()->json(['status' => 'success', 'user' => $person]);
    }

    public function uploadUserAvatar(Request $request)
    {

        if (!Input::file('file')) {
            return json_encode([
                "status" => "failed"
            ]);
        }

        $identifier = $request->input('identifier');
        $uploadType = $request->input('uploadType');

        $file = array('file' => Input::file('file'));
        $rules = array('file' => 'required|mimes:jpeg,jpg,png');
        $validator = Validator::make($file, $rules);
        if ($validator->fails()) {
            return json_encode([
                "status" => "failed"
            ]);
        }


        if ($uploadType == 'add') {
            $user = null;
            $destinationPath = Helper::pathFile('tempAvatar');
            $generatedFileName = rand(1111111111, 9999999999);
        } else {
            $person = USRPersons::whereId($identifier)->with('oneUser')->first();
            $user = $person->oneUser ? $person->oneUser : null;

            $destinationPath = Helper::pathFile('avatar');
            $generatedFileName = 'u' . $user->id . '_' . time() . '_' . rand(999, 9999);

            if ($user && $user->avatar != '') {
                $avatar = public_path() . "\pictures\avatar" . '\\' . $user->avatar;
                if (file_exists($avatar)) {
                    unlink($avatar);
                }
            }
        }


        if (Input::file('file')->isValid()) {
            $extension = Input::file('file')->getClientOriginalExtension();
            $fileName = $generatedFileName . '.' . $extension;
            $img = Image::make(Input::file('file'))->resize(400, 400)->save($destinationPath . '/' . $fileName);

            if ($user) {
                $user->avatar = $fileName;
                $user->update();
            }

            return json_encode([
                'status' => 'success',
                'result' => [
                    'file' => $fileName,
                    'type' => $uploadType
                ],
                'code' => 1
            ]);
        }

        return json_encode([
        "status" => "failed"
    ]);
    }

}
