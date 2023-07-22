@extends('layouts.front')
@section('content')
<div class="container">
    @forelse($campaigns as $campaign)
      
    
            <div class="row mt-30">
                <div class="col-md-12">
                <div class="jumbotron jumbotron-fluid"
                
                style="
                 background-image: url({{asset('assets/images/banners/'.$campaign->banner.'')}});
                 background-repeat: no-repeat;
                 background-size: 100% 100%;
                 background-position: center;" >
                    <div class="container">
                        {{-- <h1 class="display-4">{{$campaign->title}}</h1> --}}
                        {{-- <p class="lead">{{$campaign->name}}</p> --}}
                    @if($campaign->start_date .' '. $campaign->start_time>= date('Y-m-d H:i:s'))  
                    <p class="lead"> Will go live in : <span class='countdown' value='{{ $campaign->start_date .' '. $campaign->start_time}}'></span>
                    </p> 
                    @else
                    <p class="lead" style="text-align:left;">Live Now</p>

                    @endif
                        <hr class="my-4">

                        @if($campaign->start_date .' '. $campaign->start_time<= date('Y-m-d H:i:s'))
                       
                          <a class="btn btn-warning btn-md" href="{{ route('front.campaign',$campaign->code) }}" >Shop Now</a>
                      
                        @endif


                    </div>
                  </div>
                </div>
            </div>


    @empty
    <div class="row mt-30">
        <div class="col-md-12">
        <div class="jumbotron jumbotron-fluid"
        
        style="
         background-image: linear-gradient(to right, red , yellow);
         background-repeat: no-repeat;
         background-size: 100% 100%;
         background-position: center;
        " >
            <div class="container">
        
                <h1 class="display-4">Coming Soon....</h1>
                <p class="lead">Stay Tuned.....</p>
            </div>
          </div>
        </div>
    </div>
        
   
        
     @endforelse



   
</div>



@endsection


@section('scripts')
    <script>
        $(function(){
	$('.countdown').each(function(){
		$(this).countdown($(this).attr('value'), function(event) {
    	$(this).text(
      	event.strftime('%D days %H:%M:%S')
      );
		});
	});
});
    </script>
@endsection
