@extends('layouts.front')
@section('content')
<!-- User Dashbord Area Start -->
<section class="user-dashbord">
    <div class="container">
        <div class="row">
            @include('includes.user-dashboard-sidebar')
            <div class="col-lg-9">
             <div class="row">
                <div class="col-md-12">
                    @include('includes.form-success')
                </div>
             </div>
                <div class="user-profile-details">
                    <div class="order-details">

                     
                        <div class="process-steps-area">
                            @include('includes.order-process')
                        </div>
                        


                        <div class="header-area">
                            {{-- <h4 class="title">
                                {{ $langg->lang284 }}
                            </h4> --}}
                        </div>
                        <div class="view-order-page">
                            <h3 class="order-code">{{ $langg->lang285 }} {{$order->order_number}} [
                                {{$order->status=='completed' ? 'delivered' : ($order->status =='confirmed' ? 'picked' : $order->status)}}]
                            </h3>
                            <div class="print-order text-right">
                                <a href="{{route('user-order-print',$order->id)}}" target="_blank"
                                    class="print-order-btn">
                                    <i class="fa fa-print"></i> {{ $langg->lang286 }}
                                </a>
                            </div>
                            <p class="order-date">{{ $langg->lang301 }} {{date('d/m/Y h:i:s A', strtotime($order->created_at))}}
                            </p>

                            @if($order->dp == 1)

                            <div class="billing-add-area">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>{{ $langg->lang287 }}</h5>
                                        <address>
                                            {{ $langg->lang288 }} {{ $order->customer_name }}<br>
                                            {{ $langg->lang289 }} {{ $order->customer_email }}<br>
                                            {{ $langg->lang290 }} {{ $order->customer_phone }}<br>
                                            {{ $langg->lang291 }} {{ $order->customer_address }}<br>
                                            {{ $langg->lang829 }} {{ $order->customer_country }}<br>
                                            {{ $langg->lang830 }} {{ $order->customer_state }}<br>
                                            {{$order->customer_city}}-{{$order->customer_zip}}
                                        </address>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>{{ $langg->lang292 }}</h5>

                                        <p>{{ $langg->lang798 }}:
                                             {!! $order->payment_status == 'Pending' ? "<span class='badge badge-danger'>". $langg->lang799 ."</span>":"<span class='badge badge-success'>". $langg->lang800 ."</span>" !!}
                                        </p>

                                        <p>{{ $langg->lang293 }}
                                            {{$order->currency_sign}}{{ round(($order->pay_amount + $order->wallet_price) * $order->currency_value , 2) }}
                                        </p>
                                        <p>{{ $langg->lang294 }} {{$order->method}}</p>

                                        @if($order->method != "Cash On Delivery")
                                        @if($order->method=="Stripe")
                                        {{$order->method}} {{ $langg->lang295 }} <p>{{$order->charge_id}}</p>
                                        @endif
                                        {{$order->method}} {{ $langg->lang296 }} <p id="ttn">{{$order->txnid}}</p>
                                        <!-- <a id="tid" style="cursor: pointer;" class="mybtn2">{{ $langg->lang297 }}</a>  -->

                                        <form id="tform">
                                            <input style="display: none; width: 100%;" type="text" id="tin" placeholder="{{ $langg->lang299 }}" required="" class="mb-3">
                                            <input type="hidden" id="oid" value="{{$order->id}}">

                                            <button style="display: none; padding: 5px 15px; height: auto; width: auto; line-height: unset;" id="tbtn" type="submit" class="mybtn1">{{ $langg->lang300 }}</button>
                                                
                                                <a style="display: none; cursor: pointer;  padding: 5px 15px; height: auto; width: auto; line-height: unset;" id="tc"  class="mybtn1">{{ $langg->lang298 }}</a>
                                                
                                                {{-- Change 1 --}}
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @else
                            <div class="shipping-add-area">
                                <div class="row">
                                    <div class="col-md-6">
                                        @if($order->shipping == "shipto")
                                        <h5>{{ $langg->lang302 }}</h5>
                                        <address>
                                            {{ $langg->lang288 }}
                                            {{$order->shipping_name == null ? $order->customer_name : $order->shipping_name}}<br>
                                            {{ $langg->lang289 }}
                                            {{$order->shipping_email == null ? $order->customer_email : $order->shipping_email}}<br>
                                            {{ $langg->lang290 }}
                                            {{$order->shipping_phone == null ? $order->customer_phone : $order->shipping_phone}}<br>
                                            {{ $langg->lang291 }}
                                            {{$order->shipping_address == null ? $order->customer_address : $order->shipping_address}}<br>
                                            {{ $langg->lang830 }}: {{ $order->shipping_state == null ? $order->customer_state: $order->shipping_state}}<br>
                                            {{$order->shipping_city == null ? $order->customer_city : $order->shipping_city}}-{{$order->shipping_zip == null ? $order->customer_zip : $order->shipping_zip}}
                                        </address>
                                        @else
                                        <h5>{{ $langg->lang303 }}</h5>
                                        <address>
                                            {{ $langg->lang304 }} {{$order->pickup_location}}<br>
                                        </address>
                                        @endif

                                    </div>
                                    <div class="col-md-6">
                                        <h5>{{ $langg->lang305 }}</h5>
                                        @if($order->shipping == "shipto")
                                        <p>{{ $langg->lang306 }}</p>
                                        @else
                                        <p>{{ $langg->lang307 }}</p>
                                        @endif

                                        @if($order->shipping_cost != 0)
                                        @php 
                                        $price = round(($order->shipping_cost / $order->currency_value),2);
                                        @endphp
                                        @if(DB::table('shippings')->where('price','=',$price)->count() > 0)
                                <p>
                                    {{ DB::table('shippings')->where('price','=',$price)->first()->title }}: {{$order->currency_sign}}{{ round($order->shipping_cost, 2) }}
                                </p>
                                        @endif
                                    @endif

                                    @if($order->packing_cost != 0)

                                        @php 
                                        $pprice = round(($order->packing_cost / $order->currency_value),2);
                                        @endphp


                                        @if(DB::table('packages')->where('price','=',$pprice)->count() > 0)
                                <p>
                                    {{ DB::table('packages')->where('price','=',$pprice)->first()->title }}: {{$order->currency_sign}}{{ round($order->packing_cost, 2) }}
                                </p>
                                        @endif
                                    @endif


                                    </div>
                                </div>
                            </div>
                            <div class="billing-add-area">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>{{ $langg->lang287 }}</h5>
                                        <address>
                                            {{ $langg->lang288 }} {{ $order->customer_name }}<br>
                                            {{ $langg->lang289 }} {{ $order->customer_email }}<br>
                                            {{ $langg->lang290 }} {{ $order->customer_phone }}<br>
                                            {{ $langg->lang291 }} {{ $order->customer_address }}<br>
                                            {{ $langg->lang830 }}: {{ $order->customer_state }}<br>
                                            {{$order->customer_city}}-{{$order->customer_zip}}
                                        </address>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>{{ $langg->lang292 }}</h5>

                                        <p>{{ $langg->lang798 }}
                                             {!! $order->payment_status == 'pending' ? "<span class='badge badge-warning'>". $order->payment_status ."</span>":"<span class='badge badge-success'>". $order->payment_status ."</span>" !!}
                                        </p>

                                        <p>Order Status

                                            @php $currStatus= $order->status=='completed' ? 'delivered' : ($order->status =='confirmed' ? 'picked' : $order->status)  @endphp
                                             {!! $order->status == 'pending' ? "<span style='color:#fff;' class='badge  $currStatus'>". $currStatus ."</span>":"<span style='color:#fff;' class='badge $currStatus'>". $currStatus ."</span>" !!}
                                        </p>



                                        <p class="order-txt">Order Amount:
                                            {{$order->currency_sign}} {{ round(($order->pay_amount) * $order->currency_value , 2) }}
                                        </p>
                                        <p class="order-txt">Paid Amount:
                                            {{$order->currency_sign}}  {{ round(($order->paid_amount) * $order->currency_value , 2) }}
                                        </p>
                                        <p class="order-txt"> To be Paid Amount:
                                            {{$order->currency_sign}}  {{ round((($order->pay_amount)-($order->paid_amount)) * ($order->currency_value ), 2) }}
                                        </p>
                                        <p class="order-txt">{{ $langg->lang294 }} {{$order->method}}</p>

                                        @if($order->method=="BKASH" && $order->payment_status=="pending" && $order->status=='pending')
                                        <a href="{{route('bkash.paynow',$order->order_number)}}" class="btn-sm btn-success">Pay now</a>
                                        @endif

                                      
                                    </div>
                                </div>
                            </div>
                            @endif
                            <br>




                            <div class="table-responsive">
                                <h5>{{ $langg->lang308 }}</h5>
                                <table class="table table-bordered veiw-details-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">Sl</th>
                                            <th width="5%">Image</th>
                                            <th width="30%">{{ $langg->lang310 }}</th>
                                            <th width="20%">{{ $langg->lang539 }}</th>
                                            <th>{{ $langg->lang314 }}</th>
                                            <th>{{ $langg->lang315 }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cart->items as $product)
                                        <tr>
                                            <td> {{ $loop->index+1 }}</td>
                                            <td><img width="100" height="80" src="{{asset('assets/images/products/'.$product['item']['photo'])}}"></td>
                                            <td>
                                                <input type="hidden" value="{{ $product['license'] }}">

                                                @if($product['item']['user_id'] != 0)
                                                @php
                                                $user = App\Models\User::find($product['item']['user_id']);
                                                @endphp
                                                @if(isset($user))
                                             
                                                
                                                
                                                <a target="_blank" 
                                                    href="{{ route('front.product', $product['item']['slug']) }}">{{mb_strlen($product['item']['name'],'utf-8') > 30 ? mb_substr($product['item']['name'],0,30,'utf-8').'...' : $product['item']['name']}}</a>
                                                @else
                                                <a target="_blank"
                                                    href="{{ route('front.product', $product['item']['slug']) }}">{{mb_strlen($product['item']['name'],'utf-8') > 30 ? mb_substr($product['item']['name'],0,30,'utf-8').'...' : $product['item']['name']}}</a>
                                                @endif
                                                @else

                                                <a target="_blank"
                                                    href="{{ route('front.product', $product['item']['slug']) }}">{{mb_strlen($product['item']['name'],'utf-8') > 30 ? mb_substr($product['item']['name'],0,30,'utf-8').'...' : $product['item']['name']}}</a>

                                                @endif
                                                @if($product['item']['type'] != 'Physical')
                                                @if($order->payment_status == 'Completed')
                                                @if($product['item']['file'] != null)

                                            
                                                <a href="{{ route('user-order-download',['slug' => $order->order_number , 'id' => $product['item']['id']]) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fa fa-download"></i> {{ $langg->lang316 }}
                                                </a>
                                                @else
                                                <a target="_blank" href="{{ $product['item']['link'] }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fa fa-download"></i> {{ $langg->lang316 }}
                                                </a>
                                                @endif
                                                @if($product['license'] != '')
                                                <a href="javascript:;" data-toggle="modal" data-target="#confirm-delete"
                                                    class="btn btn-sm btn-info product-btn" id="license"><i
                                                        class="fa fa-eye"></i> {{ $langg->lang317 }}</a>
                                                @endif
                                                @endif
                                                @endif
                                            </td>
                                            <td>
                                                <b>{{ $langg->lang311 }}</b>: {{$product['qty']}} <br>
                                                @if(!empty($product['size']))
                                                <b>{{ $langg->lang312 }}</b>: {{ $product['item']['measure'] }}{{str_replace('-',' ',$product['size'])}} <br>
                                                @endif
                                                @if(!empty($product['color']))
                                                <div class="d-flex mt-2">
                                                <b>{{ $langg->lang313 }}</b>:  <span id="color-bar" style="border: 10px solid {{$product['color'] == "" ? "white" : '#'.$product['color']}};"></span>
                                                </div>
                                                @endif

                                                    @if(!empty($product['keys']))

                                                    @foreach( array_combine(explode(',', $product['keys']), explode(',', $product['values']))  as $key => $value)

                                                        <b>{{ ucwords(str_replace('_', ' ', $key))  }} : </b> {{ $value }} <br>
                                                    @endforeach

                                                    @endif

                                                  </td>
                                            <td>{{$order->currency_sign}}{{round($product['item_price']* $order->currency_value,2)}}
                                            </td>
                                            <td>{{$order->currency_sign}}{{round($product['price'] * $order->currency_value,2)}}
                                            </td>

                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="edit-account-info-div">
                                    <div class="form-group">
                                        <a class="back-btn" href="{{ route('user-orders') }}">{{ $langg->lang318 }}</a>
                                        
                                        <a data-toggle="modal" data-target="#vendorform"  href="javascript:;"
                                            class="back-btn">
                                            <i class="fa fa-wrench" aria-hidden="true"></i>Create Issue
                                        </a>

                                        <a href="{{route('user-dmessage-index')}}"
                                            class="back-btn">
                                            <i class="fa fa-list-alt" aria-hidden="true"></i>
                                            Issue List
                                        </a>
                                    </div>

                                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header d-block text-center">
                <h4 class="modal-title d-inline-block">{{ $langg->lang319 }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-center">{{ $langg->lang320 }} <span id="key"></span></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ $langg->lang321 }}</button>
            </div>
        </div>
    </div>
</div>


<div class="message-modal">
    <div class="modal" id="vendorform" tabindex="-1" role="dialog" aria-labelledby="vendorformLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="vendorformLabel">Add Issue</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
          </div>
        <div class="modal-body">
          <div class="container-fluid p-0">
            <div class="row">
              <div class="col-md-12">
                <div class="contact-form">
                  <form id="emailreply1">
                    {{csrf_field()}}
                    <ul>
                      <li>
                        Order:
                        <input type="text" readonly class="input-field" id="order" name="order_number" value="{{$order->order_number}}" placeholder="{{ $langg->lang386 }} *" required="">
                      </li>
  
                      <li>
                        {{-- <input type="text" class="input-field" id="subj1" name="subject" placeholder="{{ $langg->lang387 }} *" required=""> --}}

                        Select an Issue:
                        <select class="input-field"  name="subject" >

                            <option value="Purchase Issue">Purchase Issue</option>
                            <option value="Product Issue">Product Issue</option>
                            <option value="Delivery Issue">Delivery Issue</option>
                            <option value="Payment Issue">Payment Issue</option>
                            <option value="Refund Issue">Refund Issue</option>
                            <option value="Cancel Issue">Cancel Issue</option>
                            <option value="Other Issue">Other Issue</option>

                            
                        </select>
                      </li>
                      <li>
                        Details:
                        <textarea class="input-field textarea" name="message" id="msg1" placeholder="{{ $langg->lang388 }} *" required=""></textarea>
                      </li>
                    </ul>
                      <input type="hidden"  name="type" value="Dispute">
  
                    <button class="submit-btn" id="emlsub1" type="submit">{{ $langg->lang389 }}</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
  

@endsection


@section('scripts')

<script type="text/javascript">
    $('#example').dataTable({
        "ordering": false,
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': false,
        'info': false,
        'autoWidth': false,
        'responsive': true
    });
</script>
<script>
    $(document).on("click", "#tid", function (e) {
        $(this).hide();
        $("#tc").show();
        $("#tin").show();
        $("#tbtn").show();
    });
    $(document).on("click", "#tc", function (e) {
        $(this).hide();
        $("#tid").show();
        $("#tin").hide();
        $("#tbtn").hide();
    });
    $(document).on("submit", "#tform", function (e) {
        var oid = $("#oid").val();
        var tin = $("#tin").val();
        $.ajax({
            type: "GET",
            url: "{{URL::to('user/json/trans')}}",
            data: {
                id: oid,
                tin: tin
            },
            success: function (data) {
                $("#ttn").html(data);
                $("#tin").val("");
                $("#tid").show();
                $("#tin").hide();
                $("#tbtn").hide();
                $("#tc").hide();
            }
        });
        return false;
    });
</script>


<script type="text/javascript">
    
    $(document).on("submit", "#emailreply1" , function(){
    var token = $(this).find('input[name=_token]').val();
    var subject = $(this).find('select[name=subject]').val();
    var message =  $(this).find('textarea[name=message]').val();
    var $type  = $(this).find('input[name=type]').val();
    var order = $('#order').val();
    $('#subj1').prop('disabled', true);
    $('#msg1').prop('disabled', true);
    $('#emlsub1').prop('disabled', true);
$.ajax({
      type: 'post',
      url: "{{URL::to('/user/admin/user/send/message')}}",
      data: {
          '_token': token,
          'subject'   : subject,
          'message'  : message,
          'type'   : $type,
          'order'  : order
            },
      success: function( data) {
    $('#subj1').prop('disabled', false);
    $('#msg1').prop('disabled', false);
    $('#subj1').val('');
    $('#msg1').val('');
  $('#emlsub1').prop('disabled', false);
  if(data == 0)
    toastr.error("{{ $langg->something_wrong }} ");
  else
    toastr.success("Issue Created");
  $('.close').click();
      }

  });          
    return false;
  });

</script>

<script type="text/javascript">
    $(document).on('click', '#license', function (e) {
        var id = $(this).parent().find('input[type=hidden]').val();
        $('#key').html(id);
    });
</script>
@endsection