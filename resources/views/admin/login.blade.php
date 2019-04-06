<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ورود به پنل مدیریت</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="" name="description" />
    <meta content="themes-lab" name="author" />
    <link rel="shortcut icon" href="../assets/global/images/favicon.png">
    <link href="{{asset("assets/css/admin/style.css")}}" rel="stylesheet">
    <link href="{{asset("assets/css/admin/ui.css")}}" rel="stylesheet">
    <link href="{{asset("assets/plugins/admin/bootstrap-loading/lada.min.css")}}" rel="stylesheet">
</head>
<body class="rtl account separate-inputs" data-page="login">
<!-- BEGIN LOGIN BOX -->
<div class="container" id="login-block">
    <div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <div class="account-wall">
                <i class="user-img icons-faces-users-03"></i>
                <form class="form-signin" role="form">
                    {{csrf_field()}}
                    <div class="append-icon">
                        <input type="text" name="username" id="username" class="form-control form-white username" placeholder="نام کاربری" required>
                        <i class="icon-user"></i>
                    </div>
                    <div class="append-icon m-b-20">
                        <input type="password" name="password" class="form-control form-white password" placeholder="رمز عبور" required>
                        <i class="icon-lock"></i>
                    </div>
                    <label generated="true" class="error error-user" style="display: none;">نام کاربری/کلمه عبور اشتباه است</label>
                    <button type="submit" id="submit-form" class="btn btn-lg btn-danger btn-block ladda-button" data-style="expand-left">ورود</button>

                    {{--<div class="clearfix">
                        <p class="pull-left m-t-20"><a id="password" href="#">فراموشی رمز عبور?</a></p>
                        <p class="pull-right m-t-20"><a href="user-signup-v1.html">ثبت نام</a></p>
                    </div>--}}
                </form>
               {{-- <form class="form-password" role="form">
                    <div class="append-icon m-b-20">
                        <input type="password" name="password" class="form-control form-white password" placeholder="رمز عبور" required>
                        <i class="icon-lock"></i>
                    </div>
                    <button type="submit" id="submit-password" class="btn btn-lg btn-danger btn-block ladda-button" data-style="expand-left">ارسال لینک بازنشانی رمز عبور</button>
                    <div class="clearfix">
                        <p class="pull-left m-t-20"><a id="login" href="#">قبلا ثبت نام کرده اید؟ ورود</a></p>
                        <p class="pull-right m-t-20"><a href="user-signup-v1.html">ثبت نام</a></p>
                    </div>
                </form>--}}
            </div>
        </div>
    </div>
    <p class="account-copyright">
        <span>Copyright © 2018 </span><span>Sahand</span>.<span>All rights reserved.</span>
    </p>

</div>
<script src="{{asset("assets/plugins/admin/jquery/jquery-3.1.0.min.js")}}"></script>
<script src="{{asset("assets/plugins/admin/jquery/jquery-migrate-3.0.0.min.js")}}"></script>
<script src="{{asset("assets/plugins/admin/gsap/main-gsap.min.js")}}"></script>
<script src="{{asset("assets/plugins/admin/tether/js/tether.min.js")}}"></script>
<script src="{{asset("assets/plugins/admin/bootstrap/js/bootstrap.min.js")}}"></script>
<script src="{{asset("assets/plugins/admin/backstretch/backstretch.min.js")}}"></script>
<script src="{{asset("assets/plugins/admin/bootstrap-loading/lada.min.js")}}"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
<script src="{{asset("assets/js/admin/pages/login-v1.js")}}"></script>
</body>

</html>