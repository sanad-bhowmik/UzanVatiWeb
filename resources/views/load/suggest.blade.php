@foreach($prods as $prod)
	<div class="docname">
		
		<a href="{{ $prod->campaign_product ==0 ? route('front.product', $prod->slug)  : route('front.product-campaign', $prod->slug)  }}">
			<img src="{{ asset('assets/images/thumbnails/'.$prod->thumbnail) }}" alt="">
			<div class="search-content">
				<p>{!! mb_strlen($prod->name,'utf-8') > 66 ? str_replace($slug,'<b>'.$slug.'</b>',mb_substr($prod->name,0,66,'utf-8')).'...' : str_replace($slug,'<b>'.$slug.'</b>',$prod->name)  !!} </p>
				<span style="font-size: 14px; font-weight:600; display:block;">
					BDT {{ $prod->showPrice() }}
					<del style="color:red;" > {{ ''.$prod->showPreviousPrice() }} </del> 
					 {{ $prod->showDiscountPercent() }}

					 @if ( isset($prod->stock) && $prod->stock == 0)
					 <span  style="background-color:red; color:white;">STOCK OUT </span>
				 	@endif
				 </span>
			</div>
		</a>
	</div> 
@endforeach