@extends('layouts.front-campaign')
@section('content')
<div class="container">



    <!-- Tabs navs -->
    <ul class="nav nav-tabs nav-justified mb-3 mt-15 campaign-nav" id="ex1" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link item-link active" data-value="products">Products</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link item-link" data-value="shops">Shops</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link item-link" data-value="brands">Brands</a>
        </li>
    </ul>
    <!-- Tabs navs -->

    <div class="row mt-30">
        <div class="col-md-12">
            <div class="jumbotron jumbotron-fluid" style="
             background-image: url({{asset('assets/images/banners/'.$campaign->banner.'')}});
             background-repeat: no-repeat;
             background-size: 100% 100%;
             background-position: center;
            ">
                <div class="container">
                    {{-- <h1 class="display-4">{{$campaign->title}}</h1> --}}
                    {{-- <p class="lead">{{$campaign->name}}</p> --}}
                    @if($campaign->start_date .' '. $campaign->start_time>= date('Y-m-d H:i:s'))
                    <p class="lead"> Will go live in : <span class=' countdown' value='{{ $campaign->start_date .' '. $campaign->start_time}}'></span>
                    </p>
                    @else
                    <p class="lead" style="text-align:left;">Live Now</p>

                    @endif
                    <hr class="my-4">

                    <!-- @if($campaign->start_date .' '. $campaign->start_time<= date('Y-m-d H:i:s'))
                   
                      <a class="btn btn-warning btn-md" href="{{ route('front.campaign',$campaign->code) }}" >Shop Now</a>
                    
                    @endif -->


                </div>
            </div>
        </div>
    </div>







    <!-- Tabs content -->
    <div class="tab-content mt-15" id="ajax-content">
        <div class="row">

            @foreach($datas as $prod)
            @include('front.campaign.includes.product.product-campaign')
            @endforeach

        </div>


    </div>
    <!-- Tabs content -->

    <div class="mt-25"></div>
</div>
@endsection


@section('scripts')
<script>
    $(document).ready(function() {
        $(".item-link").click(function(e) {
            $(".item-link").removeClass("active");
            e.preventDefault();
            $("#ajaxLoader").show();
            var data_for = $(this).attr('data-value')
            $(this).addClass("active");
            items_show(data_for);

        });

    });

    function items_show(data_for) {
        let filterlink = '';


        filterlink = "{{ route('front.campaign-items', $campaign->code) }}?for=" + data_for;



        //console.log(filterlink);
        // console.log(encodeURI(filterlink));
        $("#ajax-content").load(encodeURI(filterlink), function(data) {
            // add query string to pagination
            //  addToPagination();
            // console.log(data);
            $("#ajaxLoader").fadeOut(1000);
        });
    }
</script>
@endsection