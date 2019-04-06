<!-- Modal -->
<div id="editRoleModal" class="modal fade admin-modal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ویرایش گروه</h4>
            </div>
            <div class="modal-body content-modal edit-box" style="padding-top: 40px">
                <div id="validation-errors"></div>
                <form class="form-label form-css-label edit-role">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-xs-12">
                            <label class="edit-label"><i class="fa fa-plus" aria-hidden="true"></i> نام گروه:</label>
                        </div>
                        <div class="col-lg-8 col-md-8 col-xs-12">
                            <div class="form-group">
                                <input type="text" id="role-name-in-edit-modal" name="name" class="justPersian form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-xs-12">
                            <label class="edit-label"><i class="fa fa-pencil" aria-hidden="true"></i> دسترسی ها:</label>
                        </div>
                        <div class="col-lg-8 col-md-8 col-xs-12">
                            <div class="form-group">
                                <select name="permissions[]" multiple="multiple" class="form-control chosen-select" size="1" data-placeholder="انتخاب موارد">
                                    @foreach($permissionList as $permission)
                                        <option value="{{$permission->id}}">{{$permission->f_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="editIdentifier" name="identifier" value="22"/>
                    <div class="row">
                        <div class="col-md-6 col-xs-12 MB10 text-center">
                            <button type="button" class="btn btn-primary btn-embossed bnt-square submit-edit-role-modal" data-dismiss="modal"><i class="fa fa-check"></i> ذخیره</button>
                        </div>
                        <div class="col-md-6 col-xs-12 MB10 text-center">
                            <button type="button" class="btn btn-primary btn-embossed bnt-square cancleEditBtn" data-dismiss="modal"><i class="fa fa-check"></i> انصراف</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>