@extends('layouts.vendor') 

@section('content')
                    <div class="content-area">

                            @if($user->checkWarning())

                                <div class="alert alert-danger validation text-center">
                                        <h3>{{ $user->displayWarning() }} </h3> <a href="{{ route('vendor-warning',$user->verifies()->where('admin_warning','=','1')->orderBy('id','desc')->first()->id) }}"> {{$langg->lang803}} </a>
                                </div>

                            @endif

                        
                        @include('includes.form-success')
                        <div class="row row-cards-one">

                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bgPending">
                                        <div class="left">
                                            <h5 class="title">Pending</h5>
                                            <span class="number">{{ count($pending) }}</span>
                                            <a href="{{route('vendor-order-pending')}}" class="link">{{ $langg->lang471 }}</a>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                                <i class="icofont-dollar"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bgProcessing">
                                        <div class="left">
                                            <h5 class="title">Processing</h5>
                                            <span class="number">{{ count($processing) }}</span>
                                            <a href="{{route('vendor-order-processing')}}" class="link">{{ $langg->lang471 }}</a>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                                <i class="icofont-inbox"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bgPicked">
                                        <div class="left">
                                            <h5 class="title">Picked</h5>
                                            <span class="number">{{ count($confirmed) }}</span>
                                            <a href="{{route('vendor-order-confirmed')}}" class="link">{{ $langg->lang471 }}</a>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                                <i class="icofont-inbox"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bgShipped">
                                        <div class="left">
                                            <h5 class="title">Shipped</h5>
                                            <span class="number">{{ count($shipped) }}</span>
                                            <a href="{{route('vendor-order-shipped')}}" class="link">{{ $langg->lang471 }}</a>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                                <i class="icofont-truck-alt"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bgDelivered">
                                        <div class="left">
                                            <h5 class="title">Delivered</h5>
                                            <span class="number">{{ count($completed) }}</span>
                                            <a href="{{route('vendor-order-completed')}}" class="link">{{ $langg->lang471 }}</a>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                                <i class="icofont-check-circled"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bgCanceled">
                                        <div class="left">
                                            <h5 class="title">Cancel</h5>
                                            <span class="number">{{ count($declined) }}</span>
                                            <a href="{{route('vendor-order-canceled')}}" class="link">{{ $langg->lang471 }}</a>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                                <i class="icofont-check-circled"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bg4">
                                        <div class="left">
                                            <h5 class="title">{{ $langg->lang468 }}</h5>
                                            <span class="number">{{ count($user->products) }}</span>
                                            <a href="{{route('vendor-prod-index')}}" class="link">{{ $langg->lang471 }}</a>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                                <i class="icofont-cart-alt"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>  

                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bg7">
                                        <div class="left">
                                            <h5 class="title">{{ Auth::user()->subscribes()->orderBy('id','desc')->first() ? Auth::user()->subscribes()->orderBy('id','desc')->first()->title : 'No' }} Plan</h5>
                                            <span class="number">{{ App\Models\Product::vendorConvertPrice( Auth::user()->subscribes()->orderBy('id','desc')->first() ? Auth::user()->subscribes()->orderBy('id','desc')->first()->price : 0 ) }}</span>
                                            <a href="{{route('vendor-package')}}" class="link">Change</a>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                               <i class="icofont-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bg5">
                                        <div class="left">
                                            <h5 class="title">{{ $langg->lang469 }}</h5>
                                            <span class="number">{{ App\Models\VendorOrder::where('user_id','=',$user->id)->where('status','=','completed')->sum('qty') }}</span>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                                <i class="icofont-shopify"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bg6">
                                        <div class="left">
                                            <h5 class="title">{{ $langg->lang470 }}</h5>
                                            <span class="number">{{ App\Models\Product::vendorConvertPrice( Round(App\Models\VendorOrder::where('user_id','=',$user->id)->where('status','=','completed')->sum('price')) ) }}</span>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                               <i class="icofont-dollar-true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bg3">
                                        <div class="left">
                                            <h5 class="title">On Hold</h5>
                                            <span class="number">{{ 
                                                App\Models\Product::vendorConvertPrice( App\Models\User::where('id','=',$user->id)->sum('current_balance') ) }}</span>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                               <i class="icofont-dollar-true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bg2">
                                        <div class="left">
                                            <h5 class="title">Withdraw</h5>
                                            <span class="number">{{
                                                App\Models\Product::vendorConvertPrice( Round(App\Models\VendorOrder::where('user_id','=',$user->id)->where('status','=','completed')->sum('price') *
                                                
                                                (App\Models\Generalsetting::value('pay_percent')/100)
                                                )- 
                                            (App\Models\User::where('id','=',$user->id)->sum('current_balance')) ) }}</span>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                               <i class="icofont-dollar-true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="col-md-12 col-lg-6 col-xl-4">
                                    <div class="mycard bg7">
                                        <div class="left">
                                            <h5 class="title">Pending Value</h5>
                                            <span class="number">{{ App\Models\Product::vendorConvertPrice( Round(App\Models\VendorOrder::where('user_id','=',$user->id)->where('status','=','pending')->sum('price')) ) }}</span>
                                        </div>
                                        <div class="right d-flex align-self-center">
                                            <div class="icon">
                                               <i class="icofont-dollar-true"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                    </div>

@endsection
