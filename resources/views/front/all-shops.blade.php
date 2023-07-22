@extends('layouts.front')
@section('content')



<section >
  
<div class="container mt-30 text-center">
  <div class="row login-area">

  <div class="col-lg-3">

<div class="form-input">
    <select required  name="division" id="division" >
  @include('includes.divisions')
  </select>
                
                  
              </div>
          </div>
<div class="col-lg-3">

<div class="form-input">
<select required  name="district" id="district" >
    <option value="" >Select Zilla</option>
    </select>
  </div>
</div>
<div class="col-lg-3">

<div class="form-input">
  <select required name="upazila" id="upazila" >
    <option value="" >Select UpZilla</option>
    </select>
  </div>
</div>

<div class="col-lg-3">

<div class="form-input">
<button id="filterShops" class="mybtn1">Filter Shops</a>
  </div>
</div>

</div>
</div>

<div class="my-container">

    <div class="row">
<div class="col-md-12 text-center mt-20">
    <h4>ALL SHOPS</h4>
</div>
    </div>

<div class="row"  id="ajaxContent">
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


</div>
  </div>

  <div class="mt-20"></div>
</section>

@endsection



@section('scripts')
<script>


$(document).ready(function() {

// when price changed & clicked in search button
$("#filterShops").on('click', function(e) {
  e.preventDefault();
  $("#ajaxLoader").show();
  shopfilter();
});
});

function shopfilter() {
    let filterlink = '';

    if ($("#division").val() != '') {
      if (filterlink == '') {
        filterlink += '{{route('front.allShops')}}' + '?division='+$("#division").val();
      } else {
        filterlink += '&division='+$("#division").val();
      }
    }

    if ($("#district").val() != '') {
      if (filterlink == '') {
        filterlink += '{{route('front.allShops')}}' + '?district='+$("#district").val();
      } else {
        filterlink += '&district='+$("#district").val();
      }
    }
    if ($("#upazila").val() != '') {
      if (filterlink == '') {
        filterlink += '{{route('front.allShops')}}' + '?upazila='+$("#upazila").val();
      } else {
        filterlink += '&upazila='+$("#upazila").val();
      }
    }

    
    //console.log(filterlink);
   // console.log(encodeURI(filterlink));
    $("#ajaxContent").load(encodeURI(filterlink), function(data) {
      // add query string to pagination
    //  addToPagination();
   // console.log(data);
      $("#ajaxLoader").fadeOut(1000);
    });
  }
</script>

@endsection