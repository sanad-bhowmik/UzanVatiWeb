(function($) {
    "use strict";

  $(document).ready(function() {


function disablekey()
{
 document.onkeydown = function (e)
 {
  return false;
 }
}

function enablekey()
{
 document.onkeydown = function (e)
 {
  return true;
 }
}

// **************************************  AJAX REQUESTS SECTION *****************************************

$(document).on('change','#vendor-status',function () {


var link =$(this).val();
$.get( link, function( data ) {


  if(data==1){

    alert("Order is already Completed");
  }else if(data==2){

    alert("Order status updated");

  }



});





});













  // Status Start
      $(document).on('click','.status',function () {
        var link = $(this).attr('data-href');
            $.get( link, function(data) {
              }).done(function(data) {
                  table.ajax.reload();
                  $('.alert-danger').hide();
                  $('.alert-success').show();
                  $('.alert-success p').html(data);
            })
          });
  // Status Ends


  // Display Subcategories & attributes
      $(document).on('change','#cat',function () {
        var link = $(this).find(':selected').attr('data-href');
        if(link != "")
        {
          $('#subcat').load(link);
          $('#subcat').prop('disabled',false);
        }
        $.get(getattrUrl + '?id=' + this.value + '&type=category', function(data) {
          console.log(data);
          let attrHtml = '';
          for (var i = 0; i < data.length; i++) {
            attrHtml += `
            <div class="row">
              <div class="col-lg-4">
                <div class="left-area">
                    <h4 class="heading">${data[i].attribute.name} *</h4>
                </div>
              </div>
              <div class="col-lg-7">
            `;

            for (var j = 0; j < data[i].options.length; j++) {
              let priceClass = '';
              if (data[i].attribute.price_status == 0) {
                priceClass = 'd-none';
              }
              attrHtml += `
                <div class="row mb-0 option-row">
                  <div class="col-lg-5">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" id="${data[i].attribute.input_name}${data[i].options[j].id}" name="${data[i].attribute.input_name}[]" value="${data[i].options[j].name}" class="custom-control-input attr-checkbox">
                      <label class="custom-control-label" for="${data[i].attribute.input_name}${data[i].options[j].id}">${data[i].options[j].name}</label>
                    </div>
                  </div>
                  <div class="col-lg-7 ${priceClass}">
                    <div class="row">
                      <div class="col-2">
                        +
                      </div>
                      <div class="col-10">
                        <div class="price-container">
                          <span class="price-curr">${curr.sign}</span>
                          <input type="text" class="input-field price-input" id="${data[i].attribute.input_name}${data[i].options[j].id}_price" data-name="${data[i].attribute.input_name}_price[]" placeholder="0.00 (Additional Price)" value="">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              `;
            }

            attrHtml +=  `
              </div>
            </div>
            `;
          }

          $("#catAttributes").html(attrHtml);
          $("#subcatAttributes").html('');
          $("#childcatAttributes").html('');
        });
      });
  // Display Subcategories Ends

  // Display Childcategories & Attributes
      $(document).on('change','#subcat',function () {
        var link = $(this).find(':selected').attr('data-href');
        if(link != "")
        {
          $('#childcat').load(link);
          $('#childcat').prop('disabled',false);
        }

        $.get(getattrUrl + '?id=' + this.value + '&type=subcategory', function(data) {
          console.log(data);
          let attrHtml = '';
          for (var i = 0; i < data.length; i++) {
            attrHtml += `
            <div class="row">
              <div class="col-lg-4">
                <div class="left-area">
                    <h4 class="heading">${data[i].attribute.name} *</h4>
                </div>
              </div>
              <div class="col-lg-7">
            `;

            for (var j = 0; j < data[i].options.length; j++) {
              let priceClass = '';
              if (data[i].attribute.price_status == 0) {
                priceClass = 'd-none';
              }
              attrHtml += `
                  <div class="row option-row">
                    <div class="col-lg-5">
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" id="${data[i].attribute.input_name}${data[i].options[j].id}" name="${data[i].attribute.input_name}[]" value="${data[i].options[j].name}" class="custom-control-input attr-checkbox">
                        <label class="custom-control-label" for="${data[i].attribute.input_name}${data[i].options[j].id}">${data[i].options[j].name}</label>
                      </div>
                    </div>
                    <div class="col-lg-7 ${priceClass}">
                      <div class="row">
                        <div class="col-2">
                          +
                        </div>
                        <div class="col-10">
                          <div class="price-container">
                            <span class="price-curr">${curr.sign}</span>
                            <input type="text" class="input-field price-input" id="${data[i].attribute.input_name}${data[i].options[j].id}_price" data-name="${data[i].attribute.input_name}_price[]" placeholder="0.00 (Additional Price)" value="">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              `;
            }

            attrHtml +=  `
              </div>
            </div>
            `;
          }

          $("#subcatAttributes").html(attrHtml);
          $("#childcatAttributes").html('');
        });
      });
  // Display Childcateogries & Attributes Ends


  // Display Attributes for Selected Childcategory Starts
      $(document).on('change','#childcat',function () {

        $.get(getattrUrl + '?id=' + this.value + '&type=childcategory', function(data) {
          console.log(data);
          let attrHtml = '';
          for (var i = 0; i < data.length; i++) {
            attrHtml += `
            <div class="row">
              <div class="col-lg-4">
                <div class="left-area">
                    <h4 class="heading">${data[i].attribute.name} *</h4>
                </div>
              </div>
              <div class="col-lg-7">
            `;

            for (var j = 0; j < data[i].options.length; j++) {
              let priceClass = '';
              if (data[i].attribute.price_status == 0) {
                priceClass = 'd-none';
              }
              attrHtml += `
                  <div class="row option-row">
                    <div class="col-lg-5">
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" id="${data[i].attribute.input_name}${data[i].options[j].id}" name="${data[i].attribute.input_name}[]" value="${data[i].options[j].name}" class="custom-control-input attr-checkbox">
                        <label class="custom-control-label" for="${data[i].attribute.input_name}${data[i].options[j].id}">${data[i].options[j].name}</label>
                      </div>
                    </div>
                    <div class="col-lg-7 ${priceClass}">
                      <div class="row">
                        <div class="col-2">
                          +
                        </div>
                        <div class="col-10">
                          <div class="price-container">
                            <span class="price-curr">${curr.sign}</span>
                            <input type="text" id="${data[i].attribute.input_name}${data[i].options[j].id}_price" class="input-field price-input" data-name="${data[i].attribute.input_name}_price[]" placeholder="0.00 (Additional Price)" value="">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

              `;
            }

            attrHtml +=  `
              </div>
            </div>
            `;
          }

          $("#childcatAttributes").html(attrHtml);
        });
      });
  // Display Attributes for Selected Childcategory Ends


  // Droplinks Start
      $(document).on('change','.droplinks',function () {

        var link = $(this).val();
        var data = $(this).find(':selected').attr('data-val');
        if(data == 0)
        {
          $(this).next(".nice-select.process.select.droplinks").removeClass("drop-success").addClass("drop-danger");
        }
        else{
          $(this).next(".nice-select.process.select.droplinks").removeClass("drop-danger").addClass("drop-success");
        }
        $.get(link);
        $.notify(alang.status,"success");
      });


      $(document).on('change','.vdroplinks',function () {

        var link = $(this).val();
        var data = $(this).find(':selected').attr('data-val');
        if(data == 0)
        {
          $(this).next(".nice-select.process.select1.vdroplinks").removeClass("drop-success").addClass("drop-danger");
        }
        else{
          $(this).next(".nice-select.process.select1.vdroplinks").removeClass("drop-danger").addClass("drop-success");
        }
        $.get(link);
        $.notify(alang.status,"success");
      });

      $(document).on('change','.data-droplinks',function (e) {
          $('#confirm-delete').modal('show');
          $('#confirm-delete').find('.btn-ok').attr('href', $(this).val());
          table.ajax.reload();
        });


  // Droplinks Ends



// ADD OPERATION

$(document).on('click','#add-data',function(){
if(admin_loader == 1)
  {
  $('.submit-loader').show();
}
  $('#modal1').find('.modal-title').html(langg.lang518+' '+$('#headerdata').val());
  $('#modal1 .modal-content .modal-body').html('').load($(this).attr('data-href'),function(response, status, xhr){
      if(status == "success")
      {
        if(admin_loader == 1)
          {
            $('.submit-loader').hide();
          }
      }

    });
});

// ADD OPERATION END



// EDIT OPERATION

$(document).on('click','.edit',function(){
if(admin_loader == 1)
  {
  $('.submit-loader').show();
}
  $('#modal1').find('.modal-title').html(langg.lang519+' '+$('#headerdata').val());
  $('#modal1 .modal-content .modal-body').html('').load($(this).attr('data-href'),function(response, status, xhr){
      if(status == "success")
      {
        if(admin_loader == 1)
          {
            $('.submit-loader').hide();
          }
      }
    });
});


// EDIT OPERATION END


// FEATURE OPERATION

$(document).on('click','.feature',function(){
if(admin_loader == 1)
  {
  $('.submit-loader').show();
}
  $('#modal2').find('.modal-title').html($('#headerdata').val()+' Highlight');
  $('#modal2 .modal-content .modal-body').html('').load($(this).attr('data-href'),function(response, status, xhr){
      if(status == "success")
      {
        if(admin_loader == 1)
          {
            $('.submit-loader').hide();
          }
      }
    });
});


// EDIT OPERATION END


// SHOW OPERATION

$(document).on('click','.view',function(){
if(admin_loader == 1)
  {
  $('.submit-loader').show();
}
  $('#modal1').find('.modal-title').html($('#headerdata').val()+' DETAILS');
  $('#modal1 .modal-content .modal-body').html('').load($(this).attr('data-href'),function(response, status, xhr){
      if(status == "success")
      {
        if(admin_loader == 1)
          {
            $('.submit-loader').hide();
          }
      }

    });
});


// SHOW OPERATION END



// ADD / EDIT FORM SUBMIT FOR DATA TABLE


$(document).on('submit','#Dasformdata',function(e){
  e.preventDefault();
if(admin_loader == 1)
  {
  $('.submit-loader').show();
}
  $('button.addProductSubmit-btn').prop('disabled',true);
  disablekey();
      $.ajax({
       method:"POST",
       url:$(this).prop('action'),
       data:new FormData(this),
       dataType:'JSON',
       contentType: false,
       cache: false,
       processData: false,
       success:function(data)
       {
          if ((data.errors)) {
          $('.alert-danger').show();
          $('.alert-danger ul').html('');
            for(var error in data.errors)
            {
              $('.alert-danger ul').append('<li>'+ data.errors[error] +'</li>');
            }
        if(admin_loader == 1)
          {
            $('.submit-loader').hide();
          }
            $("#modal1 .modal-content .modal-body .alert-danger").focus();
            $('button.addProductSubmit-btn').prop('disabled',false);
            $('#Dasformdata input , #Dasformdata select , #Dasformdata textarea').eq(1).focus();
          }
          else
          {
            table.ajax.reload();
            $('.alert-success').show();
            $('.alert-success p').html(data);
        if(admin_loader == 1)
          {
            $('.submit-loader').hide();
          }
            $('button.addProductSubmit-btn').prop('disabled',false);
            $('#modal1,#modal2').modal('toggle');

           }
          enablekey();
       }

      });

});


// ADD / EDIT FORM SUBMIT FOR DATA TABLE ENDS



// DELETE OPERATION

      $('#confirm-delete').on('show.bs.modal', function(e) {
          $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
      });

      $('#confirm-delete .btn-ok').on('click', function(e) {
        if($('#confirm-delete .btn-ok').hasClass("order-btn")){
if(admin_loader == 1)
  {
  $('.submit-loader').show();
}
        }
        $.ajax({
         type:"GET",
         url:$(this).attr('href'),
         success:function(data)
         {
              $('#confirm-delete').modal('toggle');
              table.ajax.reload();
              $('.alert-danger').hide();
              $('.alert-success').show();
              $('.alert-success p').html(data);

              if($('#confirm-delete .btn-ok').hasClass("order-btn")){
        if(admin_loader == 1)
          {
            $('.submit-loader').hide();
          }
              }

         }
        });
        return false;
      });

      $('#confirm-delete1 .btn-ok').on('click', function(e) {

        $.ajax({
         type:"GET",
         url:$(this).attr('href'),
         success:function(data)
         {
              $('#confirm-delete1').modal('toggle');
              table.ajax.reload();
              $('.alert-danger').hide();
              $('.alert-success').show();
              $('.alert-success p').html(data[0]);
         }
        });
        return false;
      });




// DELETE OPERATION END

  });



  // NORMAL FORM

  $(document).on('submit','#Dasform',function(e){
    e.preventDefault();
    if(admin_loader == 1)
    {
    $('.gocover').show();
    }

    var fd = new FormData(this);

    if ($('.attr-checkbox').length > 0) {
      $('.attr-checkbox').each(function() {

        // if checkbox checked then take the value of corresponsig price input (if price input exists)
        if($(this).prop('checked') == true) {

          if ($("#"+$(this).attr('id')+'_price').val().length > 0) {
            // if price value is given
            fd.append($("#"+$(this).attr('id')+'_price').data('name'), $("#"+$(this).attr('id')+'_price').val());
          } else {
            // if price value is not given then take 0
            fd.append($("#"+$(this).attr('id')+'_price').data('name'), 0.00);
          }

          // $("#"+$(this).attr('id')+'_price').val(0.00);
        }
      });
    }

    $('button.addProductSubmit-btn').prop('disabled',true);
        $.ajax({
         method:"POST",
         url:$(this).prop('action'),
         data: fd,
         contentType: false,
         cache: false,
         processData: false,
         success:function(data)
         {
            console.log(data);
            if ((data.errors)) {
            $('.alert-success').hide();
            $('.alert-danger').show();
            $('.alert-danger ul').html('');
              for(var error in data.errors)
              {
                $('.alert-danger ul').append('<li>'+ data.errors[error] +'</li>')
              }
              $('#Dasform input , #Dasform select , #Dasform textarea').eq(1).focus();
            }
            else
            {
              $('.alert-danger').hide();
              $('.alert-success').show();
              $('.alert-success p').html(data);
              $('#Dasform input , #Dasform select , #Dasform textarea').eq(1).focus();
            }
    if(admin_loader == 1)
    {
            $('.gocover').hide();
    }

            $('button.addProductSubmit-btn').prop('disabled',false);
         }

        });

  });

  // NORMAL FORM ENDS


// MESSAGE FORM

$(document).on('submit','#messageform',function(e){
  e.preventDefault();
  var href = $(this).data('href');
  if(admin_loader == 1)
  {
  $('.gocover').show();
  }
  $('button.mybtn1').prop('disabled',true);
      $.ajax({
       method:"POST",
       url:$(this).prop('action'),
       data:new FormData(this),
       contentType: false,
       cache: false,
       processData: false,
       success:function(data)
       {
          if ((data.errors)) {
          $('.alert-success').hide();
          $('.alert-danger').show();
          $('.alert-danger ul').html('');
            for(var error in data.errors)
            {
              $('.alert-danger ul').append('<li>'+ data.errors[error] +'</li>')
            }
            $('#messageform textarea').val('');
          }
          else
          {
            $('.alert-danger').hide();
            $('.alert-success').show();
            $('.alert-success p').html(data);
            $('#messageform textarea').val('');
            $('#messages').load(href);
          }
  if(admin_loader == 1)
  {
          $('.gocover').hide();
  }
          $('button.mybtn1').prop('disabled',false);
       }
      });
});

// MESSAGE FORM ENDS

// LOGIN FORM

$("#loginform").on('submit',function(e){
  e.preventDefault();
  $('button.submit-btn').prop('disabled',true);
  $('.alert-info').show();
  $('.alert-info p').html($('#authdata').val());
      $.ajax({
       method:"POST",
       url:$(this).prop('action'),
       data:new FormData(this),
       dataType:'JSON',
       contentType: false,
       cache: false,
       processData: false,
       success:function(data)
       {
          if ((data.errors)) {
          $('.alert-success').hide();
          $('.alert-info').hide();
          $('.alert-danger').show();
          $('.alert-danger ul').html('');
            for(var error in data.errors)
            {
              $('.alert-danger p').html(data.errors[error]);
            }
          }
          else
          {
            $('.alert-info').hide();
            $('.alert-danger').hide();
            $('.alert-success').show();
            $('.alert-success p').html('Success !');
            window.location = data;
          }
          $('button.submit-btn').prop('disabled',false);
       }

      });

});


// LOGIN FORM ENDS

// FORGOT FORM

$("#forgotform").on('submit',function(e){
  e.preventDefault();
  $('button.submit-btn').prop('disabled',true);
  $('.alert-info').show();
  $('.alert-info p').html($('#authdata').val());
      $.ajax({
       method:"POST",
       url:$(this).prop('action'),
       data:new FormData(this),
       dataType:'JSON',
       contentType: false,
       cache: false,
       processData: false,
       success:function(data)
       {
          if ((data.errors)) {
          $('.alert-success').hide();
          $('.alert-info').hide();
          $('.alert-danger').show();
          $('.alert-danger ul').html('');
            for(var error in data.errors)
            {
              $('.alert-danger p').html(data.errors[error]);
            }
          }
          else
          {
            $('.alert-info').hide();
            $('.alert-danger').hide();
            $('.alert-success').show();
            $('.alert-success p').html(data);
            $('input[type=email]').val('');
          }
          $('button.submit-btn').prop('disabled',false);
       }

      });

});

// FORGOT FORM ENDS

// ORDER NOTIFICATION

$(document).ready(function(){
    setInterval(function(){
            $.ajax({
                    type: "GET",
                    url:$("#order-notf-count").data('href'),
                    success:function(data){
                        $("#order-notf-count").html(data);
                      }
              });
    }, 300000);
});

$(document).on('click','#notf_order',function(){
  $("#order-notf-count").html('0');
  $('#order-notf-show').load($("#order-notf-show").data('href'));
});

$(document).on('click','#order-notf-clear',function(){
  $(this).parent().parent().trigger('click');
  $.get($('#order-notf-clear').data('href'));
});

// ORDER NOTIFICATION ENDS

// SEND MESSAGE SECTION
$(document).on('click','.send',function(){
  $('.eml-val').val($(this).data('email'));
});

          $(document).on("submit", "#emailreply1" , function(){
          var token = $(this).find('input[name=_token]').val();
          var subject = $(this).find('input[name=subject]').val();
          var message =  $(this).find('textarea[name=message]').val();
          var to = $(this).find('input[name=to]').val();
          $('#eml1').prop('disabled', true);
          $('#subj1').prop('disabled', true);
          $('#msg1').prop('disabled', true);
          $('#emlsub1').prop('disabled', true);
            $.ajax({
            type: 'post',
            url: mainurl+'/admin/user/send/message',
            data: {
                '_token': token,
                'subject'   : subject,
                'message'  : message,
                'to'   : to
                  },
                 success: function( data) {
                  $('#eml1').prop('disabled', false);
                  $('#subj1').prop('disabled', false);
                  $('#msg1').prop('disabled', false);
                  $('#subj1').val('');
                  $('#msg1').val('');
                  $('#emlsub1').prop('disabled', false);
                  if(data == 0)
                    $.notify("Oops Something Goes Wrong !!","error");
                  else
                    $.notify("Message Sent !!","success");
                  $('.close').click();
            }
        });
          return false;
        });

// SEND MESSAGE SECTION ENDS

// NORMAL FORM

$(document).on('submit','#verifyform',function(e){
  e.preventDefault();
  if(admin_loader == 1)
  {
  $('.gocover').show();
  }

  $('button.addProductSubmit-btn').prop('disabled',true);
      $.ajax({
       method:"POST",
       url:$(this).prop('action'),
       data:new FormData(this),
       contentType: false,
       cache: false,
       processData: false,
       success:function(data)
       {

          if ((data.errors)) {
          $('.alert-success').hide();
          $('.alert-danger').show();
          $('.alert-danger ul').html('');
            for(var error in data.errors)
            {
              $('.alert-danger ul').append('<li>'+ data.errors[error] +'</li>')
            }
            $('#verifyform input , #verifyform select , #verifyform textarea').eq(1).focus();
          }
          else
          {
            $('.alert-danger').hide();
            $('.alert-success').show();
            $('.alert-success p').html(data);
            $('#verifyform').html('');
          }
  if(admin_loader == 1)
  {
          $('.gocover').hide();
  }

          $('button.addProductSubmit-btn').prop('disabled',false);
       }

      });

});

// NORMAL FORM ENDS

// SEND EMAIL SECTION

          $(document).on("submit", "#emailreply" , function(){
          var token = $(this).find('input[name=_token]').val();
          var subject = $(this).find('input[name=subject]').val();
          var message =  $(this).find('textarea[name=message]').val();
          var to = $(this).find('input[name=to]').val();
          $('#eml').prop('disabled', true);
          $('#subj').prop('disabled', true);
          $('#msg').prop('disabled', true);
          $('#emlsub').prop('disabled', true);
     $.ajax({
            type: 'post',
            url: mainurl+'/admin/order/email',
            data: {
                '_token': token,
                'subject'   : subject,
                'message'  : message,
                'to'   : to
                  },
            success: function( data) {
          $('#eml').prop('disabled', false);
          $('#subj').prop('disabled', false);
          $('#msg').prop('disabled', false);
          $('#subj').val('');
          $('#msg').val('');
        $('#emlsub').prop('disabled', false);
        if(data == 0)
        $.notify("Oops Something Goes Wrong !!","error");
        else
        $.notify("Email Sent !!","success");
        $('.close').click();
            }

        });
          return false;
        });
// SEND EMAIL SECTION ENDS

// **************************************  AJAX REQUESTS SECTION ENDS *****************************************

})(jQuery);









  
  
  //