@extends("admin.master")
@section("content")
@section("css")
    <link href="{{asset("assets/plugins/admin/datatables/dataTables.min.css")}}" rel="stylesheet">
@stop
    <div class="page-content">
        <div class="header">
            <h2>مدیریت رنگ ها</h2>
        </div>
        <form class="form-manage-tags" action="" method="post">

            <div class="row">
                <div class=" col-md-12 col-lg-12 portlets">
                    <div class="panel">
                        <div class="panel-content">
                            <div class="m-b-20">
                                <div class="btn-group">
                                    <button id="table-edit_new" class="btn btn-sm btn-dark"><i class="fa fa-plus"></i> اضافه کردن رنگ جدید</button>
                                </div>
                            </div>
                            <table class="table table-hover dataTable" id="table-editable">
                                <thead>
                                <tr>
                                    <th>نام رنگ(فارسی)</th>
                                    <th>نام رنگ(انگلیسی)</th>
                                    <th>نام رنگ(روسی)</th>
                                    <th>نام رنگ(عربی)</th>
                                    <th class="text-right">عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($colors as $color)
                                    <tr data-id="{{$color->id}}">
                                        <td>{{$color->name_fa}}</td>
                                        <td>{{$color->name_en}}</td>
                                        <td>{{$color->name_ru}}</td>
                                        <td>{{$color->name_ar}}</td>
                                        <td class="text-right" data-id="{{$color->id}}"><a class="edit btn btn-sm btn-default" href="javascript:;"><i class="icon-note"></i></a>  <a class="delete btn btn-sm btn-danger" href="javascript:;"><i class="icons-office-52"></i></a>
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
            <input type="hidden" class="color-id" name="color_id">
        </form>
    </div>
@section("js_footer")
    <script src="{{asset("assets/plugins/admin/datatables/jquery.dataTables.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/datatables/dataTables.bootstrap.min.js")}}"></script>
    <script src="{{asset("assets/js/admin/pages/table_editable_color.js")}}"></script>

@stop

@stop