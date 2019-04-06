<div class="modal fade" id="modal-add-user" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
                <h4 class="modal-title">ایجاد کاربر جدید</h4>
            </div>
            <div class="diffUser">
                <div class="image-holder">
                    <img alt="user image" src="{{asset("assets/img/admin/avatars/user-pic.png")}}" class="userPic">
                </div>
                <p class="uploadPhotoText">آپلود عکس</p>
                <form id="addUserAvatarUploadForm" enctype="multipart/form-data" style="position:relative;">
                    <input type="file" name="imageFile" data-type="add" class="input-1 imageFile hidden">
                </form>
            </div>
            <form class="add-user">
                {{csrf_field()}}
                <input type="hidden" name="avatarName" id="avatarName" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="field-1" class="control-label">نام</label>
                                <input type="text" name="name" class="form-control" id="field-1" placeholder="مهدی">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="field-2" class="control-label">نام خانوادگی</label>
                                <input type="text" name="family" class="form-control" id="field-2" placeholder="شکی">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="field-8" class="control-label">جنسیت</label>
                                <div class="radio radio-primary">
                                    <label>
                                        <input type="radio" name="gender_id" value="1" checked="checked" class="md-radio"><span class="circle"></span><span class="check"></span>
                                        مرد
                                    </label>
                                    <label>
                                        <input type="radio" name="gender_id" value="0"  class="md-radio"><span class="circle"></span><span class="check"></span>
                                        زن
                                    </label>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="field-7" class="control-label">کد ملی</label>
                                <input type="text" name="national_code" class="form-control" id="field-7" data-mask="999-999999-9" placeholder="2261122536">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="field-6" class="control-label">تاریخ تولد</label>
                                <input type="text" name="dob" class="form-control" data-mask="9999-99-99" id="field-6" placeholder="1370-11-22">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="field-3" class="control-label">آدرس</label>
                                <input type="text" name="address" class="form-control" id="field-3" placeholder="تهران - پونک">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="field-4" class="control-label">پست الکترونیک</label>
                                <input type="text" name="email" class="form-control" id="field-4" placeholder="example@website.com">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="field-5" class="control-label">شماره موبایل</label>
                                <input type="text" name="mobile" class="form-control" data-mask="9999-999-9999" id="field-5" placeholder="09123456789">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-primary btn-embossed bnt-square submit-add-user" data-dismiss="modal"><i class="fa fa-check"></i> ثبت</button>
                </div>
            </form>

        </div>
    </div>
</div>