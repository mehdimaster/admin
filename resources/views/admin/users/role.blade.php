@extends('frontend.adminportal.masterAdmin')
@section("title")
    تعریف گروه
@stop
@section('css')
    <link rel="stylesheet" href="{{staticAsset("assets/css/chosen.css?version=1")}}">
    <link rel="stylesheet" href="{{staticAsset("assets/css/en/adminportal/datepicker.css")}}">
@stop

@section('javascript_header')

@stop
@section('content')
    <section class="portal-content">
        <div class="container-fluid">
            <div class="row">
                @include('frontend.adminportal.partials.menu')
                @include('frontend.adminportal.partials.contentHeader')
                <div class="col-lg-10 col-md-9 col-sm-12 col-xs-12 PA0 table-parent">
                    <div class="edit-box">
                        <div class="row">
                            <div class="col-lg-12 col-xs-12">
                                <h2 class="page-title"><i class="fa fa-puzzle-piece" aria-hidden="true"></i> مدیریت دسترسی</h2>
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <div  style="overflow-x: auto;">
                                    <table id="admin_table" class="display portal-table cell-border">
                                        <thead>
                                        <tr>
                                            <th>ردیف</th>
                                            <th>نام و نام خانوادگی</th>
                                            <th>ایمیل</th>
                                            <th>موبایل</th>
                                            <th>نقش</th>
                                            <th>عملیات</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>سپیده کردبچه</td>
                                            <td>sepideh.kordbacheh@gmail.com</td>
                                            <td>09121234567</td>
                                            <td>
                                                <select class="form-control">
                                                    <option value="">ادمین اصلی</option>
                                                    <option value="">یوزر</option>
                                                    <option value="">ادمین 2</option>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="infobtn editbtn" data-toggle="tooltip" title="ویرایش سطح دسترسی">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>مهدی شکی</td>
                                            <td>mehdi.shakki@gmail.com</td>
                                            <td>09121234567</td>
                                            <td>
                                                <select class="form-control">
                                                    <option value="">ادمین اصلی</option>
                                                    <option value="">یوزر</option>
                                                    <option value="">ادمین 2</option>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="infobtn editbtn" data-toggle="tooltip" title="ویرایش سطح دسترسی">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>سمیرا هوشمند</td>
                                            <td>samira.hooshmand@gmail.com</td>
                                            <td>09121234567</td>
                                            <td>
                                                <select class="form-control">
                                                    <option value="">ادمین اصلی</option>
                                                    <option value="">یوزر</option>
                                                    <option value="">ادمین 2</option>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="infobtn editbtn" data-toggle="tooltip" title="ویرایش سطح دسترسی">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--portal-content-->
@stop
@section('javascript_footer')
    <script src="{{staticAsset("assets/js/chosen.jquery.js")}}"></script>
    <script src="{{staticAsset('assets/js/datepicker/JDatepicker.js')}}"></script>
    <script src="{{staticAsset('assets/js/datepicker/datepickerButtons.js')}}"></script>
    <script src="{{staticAsset('assets/js/adminPortal/persian-date.js')}}"></script>
    <script src="{{staticAsset('assets/js/adminPortal/persian-datepicker.js')}}"></script>
@stop