@extends("admin.master")
@section("content")
@section("css")
    <link href="{{asset("assets/plugins/admin/datatables/dataTables.min.css")}}" rel="stylesheet">
@stop
    <div class="page-content">
        <div class="header">
            <h2>مدیریت مکان ها</h2>
        </div>
        <form class="form-manage-tags" action="" method="post">

            <div class="row">
                <div class=" col-md-12 col-lg-12 portlets">
                    <div class="panel">
                        <div class="panel-content">
                            <div class="m-b-20">
                                <div class="btn-group">
                                    <button id="table-edit_new" class="btn btn-sm btn-dark"><i class="fa fa-plus"></i> اضافه کردن مکان جدید</button>
                                </div>
                            </div>
                            <table class="table table-hover dataTable" id="table-editable">
                                <thead>
                                <tr>
                                    <th>نام مکان(فارسی)</th>
                                    <th>نام مکان(انگلیسی)</th>
                                    <th>نام مکان(روسی)</th>
                                    <th>نام مکان(عربی)</th>
                                    <th class="text-right">عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($requiredUses as $requiredUse)
                                    <tr data-id="{{$requiredUse->id}}">
                                        <td>{{$requiredUse->name_fa}}</td>
                                        <td>{{$requiredUse->name_en}}</td>
                                        <td>{{$requiredUse->name_ru}}</td>
                                        <td>{{$requiredUse->name_ar}}</td>
                                        <td class="text-right" data-id="{{$requiredUse->id}}"><a class="edit btn btn-sm btn-default" href="javascript:;"><i class="icon-note"></i></a>  <a class="delete btn btn-sm btn-danger" href="javascript:;"><i class="icons-office-52"></i></a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{csrf_field()}}
            <input type="hidden" class="required-id" name="required_id">
        </form>
    </div>
@section("js_footer")
    <script src="{{asset("assets/plugins/admin/datatables/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/datatables/dataTables.bootstrap.min.js")}}"></script>
    <script src="{{asset("assets/js/admin/pages/table_editable_required_use.js")}}"></script>

@stop

@stop