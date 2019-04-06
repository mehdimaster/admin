<?php

Route::get("admin/login","UserController@index");
Route::post("admin/login","UserController@login");
Route::prefix("/admin")->group(function(){

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
//'middleware' => ['portalAuthCheck']

