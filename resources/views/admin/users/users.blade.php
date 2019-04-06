@extends("admin.master")
@section("content")
@section("title")
    کاربران
@stop
@section("css")
    <link href="{{asset("assets/plugins/admin/datatables/dataTables.min.css")}}" rel="stylesheet">
    <style>
        .diffUser {
            text-align: center !important;
            margin-bottom: 30px;
        }
        .diffUser .image-holder {
            height: 60px;
            width: 60px;
            text-align: center !important;
            display: inline-block;
        }
        .uploadPhotoText{
            text-align: center !important;
            width: 100%;
            display: inline-block;
            color: #afafaf;
            font-size: 14px;
        }
        .diffUser .userPic {
            margin: 0 auto;
            height: 100%;
            width: 100%;
            -o-object-fit: contain;
            object-fit: contain;
            vertical-align: middle;
            display: inline-block;
            box-shadow: 1px 2px 7px 0 rgba(0, 0, 0, 0.4);
            border-radius: 100%;
        }
    </style>
@stop
<div class="page-content">
    <div class="header">
        <h2>کاربران</h2>
    </div>
    <div class="row">
        <div class=" col-md-12 col-lg-12 portlets">
            <div class="panel">
                <div class="panel-content">
                    <div class="m-b-40">
                        <form id="filterForm">
                            <div class="col-md-2 form-group">
                                <input type="text" name="search_name" id="search_name" class="form-control floating-label liveItems" placeholder="نام">
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="text" name="search_family" id="search_family" class="form-control floating-label liveItems" placeholder="نام خانوادگی">
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="text" name="search_email" id="search_email" class="form-control floating-label liveItems" placeholder="پست الکترونیک">
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="text" name="search_mobile" id="search_mobile" class="form-control floating-label liveItems" placeholder="تلفن همراه">
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="text" name="search_national" id="search_national" class="form-control floating-label liveItems" placeholder="کد ملی">
                            </div>
                            <div class="col-md-2 form-group ">
                                <select name="search_role[]" id="search_role"
                                        class="form-control select2-hidden-accessible multiselect liveItems" aria-hidden="true"
                                        multiple="multiple" data-placeholder="نقش">
                                    @foreach($data['roles'] as $role)
                                        @if($role!=null)
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form class="form-users" action="" method="post">

        <div class="row">
            <div class=" col-md-12 col-lg-12 portlets">
                <div class="panel">
                    <div class="panel-content">
                        <div class="m-b-20">
                            <div class="btn-group">
                                <button  class="btn btn-sm btn-dark" data-toggle="modal" data-target="#modal-add-user"><i class="fa fa-plus"></i> اضافه کردن کاربر جدید</button>
                            </div>
                        </div>
                        <table class="table table-hover dataTable" id="table-editable">
                            <thead>
                            <tr>
                                <th>نام</th>
                                <th>نام خانوادگی</th>
                                <th>ایمیل</th>
                                <th>موبایل</th>
                                <th>تاریخ تولد</th>
                                <th>کد ملی</th>
                                <th>نقش</th>
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
    @include('admin.users.modals.addUserModal')
    @include('admin.users.modals.editUserModal')
</div>
@section("js_footer")
    <link href="{{asset("assets/plugins/admin/select2/select2.css")}}" rel="stylesheet" />
    <script src="{{asset("assets/plugins/admin/select2/select2.full.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/datatables/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/datatables/dataTables.bootstrap.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/bootstrap/js/jasny-bootstrap.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/noty/jquery.noty.packaged.min.js")}}"></script>

    <script src="{{asset("assets/js/config.js")}}"></script>
    <script src="{{asset("assets/js/admin/pages/table_editable_users.js")}}"></script>
    <script src="{{asset("assets/js/admin/pages/notifications.js")}}"></script>
    <script>
        $(".multiselect").select2();

        // Search and refresh

        function debounce(func, wait, immediate) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                var later = function () {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        }

        $('.liveItems').on('input change', debounce(function (e) {
            e.preventDefault();
            $('#table-editable').DataTable().ajax.reload();
        }, 1000));

        /*upload avatar*/
        $(document).on('click', '.userPic', function () {
            var $this = $(this),
                imageFile = $this.parents('.diffUser').find('input.imageFile ');

            imageFile.click();
        });

        $('.imageFile').on('change', fileUpload);

        function fileUpload(event) {
            const uploadType = $(this).data('type');
            let files = event.target.files;
            let data = new FormData();
            let file = files[0];

            if (!file.type.match('image.*')) {
                /*showAlert("Please choose an images file.", 'validImageFile');*/
                toastr.error("فایل مورد نظر می بایست عکس باشد");
            } else if (file.size > 307200) {
                /* showAlert("Sorry, your file is too large (>300 KB)", 'imageSize');*/
                toastr.error("حجم فایل آپلود شده می بایست زیر 300 کیلو بایت باشد");
            } else {
                const avatarUploadAjaxURL = path.mainURL + '/admin/' + 'users/uploadUserAvatar';
                const identifier = 1;//$('#edit_identifier').val();

                data.append('file', file, file.name);
                data.append('uploadType', uploadType);
                data.append('identifier', identifier);
                data.append('_token', api.token);

                $.ajax({
                    type: "POST",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    url: avatarUploadAjaxURL,
                    beforeSend: function () {
                        var loadingImage = $('<img/>', {
                            class: 'loading24',
                            // src: path.loading24
                        }).css({
                            position: 'absolute',
                            top: '20%',
                            right: '48.4%'
                        });

                        $('#userAvatarUploadForm').append(loadingImage);
                        $(this).prop('disabled', true);
                    },
                    success: function (response) {
                        $('body').find('.loading24').remove();
                        $(this).prop('disabled', false);

                        if (response != '' && response != 'undefined' && response != null) {
                            response = $.parseJSON(response);
                            if (response.result != null && response.result != 'undefined' && response.status == 'success') {
                                if (!$.isEmptyObject(response.result)) {
                                    if (response.result.type == 'edit') {
                                        let imagePath = path.avatarDir + '/' + response.result.file;
                                        $('.userPic').attr('src', imagePath);
                                    } else {
                                        let imagePath = path.avatarTempDir + '/' + response.result.file;
                                        $('#avatarName').val(response.result.file);
                                        $('.userPic').attr('src', imagePath);
                                    }
                                }
                            }
                        }
                    },
                    error: function () {
                        $('body').find('.loading24').remove();
                        $(this).prop('disabled', false);
                    }
                });
            }
        }

        /*add user*/
        $(document).on('click', '.submit-add-user', function () {
            $.ajax({
                url: window.path.mainURL + '/admin/users/userCreate',
                type: 'POST',
                data: $(".add-user").serialize(),
                beforeSend: function () {
                    $('#loadingFilterDiv').show();
                },
                success: function (response) {
                    if (response.status === 'success') {
                        $('#editProfile').modal('toggle');
                        resetAddUserForm();
                        $('#table-editable').DataTable().ajax.reload();
                        generate("topRight","","کاربر با موفقیت ثبت گردید","success","basic","5000");

                    } else {
                        generate("topRight","","خطایی رخ داده است","danger","basic","5000");
                    }
                    $('#loadingFilterDiv').hide();
                },
                error: function (xhr) {
                    $('#loadingFilterDiv').hide();
                    $('.validation-errors').html('');
                    let errorsArray = [];
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        errorsArray.push(value);
                    });
                    generate("topRight","",errorsArray,"danger","basic","5000");
                }
            });
        });

        /*edit user*/
        $(document).on('click','.edit-btn', function (event) {
            resetEditUserForm();
            $('#loadingFilterDiv').show();
            const button = $(event.currentTarget);
            const id = button.data('id');
            $('#edit_identifier').val(id);
            $.ajax({
                url: window.path.mainURL + '/admin/users/getUserInfoById',
                type: "POST",
                data: {_token: api.token, identifier: id},
                beforeSend: function () {

                    $('#loadingFilterDiv').show();
                },
                success: function (response) {
                    $('#loadingFilterDiv').hide();
                    $("#field-1-edit").val(response.user.name);
                    $("#field-2-edit").val(response.user.family);
                    $("#field-7-edit").val(response.user.national_code);
                    $("#field-3-edit").val(response.user.address);
                    // passwordEl.val('');

                    if (response.user.one_user && response.user.one_user.email) {
                        $("#field-4-edit").val(response.user.one_user ? response.user.one_user.email : '');
                        $("#field-4-edit").prop('disabled', true);
                    }
                    if (response.user.one_user && response.user.one_user.mobile) {
                        $("#field-5-edit").val(response.user.one_user ? response.user.one_user.mobile : '');
                        $("#field-5-edit").prop('disabled', true);
                    }

                    if (response.user.gender_id) {
                        $("#male").prop('checked', true);
                        $("#male").val(response.user.gender_id);
                    } else {
                        $("#female").prop('checked', true);
                        $("#female").val(response.user.gender_id);
                    }

                    $(".userPic").attr('src', response.user.one_user.fullAvatar);
                }
            });
        });

        $(document).on('click', '.submit-edit-user', function () {
            $.ajax({
                url: window.path.mainURL + '/admin/users/userUpdate',
                type: 'POST',
                data: $(".edit-user").serialize(),
                beforeSend: function () {
                    $('#loadingFilterDiv').show();
                },
                success: function (response) {
                    $('#loadingFilterDiv').hide();
                    if (response.status === 'success') {
                        $('#table-editable').DataTable().ajax.reload();
                        generate("topRight","","ویرایش کاربر با موفقیت انجام گردید","success","basic","5000");
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
                },

            });
            $('#loadingFilterDiv').hide();
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
        // Delete
        let identifier = 0;
        $(document).on('click','.delete-btn', function (event) {
            generate("center","","آیا از حذف کاربر مطمئن هستید؟","warning","buttons-preview",false);
            const button = $(event.currentTarget);
            const id = button.data('id');
            $('.confirm-btn').data('id', id);
            identifier = id;
        });

        function confirm(){
            removeUser(identifier);
        }

        function removeUser(identifier) {
            $.ajax({
                url: window.path.mainURL + '/admin/users/delete',
                type: "POST",
                data: {_token: api.token, identifier: identifier},
                success: function (response) {
                    if (response.status == 'success') {
                        $('#table-editable').DataTable().ajax.reload();
                        generate("topRight","","کاربر با موفقیت حذف گردید","success","basic","5000");
                    } else {
                        console.log(response.message);
                        generate("topRight","","مشکلی در حذف کاربر بوجود آمده است","danger","basic","5000");
                    }
                }
            });
        }
    </script>



@stop

@stop