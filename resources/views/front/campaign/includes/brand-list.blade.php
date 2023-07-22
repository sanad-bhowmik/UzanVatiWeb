
<div class="row">
@forelse($datas as $shop)
<div class="col-lg-2 col-md-2 col-sm-6 col-6 remove-padding mt-15">
<div class="card card-shadow" >
  
<img class="card-img-top shop-banner" src="{{ $shop->photo !='' ? asset('assets/images/partner/'.$shop->photo) : asset('assets/images/vendorbanner/default.jpg') }}" alt="{{$shop->brand_name}}">

<div class="card-body text-center">
    <h5 class="card-title">{{$shop->brand_name}}</h5>

    <a href="{{ route('front.brand-products',[$shop->brand_code,$campaign->code]) }}" class="mybtn2">Show</a>
  </div>
</div>

</div>
@empty
<h5>No Brand Found</h5>
@endforelse
</div>