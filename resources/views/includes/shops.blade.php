






<div class="row">
<div class="col-md-12 text-center mt-20">
    <h4>TOP SHOPS</h4>
</div>

@foreach($shops as $shop)
<div class="col-lg-2 col-md-6 mt-15">
<div class="card card-shadow" >
  
<img class="card-img-top shop-banner" src="{{ $shop->shop_image !='' ? asset('assets/images/vendorbanner/'.$shop->shop_image) : asset('assets/images/vendorbanner/default.jpg') }}" alt="{{$shop->shop_name}}">

<div class="card-body text-center">
    <h5 class="card-title">{{$shop->shop_name}}</h5>
    <p class="card-text">{{$shop->shop_address}}</p>
    <a href="{{ route('front.vendor',str_replace(' ', '_', $shop->shop_name)) }}" class="mybtn2">Visit Shop</a>
  </div>
</div>

</div>

@endforeach


<div class="col-md-12 text-center mt-20">
<a href="{{ route('front.allShops') }}" class="mybtn1">View All Shops</a>
</div>


</div>