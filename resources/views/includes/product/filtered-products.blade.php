	@if (count($prods) > 0)
			@foreach ($prods as $key => $prod)
			<div class="col-lg-3 col-md-3 col-6 remove-padding">

				<a href="{{ route('front.product', $prod->slug) }}" class="item">
					<div class="item-img">

					<div class="extra-list">
					
					<ul>
								
							<li>
							<span  href="javascript:;" class="add-to-cart-quick add-to-cart-btn" data-href="{{ route('product.cart.quickadd',$prod->id) }}" data-toggle="tooltip" data-placement="top" title="{{ $langg->lang251 }}">
							<i class="icofont-cart"></i> 
							</span>
							</li>

							<li>
									@if(Auth::guard('web')->check())

									<span href="javascript:;" class="add-to-wish" data-href="{{ route('user-wishlist-add',$prod->id) }}" data-toggle="tooltip" data-placement="top" title="{{ $langg->lang54 }}"><i class="icofont-heart-alt"></i>
									</span>

									@else

									<span href="javascript:;" rel-toggle="tooltip" title="{{ $langg->lang54 }}" data-toggle="modal" id="wish-btn" data-target="#comment-log-reg" data-placement="top">
										<i class="icofont-heart-alt"></i>
									</span>

									@endif
								</li>
								<li>
									<span class="quick-view" rel-toggle="tooltip" title="{{ $langg->lang55 }}" href="javascript:;" data-href="{{ route('product.quick',$prod->id) }}" data-toggle="modal" data-target="#quickview" data-placement="top">
									<i class="icofont-eye-alt"></i>
									</span>
								</li>

								<li>
									<span href="javascript:;" class="add-to-compare" data-href="{{ route('product.compare.add',$prod->id) }}" data-toggle="tooltip" data-placement="top" title="{{ $langg->lang57 }}">
										<i class="icofont-exchange"></i>
									</span>
								</li>

								
									
					
								
					</ul>

					</div>

					@if (!empty($prod->showDiscountPercent()) && $prod->showDiscountPercent() > 0)
					<div class="sell-area">
	
						<span class="sale" style="background-color:red">{{ $prod->showDiscountPercent() }}</span>
					</div>
				@endif

						@if(!empty($prod->features))
						<div class="sell-area">
							@foreach($prod->features as $key => $data1)
							<span class="sale" style="background-color:{{ $prod->colors[$key] }}">{{ $prod->features[$key] }}</span>
							@endforeach
						</div>
						@endif
						<img class="img-fluid" src="{{ $prod->photo ? asset('assets/images/thumbnails/'.$prod->thumbnail):asset('assets/images/noimage.png') }}" alt="">
					</div>
					<div class="info">

						@if ( !empty($prod->stock) && $prod->stock == 0)
						<h4 class="price" style="background-color:red; color:white;">STOCK OUT </h4>
					@endif


						<h5 class="name">{{ substr($prod->showName(), 0, 40) }}</h5>
						<h4 class="price">{{ $prod->setCurrency() }} <del><small>{{ $prod->showPreviousPrice() }}</small></del></h4>
						 <div class="stars">
							<div class="ratings">
								<div class="empty-stars"></div>
								<div class="full-stars" style="width:{{App\Models\Rating::ratings($prod->id)}}%"></div>
							</div>
						</div> 


						<div class="row">
							<div class="col-md-12">
							<span  href="javascript:;" class="add-to-cart-quick add-to-cart-btn" data-href="{{ route('product.cart.quickadd',$prod->id) }}" data-toggle="tooltip" data-placement="top" title="{{ $langg->lang251 }}">
							<i class="icofont-cart"></i> Buy Now
							</span>
					

						</div>
							<div class="col-md-12">
							<span   class="add-to-cart-btn add-to-cart" data-href="{{ route('product.cart.add',$prod->id) }}" data-toggle="tooltip" data-placement="top" title="{{ $langg->lang251 }}">
					<i class="icofont-cart"></i> Add To Cart
					</span>
							</div>

						</div>
						
					
						
						<!-- <div class="item-cart-area">

					


							<ul class="item-cart-options">


								 <li>
									@if(Auth::guard('web')->check())

									<span href="javascript:;" class="add-to-wish" data-href="{{ route('user-wishlist-add',$prod->id) }}" data-toggle="tooltip" data-placement="top" title="{{ $langg->lang54 }}"><i class="icofont-heart-alt"></i>
									</span>

									@else

									<span href="javascript:;" rel-toggle="tooltip" title="{{ $langg->lang54 }}" data-toggle="modal" id="wish-btn" data-target="#comment-log-reg" data-placement="top">
										<i class="icofont-heart-alt"></i>
									</span>

									@endif
								</li>
								<li>
									<span class="quick-view" rel-toggle="tooltip" title="{{ $langg->lang55 }}" href="javascript:;" data-href="{{ route('product.quick',$prod->id) }}" data-toggle="modal" data-target="#quickview" data-placement="top">
										<i class="fas fa-shopping-basket"></i>
									</span>
								</li>

								<li>
									<span href="javascript:;" class="add-to-compare" data-href="{{ route('product.compare.add',$prod->id) }}" data-toggle="tooltip" data-placement="top" title="{{ $langg->lang57 }}">
										<i class="icofont-exchange"></i>
									</span>
								</li>

								
									
 

								
							</ul>
						</div> -->
					</div>
				</a>

			</div>
			@endforeach






			<div class="col-lg-12">
				<div class="page-center mt-5">
					{!! $prods->appends(['search' => request()->input('search')])->links() !!}
				</div>
			</div>
			@else
			<div class="col-lg-12">
				<div class="page-center">
					<h4 class="text-center">{{ $langg->lang60 }}</h4>
				</div>
			</div>
			@endif


			@if(isset($ajax_check))


			<script type="text/javascript">
				// Tooltip Section


				$('[data-toggle="tooltip"]').tooltip({});
				$('[data-toggle="tooltip"]').on('click', function() {
					$(this).tooltip('hide');
				});




				$('[rel-toggle="tooltip"]').tooltip();

				$('[rel-toggle="tooltip"]').on('click', function() {
					$(this).tooltip('hide');
				});


				// Tooltip Section Ends
			</script>

			@endif