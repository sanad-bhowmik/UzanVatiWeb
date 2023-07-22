@extends('layouts.vendor')

@section('content')
    <div class="content-area">
        <div class="mr-breadcrumb">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="heading">CREATE FROM LIST <a class="add-btn" href=""><i class="fas fa-arrow-left"></i>
                            {{ $langg->lang550 }}</a></h4>
                    <ul class="links">
                        <li>
                            <a href="{{ route('vendor-dashboard') }}">{{ $langg->lang441 }}</a>
                        </li>
                        <li>
                            <a href="javascript:;">{{ $langg->lang444 }} </a>
                        </li>
                        <li>
                            <a href="{{ route('vendor-prod-index') }}">{{ $langg->lang446 }}</a>
                        </li>
                        <li>
                            <a href="{{route('vendor-prod-add-from-list-campaign')}}">Create from list Campaign</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
            
            <input type="text" placeholder="Search Brand/Product Here......." class="form-control search-box" id="filterProducts" >
            
            </div>
            <div class="col-md-12">
                @php 
                $letters=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
                
                @endphp
                <p>
                @foreach($letters as $letter)
               <span class="search-letter"><a href="{{route('vendor-prod-add-from-list-campaign')}}?letter={{$letter}}">{{$letter}}</a></span>
                @endforeach
                <p>


            </div>
            
        </div>
        <div class="add-product-content">
           
            <div class="row"  id="ajaxContent">
                @foreach($datas as $data)
                <div class="col-lg-3 col-md-3 mt-15">
                <div class="card card-shadow" >
                  
                <img class="card-img-top" alt="NO-IMAGE" src="{{asset('assets/images/thumbnails/'.$data->thumbnail)}}">
                
                <div class="card-body text-center">
                    <p class="card-title">{{ $data->name }}</p>
                    <p class="card-text">BDT {{ $data->price }} TK</p>
                    <button  onclick="confirm('Sure you want to add this?')" data-href="{{route('vendor-prod-store-from-list-campaign',$data->id)}}" class="btn btn-success add-to-shop">Add to Shop</button>
                  </div>
                </div>
                
                </div>
                
                @endforeach
                
                
                </div>






        </div>



    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/admin/js/jquery.Jcrop.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.SimpleCropper.js') }}"></script>
	<script src="{{ asset('assets/admin/js/product.js') }}"></script>

    <script>


        $(document).ready(function() {
        
        $(".add-to-shop").on('click',function(e) {
           
            var link = $(this).attr('data-href');
            console.log(link);
            $.get(link, function(data, status){
           // console.log(data);
             if(data==0){

                $.notify("Product is already in your shop..", "warning");
             }
             else if(data==2){
                $.notify("Faild to add this into shop..", "error");
             }else{
                $.notify("Successfuly added to shop..", "success");

             }
              
         });
          
          e.preventDefault();
          
        });
      


        $("#filterProducts").keyup(function(e) {
          //  console.log("hello");
          e.preventDefault();
          $("#ajaxLoader").show();
          productfilter();
        });
        
    });
        
        function productfilter() {
            let filterlink = '';
        
            if ($("#filterProducts").val() != '') {
              
                filterlink = '{{route('vendor-prod-list-campaign')}}' + '?products='+$("#filterProducts").val();
             
            }else{

                filterlink = '{{route('vendor-prod-list-campaign')}}' + '?products='+$("#filterProducts").val();
            }
        
        
            
            //console.log(filterlink);
           // console.log(encodeURI(filterlink));
            $("#ajaxContent").load(encodeURI(filterlink), function(data) {
              // add query string to pagination
            //  addToPagination();
           //console.log(data);
            $("#ajaxLoader").fadeOut(1000);
            });
          }
        </script>
        





@endsection
