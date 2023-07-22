@if(isset($order))
<div class="tracking-steps-area">
@php
 array_push($datas,'Confirmed','Declined','Completed') ;
$line ="";
if(ucwords($order->status)=='Completed'){
$line="green-line";
}
else if(ucwords($order->status)=='Declined'){
$line="red-line";
}



@endphp
        <ul class="tracking-steps">
            @foreach($order->tracks as $track)
                <li class="{{ in_array(ucfirst($track->title), $datas) ? 'active' : '' }}   {{$line}}">
                    <div class="icon {{strtolower($track->title)}} ">{{ $loop->index + 1 }}</div>
                    <div class="content">
                            <h4 class="title">
                                @if (ucwords($track->title) == 'Confirmed')
                                                        {{ __('Picked') }}
                                                    @elseif(ucwords($track->title) == 'Declined')
                                                        {{ __('Canceled') }}
                                                        @elseif(ucwords($track->title) == 'Completed')
                                                        {{ __('Delivered') }}
                                                    @else
                                                        {{ ucwords($track->title) }}
                                                    @endif

                            
                            </h4>
                            <p class="date">{{ date('d/m/Y h:i:s A', strtotime($order->created_at)) }}</p>
                            <p class="details">{{ $track->text }}</p>
                    </div>
                </li>
            @endforeach

            </ul>
</div>


    @else 
    <h3 class="text-center">{{ $langg->lang775 }}</h3>
    @endif          