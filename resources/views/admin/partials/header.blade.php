<!DOCTYPE html>
<html lang="en" class="rtl" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="admin-themes-lab">
    <meta name="author" content="themes-lab">
    <link rel="shortcut icon" href="../assets/global/images/favicon.png" type="image/png">
    <title>پنل مدیریت | @yield("title")</title>
    <link href="{{asset("assets/css/admin/style.css")}}" rel="stylesheet" type="text/css">
    <link href="{{asset("assets/css/admin/theme.css")}}" rel="stylesheet" type="text/css">
    <link href="{{asset("assets/css/admin/ui.css")}}" rel="stylesheet" type="text/css">
    <link href="{{asset("assets/css/admin/material.css")}}" rel="stylesheet" type="text/css">
    <link href="{{asset("assets/css/admin/layout.css")}}" rel="stylesheet" type="text/css">
    <link href="{{asset("assets/fonts/fontawesome/css/all.css")}}" rel="stylesheet">
    <!-- BEGIN PAGE STYLE -->

    @yield("css")
    <!-- END PAGE STYLE -->
    <script src="{{asset("assets/plugins/admin/modernizr/modernizr-2.6.2-respond-1.1.0.min.js")}}"></script>
    @yield("js_header")
</head>

<!-- BEGIN BODY -->
<body class="rtl fixed-topbar fixed-sidebar theme-sdtl color-default dashboard">
<!--[if lt IE 7]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<section>
    <!-- BEGIN SIDEBAR -->
    <div class="sidebar">
        <div class="logopanel">
            <h1>
                <a href="dashboard.html"></a>
            </h1>
        </div>
        <div class="sidebar-inner">
            <div class="sidebar-top">
                {{--<form action="http://4example.ir/make/admin_rtl/md-layout1/search-result.html" method="post" class="searchform" id="search-results">
                    <input type="text" class="form-control" name="keyword" placeholder="جستجو...">
                </form>--}}
                <div class="userlogged clearfix">
                    <i class="icon icons-faces-users-01"></i>
                    <div class="user-details">
                        <h4>مهدی شکی</h4>
                        <div class="dropdown user-login">
                            <button class="btn btn-xs dropdown-toggle btn-rounded" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" data-delay="300">
                                <i class="online"></i><span> آن لاین</span><i class="fa fa-angle-down"></i>
                            </button>
                            {{--<ul class="dropdown-menu">
                                <li><a href="#"><i class="busy"></i><span>مشغول</span></a></li>
                                <li><a  href="#"><i class="turquoise"></i><span>مخفی</span></a></li>
                                <li><a href="#"><i class="away"></i><span>Away</span></a></li>
                            </ul>--}}
                        </div>
                    </div>
                </div>
            </div>
            {{--<div class="menu-title">
                Navigation
                <div class="pull-right menu-settings">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" data-delay="300">
                        <i class="icon-settings"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#" id="reorder-menu" class="reorder-menu">Reorder menu</a></li>
                        <li><a href="#" id="remove-menu" class="remove-menu"> حذف elements</a></li>
                        <li><a href="#" id="hide-top-sidebar" class="hide-top-sidebar">Hide user &amp; search</a></li>
                    </ul>
                </div>
            </div>--}}
            <ul class="nav nav-sidebar">
                @if(auth()->user()->hasAnyPermission(['Dashboard']))
                    <li class=" nav-active active"><a href="{{url("/admin/dashboard")}}"><i class="icon-home"></i><span>داشبورد</span></a></li>
                @endif
                <li class="nav-parent">

                    <ul class="children collapse">



                    </ul>
                </li>
                @if(auth()->user()->hasAnyPermission(['product']))
                <li class="nav-parent">
                    <a href="#"><i class="icon-bulb"></i><span>محصولات</span> <span class="fa arrow"></span></a>
                    <ul class="children collapse">
                        <li><a href="{{url("admin/add-product")}}">اضافه کردن محصول جدید</a></li>
                        <li><a href="mailbox-send.html">ویرایش/حذف محصول</a></li>
                        <li><a href="{{url("admin/manage-tags")}}">مدیریت تگ ها</a></li>
                        <li><a href="{{url("admin/manage-colors")}}">مدیریت رنگ ها</a></li>
                        <li><a href="{{url("admin/manage-required-use")}}">مدیریت مکان ها</a></li>
                    </ul>
                </li>
                @endif
                @if(auth()->user()->hasAnyPermission(['Users list' , 'Roles' , 'Permissions']))
                <li class="nav-parent">
                    <a href="default.html"><i class="icon-screen-desktop"></i><span>مدیریت کاربران</span> <span class="fa arrow"></span></a>
                    <ul class="children collapse">
                        @can('Users list')
                        <li><a href="{{url("admin/users/users")}}">حذف و اضافه کاربران</a></li>
                        @endcan
                        @can('Permissions')
                        <li><a href="{{url("admin/users/permission")}}">مدیریت دسترسی</a></li>
                        @endcan
                        @can('Roles')
                        <li><a href="{{url("admin/users/group")}}">تعریف گروه</a></li>
                        @endcan
                    </ul>
                </li>
                @endif
                @if(auth()->user()->hasAnyPermission(['blog']))
                <li class="nav-parent">
                    <a href="default.html"><i class="icon-screen-desktop"></i><span>بلاگ</span> <span class="fa arrow"></span></a>
                    <ul class="children collapse">
                        <li><a href="material-buttons.html">اضافه کردن خبر جدید</a></li>
                        <li><a href="material-colors.html">ویرایش/حذف اخبار</a></li>
                    </ul>
                </li>
                @endif
                @if(auth()->user()->hasAnyPermission(['comment']))
                <li class="nav-parent">
                    <a href="default.html"><i class="icon-layers"></i><span>مدیریت نظرات</span><span class="fa arrow"></span></a>
                    <ul class="children collapse">
                        <li><a href="layouts-api.html">مشاهده نظرات</a></li>
                    </ul>
                </li>
                @endif
                @if(auth()->user()->hasAnyPermission(['contactus']))
                <li class="nav-parent">
                    <a href="default.html"><i class="icon-note"></i><span>مدیریت تماس با ما</span><span class="fa arrow"></span></a>
                    <ul class="children collapse">
                        <li><a href="material-forms.html">نمایش درخواست ها</a></li>
                    </ul>
                </li>
                @endif

            </ul>

            <div class="sidebar-footer clearfix">
                {{--<a class="pull-left footer-settings" href="#" data-rel="tooltip" data-placement="top" data-original-title="Settings">
                    <i class="icon-settings"></i></a>--}}
                <a class="pull-left toggle_fullscreen" href="#" data-rel="tooltip" data-placement="top" data-original-title="Fullscreen">
                    <i class="icon-size-fullscreen"></i></a>
                <a class="pull-left" href="user-lockscreen.html" data-rel="tooltip" data-placement="top" data-original-title="Lockscreen">
                    <i class="icon-lock"></i></a>
                <a class="pull-left btn-effect" href="user-login-v1.html" data-modal="modal-1" data-rel="tooltip" data-placement="top" data-original-title="Logout">
                    <i class="icon-power"></i></a>
            </div>
        </div>
    </div>
    <!-- END SIDEBAR -->
    <div class="main-content">
        <!-- BEGIN TOPBAR -->
        <div class="topbar">
            <div class="header-left">
                <div class="topnav">
                    <a class="menutoggle" href="#" data-toggle="sidebar-collapsed"><span class="menu__handle"><span>Menu</span></span></a>
                    {{--<ul class="nav nav-icons">
                        <li><a href="#" class="toggle-sidebar-top"><span class="icon-user-following"></span></a></li>
                        <li><a href="mailbox.html"><span class="octicon octicon-mail-read"></span></a></li>
                        <li><a href="#"><span class="octicon octicon-flame"></span></a></li>
                        <li><a href="builder-page.html"><span class="octicon octicon-rocket"></span></a></li>
                    </ul>--}}
                </div>
            </div>
            <div class="header-right">
                <ul class="header-menu nav navbar-nav">
                    <!-- BEGIN USER DROPDOWN -->
                    {{--<li class="dropdown" id="language-header">
                        <a href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="icon-globe"></i>
                            <span>زبان</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#" data-lang="en"><img src="../assets/global/images/flags/usa.png" alt="flag-english"> <span>English</span></a>
                            </li>
                            <li>
                                <a href="#" data-lang="es"><img src="../assets/global/images/flags/spanish.png" alt="flag-english"> <span>Español</span></a>
                            </li>
                            <li>
                                <a href="#" data-lang="fr"><img src="../assets/global/images/flags/french.png" alt="flag-english"> <span>Français</span></a>
                            </li>
                        </ul>
                    </li>--}}
                    <!-- END USER DROPDOWN -->
                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                   {{-- <li class="dropdown" id="notifications-header">
                        <a href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="icon-bell"></i>
                            <span class="badge badge-danger badge-header">6</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-header clearfix">
                                <p class="pull-left">12 نوتیفیکیشن معلق</p>
                            </li>
                            <li>
                                <ul class="dropdown-menu-list withScroll" data-height="220">
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-star p-r-10 f-18 c-orange"></i>
                                            رضا تصویر شما را پسندید
                                            <span class="dropdown-time">اکنون</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-heart p-r-10 f-18 c-red"></i>
                                            احمد شما را به لیست دوستان خود اضافه کرد
                                            <span class="dropdown-time">15 دقیقه قبل</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-file-text p-r-10 f-18"></i>
                                            اسناد جدید در دسترس قرار گرفتند
                                            <span class="dropdown-time">22 دقیقه قبل</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-picture-o p-r-10 f-18 c-blue"></i>
                                            تصویر جدید اضافه شد
                                            <span class="dropdown-time">40 دقیقه قبل</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-bell p-r-10 f-18 c-orange"></i>
                                            ملاقات صورت گرفته 1 ساعت پیش
                                            <span class="dropdown-time">1 ساعت پیش</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-bell p-r-10 f-18"></i>
                                            5 مشکل در سرور
                                            <span class="dropdown-time">2 ساعت پیش</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-comment p-r-10 f-18 c-gray"></i>
                                            محسن زیر پست شما نظر گذاشت
                                            <span class="dropdown-time">3 ساعت پیش</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i class="fa fa-picture-o p-r-10 f-18 c-blue"></i>
                                            تصویر جدید اضافه شد
                                            <span class="dropdown-time">2 روز پیش</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown-footer clearfix">
                                <a href="#" class="pull-left">نمایش تمام نوتیفیکیشن ها</a>
                                <a href="#" class="pull-right">
                                    <i class="icon-settings"></i>
                                </a>
                            </li>
                        </ul>
                    </li>--}}
                    <!-- END NOTIFICATION DROPDOWN -->
                    <!-- BEGIN MESSAGES DROPDOWN -->
                    {{--<li class="dropdown" id="messages-header">
                        <a href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="icon-paper-plane"></i>
                            <span class="badge badge-primary badge-header">
                8
                </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-header clearfix">
                                <p class="pull-left">
                                    شما 8 پیام دارید
                                </p>
                            </li>
                            <li class="dropdown-body">
                                <ul class="dropdown-menu-list withScroll" data-height="220">
                                    <li class="clearfix">
                        <span class="pull-left p-r-5">
                        <img src="../assets/global/images/avatars/avatar3.png" alt="avatar 3">
                        </span>
                                        <div class="clearfix">
                                            <div>
                                                <strong>علی احمدی</strong>
                                                <small class="pull-right text-muted">
                                                    <span class="glyphicon glyphicon-time p-r-5"></span>12 دقیقه قبل
                                                </small>
                                            </div>
                                            <p>لورم ایپسوم متن ساختگی با تولید سادگی....</p>
                                        </div>
                                    </li>
                                    <li class="clearfix">
                        <span class="pull-left p-r-5">
                        <img src="../assets/global/images/avatars/avatar4.png" alt="avatar 4">
                        </span>
                                        <div class="clearfix">
                                            <div>
                                                <strong>علی احمدی</strong>
                                                <small class="pull-right text-muted">
                                                    <span class="glyphicon glyphicon-time p-r-5"></span>47 دقیقه قبل
                                                </small>
                                            </div>
                                            <p>لورم ایپسوم متن ساختگی با تولید سادگی....</p>
                                        </div>
                                    </li>
                                    <li class="clearfix">
                        <span class="pull-left p-r-5">
                        <img src="../assets/global/images/avatars/avatar5.png" alt="avatar 5">
                        </span>
                                        <div class="clearfix">
                                            <div>
                                                <strong>حسین حسینی</strong>
                                                <small class="pull-right text-muted">
                                                    <span class="glyphicon glyphicon-time p-r-5"></span>1 ساعت قبل
                                                </small>
                                            </div>
                                            <p>لورم ایپسوم متن ساختگی با تولید سادگی....</p>
                                        </div>
                                    </li>
                                    <li class="clearfix">
                        <span class="pull-left p-r-5">
                        <img src="../assets/global/images/avatars/avatar6.png" alt="avatar 6">
                        </span>
                                        <div class="clearfix">
                                            <div>
                                                <strong>محمد اکبری</strong>
                                                <small class="pull-right text-muted">
                                                    <span class="glyphicon glyphicon-time p-r-5"></span>2 روز قبل
                                                </small>
                                            </div>
                                            <p>لورم ایپسوم متن ساختگی با تولید سادگی....</p>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown-footer clearfix">
                                <a href="mailbox.html" class="pull-left">مشاهده تمام پیام ها</a>
                                <a href="#" class="pull-right">
                                    <i class="icon-settings"></i>
                                </a>
                            </li>
                        </ul>
                    </li>--}}
                    <!-- END MESSAGES DROPDOWN -->
                    <!-- BEGIN USER DROPDOWN -->
                    <li class="dropdown" id="user-header">
                        <a href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <img src="{{asset("assets/img/admin/avatars/user1.png")}}" alt="تصویر کاربر">
                            <span class="username">سلام، مهدی شکی</span>
                        </a>
                        <ul class="dropdown-menu">
                            {{--<li>
                                <a href="#"><i class="icon-user"></i><span>پروفایل من</span></a>
                            </li>
                            <li>
                                <a href="#"><i class="icon-calendar"></i><span>تقویم من</span></a>
                            </li>
                            <li>
                                <a href="#"><i class="icon-settings"></i><span>تنظیمات حساب کاربری</span></a>
                            </li>--}}
                            <li>
                                <a href="#"><i class="icon-logout"></i><span>خروج</span></a>
                            </li>
                        </ul>
                    </li>
                    <!-- END USER DROPDOWN -->
                </ul>
            </div>
            <!-- header-right -->
        </div>
        <!-- END TOPBAR -->