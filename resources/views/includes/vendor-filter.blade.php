					<div class="row">
						<div class="col-md-3">
							<h4 class="vendor-shop-name">{{ $vendor->shop_name }}</h4>
						</div>
						<div class="col-md-5">

							<form class="search-form-das" action="">

								<input style="width:85%;" type="text" class="form-control search-box" name="product_model" id="product_model" placeholder="Search any available product in this shop.." autofocus autocomplete="off" required>
								<!--  <input type="submit" value="search"> -->
								<input  type="hidden"  class="shop_id" name="shop_id" id="shop_id_search" value="{{ $vendor->id }}">
								<div class="search-result" id="show-list">
									<ul></ul>

								</div>


							</form>
						</div>
						<div class="col-md-4">
							<div class="item-filter">
								<ul class="filter-list">
									<li class="item-short-area">
										<p>{{$langg->lang64}} :</p>
										<form id="sortForm" class="d-inline-block" action="{{ route('front.vendor', Request::route('category')) }}" method="get">
											@if (!empty(request()->input('min')))
											<input type="hidden" name="min" value="{{ request()->input('min') }}">
											@endif
											@if (!empty(request()->input('max')))
											<input type="hidden" name="max" value="{{ request()->input('max') }}">
											@endif
											<select name="sort" class="form-control short-item" onchange="document.getElementById('sortForm').submit()">
												<option value="date_desc" {{ request()->input('sort') == 'date_desc' ? 'selected' : '' }}>{{$langg->lang65}}</option>
												<option value="date_asc" {{ request()->input('sort') == 'date_asc' ? 'selected' : '' }}>{{$langg->lang66}}</option>
												<option value="price_asc" {{ request()->input('sort') == 'price_asc' ? 'selected' : '' }}>{{$langg->lang67}}</option>
												<option value="price_desc" {{ request()->input('sort') == 'price_desc' ? 'selected' : '' }}>{{$langg->lang68}}</option>
											</select>
										</form>
									</li>
								</ul>
							</div>
						</div>


					</div>