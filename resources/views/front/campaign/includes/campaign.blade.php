<div class="row">
    <div class="col-md-12">
    <div class="jumbotron jumbotron-fluid"
    
    style="
     background-image: url({{asset('assets/images/banners/'.$campaign->banner.'')}});
     background-repeat: no-repeat;
     background-size: 100% 100%;
     background-position: center;
    " >
        <div class="container">
            <h1 class="display-4">{{$campaign->title}}</h1>
            <p class="lead">{{$campaign->name}}</p>
            <hr class="my-4">
            <p class="demo"></p>
            <p class="lead">
              <a class="btn btn-warning btn-lg" href="#" role="button">Live Now</a>
            </p>
        </div>
      </div>
    </div>
</div>


<script>
    // Set the date we're counting down to
    var countDownDate = new Date("Jan 5, 2024 15:37:25").getTime();
    
    // Update the count down every 1 second
    var x = setInterval(function() {
    
      // Get today's date and time
      var now = new Date().getTime();
        
      // Find the distance between now and the count down date
      var distance = countDownDate - now;
        
      // Time calculations for days, hours, minutes and seconds
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
      // Output the result in an element with id="demo"
      document.getElementByClass("demo").innerHTML = days + "d " + hours + "h "
      + minutes + "m " + seconds + "s ";
        
      // If the count down is over, write some text 
      if (distance < 0) {
        clearInterval(x);
        document.getElementByClass("demo").innerHTML = "EXPIRED";
      }
    }, 1000);
    </script>