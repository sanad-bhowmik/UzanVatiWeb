@extends('layouts.front-campaign')
@section('content')
<!-- Vendor Area Start -->
<div class="container mt-25">
<div class="vendor-banner" style="background: url({{  $campaign->banner != null ? asset('assets/images/banners/'.$campaign->banner ) : '' }}); background-repeat: no-repeat; background-size: contain;background-position: center;{!! $campaign->banner  != null ? '' : 'background-color:'.$gs->vendor_color !!} ;">
  <div class="container" >
    <div class="row">
      <div class="col-lg-12">
        <div class="content">
          <p class="sub-title">
            {{$campaign->title }} 
          </p>
          <h2 class="title">
            {{$campaign->name}} 
          </h2>
        </div> 
      </div>
    </div>
  </div>
</div>
</div>
<div class="container mt-25">

  @if(count($datas) > 0)


  <div class="categori-item-area">
    {{-- <div id="ajaxContent"> --}}
      <div class="row">

        @foreach($datas as $prod)
          @include('front.campaign.includes.product.product-campaign')
        @endforeach

      </div>
     
    {{-- </div> --}}
  </div>

  @else
    <div class="page-center">
      <h4 class="text-center">{{ $langg->lang60 }}</h4>
    </div>
  @endif


</div>

<div class="mt-25"></div>
@endsection