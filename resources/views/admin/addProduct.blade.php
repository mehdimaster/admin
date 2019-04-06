@extends("admin.master")
@section("content")
@section("css")
<link href="{{asset("assets/plugins/admin/rateit/rateit.css")}}" rel="stylesheet">
<link href="{{asset("assets/plugins/admin/chosen/chosen.css")}}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/admin/dropzone.css') }}">
<style>



</style>
@stop
    <div class="page-content">
        <div class="header">
            <h2>اضافه کردن محصول</h2>
        </div>
        <form class="form-add-product" action="" method="post"  enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="row">
                <div class=" col-md-12 col-lg-12 portlets">
                    <div class="panel">
                        <div class="panel-content">
                            @if(isset($status) && $status)
                                <div class="alert alert-success alert-dismissible">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <strong>عملیات با موفقیت انجام شد</strong>
                                </div>
                            @endif
                            <ul class="nav nav-tabs nav-primary">
                                <li class="active"><a href="#fa" data-toggle="tab">فارسی</a></li>
                                <li class=""><a href="#en" data-toggle="tab">انگلیسی</a></li>
                                <li><a href="#ru" data-toggle="tab">روسی</a></li>
                                <li><a href="#ar" data-toggle="tab">عربی</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="fa">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="col-md-12 form-group m-b-30">
                                                <input type="text" name="title_fa" class="form-control floating-label" placeholder="عنوان محصول">
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-group m-b-30">
                                            <p>توضیحات خلاصه محصول</p>
                                            <textarea class="form-control floating-label" contenteditable="true" id="editor_small_fa" name="editor_small_fa" ></textarea>
                                        </div>
                                        <div class="col-md-12 form-group m-b-30">
                                            <p>توضیحات مفصل محصول</p>
                                            <textarea class="form-control floating-label" contenteditable="true" id="editor_long_fa" name="editor_long_fa" ></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="en">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="col-md-12 form-group m-b-30">
                                                <input type="text" name="title_en" class="form-control floating-label" placeholder="عنوان محصول">
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-group m-b-30">
                                            <p>توضیحات خلاصه محصول</p>
                                            <textarea class="form-control floating-label" contenteditable="true" id="editor_small_en" name="editor_small_en" ></textarea>
                                        </div>
                                        <div class="col-md-12 form-group m-b-30">
                                            <p>توضیحات مفصل محصول</p>
                                            <textarea class="form-control floating-label" contenteditable="true" id="editor_long_en" name="editor_long_en" ></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="ru">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="col-md-12 form-group m-b-30">
                                                <input type="text" name="title_ru" class="form-control floating-label" placeholder="عنوان محصول">
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-group m-b-30">
                                            <p>توضیحات خلاصه محصول</p>
                                            <textarea class="form-control floating-label" contenteditable="true" id="editor_small_ru" name="editor_small_ru" ></textarea>
                                        </div>
                                        <div class="col-md-12 form-group m-b-30">
                                            <p>توضیحات مفصل محصول</p>
                                            <textarea class="form-control floating-label" contenteditable="true" id="editor_long_ru" name="editor_long_ru" ></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="ar">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="col-md-12 form-group m-b-30">
                                                <input type="text" name="title_ar" class="form-control floating-label" placeholder="عنوان محصول">
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-group m-b-30">
                                            <p>توضیحات خلاصه محصول</p>
                                            <textarea class="form-control floating-label" contenteditable="true" id="editor_small_ar" name="editor_small_ar" ></textarea>
                                        </div>
                                        <div class="col-md-12 form-group m-b-30">
                                            <p>توضیحات مفصل محصول</p>
                                            <textarea class="form-control floating-label" contenteditable="true" id="editor_long_ar" name="editor_long_ar" ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class=" col-md-12 col-lg-12 portlets">
                    <div class="panel">
                        <div class="panel-content">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group togglebutton togglebutton-material-indigo">
                                        <label class="col-sm-3 control-label">اضافه شدن به اسلایدر</label>
                                        <label>
                                            <input type="checkbox" checked="" name="add_to_slider"  class="col-sm-9 md-checkbox mmm">
                                        </label>
                                    </div>
                                    <div class="form-group togglebutton togglebutton-material-indigo">
                                        <label class="col-sm-3 control-label">اضافه شدن به محصولات ویژه</label>
                                        <label>
                                            <input type="checkbox" checked="" name="add_to_special_product"  class="col-sm-9 md-checkbox">
                                        </label>
                                    </div>
                                    <div class="form-group togglebutton togglebutton-material-indigo">
                                        <label class="col-sm-3 control-label">اضافه شدن به بهترین قیمت</label>
                                        <label>
                                            <input type="checkbox" checked=""  name="add_to_best_price" class="col-sm-9 md-checkbox">
                                        </label>
                                    </div>
                                    <div class="form-group togglebutton togglebutton-material-indigo">
                                        <label class="col-sm-3 control-label">آیا محصول موجود می باشد؟</label>
                                        <label>
                                            <input type="checkbox" checked=""  name="availability" class="col-sm-9 md-checkbox">
                                        </label>
                                    </div>
                                    <div class="form-group togglebutton togglebutton-material-indigo">
                                        <label class="col-sm-3 control-label">محبوبیت محصول</label>
                                        <input type="hidden" value="4" name="star_product" class="star-product">
                                        <div class="rateit" data-value="4" data-step="1"></div>
                                    </div>
                                    <div class="form-group togglebutton togglebutton-material-indigo">
                                        <label class="col-sm-3 control-label">تگ ها</label>
                                        <label>
                                            <select data-placeholder="انتخاب تگ ها" name="tags[]" class="chosen-select" multiple="">
                                                @foreach($tags as $tag)
                                                    <option value="{{$tag->id}}">
                                                        @if($tag->name_fa) {{$tag->name_fa}}
                                                        @elseif($tag->name_en) {{$tag->name_en}}
                                                        @elseif($tag->name_ru) {{$tag->name_ru}}
                                                        @elseif($tag->name_ar) {{$tag->name_ar}}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </label>
                                    </div>
                                    <div class="form-group togglebutton togglebutton-material-indigo">
                                        <label class="col-sm-3 control-label">رنگ ها</label>
                                        <label>
                                            <select data-placeholder="انتخاب رنگ ها" name="colors[]" class="chosen-select" multiple="">
                                                @foreach($colors as $color)
                                                    <option value="{{$color->id}}">
                                                        @if($color->name_fa) {{$color->name_fa}}
                                                        @elseif($color->name_en) {{$color->name_en}}
                                                        @elseif($color->name_ru) {{$color->name_ru}}
                                                        @elseif($color->name_ar) {{$color->name_ar}}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </label>
                                    </div>
                                    <div class="form-group togglebutton togglebutton-material-indigo">
                                        <label class="col-sm-3 control-label">مکان ها</label>
                                        <label>
                                            <select data-placeholder="انتخاب مکان ها" name="required_uses[]" class="chosen-select" multiple="">
                                                @foreach($requiredUses as $requiredUse)
                                                    <option value="{{$requiredUse->id}}">
                                                        @if($requiredUse->name_fa) {{$requiredUse->name_fa}}
                                                        @elseif($requiredUse->name_en) {{$requiredUse->name_en}}
                                                        @elseif($requiredUse->name_ru) {{$requiredUse->name_ru}}
                                                        @elseif($requiredUse->name_ar) {{$requiredUse->name_ar}}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </label>
                                    </div>

                                    <div class="form-group togglebutton togglebutton-material-indigo">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <p><strong>آپلود تصویر پیش فرض محصول</strong></p>
                                            <div class="fileinput-new thumbnail">
                                                <img data-src="" src="" class="img-responsive">
                                            </div>
                                            <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                            <div>
                                            <span class="btn btn-default btn-file"><span class="fileinput-new">انتخاب تصویر...</span><span class="fileinput-exists"> تغییر</span>
                                                <input type="file" name="thumb_pic">
                                            </span>
                                                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"> حذف</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group togglebutton togglebutton-material-indigo">
                                        <div class="row">
                                            <div class="col-sm-10 offset-sm-1">
                                                <h2 class="page-heading">آپلود عکس های گالری محصول <span id="counter"></span></h2>
                                                <div class="dropzone" >

                                                <div class="dropzones">
                                                    <div class="dz-message">
                                                        <div class="col-xs-8">
                                                            <div class="message">
                                                                <p>برای آپلود عکس ها کلیک کنید</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="fallback">
                                                        <input type="file" name="file" multiple>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{--Dropzone Preview Template--}}
                                        <div id="preview" style="display: none;">

                                            <div class="dz-preview dz-file-preview">
                                                <div class="dz-image"><img data-dz-thumbnail /></div>

                                                <div class="dz-details">
                                                    <div class="dz-size"><span data-dz-size></span></div>
                                                    <div class="dz-filename"><span data-dz-name></span></div>
                                                </div>
                                                <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>



                                            </div>
                                        </div>
                                        {{--End of Dropzone Preview Template--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row col-md-12">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <input type="hidden" name="idPic" class="images-id"/>
                    <input type="submit" value="ثبت محصول" class="btn btn-success center-block">
                </div>
                <div class="col-md-4"></div>

            </div>

        </form>
    </div>



@section("js_footer")
    <script src="{{asset("assets/plugins/admin/rateit/jquery.rateit.min.js")}}"></script>
    <script src="{{asset("assets/plugins/admin/bootstrap/js/jasny-bootstrap.min.js")}}"></script>
    <script src="{{asset('assets/plugins/admin/ckeditor/ckeditor.js')}}"></script>
    <script src="{{asset('assets/plugins/admin/chosen/chosen.jquery.js')}}"></script>
    <script src="{{asset('assets/js/admin/dropzone.js') }}"></script>
    <script src="{{asset('assets/js/admin/dropzone-config.js') }}"></script>
    <script>
        $(".chosen-select").chosen({width: "350px" , rtl: true});
        CKEDITOR.editorConfig = function (config) {
            config.language = 'fa';
            config.uiColor = '#F7B42C';
            config.height = 300;
            config.toolbarCanCollapse = true;

        };
        CKEDITOR.replace('editor_small_fa');
        CKEDITOR.replace('editor_long_fa');

        CKEDITOR.replace('editor_small_en');
        CKEDITOR.replace('editor_long_en');

        CKEDITOR.replace('editor_small_ru');
        CKEDITOR.replace('editor_long_ru');

        CKEDITOR.replace('editor_small_ar');
        CKEDITOR.replace('editor_long_ar');

        $(document).on("click",".rateit-range",function () {
            $(".star-product").val($(this).attr('aria-valuenow'));
        });


    </script>
@stop

@stop