@extends('layouts.admin')

@section('title', tr('edit_page'))

@section('content-header', tr('edit_page'))

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li><a href="{{route('static_pages.index')}}"><i class="fa fa-book"></i> {{tr('pages')}}</a></li>
    <li class="active"> {{tr('edit_page')}}</li>
@endsection

@section('content')

@include('notification.notify')

<div class="row">

    <div class="col-md-10">

        <div class="box box-primary">

            <div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('pages')}}</b>
                <a href="{{route('static_pages.add')}}" class="btn btn-default pull-right">{{tr('add_page')}}</a>
            </div>

            <form  action="{{(Setting::get('admin_delete_control') == 1) ? '' : route('static_pages.edit.save')}}" method="POST" enctype="multipart/form-data" role="form">

                <div class="box-body">
                    <input type="hidden" name="id" value="{{$data->id}}">

                    <div class="form-group">
                        <label for="heading">{{tr('heading')}}</label>
                        <input type="text" class="form-control" name="heading" value="{{ $data->heading  }}" id="heading" placeholder="{{tr('enter')}} {{tr('heading')}}">
                    </div>

                    <div class="form-group">
                        <label for="description">{{tr('description')}}</label>

                        <textarea id="ckeditor" name="description" class="form-control" placeholder="{{tr('enter')}} {{tr('description')}}">{{$data->description}}</textarea>
                        
                    </div>

                </div>

              <div class="box-footer">
                    <button type="reset" class="btn btn-danger">{{tr('cancel')}}</button>
                    @if(Setting::get('admin_delete_control') == 1) 
                        <button type="submit" class="btn btn-success pull-right" disabled>{{tr('submit')}}</button>
                    @else
                        <button type="submit" class="btn btn-success pull-right">{{tr('submit')}}</button>
                    @endif
              </div>

            </form>
        
        </div>

    </div>

</div>
   
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.5.5/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'ckeditor' );
    </script>
@endsection
