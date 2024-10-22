@extends('layouts.front')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<style>
    #newpass,
    #confirmpass {
        background-color: #f3f8fc;
        height: 50px;
        width: 100%;
        padding: 0px 30px 0px 65px;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    #newpass {
        margin-bottom: 2px;
    }
</style>
<div class="breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="pages">
                    <li>
                        <a href="{{ route('front.index') }}">
                            {{ $langg->lang17 }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user-forgot') }}">
                            {{ $langg->lang190 }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<section class="login-signup forgot-password">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="login-area">
                    <div class="header-area forgot-passwor-area">
                        <h4 class="title">{{ $langg->lang191 }} </h4>
                        <p class="text">Please write your phone </p>
                    </div>
                    <div class="login-form">
                        @include('includes.admin.form-login')
                        <form id="forgotform" action="{{route('user-forgot-submit')}}" method="POST">
                            {{ csrf_field() }}

                            <div class="form-input">
                                <input id="phone" type="text" autocomplete="off" class="User Name" name="phone" placeholder="Please enter your number" required="">
                                <i class="icofont-phone">+88 &nbsp;</i>
                            </div>
                            <div id="otp-check" class="display-none">

                                <button id="btnCounter" class="otp-send"><span id="count">Send Otp</span></button>
                                <hr>
                                <div class="form-input">
                                    <input id="otp" type="number" autocomplete="off" disabled class="User Name" placeholder="Please enter Otp" required="">
                                    <i class="icofont-qr-code"></i>
                                </div>

                                <button id="btnVerify" class="otp-send display-none">Verify</button>
                            </div>


                            <div id="otp-confirm" class="display-none">
                                <div class="to-login-page">
                                    <a href="{{ route('user.login') }}">
                                        {{ $langg->lang194 }}
                                    </a>
                                </div>
                                <input class="authdata" type="hidden" value="{{ $langg->lang195 }}">
                                <button id="submit-data" type="submit" class="submit-btn">Reset Now</button>
                            </div>

                            <div id="new-pass" class="display-none">
                                <div class="form-input">
                                    <input id="newpass" class="authdata " type="" value="" placeholder="New password" required>
                                    <i class="icofont-lock"></i>
                                </div>
                                <div class="form-input">
                                    <input id="confirmpass" class="authdata" type="" value="" placeholder="Confirm password" required>
                                    <i class="icofont-ssl-security"></i>
                                </div>
                                <button id="submit" type="submit" class="submit-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection


@section('scripts')

<script>
    $(document).ready(function() {

        // 01831370709
        // password varification
        // $("#submit").on("click", function(event) {
        //     event.preventDefault();

        //     var newPassword = $("#newpass").val();
        //     var confirmPassword = $("#confirmpass").val();

        //     if (newPassword === confirmPassword) {
        //         if (newPassword.length >= 6 && confirmPassword.length >= 6) {
        //         } else {
        //             toastr["error"]("Passwords must be at least 6 characters long.");
        //         }
        //     } else {
        //         toastr["error"]("New Password and Confirm Password do not match.");
        //     }
        // });
        $("#submit").on("click", function(event) {
            event.preventDefault();


            var phone = $("#phone").val();
            var newPassword = $("#newpass").val();
            var confirmPassword = $("#confirmpass").val();

            if (newPassword === confirmPassword && newPassword.length >= 6) {
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    type: 'POST',
                    url: '{{ route('user-forgot-submit') }}',
                    // url: '{{ route('user-forgot-submit') }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        password: newPassword,
                        phone: phone
                    },
                    success: function(data) {
                        toastr["success"](data.message);
                        $("#newpass").val('');
                        $("#confirmpass").val('');

                    },
                    error: function(xhr, status, error) {
                        toastr["error"]("Error updating password");
                    }
                });
            } else if (newPassword.length < 6) {
                toastr["error"]("Password must be at least 6 characters long");
            } else {
                toastr["error"]("Passwords do not match");
            }
        });

        // password varification

        $('#phone').on('keyup', function() {

            var val = $(this).val();
            // console.log(val)

            if (val.length == 11) {

                $('#otp-check').removeClass('display-none');

                //console.log(val);

            } else {
                $('#otp-check').addClass('display-none');
            }




        });


        var spn = document.getElementById("count");
        var btn = document.getElementById("btnCounter");



        $("#btnCounter").on("click", function() {


            $("#phone").prop("readonly", true);
            $("#otp").prop("disabled", false);
            $("#submit-data").prop("disabled", true);
            $('#btnVerify').removeClass("display-none");

            disableOtpBtn(300);


            toastr.options = {

                "positionClass": "toast-top-full-width",

            }


        }); //end btn counter

        $("#btnVerify").on("click", function() {

            //console.log("hello");
            event.preventDefault();
            verifyOtp();


        }); //end btn otp verify

        $("#submit-data").on("click", function(event) {
            event.preventDefault();
            // Hide the #otp-confirm elements
            $('#otp-confirm').addClass('display-none');
            // Show the #new-pass elements
            $('#new-pass').removeClass('display-none');
        });


        function disableOtpBtn(time) {

            btn.setAttribute("disabled", true);
            var count = time; // Set count
            var timer = null; // For referencing the timer
            btn.style.backgroundColor = "red";

            sendOtp();

            (function countDown() {
                // Display counter and start counting down
                spn.textContent = "Wait " + count + " sec before sending another otp!!";

                // Run the function again every second if the count is not zero
                if (count !== 0) {
                    timer = setTimeout(countDown, 1000);
                    count--; // decrease the timer
                } else {
                    // Enable the button
                    btn.removeAttribute("disabled");
                    spn.textContent = "Resend Otp";
                    btn.style.backgroundColor = "#047204";
                }
            }());

        } // end disable otp




        function sendOtp() {

            var number = $('#phone').val();

            //console.log(mainurl);

            $.ajax({
                type: 'GET',
                data: {
                    phone: number
                },
                url: mainurl + '/user/otp',
                error: function(xhr, status, error) {

                    var err = eval('(' + xhr.responseText + ')');
                    alert(err.Message);

                    // console.log(JSON.parse(xhr.responseText))
                },
                success: function(data) {

                    var res = data.data;
                    console.log(data);
                    if (res == true) {
                        toastr["success"]("Otp Sent !! Please Check Inbox !!")
                    } else {
                        toastr["warning"]("Too Many OTP Reqest for Today");
                    }

                }

            });


        } //end send otp
        function verifyOtp() {

            var number = $('#otp').val();

            //console.log(mainurl);

            $.ajax({
                type: 'GET',
                data: {
                    otp: number
                },
                url: mainurl + '/user/otpVerify',
                error: function(xhr, status, error) {

                    var err = eval('(' + xhr.responseText + ')');
                    alert(err.Message);

                    // console.log(JSON.parse(xhr.responseText))
                },
                success: function(data) {
                    var res = data.data;
                    // console.log(res);
                    if (res == true) {
                        $('#otp-check').addClass('display-none');
                        $('#otpVerify').addClass('display-none');
                        $('#btnCounter').addClass('display-none');
                        $('#otp-confirm').removeClass('display-none');
                        $("#submit-data").prop("disabled", false);
                        toastr["success"]("OTP VERIFYED !!");
                    } else {
                        toastr["warning"]("OTP VERIFYED FAILED !!");
                    }


                }
            });


        } //end send otp



    }); // end  of document ready
</script>

@endsection