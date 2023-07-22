

  @if($campaign->start_date .' '. $campaign->start_time<= date('Y-m-d H:i:s'))
                       
         <a href="{{ route('front.campaign',$campaign->code) }}" >
						
			<div class="card banner-effect" >
				<img class="card-img-top" src="{{asset('assets/images/banners/'.$campaign->banner.'')}}" alt="{{$campaign->title}}">
				<p class="lead2" style="text-align:left;">Live Now</p>

			  </div>		
						
						
			</a>
	@else
                      
	<div class="card banner-effect" >
		<img class="card-img-top" src="{{asset('assets/images/banners/'.$campaign->banner.'')}}" alt="{{$campaign->title}}">
		
		
			@if($campaign->start_date .' '. $campaign->start_time>= date('Y-m-d H:i:s'))  
                    <p class="lead2"> Will go live in : <span class='countdown' value='{{ $campaign->start_date .' '. $campaign->start_time}}'></span>
                    </p> 
       		 @endif
		 
		
		
		
	  </div>               
			
			
	@endif