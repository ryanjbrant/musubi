@extends('layouts.moderator')

@section('title', tr('revenues'))

@section('content-header') 

{{tr('revenues') }} {{Setting::get('currency')}} {{Auth::guard('moderator')->user()->moderatorRedeem ? Auth::guard('moderator')->user()->moderatorRedeem->total_moderator_amount : "0.00"}} 

<a href="#" id="help-popover" class="btn btn-danger" style="font-size: 14px;font-weight: 600;border-radius:50%" title="Any Help ?"><i class="fa fa-question"></i></a>

<div id="help-content" style="display: none">

	<h5 class="popover-h5">Usage : This section used to display the detailed reports for revenues based on the videos (VIEW COUNT and PPV )</h5>

    <ul class="popover-list">
        <li><b>ppv - </b> Pay Per View</li>
        <li><b>{{tr('watch_count_revenue')}} - </b> Revenue Based on the View Count </li>
        <li><b>{{tr('ppv_revenue')}} - </b> Revenue Based on the PPV amount paid  </li>
    </ul>
    
</div>

@endsection

@section('breadcrumb')
    <li><a href="{{route('moderator.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
    <li class="active"><i class="fa fa-credit-card	"></i> {{tr('revenues')}}</li>
@endsection

@section('content')

@include('notification.notify')

	<div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">
            	@if(count($data) > 0)

	              	<table id="example1" class="table table-bordered table-striped">

						<thead>
						    <tr>
						      <th>{{tr('id')}}</th>
						      <th>{{tr('video')}}</th>
						      <th>{{tr('viewers_cnt')}}</th>
						      <th>{{tr('watch_count_revenue')}}</th>
						      <th>{{tr('ppv_revenue')}}</th>
						      <th>{{tr('total')}}</th>
						    </tr>
						</thead>

						<tbody>

							@foreach($data as $i => $payment)

							    <tr>
							      	<td>{{$i+1}}</td>
							      	<td><a href="{{route('moderator.view.video' , array('id' => $payment->id))}}">{{$payment->title}}</a></td>
							      	<td> {{$payment->watch_count}}</td>
							      	<td>{{Setting::get('currency')}} {{$payment->redeem_amount}}</td>
							      	<td>{{Setting::get('currency')}} {{$payment->user_amount}}</td>
							      	<td>{{Setting::get('currency')}} {{$payment->user_amount+$payment->redeem_amount}}</td>
							    </tr>					

							@endforeach
						</tbody>
					</table>
				@else
					<h3 class="no-result">{{tr('no_result_found')}}</h3>
				@endif
            </div>
          </div>
        </div>
    </div>

@endsection


