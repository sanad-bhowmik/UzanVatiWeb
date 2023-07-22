@if (count($shops) > 0)
@foreach($shops as $shop)
<div class="col-md-2 mt-15">
<div class="card card-shadow" style="width: 12rem;">
  
<img class="card-img-top shop-banner" src="{{ $shop->shop_image !='' ? asset('assets/images/vendorbanner/'.$shop->shop_image) : asset('assets/images/vendorbanner/default.jpg') }}" alt="{{$shop->shop_name}}">

<div class="card-body text-center">
    <h5 class="card-title">{{$shop->shop_name}}</h5>
    <p class="card-text">{{$shop->shop_address}}</p>
    <a href="{{ route('front.vendor',str_replace(' ', '_, $shop->shop_name)) }}" class="mybtn1">Visit Shop</a>
  </div>
</div>

</div>

@endforeach
@else
<div class="col-md-12 mt35">

<h4 style="text-align: center;" >NO SHOP FOUND</h4></div>

@endif
