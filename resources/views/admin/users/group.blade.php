@extends("admin.master")
@section("content")
@section("title")
    تعریف گروه
@stop
@section("css")
    <link href="{{asset("assets/plugins/admin/datatables/dataTables.min.css")}}" rel="stylesheet">

@stop
<div class="page-content">
    <div class="header">
        <h2>تعریف گروه</h2>
    </div>

    <form class="form-users" action="" method="post">

        <div class="row">
            <div class=" col-md-12 col-lg-12 portlets">
                <div class="panel">
                    <div class="panel-content">
                        <div class="m-b-20">
                            <div class="btn-group">
                                <button  class="btn btn-sm btn-dark" data-toggle="modal" data-target="#modal-add-group"><i class="fa fa-plus"></i>اضافه کردن گروه جدید</button>
                            </div>
                        </div>
                        <table class="table table-hover dataTable" id="table-group">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>نام گروه</th>
                                <th class="text-right">عملیات</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{csrf_field()}}
    </form>
    <!--portal-content-->
    @include('admin.users.modals.addRoleModal')
    @include('admin.users.modals.editRoleModal')
    @include('admin.users.includes.loadingOverlay')
</div>
@section("js_footer")
    <link href="{{asset("assets/plugins/admin/select2/select2.css")}}" rel="stylesheet" />
    <script src="{{asset("assets/plugins/admin/select2/select2.full.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/datatables/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/datatables/dataTables.bootstrap.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/bootstrap/js/jasny-bootstrap.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/noty/jquery.noty.packaged.min.js")}}"></script>

    <script src="{{asset("assets/js/config.js")}}"></script>
    <script src="{{asset("assets/js/admin/pages/table_editable_group.js")}}"></script>
    <script src="{{asset("assets/js/admin/pages/notifications.js")}}"></script>
    <script>
        const Path = window.path.mainURL+'/admin/';
        $(document).ready( function () {

            const dataTableAjaxURL = Path+'users/group/dataTables';
            const dataTableName = $('#table-group');

            const dataTable = dataTableName.DataTable({
                "sLengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "All"] // change per page values here
                ],
                "Dom" : "<'row'<'col-md-6 filter-left'f><'col-md-6'T>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "oTableTools" : {
                    "sSwfPath": "../assets/global/plugins/datatables/swf/copy_csv_xls_pdf.swf",
                    "aButtons":[
                        {
                            "sExtends":"pdf",
                            "mColumns":[0, 1, 2, 3],
                            "sPdfOrientation":"landscape"
                        },
                        {
                            "sExtends":"print",
                            "mColumns":[0, 1, 2, 3],
                            "sPdfOrientation":"landscape"
                        },{
                            "sExtends":"xls",
                            "mColumns":[0, 1, 2, 3],
                            "sPdfOrientation":"landscape"
                        },{
                            "sExtends":"csv",
                            "mColumns":[0, 1, 2, 3],
                            "sPdfOrientation":"landscape"
                        }
                    ]
                },
                // set the initial value
                "DisplayLength": 10,
                "processing": true,
                "serverSide": true,
                "order": [[0, 'desc']],
                "responsive": true,
                "searching": false,
                "Paginate": false,
                "PaginationType": "bootstrap",
                "oLanguage": dataTableLang,
                ajax: {
                    type : 'post',
                    url  : dataTableAjaxURL,
                    data : {
                        _token : api.token
                    }
                },
                columnDefs: [
                    { name: 'id'},
                    { name: 'name'},
                    { name: 'action' }
                ],
                columns: [
                    { data: 'id'   , sortable: false , searchable: false},
                    { data: 'name'   , sortable: false , searchable: true},
                    { data: 'action'    , sortable: false , searchable: false}
                ],
                'initComplete': function () {
                    $('#table-group').show();
                }
            });
        });
        // dataTable.on( 'order.dt search.dt', function () {
        //     dataTable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
        //         cell.innerHTML = i+1;
        //     } );
        // } ).draw();

        $(document).on('click','.submit-add-role-model' ,function(){
            const data = $(".submit-add-role-model").serialize();
            $.ajax({
                url: Path+'users/group/create',
                type: 'POST',
                data: $(".add-role").serialize(),
                beforeSend: function() {
                    $('#loadingFilterDiv').show();
                },
                success: function( response ) {
                    $('#loadingFilterDiv').hide();
                    if (response.status === 'success') {
                        generate("topRight","","گروه با موفقیت اضافه گردید","success","basic","5000");
                        $('#addRoleModal').modal('toggle');
                        $('#table-group').DataTable().ajax.reload();
                    } else {
                        generate("topRight","","مشکلی در تعریف گروه بوجود آمده است","danger","basic","5000");
                    }
                },
                error: function (xhr) {
                    $('#validation-errors').html('');
                    let errorsArray = [];
                    $.each(xhr.responseJSON.errors, function(key,value) {
                        errorsArray.push(value);
                    });
                    generate("topRight","",errorsArray,"danger","basic","5000");
                },

            });
            $('#loadingFilterDiv').hide();
        });



        // Edit modal
        const chosenEditElement = $('.chosen-select');
        const editRoleModalElement = $('#editRoleModal');
        const loadingElement = $('#loadingFilterDiv');

        chosenEditElement.select2({no_results_text: "Oops, nothing found!"});
        $(document).on('click',".editbtn", function (event) {
            $('#loadingFilterDiv').show();
            const button = $(event.currentTarget);
            const id = button.data('id');
            $.ajax({
                url: Path+'users/group/getRoleInfo',
                type: "POST",
                data: { _token: api.token , identifier: id },
                success: function(response){
                    $('#editIdentifier').val(id);
                    $('#loadingFilterDiv').hide();
                    $('#role-name-in-edit-modal').val(response.role.name);
                    chosenEditElement.val(response.permissions);
                    chosenEditElement.trigger("change.select2");
                }
            });
        });


        $(document).on('click',".close", function (event) {
            $('#editIdentifier').val('');
            $('#role-name-in-edit-modal').val('');
            chosenEditElement.val([]);
            chosenEditElement.trigger("change.select2");
        });

        $(document).on('click','.submit-edit-role-modal' ,function(){
            $.ajax({
                url: Path+'users/group/update',
                type: 'POST',
                data: $(".edit-role").serialize(),
                beforeSend: function() {
                    loadingElement.show();
                },
                success: function( response ) {
                    loadingElement.hide();
                    if (response.status === 'success') {
                        generate("topRight","","تغییرات با موفقیت ثبت گردید.","success","basic","5000");
                        $('#table-group').DataTable().ajax.reload();
                    } else {
                        generate("topRight","","مشکلی در ویرایش گروه بوجود آمده است","danger","basic","5000");
                    }
                },
                error: function (xhr) {
                    $('#validation-errors').html('');
                    let errorsArray = [];
                    $.each(xhr.responseJSON.errors, function(key,value) {
                        errorsArray.push(value);
                    });
                    generate("topRight","",errorsArray,"danger","basic","5000");
                },

            });
        });

        // Edit modal

        $(document).on('click' , '.cancleEditBtn' , function (e) {
            $(".close").click();
        });

        $(document).on('click' , '.cancelADDBtn' , function (e) {
            $(".close").click();
        });


        // Delete
        const deleteRoleModalElement = $('#deleteModal');
        $(document).on('click',".deletebtn", function (event) {
            generate("center","","آیا از حذف گروه مطمئن هستید؟","warning","buttons-preview",false);
            const button = $(event.currentTarget);
            const id = button.data('id');

            identifier = id;
        });

        function confirm(){
            console.log("ok");
            removeGroup(identifier);
        }

        function removeGroup(identifier) {
            $.ajax({
                url: window.path.mainURL + '/admin/users/group/delete',
                type: "POST",
                data: { _token: api.token , identifier: identifier },
                success: function(response){
                    if(response.status == 'success'){
                        $('#table-group').DataTable().ajax.reload();
                        generate("topRight","","گروه با موفقیت حذف گردید","success","basic","5000");
                    } else {
                        generate("topRight","","مشکلی در حذف گروه بوجود آمده است","danger","basic","5000");
                    }
                }
            });

        }
        // Delete
    </script>



@stop

@stop