@extends("admin.master")
@section("content")
@section("title")
    مدیریت دسترسی
@stop
@section("css")
    <link href="{{asset("assets/plugins/admin/datatables/dataTables.min.css")}}" rel="stylesheet">

@stop
<div class="page-content">
    <div class="header">
        <h2>مدیریت دسترسی</h2>
    </div>

    <form class="form-users" action="" method="post">

        <div class="row">
            <div class=" col-md-12 col-lg-12 portlets">
                <div class="panel">
                    <div class="panel-content">
                        <table class="table table-hover dataTable" id="table-role">
                            <thead>
                            <tr>
                                <th>نام و نام خانوادگی</th>
                                <th>ایمیل</th>
                                <th>موبایل</th>
                                <th class="text-right">عملیات</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{csrf_field()}}
        <input type="hidden" class="color-id" name="color_id">
    </form>
    <!--portal-content-->
    @include('admin.users.modals.editRolePermissionModal')
</div>
@section("js_footer")
    <link href="{{asset("assets/plugins/admin/select2/select2.css")}}" rel="stylesheet" />
    <script src="{{asset("assets/plugins/admin/select2/select2.full.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/datatables/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/datatables/dataTables.bootstrap.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/bootstrap/js/jasny-bootstrap.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/noty/jquery.noty.packaged.min.js")}}"></script>

    <script src="{{asset("assets/js/config.js")}}"></script>
    <script src="{{asset("assets/js/admin/pages/table_editable_role.js")}}"></script>
    <script src="{{asset("assets/js/admin/pages/notifications.js")}}"></script>
    <script>
        const roleSelectElement = $('.chosen-role-select');
        const permissionSelectElement = $('.chosen-permission-select');
        const permissionDenyElement = $('.chosen-permission-deny-select');
        roleSelectElement.select2({no_results_text: "متاسفانه داده ای یافت نشد"}).change(function(){
            const rolesData = { roles : $(this).val() , _token : window.api.token};
            $.ajax({
                url: window.path.mainURL+'/admin/users/permission/getNewPermissionOnRoleChange',
                type: 'POST',
                data: rolesData,
                beforeSend: function() {
                    $('#loadingFilterDiv').show();
                },
                success: function( response ) {
                    $('#loadingFilterDiv').hide();
                    if (response.status === 'success') {
                        const oldDenyValues = permissionDenyElement.val();

                        const oldExtraValues = permissionSelectElement.val();

                        if (response.newPermissions) {
                            permissionSelectElement.empty();
                            permissionSelectElement.trigger("change.select2");
                            $.map(response.newPermissions, function (key , value) {
                                permissionSelectElement.append('<option value="'+key+'">' + value + '</option>');
                            });
                            permissionSelectElement.val(oldExtraValues);
                            permissionSelectElement.trigger("change.select2");
                        }

                        if (response.newDenialPermissions.length > 0) {
                            permissionDenyElement.empty();
                            permissionDenyElement.trigger("change.select2");
                            $.map(response.newDenialPermissions, function (item) {
                                permissionDenyElement.append('<option value="'+item.id+'">' + item.f_name + '</option>');
                            });
                            permissionDenyElement.prop('disabled', false);
                            if (oldDenyValues) {
                                permissionDenyElement.val(oldDenyvalues);
                            }
                            permissionDenyElement.trigger("change.select2");
                        } else {
                            permissionDenyElement.empty();
                            permissionDenyElement.prop('disabled', true);
                            permissionDenyElement.trigger("change.select2");
                        }
                    }
                },
                error: function (xhr) {
                    $('#loadingFilterDiv').hide();
                    console.log(xhr);
                }
            });
        });
        permissionSelectElement.select2({no_results_text: "متاسفانه داده ای یافت نشد"});
        permissionDenyElement.select2({no_results_text: "متاسفانه داده ای یافت نشد"});
        /*edit role*/
        $(document).on('click','.editbtn-role', function (event) {
            const button = $(event.currentTarget);
            const id = button.data('id');
            $('#identifier').val(id);
            const data ={ identifier : id , _token: api.token};
            $.ajax({
                url: window.path.mainURL+'/admin/users/permission/getUserRolePerInfo',
                type: 'POST',
                data: data,
                beforeSend: function() {
                    $('#loadingFilterDiv').show();
                },
                success: function( response ) {
                    $('#loadingFilterDiv').hide();

                    if (response.roles) {
                        roleSelectElement.val(response.roles);
                        roleSelectElement.trigger("change.select2");
                    }
                    htmlPermission = '';
                    if (response.permissionList) {
                        $.map( response.permissionList , function( key ,value ) {
                            htmlPermission += '<option value="'+key+'">' + value + '</option>';
                        });
                        permissionSelectElement.html(htmlPermission);
                        permissionSelectElement.trigger("change.select2");
                    }

                    if (response.selectedPermissions) {
                        permissionSelectElement.val(response.selectedPermissions);
                        permissionSelectElement.trigger("change.select2");
                    }
                    htmlOption = '';
                    if (response.denialPermissionList.length > 0) {
                        $.map( response.denialPermissionList , function( item ) {
                            htmlOption+= '<option value="'+item.id+'">' + item.f_name + '</option>';
                        });
                        permissionDenyElement.html(htmlOption);
                        permissionDenyElement.prop('disabled', false);
                        permissionDenyElement.trigger("change.select2");
                    } else {
                        permissionDenyElement.prop('disabled', true);
                        permissionDenyElement.trigger("change.select2");
                    }

                    if (response.selectedDenialPermission) {
                        permissionDenyElement.val(response.selectedDenialPermission);
                        permissionDenyElement.trigger("change.select2");
                    }
                },
                error: function (xhr) {
                    $('#loadingFilterDiv').hide();
                    console.log(xhr);
                }
            });
        });

        $(document).on('click', '.submit-edit-role', function () {
            $('#loadingFilterDiv').show();
            const data = $(".add-edit-permission-role").serialize();

            $.ajax({
                url: window.path.mainURL+'/admin/users/permission/permissionSave',
                type: 'POST',
                data: data,
                success: function( response ) {
                    $('#loadingFilterDiv').hide();
                    if (response.status === 'success') {
                        generate("topRight","","ویرایش کاربر با موفقیت انجام گردید","success","basic","5000");
                        $(".close").click();
                    } else {
                        generate("topRight","","مشکلی در ویرایش کاربر بوجود آمده است","danger","basic","5000");
                    }
                },
                error: function (xhr) {
                    let editErrorsArray = [];
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        editErrorsArray.push(value);
                    });
                    generate("topRight","",editErrorsArray,"danger","basic","5000");
                }
            });

            $('#loadingFilterDiv').hide();
        });

        $(document).on("click",".cancleEditBtn",function () {
            $(".close").click();
        });

        /*reset modal add user*/
        function resetAddUserForm() {
            $('#field-1').val('');
            $('#field-2').val('');
            $('#field-3').val('');
            $('#field-4').val('');
            $('#field-5').val('');
            $('#field-6').val('');
            $('#field-7').val('');
            $('#avatarName').val('');
        }

        /*reset modal edit user*/
        function resetEditUserForm() {
            $('#field-1-edit').val('');
            $('#field-2-edit').val('');
            $('#field-3-edit').val('');
            $('#field-4-edit').val('');
            $('#field-5-edit').val('');
            $('#field-6-edit').val('');
            $('#field-7-edit').val('');
            $('#avatarName').val('');
        }

    </script>



@stop

@stop