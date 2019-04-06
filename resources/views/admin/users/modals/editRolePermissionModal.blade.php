<!-- Modal -->
<div id="editRolePermissionModal" class="modal fade admin-modal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ویرایش دسترسی</h4>
            </div>
            <div class="modal-body content-modal edit-box">
                <div id="validation-errors"></div>
                <form class="form-label form-css-label add-edit-permission-role">
                    {{csrf_field()}}
                    <input name="identifier" type="hidden" id="identifier" value="">

                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-xs-12">
                            <label class="edit-label"><i class="fa fa-puzzle-piece" aria-hidden="true"></i> نقش:</label>
                        </div>
                        <div class="col-lg-8 col-md-8 col-xs-12">
                            <div class="form-group">
                                <select name="roles[]" multiple="multiple" class="form-control chosen-role-select" size="1" data-placeholder="انتخاب موارد">
                                    @foreach($data['roles'] as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-xs-12">
                            <label class="edit-label"><i class="fa fa-plus" aria-hidden="true"></i> دسترسی های افزوده:</label>
                        </div>
                        <div class="col-lg-8 col-md-8 col-xs-12">
                            <div class="form-group">
                                <select name="extraPermissions[]" multiple="multiple" class="form-control chosen-permission-select" size="1" data-placeholder="انتخاب موارد"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-xs-12">
                            <label class="edit-label"><i class="fa fa-ban" aria-hidden="true"></i> محدود کردن دسترسی ها:</label>
                        </div>
                        <div class="col-lg-8 col-md-8 col-xs-12">
                            <div class="form-group">
                                <select name="denyPermissions[]" multiple="multiple" class="form-control chosen-permission-deny-select" size="1" data-placeholder="انتخاب موارد"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-12 MB10 text-center">
                            <button type="button" class="btn btn-primary submit submit-edit-role">ثبت</button>
                        </div>
                        <div class="col-md-6 col-xs-12 MB10 text-center">
                            <button type="button" class="btn btn-primary cancleEditBtn" data-toggle="modal" data-target="#editProfile">انصراف</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
