<!-- Modal -->
<div id="modal-add-group" class="modal fade admin-modal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">تعریف گروه</h4>
            </div>
            <div class="modal-body content-modal edit-box">
                <div id="validation-errors"></div>
                <form class="form-label form-css-label add-role">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-xs-12">
                            <label class="edit-label"><i class="fa fa-plus" aria-hidden="true"></i> نام گروه:</label>
                        </div>
                        <div class="col-lg-8 col-md-8 col-xs-12">
                            <div class="form-group">
                                <input type="text" name="name" class="justPersian form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-xs-12">
                            <label class="edit-label"><i class="fa fa-pencil" aria-hidden="true"></i> دسترسی ها:</label>
                        </div>
                        <div class="col-lg-8 col-md-8 col-xs-12">
                            <div class="form-group">
                                <select name="permissions[]" multiple="multiple" class="form-control chosen-select" data-placeholder="انتخاب موارد">
                                    @foreach($permissionList as $permission)
                                        <option value="{{$permission->id}}">{{$permission->f_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-12 MB10 text-center">
                            <button type="button" class="btn btn-primary btn-embossed bnt-square submit-add-role-model" data-dismiss="modal"><i class="fa fa-check"></i> ثبت</button>
                        </div>
                        <div class="col-md-6 col-xs-12 MB10 text-center">
                            <button type="button" class="btn btn-primary btn-embossed bnt-square cancelADDBtn" data-dismiss="modal"><i class="fa fa-check"></i> انصراف</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>