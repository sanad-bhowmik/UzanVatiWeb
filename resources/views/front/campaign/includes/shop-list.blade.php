<div class="row">
@forelse($datas as $shop)
<div class="col-lg-2 col-md-2 col-sm-6 col-6 remove-padding mt-15">
<div class="card card-shadow" >
  
<img class="card-img-top shop-banner" src="{{ $shop->shop_image !='' ? asset('assets/images/vendorbanner/'.$shop->shop_image) : asset('assets/images/vendorbanner/default.jpg') }}" alt="{{$shop->shop_name}}">

<div class="card-body text-center">
    <h5 class="card-title">{{$shop->shop_name}}</h5>
    <p class="card-text">{{$shop->shop_address}}</p>
    <a href="{{ route('front.vendor-campaign',[str_replace(' ', '_', $shop->shop_name),$campaign->code]) }}" class="mybtn2">Visit Shop</a>
  </div>
</div>

</div>
@empty
<h5>No Shop found</h5>

@endforelse
</div>