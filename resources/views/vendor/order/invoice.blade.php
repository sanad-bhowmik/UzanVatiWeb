@extends('layouts.vendor')

@section('content')
<div class="content-area">
                    <div class="mr-breadcrumb">
                        <div class="row">
                            <div class="col-lg-12">
                                <h4 class="heading">{{ $langg->lang586 }} <a class="add-btn" href="{{ route('vendor-order-index') }}"><i class="fas fa-arrow-left"></i> {{ $langg->lang550 }}</a></h4>
                                <ul class="links">
                                    <li>
                                        <a href="{{ route('vendor-dashboard') }}">{{ $langg->lang441 }} </a>
                                    </li>
                                    <li>
                                        <a href="#">{{ $langg->lang443 }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('vendor-order-index') }}">{{ $langg->lang586 }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
    <div class="order-table-wrap">
            <div class="row">
                <div class="col-md-6">
                    <div class="action-list">

                
                        Vendor Order Status:   <select id="vendor-status" class="vendor-btn {{ $order->status  }}">
                               <option
                                   value="{{ route('vendor-order-status', [$order->order_number, 'pending']) }}"
                                   {{ $order->status == 'pending' ? 'selected' : '' }}>
                                   {{ $langg->lang540 }}</option>

                                <option
                                   value="{{ route('vendor-order-status', [$order->order_number, 'processing']) }}"
                                   {{ $order->status  == 'processing' ? 'selected' : '' }}>
                                   {{ $langg->lang541 }}</option>


                               <option
                                   value="{{ route('vendor-order-status', [$order->order_number, 'confirmed']) }}"
                                   {{ $order->status  == 'confirmed' ? 'selected' : '' }}>
                                   Picked</option>
                              

                               <option
                                   value="{{ route('vendor-order-status', [$order->order_number, 'shipped']) }}"
                                   {{ $order->status  == 'shipped' ? 'selected' : '' }}>Shipped
                               </option>

                               <option
                                   value="{{ route('vendor-order-status', [$order->order_number, 'completed']) }}"
                                   {{ $order->status  == 'completed' ? 'selected' : '' }}>
                                   Delivered</option>


                               <option
                                   value="{{ route('vendor-order-status', [$order->order_number, 'declined']) }}"
                                   {{ $order->status == 'declined' ? 'selected' : '' }}>
                                   Cancel</option>
                           </select>

                         


                       </div>

                </div>

                <div class="col-lg-6">
                    <a href="javascript:;"
                    data-href="{{ route('vendor-order-track', $order->id) }}"
                    class="track" data-toggle="modal" data-target="#modal1"><i
                        class="fas fa-truck"></i> Track Order</a>
                </div>
            </div>






        <div class="invoice-wrap">
            <div class="invoice__title">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="invoice__logo text-left">
                           <img src="{{ asset('assets/images/'.$gs->invoice_logo) }}" alt="uzanvati">
                        </div>
                    </div>
                    <div class="col-lg-6 text-right">

                    
                            @php
                        $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG(); 
                            @endphp

        <img src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($order->order_number , $generatorPNG::TYPE_CODE_128)) }}">
 
                        <a class="btn  add-newProduct-btn print" href="{{route('vendor-order-print',$order->order_number)}}"
                        target="_self"><i class="fa fa-print"></i> {{ $langg->lang607 }}</a>
                        
                    </div>
                </div>
            </div>
            <br>
            <div class="row invoice__metaInfo mb-4">
                <div class="col-lg-6">
                    <div class="invoice__orderDetails">
                        
                        <p><strong>{{ $langg->lang601 }} </strong></p>
                        <span><strong>{{ $langg->lang588 }} :</strong> {{ sprintf("%'.08d", $order->id) }}</span><br>
                        <span><strong>{{ $langg->lang589 }} :</strong>{{ date('d/m/Y h:i:s A', strtotime($order->created_at)) }}</span><br>
                        <span><strong>{{  $langg->lang590 }} :</strong> {{ $order->order_number }}</span><br>
                        @if($order->dp == 0)
                        <span> <strong>{{ $langg->lang602 }} :</strong>
                            @if($order->shipping == "pickup")
                            {{ $langg->lang603 }}
                            @else
                            {{ $langg->lang604 }}
                            @endif
                        </span><br>
                        @endif
                        <span> <strong>{{ $langg->lang605 }} :</strong> {{$order->method}}</span>
                    </div>
                </div>
            </div>
            <div class="row invoice__metaInfo">
           @if($order->dp == 0)
                <div class="col-lg-6">
                        <div class="invoice__shipping">
                            <p><strong>{{ $langg->lang606 }}</strong></p>
                           <span><strong>{{ $langg->lang557 }}</strong>: {{ $order->shipping_name == null ? $order->customer_name : $order->shipping_name}}</span><br>
                           <span><strong>{{ __('Customer Phone') }}</strong>: {{ $order->shipping_phone == null ? $order->customer_phone : $order->shipping_phone}}</span><br>
                           <span><strong>{{ $langg->lang560 }}</strong>: {{ $order->shipping_address == null ? $order->customer_address : $order->shipping_address }}</span><br>
                           <span><strong>Thana</strong>: {{ $order->shipping_state == null ? $order->customer_state : $order->shipping_state }}</span><br>
                           <span><strong>District</strong>: {{ $order->shipping_city == null ? $order->customer_city : $order->shipping_city }}</span><br>
                           <span><strong>{{ $langg->lang561 }}</strong>: {{ $order->shipping_country == null ? $order->customer_country : $order->shipping_country }}</span>

                        </div>
                </div>

            @endif

                <div class="col-lg-6">
                        <div class="buyer">
                            <p><strong>{{ $langg->lang587 }}</strong></p>
                            <span><strong>{{ $langg->lang557 }}</strong>: {{ $order->customer_name}}</span><br>
                            <span><strong>{{ __('Customer Phone') }}</strong>: {{ $order->customer_phone }}</span><br>
                            <span><strong>{{ $langg->lang560 }}</strong>: {{ $order->customer_address }}</span><br>
                            <span><strong>Thana</strong>: {{ $order->customer_state }}</span><br>
                            <span><strong>District</strong>: {{ $order->customer_city }}</span><br>
                            <span><strong>{{ $langg->lang561 }}</strong>: {{ $order->customer_country }}</span>
                        </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="invoice_table">
                        <div class="mr-table">
                            <div class="table-responsive">
                                <table id="example2" class="table table-hover dt-responsive" cellspacing="0"
                                    width="100%" >
                                    <thead>
                                        <tr>
                                            <th>{{ $langg->lang591 }}</th>
                                            <th>{{ $langg->lang539 }}</th>
                                            <th>{{ $langg->lang600 }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $subtotal = 0;
                                        $data = 0;
                                        $tax = 0;

                                        @endphp
                                        @foreach($cart->items as $product)

                                        @php $p= App\Models\Product::find($product['item']["id"]);@endphp
                                @if($product['item']['user_id'] != 0)
                                    @if($product['item']['user_id'] == $user->id)

                                        <tr>
                                            <td width="50%">
                                                @if($product['item']['user_id'] != 0)
                                                @php
                                                $user = App\Models\User::find($product['item']['user_id']);
                                                @endphp
                                                @if(isset($user))
                                                <a target="_blank"
                                                    href="{{ $p->campaign_product ==0 ? route('front.product', $p->slug)  : route('front.product-campaign', $p->slug)  }}">
                                                    <img width="60" height="60" src="{{ asset('assets/images/products/'.$product['item']['photo']) }}" alt="No-Image">
                                                
                                                    {{ $product['item']['name']}}
                                                </a>
                                                @else
                                                <a href="javascript:;">{{$product['item']['name']}}</a>
                                                @endif

                                                @else
                                                <a href="javascript:;">{{ $product['item']['name']}}</a>

                                                @endif
                                            </td>


                                            <td>
                                                @if($product['size'])
                                               <p>
                                                    <strong>{{ $langg->lang312 }} :</strong> {{str_replace('-',' ',$product['size'])}}
                                               </p>
                                               @endif
                                               @if($product['color'])
                                                <p>
                                                        <strong>{{ $langg->lang313 }} :</strong> <span
                                                        style="width: 40px; height: 20px; display: block; background: #{{$product['color']}};"></span>
                                                </p>
                                                @endif
                                                <p>
                                                        <strong>{{ $langg->lang754 }} :</strong> {{$order->currency_sign}}{{ round($product['item_price'] * $order->currency_value , 2) }}
                                                </p>
                                               <p>
                                                    <strong>{{ $langg->lang595 }} :</strong> {{$product['qty']}} {{ $product['item']['measure'] }}
                                               </p>
                                                    @if(!empty($product['keys']))

                                                    @foreach( array_combine(explode(',', $product['keys']), explode(',', $product['values']))  as $key => $value)
                                                    <p>

                                                        <b>{{ ucwords(str_replace('_', ' ', $key))  }} : </b> {{ $value }} 

                                                    </p>
                                                    @endforeach

                                                    @endif

                                            </td>

                                      
                                            <td>{{$order->currency_sign}}{{ round($product['price'] * $order->currency_value , 2) }}</td>
                                            @php
                                            $subtotal += round($product['price'] * $order->currency_value, 2);
                                            @endphp

                                        </tr>

                                    @endif
                                @endif
                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="2">{{ $langg->lang597 }}</td>
                                            <td>{{$order->currency_sign}}{{ round($subtotal, 2) }}</td>
                                        </tr>
                                        @if(Auth::user()->id == $order->vendor_shipping_id)
                                        @if($order->shipping_cost != 0)
                                            @php 
                                            $price = round(($order->shipping_cost / $order->currency_value),2);
                                            @endphp
                                            @if(DB::table('shippings')->where('price','=',$price)->count() > 0)
                                            <tr>
                                                <td colspan="2">{{ DB::table('shippings')->where('price','=',$price)->first()->title }}({{$order->currency_sign}})</td>
                                                <td>{{ round($order->shipping_cost , 2) }}</td>
                                            </tr>
                                            @endif
                                        @endif
                                        @endif
                                        @if(Auth::user()->id == $order->vendor_packing_id)
                                        @if($order->packing_cost != 0)
                                            @php 
                                            $pprice = round(($order->packing_cost / $order->currency_value),2);
                                            @endphp
                                            @if(DB::table('packages')->where('price','=',$pprice)->count() > 0)
                                            <tr>
                                                <td colspan="2">{{ DB::table('packages')->where('price','=',$pprice)->first()->title }}({{$order->currency_sign}})</td>
                                                <td>{{ round($order->packing_cost , 2) }}</td>
                                            </tr>
                                            @endif
                                        @endif
                                        @endif

                                        @if($order->tax != 0)
                                        <tr>
                                            <td colspan="2">{{ $langg->lang599 }}({{$order->currency_sign}})</td>
                                            @php
                                                $tax = ($subtotal / 100) * $order->tax;
                                                $subtotal =  $subtotal + $tax;
                                            @endphp
                                            <td>{{round($tax, 2)}}</td>
                                        </tr>
                                        @endif

                                        <tr>
                                            <td colspan="1"></td>
                                            <td>{{ $langg->lang600 }}</td>
                                            <td>{{$order->currency_sign}}{{ round(($subtotal + $data), 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Main Content Area End -->
</div>
</div>
</div>
{{-- Add ORDER MODAL --}}

<div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="submit-loader">
                <img src="{{ asset('assets/images/' . $gs->admin_loader) }}" alt="">
            </div>
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>

</div>

{{-- ORDER MODAL --}}
@endsection


@section('scripts')
<script>
$(document).on('click', '.track', function() {

    //   console.log("hello");




    $('#modal1').find('.modal-title').html('TRACK');
    $('#modal1 .modal-content .modal-body').html('').load($(this).attr('data-href'), function(response,
        status, xhr) {
        if (status == "success") {



        }

    });
});
</script>
@endsection