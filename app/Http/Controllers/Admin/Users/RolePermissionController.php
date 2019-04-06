<?php

namespace App\Http\Controllers\Admin\Users;

use App;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Plugins;
use App\User;
use App\USRMockAccess;
use App\USRPermission;
use App\USRPersons;
use App\USRRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class RolePermissionController extends Controller
{
    protected function roleCreateValidator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255'
        ]);
    }
    // --------------------------------------------------------------------------------
    public function group()
    {
        $permissionList = USRPermission::select(['id' , 'f_name'])->where('parent' , 0)->get();
        return view('admin.users.group' , compact('permissionList'));
    }
    // --------------------------------------------------------------------------------
    public function rolesAnyData()
    {
        return Datatables::of(USRRole::select(['id' , 'name']))
            ->addColumn('action' , function ($user) {
                return $this->render($user);
            })
            ->make(true);
    }
    // --------------------------------------------------------------------------------
    public function render( $role ) {
        $final = null;
        $final .= '<button type="button" class="infobtn editbtn" id="edit-btn" data-toggle="modal" title="ویرایش"
                    data-target="#editRoleModal"
                    data-id="'.$role->id.'">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                   </button>';

        $final .= '<button type="button" class="infobtn deletebtn" data-id="'.$role->id.'" data-toggle="modal" data-target="#deleteModal" title="حذف">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                   </button>';

        return $final;
    }
    // --------------------------------------------------------------------------------
    public function createRole(Request $request)
    {
        $this->roleCreateValidator($request->all())->validate();
        $permissions = $request->input('permissions');
        $data = $request->except(['_token' , 'permissions']);

        DB::beginTransaction();
        try
        {
            $role = Role::create($data);

            if ($permissions) {
                $permissions = App\Helpers\Helper::stringArrayConvertToIntArray($permissions);
                $childPermissions = USRPermission::whereIn('parent' , $permissions)->pluck('id')->toArray();
                $finalPermissions = array_merge($permissions , $childPermissions);
                $role->syncPermissions($finalPermissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();
            $success = true;
        }
        catch (\Exception $e)
        {
            $success = false;
            DB::rollback();
        }

        if ($success) {
            return response()->json(['status' => 'success' , 'message' => 'success']);
        } else {
            return response()->json(['status' => 'error' , 'message' => 'error']);
        }
    }
    // --------------------------------------------------------------------------------
    public function deleteRole(Request $request)
    {
        $id = $request->input('identifier');
        $role = USRRole::find($id);
        $role->delete();
        return response()->json(['status' => 'success' , 'message' => 'deleted']);
    }
    // --------------------------------------------------------------------------------
    public function getRoleInfo(Request $request)
    {
        $id = $request->input('identifier');
        $role = USRRole::select(['id' , 'name'])->whereId($id)->with('rolePermissions')->first();

        $permissionsArray = [];
        foreach ($role->rolePermissions as $permission) {
            $permissionsArray[] = $permission->permission_id;
        }

        return response()->json(['status' => 'success' , 'role' => $role , 'permissions' => $permissionsArray]);
    }
    // --------------------------------------------------------------------------------
    public function updateRole(Request $request)
    {
        $this->roleCreateValidator($request->all())->validate();
        $id = $request->input('identifier');
        $permissions = $request->input('permissions');
        $name = $request->input('name');
        DB::beginTransaction();
        try
        {
            $role = Role::findById($id);
            if ($permissions) {
                $permissions = App\Helpers\Helper::stringArrayConvertToIntArray($permissions);
                $childPermissions = USRPermission::whereIn('parent' , $permissions)->pluck('id')->toArray();
                $finalPermissions = array_merge($permissions , $childPermissions);
                $role->syncPermissions($finalPermissions);
            } else {
                $role->syncPermissions([]);
            }

            $role->name = $name;
            $role->update();

            DB::commit();
            $success = true;
        }
        catch (\Exception $e)
        {
            $success = false;
            DB::rollback();
        }

        if ($success) {
            return response()->json(['status' => 'success' , 'message' => 'success']);
        } else {
            return response()->json(['status' => 'error' , 'message' => 'error']);
        }
    }
    // --------------------------------------------------------------------------------
    // --------------------------------------------------------------------------------
    public function permissionIndex()
    {
        $data = [];
        $data['roles'] = USRRole::select('name' , 'id')->get();
        return view('admin.users.permission' , compact('data'));
    }
    // --------------------------------------------------------------------------------
    public function permissionAnyData(Request $request)
    {
        $model = USRPersons::query();
        return (new \Yajra\DataTables\DataTables)->eloquent($model)
            ->filter(function ($query) use ($request) {
                $query->select(['id' , 'name' , 'family']);
                $query->with(['oneUser' => function($q) {
                    $q->select(['id' , 'person_id' , 'email' , 'mobile']);
                }]);

                if ($request->has('name')) {
                    $query->where('name', 'like', "%" . request('name') . "%");
                }
            })
            ->editColumn('email' , function ($person) {
                return $person->oneUser ? $person->oneUser->email : '--';
            })
            ->editColumn('mobile' , function ($person) {
                return $person->oneUser->mobile ? $person->oneUser->mobile : '--';
            })
            ->addColumn('fullName' , function ($person) {
                $final = null;
                $final .= $person->name ? $person->name.' ' : '';
                $final .= $person->family ? $person->family : '';
                return $final;
            })
            ->addColumn('action' , function ($person) {
                return '<button type="button" class="infobtn editbtn-role" data-id="'.$person->oneUser->id.'" data-target="#editRolePermissionModal" data-toggle="modal" title="ویرایش سطح دسترسی">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                        </button>';
            })
            ->toJson();
    }
    // --------------------------------------------------------------------------------
    public function permissionSave(Request $request)
    {
        $user_id = $request->input('identifier');
        $roles = $request->input('roles');
        $extraPermissions = $request->input('extraPermissions');
        $denyPermissions = $request->input('denyPermissions');
        $mockData = [];
        $user = User::whereId($user_id)->first();
        $needToRemoveRole = [];
        $permissionsCreatedFromRoleRemoval = [];

        if ($user) {
            if ($denyPermissions) {
                $denyPermissions = App\Helpers\Helper::stringArrayConvertToIntArray($denyPermissions);
                // Remove denial from roles and permissions array
                foreach ($roles as $role){
                    $permissionsOfTheRole = USRPermission::whereHas('getRoles', function ($query) use ($role){
                        $query->where('usr_roles.id', $role);
                    })->pluck('id' , 'id')->toArray();

                    if (array_intersect( $permissionsOfTheRole , $denyPermissions)) {
                        array_push($needToRemoveRole, $role);
                        foreach ($denyPermissions as $deny) {
                            unset($permissionsOfTheRole[$deny]);
                        }
                        array_push($permissionsCreatedFromRoleRemoval, $permissionsOfTheRole);
                        $permissionsCreatedFromRoleRemoval = array_flatten($permissionsCreatedFromRoleRemoval);
                    }
                }

                $mockData['needToRemoveRole'] = App\Helpers\Helper::stringArrayConvertToIntArray($needToRemoveRole);
                $mockData['denialPermissions'] = $denyPermissions;
                $mockData['permissionsCreatedFromRoleRemoval'] = $permissionsCreatedFromRoleRemoval ;
            } else {
                $mockData['denialPermissions'] = null;
                $mockData['permissionOfRemovedRoles'] = null;
            }

            if ($roles) {
                $roles = App\Helpers\Helper::stringArrayConvertToIntArray($roles);
                $mockData['roles'] = $roles;
            } else {
                $mockData['roles'] = null;
            }

            if ($extraPermissions) {
                $extraPermissions = App\Helpers\Helper::stringArrayConvertToIntArray($extraPermissions);
                $mockData['extraPermissions'] = $extraPermissions ;
            } else {
                $mockData['extraPermissions'] = null;
            }


            DB::beginTransaction();
            try
            {
                $this->saveMockPermissions($mockData , $user->id);
                $this->saveRealPermissions($mockData , $user->id);
                DB::commit();
                $success = true;
            }
            catch (\Exception $e)
            {
                $success = false;
                DB::rollback();
            }

            if ($success)
                return response()->json(['status' => 'success']);
            else
                return response()->json(['status' => 'error']);
        }

        return response()->json(['status' => 'error' , 'message' => 'user not exist']);
    }
    // --------------------------------------------------------------------------------
    public function getUserRolePerInfo(Request $request)
    {
        $user_id = $request->input('identifier');
        $user = User::whereId($user_id)->with('mockAccess')->first();

        $selectedRolesArray = $user->mockAccess ? ($user->mockAccess->roles ? explode(',' , $user->mockAccess->roles) : []) : [];
        $selectedPermissions = $user->mockAccess ? explode(',' , $user->mockAccess->permissions) : [];
        $selectedDenialPermissions = $user->mockAccess ? explode(',' , $user->mockAccess->denialPermissions) : [];

        $permissionsOfSelectedRoles = [];

        if ($selectedRolesArray) {
            $permissionsOfSelectedRoles = USRPermission::whereHas('getRoles', function ($query) use ($selectedRolesArray){
                $query->whereIn('usr_roles.id', $selectedRolesArray);
            })->pluck('id', 'f_name')->toArray();
        }

        $allPermissions = USRPermission::pluck('id', 'f_name')->toArray();
        $permissionList = array_diff($allPermissions , $permissionsOfSelectedRoles);

        $denialPermissionList = USRPermission::whereHas('getRoles', function ($query) use ($selectedRolesArray){
            $query->whereIn('usr_roles.id', $selectedRolesArray);
        })->get();

        return response()->json(['status' => 'success' ,
                                 'roles' => $selectedRolesArray ,
                                 'permissionList' => $permissionList ,
                                 'selectedPermissions' => $selectedPermissions ,
                                 'denialPermissionList' => $denialPermissionList ,
                                 'selectedDenialPermission' => $selectedDenialPermissions
        ]);
    }
    // --------------------------------------------------------------------------------
    public function getNewPermissionOnRoleChange(Request $request)
    {
        $roles = $request->input('roles');
        $roles = App\Helpers\Helper::stringArrayConvertToIntArray($roles);

        $permissionsOfSelectedRoles = [];

        if ($roles) {
            $permissionsOfSelectedRoles = USRPermission::whereHas('getRoles', function ($query) use ($roles){
                $query->whereIn('usr_roles.id', $roles);
            })->pluck('id', 'f_name')->toArray();
        }

        $allPermissions = USRPermission::pluck('id', 'f_name')->toArray();
        $newExtraPermissions = array_diff($allPermissions , $permissionsOfSelectedRoles);


        $newDenialPermissions = [];
        if ($roles) {
            $newDenialPermissions = USRPermission::whereHas('getRoles', function ($query) use ($roles){
                    $query->whereIn('usr_roles.id', $roles);
            })->get();
        }


        return response()->json(['status' => 'success' , 'newPermissions' => $newExtraPermissions , 'newDenialPermissions' => $newDenialPermissions]);
    }
    // --------------------------------------------------------------------------------
    protected function saveMockPermissions($mockData , $userId){
        $data = [
            'roles' => isset($mockData['roles']) ? implode($mockData['roles'] , ',') : null ,
            'permissions' => isset($mockData['extraPermissions']) ? implode($mockData['extraPermissions'] , ',') : null ,
            'denialPermissions' => isset($mockData['denialPermissions']) ? implode($mockData['denialPermissions'] , ',') : null
        ];
        $res = USRMockAccess::updateOrCreate(['user_id' => $userId] , $data);
        return $res ? true : false;
    }
    // --------------------------------------------------------------------------------
    protected function saveRealPermissions($mockData , $userId){

        $user = User::whereId($userId)->first();

        if (isset($mockData['needToRemoveRole']) &&  $mockData['needToRemoveRole']) {
           $roles =  array_diff($mockData['roles'] , $mockData['needToRemoveRole']);
        } else {
            $roles =  $mockData['roles'];
        }
        $wPermissions =  isset($mockData['extraPermissions']) ? $mockData['extraPermissions'] : [];
        $rPermissions =  isset($mockData['permissionsCreatedFromRoleRemoval']) ? $mockData['permissionsCreatedFromRoleRemoval'] : [];
        $permissions = array_merge($wPermissions,$rPermissions);

        if ($roles) {
            $user->syncRoles($roles);
        } else {
            $user->syncRoles([]);
        }

        if ($permissions) {
            $user->syncPermissions($permissions);
        } else {
            $user->syncPermissions([]);
        }

        return true;
    }

}
