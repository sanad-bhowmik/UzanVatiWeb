@extends('layouts.front-campaign')
@section('content')
    <!-- Vendor Area Start -->
    <div class="container">
        <div class="row">

            <div class="col-md-6">
                <div class="vendor-banner"
                    style="background: url({{ $campaign->banner != null ? asset('assets/images/banners/' . $campaign->banner) : '' }});
                     background-repeat: no-repeat;
                 background-size: 100% 100%;
                 background-position: center;"
                      {!! $campaign->banner != null ? '' : 'background-color:' . $gs->vendor_color !!} ;">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="content">
                                <p class="sub-title">
                                    {{ $campaign->title }}
                                </p>
                                <h2 class="title">
                                    {{ $campaign->name }}
                                </h2>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <div class="col-md-6">
                <div class="vendor-banner"
                    style="background: url({{ $vendor->shop_image != null ? asset('assets/images/vendorbanner/' . $vendor->shop_image) : '' }});
                   background-repeat: no-repeat;
                 background-size: 100% 100%;
                 background-position: center;"
                     {!! $vendor->shop_image != null ? '' : 'background-color:' . $gs->vendor_color !!} ;">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="content">
                                <p class="sub-title">
                                    {{ $vendor->shop_title }}
                                </p>
                                <h2 class="title">
                                    {{ $vendor->shop_name }}
                                </h2>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


        </div>

    </div>

    <div class="container mt-25">

        @if (count($vprods) > 0)
            <div class="categori-item-area">
                {{-- <div id="ajaxContent"> --}}
                <div class="row">

                    @foreach ($vprods as $prod)
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
