@extends('layouts.admin')

@section('title', tr('add_banner_videos'))

@section('content-header', tr('add_banner_videos'))

@section('styles')

    <link rel="stylesheet" href="{{asset('assets/css/wizard.css')}}">

    <link rel="stylesheet" href="{{asset('admin-css/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}">

    <link rel="stylesheet" href="{{asset('admin-css/plugins/iCheck/all.css')}}">
@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('admin.banner.videos')}}"><i class="fa fa-university"></i>{{tr('banner_videos')}}</a></li>
    <li class="active"><i class="fa fa-university"></i> {{tr('add_banner_videos')}}</li>
@endsection 

@section('content')

@include('notification.notify')

<p class="text-justify" style="color:brown;"><b>Please Note : </b> {{tr('banner_short_notes')}}</p>

@if(envfile('QUEUE_DRIVER') != 'redis') 

 <div class="alert alert-warning">

    <button type="button" class="close" data-dismiss="alert">×</button>
    
    {{tr('warning_error_queue')}}

</div>

@endif

<div class="row">
    <div class="col-lg-12">
        <section>
        <div class="wizard">
            <div class="wizard-inner">
                <div class="connecting-line"></div>

                <ul class="nav nav-tabs" role="tablist">

                    <li role="presentation" class="active">
                        <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Video Details">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-book"></i>
                            </span>
                        </a>
                    </li>

                    <li role="presentation" class="disabled">
                        <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Category">
                            <span class="round-tab">
                                <i class="fa fa-suitcase"></i>
                            </span>
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="Sub Category">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-folder-open"></i>
                            </span>
                        </a>
                    </li>

                    <li role="presentation" class="disabled">
                        <a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Upload Video/Image">
                            <span class="round-tab">
                                <i class="glyphicon glyphicon-picture"></i>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>

            <form id="video-upload" action="{{(Setting::get('admin_delete_control') == 1) ? '' : route('admin.save.video')}}" method="POST" enctype="multipart/form-data" role="form">
                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel" id="step1">
                        <!-- <h3>Video Details</h3> -->
                        <div style="margin-left: 15px"><small>{{tr('note')}} : <span style="color:red">*</span> fields are mandatory. Please fill and click next.</small></div> 
                        <hr>
                        <div class="">
                            <input type="hidden" value="1" name="ajax_key">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="title" class="">{{tr('title')}} * </label>
                                    <input type="text" required class="form-control" id="title" name="title" placeholder="{{tr('title')}}">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="datepicker" class="">{{tr('publish_time')}} * </label>

                                    <input type="text" name="publish_time" placeholder="Select the Publish Time i.e YYYY-MM-DD" class="form-control pull-right" id="datepicker">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label>{{tr('duration')}} * : </label><small> {{tr('duration_note')}}</small>

                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input required type="text" name="duration" class="form-control" data-inputmask="'alias': 'hh:mm:ss'" data-mask id="duration">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="ratings" class="">{{tr('ratings')}} *</label>
                                    <div class="starRating">
                                        <input id="rating5" type="radio" name="ratings" value="5">
                                        <label for="rating5">5</label>

                                        <input id="rating4" type="radio" name="ratings" value="4">
                                        <label for="rating4">4</label>

                                        <input id="rating3" type="radio" name="ratings" value="3">
                                        <label for="rating3">3</label>

                                        <input id="rating2" type="radio" name="ratings" value="2">
                                        <label for="rating2">2</label>

                                        <input id="rating1" type="radio" name="ratings" value="1">
                                        <label for="rating1">1</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="description" class="">{{tr('description')}} * </label>
                                    <textarea  style="overflow:auto;resize:none" class="form-control" required rows="4" cols="50" id="description" name="description"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="reviews" class="">{{tr('reviews')}} * </label>
                                    <textarea  style="overflow:auto;resize:none" class="form-control" required rows="4" cols="50" id="reviews" name="reviews"></textarea>
                                </div>
                            </div>
                        </div>
                        <ul class="list-inline pull-right">
                            <li>
                                <button type="button" style="display: none;" id="{{REQUEST_STEP_1}}" class="btn btn-primary next-step">{{tr('next')}}</button>
                                <button type="button" class="btn btn-primary" onclick="saveVideoDetails({{REQUEST_STEP_1}})">{{tr('next')}}</button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-pane" role="tabpanel" id="step2">
                        <h3>Category</h3>
                        <hr>
                        <div id="category">
                            @foreach($categories as $category)
                            <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12">
                                <a onclick="saveCategory({{$category->id}}, {{REQUEST_STEP_2}})" class="category-item text-center">
                                    <div style="background-image: url({{$category->picture}})" class="category-img bg-img"></div>
                                    <h3 class="category-tit">{{$category->name}}</h3>
                                </a>
                            </div>
                            @endforeach
                            <input type="hidden" name="category_id" id="category_id" />
                        </div>
                        <div class="clearfix"></div>
                        <ul class="list-inline">
                            <li class="pull-left"><button type="button" class="btn btn-danger prev-step">{{tr('previous')}}</button></li>
                            <li class="pull-right" style="display: none"><button type="button" class="btn btn-primary next-step" id="{{REQUEST_STEP_2}}">{{tr('save_continue')}}</button></li>
                            <div class="clearfix"></div>
                        </ul>
                    </div>
                    <div class="tab-pane" role="tabpanel" id="step3">
                        <h3>Sub Category</h3>
                        <hr>
                        <div id="sub_category">
                            
                        </div>
                        <input type="hidden" name="sub_category_id" id="sub_category_id" />
                        <div class="clearfix"></div>
                        <ul class="list-inline">
                            <li><button type="button" class="btn btn-danger prev-step">{{tr('previous')}}</button></li>
                            <!-- <li><button type="button" class="btn btn-default next-step">Skip</button></li> -->
                            <li class="pull-right" style="display:none"><button  id="{{REQUEST_STEP_3}}" type="button" class="btn btn-primary btn-info-full next-step">{{tr('save_continue')}}</button></li>
                            <div class="clearfix"></div>
                        </ul>
                    </div>
                    <div class="tab-pane" role="tabpanel" id="complete">
                        <h3>Upload Video/Image</h3>
                        <hr>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="genre_id">
                                <div class="form-group">

                                    <label for="genre" class="">{{tr('select_genre')}}</label>

                                    <select id="genre" name="genre_id" class="form-control" disabled>
                                        <option value="">{{tr('select_genre')}}</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden"  name="is_banner" value="1">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="banner_image" class="">{{tr('banner_image')}}</label>
                                     <input type="file" id="banner_image" name="banner_image" accept="image/png, image/jpeg" placeholder="{{tr('banner_image')}}" style="display:none" onchange="loadFile(this, 'banner_img')">
                                    <div>
                                        <img src="{{asset('images/320x150.png')}}" style="width:150px;height:75px;" 
                                        onclick="$('#banner_image').click();return false;" id="banner_img"/>
                                    </div>
                                    <p class="help-block">{{tr('image_validate')}} {{tr('rectangle_image')}}</p>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="default_image" class="">{{tr('default_image')}} *</label>
                                    <input type="file" id="default_image" accept="image/png,image/jpeg" name="default_image" placeholder="{{tr('default_image')}}" style="display:none" onchange="loadFile(this,'default_img')">
                                    <div>
                                        <img src="{{asset('images/320x150.png')}}" style="width:150px;height:75px;" 
                                        onclick="$('#default_image').click();return false;" id="default_img"/>
                                    </div>
                                    <p class="help-block">{{tr('image_validate')}} {{tr('rectangle_image')}}</p>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="other_image1" class="">{{tr('other_image1')}} * </label>
                                    <input type="file" id="other_image1" accept="image/png,image/jpeg" name="other_image1" placeholder="{{tr('other_image1')}}" style="display:none" onchange="loadFile(this,'other_img1')">
                                    <div>
                                        <img src="{{asset('images/320x150.png')}}" style="width:150px;height:75px;" 
                                        onclick="$('#other_image1').click();return false;" id="other_img1"/>
                                    </div>
                                    <p class="help-block">{{tr('image_validate')}} {{tr('rectangle_image')}}</p>
                                </div>
                            </div>
                        
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="other_image2" class="">{{tr('other_image2')}} *</label>
                                    <input type="file" id="other_image2" accept="image/png,image/jpeg" name="other_image2" placeholder="{{tr('other_image2')}}" style="display:none" onchange="loadFile(this,'other_img2')">
                                    <div>
                                        <img src="{{asset('images/320x150.png')}}" style="width:150px;height:75px;" 
                                        onclick="$('#other_image2').click();return false;" id="other_img2"/>
                                    </div>
                                    <p class="help-block">{{tr('image_validate')}} {{tr('rectangle_image')}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="video_type" class="">{{tr('video_type')}} *</label></br>
                                    <label style="margin-top:10px" id="video_upload">
                                        <input required type="radio" name="video_type" value="1" class="flat-red" checked>
                                        {{tr('video_upload_link')}}
                                    </label>

                                    <label style="margin-top:10px" id="youtube">
                                        <input required type="radio" name="video_type" class="flat-red"  value="2">
                                        {{tr('youtube')}}
                                    </label>

                                    <label style="margin-top:10px" id="other_link">
                                        <input required type="radio" name="video_type" value="3" class="flat-red">
                                        {{tr('other_link')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="compress">
                                <div class="form-group">
                                    <label>{{tr('compress_video')}}</label>
                                    <br>
                                    <div>
                                        <input type="radio" name="compress_video" value="1"> <label>{{tr('yes')}}</label> &nbsp;&nbsp;
                                        <input type="radio" name="compress_video" value="0" checked> <label>{{tr('no')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="resolution">
                                <div class="form-group">
                                    <label>{{tr('resize_video_resolutions')}}</label>
                                    <br>
                                    <div>
                                        @foreach(getVideoResolutions() as $resolution)
                                            <input type="checkbox" name="video_resolutions[]" value="{{$resolution->value}}"> <label>{{$resolution->value}}</label> &nbsp;&nbsp;
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>


                            <div id="upload">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="video" class="">{{tr('video')}}</label>
                                        <input type="file" id="video" accept="video/mp4" name="video" placeholder="{{tr('picture')}}">
                                        <p class="help-block">{{tr('video_validate')}}</p>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="trailer_video" class="">{{tr('trailer_video')}}</label>
                                        <input type="file" id="trailer_video" accept="video/mp4" name="trailer_video" placeholder="{{tr('trailer_video')}}">
                                        <p class="help-block">{{tr('video_validate')}}</p>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">

                                        <label for="video_upload_type" class="">{{tr('video_upload_type')}}</label></br>

                                        @if(check_s3_configure())
                                            <label style="margin-top:10px" >
                                                <input type="radio" name="video_upload_type" value="1" class="flat-red">
                                                {{tr('s3')}}
                                            </label>
                                        @endif

                                        <label style="margin-top:10px">
                                            <input type="radio" name="video_upload_type" class="flat-red"  value="2" checked>
                                            {{tr('direct')}}
                                        </label>

                                    </div>
                                </div>
                            </div>
                            <div id="others">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="other_video" class="">{{tr('video')}}</label>
                                        <input type="text" class="form-control" id="other_video" name="other_video" placeholder="{{tr('video')}}">
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="other_trailer_video" class="">{{tr('trailer_video')}}</label>
                                        <input type="text" class="form-control" id="other_trailer_video" name="other_trailer_video" placeholder="{{tr('trailer_video')}}">
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr>
                        <ul class="list-inline">
                            <li><button type="button" class="btn btn-danger prev-step">{{tr('previous')}}</button></li>
                            <!-- <li><button type="button" class="btn btn-default next-step">Skip</button></li> -->
                            @if(Setting::get('admin_delete_control') == 1) 
                            <li class="pull-right"><button disabled id="{{REQUEST_STEP_FINAL}}" type="button" class="btn btn-primary btn-info-full">{{tr('finish')}}</button></li>
                            @else
                                <li class="pull-right"><button id="{{REQUEST_STEP_FINAL}}" type="submit" class="btn btn-primary btn-info-full">{{tr('finish')}}</button></li>
                                 <li class="pull-right">
                                    <div class="progress">
                                        <div class="bar"></div >
                                        <div class="percent">0%</div >
                                    </div>
                                <li>
                            @endif
                            <div class="clearfix"></div>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </form>
        </div>
    </section>
   </div>
</div>
<div class="overlay">
    <div id="loading-img"></div>
</div>
@endsection

@section('scripts')


    <script src="{{asset('admin-css/plugins/bootstrap-datetimepicker/js/moment.min.js')}}"></script> 

    <script src="{{asset('admin-css/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js')}}"></script> 

    <script src="{{asset('admin-css/plugins/iCheck/icheck.min.js')}}"></script>

    <script type="text/javascript">

        var banner_image = 1;

        var video_id = '';
        
        var cat_url = "{{ url('select/sub_category')}}";
        var step3 = "{{REQUEST_STEP_3}}";
        var sub_cat_url = "{{ url('select/genre')}}";
        var final = "{{REQUEST_STEP_FINAL}}";

        $('#datepicker').datetimepicker({
            minTime: "00:00:00",
            minDate: moment(),
            autoclose:true,
        });
        $('#upload').show();
        $('#others').hide();
        $("#compress").show();
        $("#resolution").show();

        $("#video_upload").click(function(){
            console.log("video upload");
            $("#upload").show();
            $("#others").hide();
            $("#compress").show();
            $("#resolution").show();
        });

        $("#youtube").click(function(){
            $("#others").show();
            $("#upload").hide();
            $("#compress").hide();
            $("#resolution").hide();
        });

        $("#other_link").click(function(){
            $("#others").show();
            $("#upload").hide();
            $("#compress").hide();
            $("#resolution").hide();
        });

    </script>

    <script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'ckeditor' );
    </script>

    <script src="{{asset('admin-css/plugins/jquery.form.js')}}"></script>

    <script src="{{asset('assets/js/wizard.js')}}"></script>
    <script>
        $('form').submit(function () {
           window.onbeforeunload = null;
        });
        window.onbeforeunload = function() {
             return "Data will be lost if you leave the page, are you sure?";
        };
    </script>
@endsection


