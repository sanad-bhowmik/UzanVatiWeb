@extends('layouts.front')
@section('content')
<style>
    .btn-purple{
        background-color: #FF4B91;
        color: white;
        font-weight: 300;
    }
	.btn-purple::h
</style>

<section class="user-dashbord">
	<div class="container">
		<div class="row">
			@include('includes.user-dashboard-sidebar')
			<div class="col-lg-9">
				<div class="user-profile-details">
					<div class="order-history">
						<div class="header-area">
							<h4 class="title">
								{{ $langg->lang277 }}
							</h4>
						</div>
						<div class="mr-table allproduct mt-4">
							<div class="table-responsiv">
								<table id="example" class="table table-hover dt-responsive" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>{{ $langg->lang278 }}</th>
											<th>{{ $langg->lang279 }}</th>
											<th>{{ $langg->lang280 }}</th>
											<th>{{ $langg->lang281 }}</th>
											<th>Pay Method</th>
											@if($orders->contains('payment_status', 'pending'))
                                            <th>Pay Now</th>
                                            @endif
											<th>{{ $langg->lang282 }}</th>
										</tr>
									</thead>
									<tbody>
										@foreach($orders as $order)
										<tr>
											<td>
												{{$order->order_number}}
											</td>
											<td>
												{{date('d M Y',strtotime($order->created_at))}}
											</td>
											<td>
												{{$order->currency_sign}}{{ round($order->pay_amount * $order->currency_value , 2) }}
											</td>
											<td>
												<div class="order-status {{ $order->status }}">

													@if (ucwords($order->status ) == 'Confirmed')
													{{ __('Picked') }}
													@elseif(ucwords($order->status) == 'Declined')
													{{ __('Canceled') }}
													@elseif(ucwords($order->status) == 'Completed')
													{{ __('Delivered') }}
													@else
													{{ ucwords($order->status) }}
													@endif



												</div>
											</td>
											<td>
												<!-- payment method -->
												@if($order->method=='BKASH')

												<p>{{ $order->method}}-{!! $order->payment_status == 'pending' ? "<span class='badge badge-warning'>". $order->payment_status ."</span>":"<span class='badge badge-success'>". $order->payment_status ."</span>" !!}</p>
												@else
												{{ $order->method}}
												@endif
											</td>
											<td>
												@if( $order->payment_status == 'pending')
												<a href="{{route('bkash.paynow',$order->order_number)}}" class="btn-sm btn-purple"><imgBkash</a>
												@endif
											</td>
											<td>
												<a class="mybtn1 sm sm1" href="{{route('user-order',$order->id)}}">
													{{ $langg->lang283 }}
												</a>
											</td>
										</tr>
										@endforeach
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
@endsection