@extends('layouts.admin')

@section('title', tr('genres'))

@section('content-header')

	<span style="color:#1d880c !important">{{$sub_category->name}} </span> - {{tr('genres') }}

@endsection

@section('breadcrumb')
    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
     <li><a href="{{route('admin.categories')}}"><i class="fa fa-suitcase"></i>{{tr('categories')}}</a></li>
    <li><a href="{{route('admin.sub_categories', array('category' => $sub_category->category_id))}}"><i class="fa fa-suitcase"></i> {{tr('sub_categories')}}</a></li>
    <li class="active"><i class="fa fa-suitcase"></i> {{tr('genres')}}</li>
@endsection

@section('content')

@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
          	<div class="box-header label-primary">
                <b style="font-size:18px;">{{tr('genres')}}</b>
                <a href="{{route('admin.add.genre' , array('sub_category' => $sub_category->id))}}" class="btn btn-default pull-right">{{tr('add_genre')}}</a>
            </div>
            <div class="box-body">

            	@if(count($data) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('category')}}</th>
						      <th>{{tr('sub_category')}}</th>
						      <th>{{tr('genre')}}</th>
						      <th>{{tr('image')}}</th>
						      <th>{{tr('status')}}</th>
						      <th>{{tr('action')}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($data as $i => $value)


							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td>{{$value->category_name}}</td>
							      	<td>{{$value->sub_category_name}}</td>
							      	<td>{{$value->genre_name}}</td>

							      	<td>
	                                	<img style="height: 30px;" src="{{$value->image}}">
	                            	</td>
							      	<td>
							      		@if($value->is_approved)
							      			<span class="label label-success">{{tr('approved')}}</span>
							       		@else
							       			<span class="label label-warning">{{tr('pending')}}</span>
							       		@endif
							       </td>


								    <td>
            							<ul class="admin-action btn btn-default">
            								<li class="dropup">
								                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
								                  {{tr('action')}} <span class="caret"></span>
								                </a>
								                <ul class="dropdown-menu">

								                	<li role="presentation">
								                		<a role="menuitem" tabindex="-1" href="{{route('admin.view.genre' , array('id' => $value->genre_id))}}">{{tr('view_genre')}}</a>
								                	</li>



								                  	<li role="presentation">
														@if(Setting::get('admin_delete_control'))
                                                            <a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('edit')}}</a>
                                                        @else
															<a role="menuitem" tabindex="-1" href="{{route('admin.edit.edit_genre' , array('sub_category_id' => $value->sub_category_id,'genre_id' => $value->genre_id))}}">{{tr('edit')}}</a>
														@endif

													</li>

								                  	<li class="divider" role="presentation"></li>

								                  	@if($value->is_approved)
								                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.genre.approve' , array('id' => $value->genre_id , 'status' =>0))}}">{{tr('decline')}}</a></li>
								                  	@else
								                  		<li role="presentation"><a role="menuitem" tabindex="-1" href="{{route('admin.genre.approve' , array('id' => $value->genre_id , 'status' => 1))}}">{{tr('approve')}}</a></li>
								                  	@endif


								                  	<li role="presentation">

								                  		@if(Setting::get('admin_delete_control'))

									                  	 	<a role="button" href="javascript:;" class="btn disabled" style="text-align: left">{{tr('delete')}}</a>

									                  	 @else

								                  			<a role="menuitem" onclick="return confirm('Are you sure?')" tabindex="-1" href="{{route('admin.delete.genre' , array('id' => $value->genre_id))}}">{{tr('delete')}}</a>
								                  		@endif

								                  	</li>
								              
								                </ul>
              								</li>
            							</ul>
								    </td>
							    </tr>

							   
							    

							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">{{tr('no_genre')}}</h3>
				@endif
            </div>
          </div>
        </div>

    </div>

@endsection
