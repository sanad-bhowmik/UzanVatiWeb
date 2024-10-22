@php
$line ="";
if(ucwords($order->status)=='Completed'){
$line="green-line";
}
else if(ucwords($order->status)=='Declined'){
$line="red-line";
}



@endphp

@if($order->status == 'pending')



<ul class="process-steps">
    <li class="active activePending  {{$line}}">
        <div class="icon">1</div>
        <div class="title">Pending
            <br><span class="process-text"> {{


                                                        !empty($order->tracks->where('title','Pending')->first()->text)  ? $order->tracks->where('title','Pending')->first()->text : 'Picked'
                                                         
                                                         
                                                         }} </span>

            <br><span class="process-date"> ({{

                                                        !empty($order->tracks->where('title','Pending')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Pending')->first()->created_at)) : 'n/a'
                                                         
                                                         
                                                         }}) </span>


        </div>
    </li>

    <li class="">
        <div class="icon">2</div>
        <div class="title">Confirmed</div>
    </li>
    <li class="">
        <div class="icon">3</div>
        <div class="title">Processing</div>
    </li>
    <li class="">
        <div class="icon">4</div>
        <div class="title">Picked</div>
    </li>
    <li class="">
        <div class="icon">5</div>
        <div class="title">Shipped</div>
    </li>
    <li class="">
        <div class="icon">6</div>
        <div class="title">Delivered</div>
    </li>
</ul>
@elseif($order->status == 'declined' )

<ul class="process-steps">
    <li class="active activePending {{$line}}">
        <div class="icon">1</div>
        <div class="title">Pending
            <br><span class="process-text"> {{


                !empty($order->tracks->where('title','Pending')->first()->text)  ? $order->tracks->where('title','Pending')->first()->text : 'Picked'
                 
                 
                 }} </span>

            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Pending')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Pending')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>

        </div>
    </li>
    <li class="active activeCancel {{$line}}">
        <div class="icon"></div>

    </li>
    <li class="active activeCancel {{$line}}">
        <div class="icon"></div>

    </li>
    <li class="active activeCancel {{$line}}">
        <div class="icon"></div>

    </li>
    <li class="active activeCancel {{$line}}">
        <div class="icon">2</div>
        <div class="title">Canceled
            <br><span class="process-text"> {{


                !empty($order->tracks->where('title','Declined')->first()->text)  ? $order->tracks->where('title','Declined')->first()->text : 'n/a'
                 
                 
                 }} </span>

            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Declined')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Declined')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>






        </div>
    </li>
</ul>

@elseif($order->status == 'processing' )

<ul class="process-steps">
    <li class="active activePending {{$line}}">
        <div class="icon">1</div>
        <div class="title">Pending
            <br><span class="process-text"> {{


                !empty($order->tracks->where('title','Pending')->first()->text)  ? $order->tracks->where('title','Pending')->first()->text : 'Picked'
                 
                 
                 }} </span>

            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Pending')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Pending')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>





        </div>
    </li>
    <li class="active activeConfirmed {{$line}}">
        <div class="icon">2</div>
        <div class="title">Confirmed
            <br><span class="process-text"> Your order confirmed successfull. </span>

            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Confirmed')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Confirmed')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>





        </div>
    </li>
    <li class="active activeProcessing {{$line}}">
        <div class="icon">3</div>
        <div class="title">Processing
            <br><span class="process-text"> Your order is Processing.</span>

            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Processing')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Processing')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>

        </div>
    </li>
    <li class="">
        <div class="icon">4</div>
        <div class="title">Picked

        </div>
    </li>
    <li class="">
        <div class="icon">5</div>
        <div class="title">Shipped</div>
    </li>
    <li class="">
        <div class="icon">6</div>
        <div class="title">Delivered</div>
    </li>
</ul>

@elseif($order->status == 'confirmed' )

<ul class="process-steps">
    <li class="active activePending {{$line}}">
        <div class="icon">1</div>
        <div class="title">Pending
            <br><span class="process-text"> {{


                !empty($order->tracks->where('title','Pending')->first()->text)  ? $order->tracks->where('title','Pending')->first()->text : 'n/a'
                 
                 
                 }} </span>


            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Pending')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Pending')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>




        </div>
    </li>
    <li class="active activeConfirmed {{$line}}">
        <div class="icon">2</div>
        <div class="title">Confirmed

            <br><span class="process-text"> Your order confirmed successfull.</span>

            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Confirmed')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Confirmed')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>



        </div>
    </li>
    <li>
        <div class="icon">3</div>
        <div class="title">Processing</div>
    </li>
    <li class="">
        <div class="icon">4</div>
        <div class="title">Picked</div>
    </li>
    <li class="">
        <div class="icon">5</div>
        <div class="title">Shipped</div>
    </li>
    <li class="">
        <div class="icon">6</div>
        <div class="title">Delivered</div>
    </li>
</ul>

<!-- update -->

@elseif($order->status == 'picked' )

<ul class="process-steps">
    <li class="active activePending {{$line}}">
        <div class="icon">1</div>
        <div class="title">Pending
            <br><span class="process-text"> {{


                !empty($order->tracks->where('title','Pending')->first()->text)  ? $order->tracks->where('title','Pending')->first()->text : 'n/a'
                 
                 
                 }} </span>


            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Pending')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Pending')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>




        </div>
    </li>
    <li class="active activeConfirmed {{$line}}">
        <div class="icon">2</div>
        <div class="title">Confirmed

            <br><span class="process-text"> Your order confirmed successfull.</span>

            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Confirmed')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Confirmed')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>



        </div>
    </li>
    <li class="active activeProcessing {{$line}}">
        <div class="icon">3</div>
        <div class="title">Processing

            <br><span class="process-text"> Your order is Processing.</span>

            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Confirmed')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Confirmed')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>

        </div>
    </li>
    <li class="active activePicked {{$line}}">
        <div class="icon">4</div>
        <div class="title">Picked
            <br><span class="process-text"> Your order has been picked.</span>
        </div>
    </li>
    <li class="">
        <div class="icon">5</div>
        <div class="title">Shipped</div>
    </li>
    <li class="">
        <div class="icon">6</div>
        <div class="title">Delivered</div>
    </li>
</ul>


<!-- undate -->


@elseif($order->status == 'on delivery' || $order->status == 'shipped')


<ul class="process-steps">
    <li class="active activePending {{$line}}">
        <div class="icon">1</div>
        <div class="title">Pending
            <br><span class="process-text"> {{


                !empty($order->tracks->where('title','Pending')->first()->text)  ? $order->tracks->where('title','Pending')->first()->text : ''
                 
                 
                 }} </span>
            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Pending')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Pending')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>



        </div>
    </li>
    <li class="active activeConfirmed {{$line}}">
        <div class="icon">2</div>
        <div class="title">Confirmed
            <br><span class="process-text"> Your order confirmed successfull.</span>
            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Confirmed')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Confirmed')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>


        </div>
    </li>
    <li class="active activeProcessing {{$line}}">
        <div class="icon">3</div>
        <div class="title">Processing

            <br><span class="process-text"> Your order is Processing.</span>

            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Confirmed')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Confirmed')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>



        </div>
    </li>
    <li class="active activePicked {{$line}}">
        <div class="icon">4</div>
        <div class="title">Picked
            <br><span class="process-text"> Your order has been picked.</span>


            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Picked')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Picked')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>






        </div>
    </li>
    <li class="active activeShipped {{$line}}">
        <div class="icon">5</div>
        <div class="title">Shipped
            <br><span class="process-text"> Your order has been Shipped.

                <br><span class="process-date"> ({{


!empty($order->tracks->where('title','Shipped')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Shipped')->first()->created_at)) : 'n/a'
 
 
 }}) </span>

        </div>

    </li>
    <li class="">
        <div class="icon">6</div>
        <div class="title">Delivered</div>
    </li>
</ul>


@elseif($order->status == 'completed' || $order->status == 'delivered')

<ul class="process-steps">
    <li class="active activePending {{$line}}">
        <div class="icon">1</div>
        <div class="title">Pending
            <br><span class="process-text"> {{


                !empty($order->tracks->where('title','Pending')->first()->text)  ? $order->tracks->where('title','Pending')->first()->text : 'Picked'
                 
                 
                 }} </span>

            <br><span class="process-date"> ({{


            !empty($order->tracks->where('title','Pending')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Pending')->first()->created_at)) : 'n/a'
             
             
             }}) </span>

        </div>
    </li>
    <li class="active activeConfirmed {{$line}}">
        <div class="icon">2</div>
        <div class="title">Confirmed


            <br><span class="process-text">Your order confirmed successfull. </span>

            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Confirmed')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Confirmed')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>



        </div>
    </li>
    <li class="active activePicked {{$line}}">
        <div class="icon">3</div>
        <div class="title">Processing

            <br><span class="process-text"> Your order is Processing.</span>

            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Confirmed')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Confirmed')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>








        </div>
    </li>
    <li class="active activeShipped {{$line}}">
        <div class="icon">4</div>
        <div class="title">Picked

            <br><span class="process-text"> Your order has been picked.</span>


            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Picked')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Picked')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>






        </div>
    </li>
    <li class="active activeShipped {{$line}}">
        <div class="icon">5</div>
        <div class="title">Shipped

            <br><span class="process-text"> Your order has been Shipped. </span>


            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Completed')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Completed')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>

        </div>
    </li>

    <li class="active activeDelivered {{$line}}">
        <div class="icon">6</div>
        <div class="title">Delivered

            <br><span class="process-text">Order has been delivered</span>


            <br><span class="process-date"> ({{


                !empty($order->tracks->where('title','Completed')->first()->created_at)  ? date('d/m/Y h:i:s A', strtotime($order->tracks->where('title','Completed')->first()->created_at)) : 'n/a'
                 
                 
                 }}) </span>

        </div>
    </li>
</ul>


@endif