<!-- VENDOR LOGIN MODAL -->
<div class="modal fade" id="vendor-login" tabindex="-1" role="dialog" aria-labelledby="vendor-login-Title" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" style="transition: .5s;" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
                  <nav class="comment-log-reg-tabmenu">
                      <div class="nav nav-tabs" id="nav-tab1" role="tablist">
                          <a class="nav-item nav-link login active" id="nav-log-tab11" data-toggle="tab" href="#nav-log11" role="tab" aria-controls="nav-log" aria-selected="true">
                              {{ $langg->lang234 }}
                          </a>
                          <a class="nav-item nav-link" id="nav-reg-tab11" data-toggle="tab" href="#nav-reg11" role="tab" aria-controls="nav-reg" aria-selected="false">
                              {{ $langg->lang235 }}
                          </a>
                      </div>
                  </nav>
                  <div class="tab-content" id="nav-tabContent">
                      <div class="tab-pane fade show active" id="nav-log11" role="tabpanel" aria-labelledby="nav-log-tab">
                          <div class="login-area">
                            <div class="login-form signin-form">
                                  @include('includes.admin.form-login')
                              <form class="mloginform" action="{{ route('user.login.submit') }}" method="POST">
                                {{ csrf_field() }}
                                <div class="form-input">
                                  <input type="text" name="phone" placeholder="Mobile Number" required="">
                                  <i class="icofont-user-alt-5"></i>
                                </div>
                                <div class="form-input">
                                  <input type="password" class="Password" name="password" placeholder="{{ $langg->lang174 }}" required="">
                                  <i class="icofont-ui-password"></i>
                                </div>
                                <div class="form-forgot-pass">
                                  <div class="left">
                                    <input type="checkbox" name="remember"  id="mrp1" {{ old('remember') ? 'checked' : '' }}>
                                    <label for="mrp1">{{ $langg->lang175 }}</label>
                                  </div>
                                  <div class="right">
                                    <!-- <a href="javascript:;" id="show-forgot1">
                                      {{ $langg->lang176 }}
                                    </a> -->
                                    <a href="{{ route('user-forgot') }}" >
                                      {{ $langg->lang176 }}
                                    </a> 
                                    
                                  </div>
                                </div>
                                <input type="hidden" name="modal"  value="1">
                                 <input type="hidden" name="vendor"  value="1">
                                <input class="mauthdata" type="hidden"  value="{{ $langg->lang177 }}">
                                <button type="submit" class="submit-btn">{{ $langg->lang178 }}</button>
                                    @if(App\Models\Socialsetting::find(1)->f_check == 1 || App\Models\Socialsetting::find(1)->g_check == 1)
                                    <!-- <div class="social-area">
                                        <h3 class="title">{{ $langg->lang179 }}</h3>
                                        <p class="text">{{ $langg->lang180 }}</p>
                                        <ul class="social-links">
                                          @if(App\Models\Socialsetting::find(1)->f_check == 1)
                                          <li>
                                            <a href="{{ route('social-provider','facebook') }}">
                                              <i class="fab fa-facebook-f"></i>
                                            </a>
                                          </li>
                                          @endif
                                          @if(App\Models\Socialsetting::find(1)->g_check == 1)
                                          <li>
                                            <a href="{{ route('social-provider','google') }}">
                                              <i class="fab fa-google-plus-g"></i>
                                            </a>
                                          </li>
                                          @endif
                                        </ul>
                                    </div> -->
                                    @endif
                              </form>
                            </div>
                          </div>
                      </div>
                      <div class="tab-pane fade" id="nav-reg11" role="tabpanel" aria-labelledby="nav-reg-tab">
                  <div class="login-area signup-area">
                      <div class="login-form signup-form">
                         @include('includes.admin.form-login')
                          <form class="mregisterform" action="{{route('user-register-submit-vendor')}}" method="POST">
                            {{ csrf_field() }}
  
                            <div class="row">
  
  
  
                          <div class="col-lg-6">
  
                              <div class="form-input">
                                      <select required  name="division" id="division" >
                                  @include('includes.divisions')
                                  </select>
                                
                                  
                              </div>
                          </div>
                          <div class="col-lg-6">
  
                              <div class="form-input">
                              <select required  name="district" id="district" >
                                      <option value="" >Select Zilla</option>
                                      </select>
                                  </div>
                              </div>
                          <div class="col-lg-6">
  
                              <div class="form-input">
                                  <select required name="upazila" id="upazila" >
                                      <option value="" >Select UpZilla</option>
                                      </select>
                                  </div>
                          </div>
  
                            <div class="col-lg-6">
                              <div class="form-input">
                                  <input type="text" class="User Name" name="name" placeholder="{{ $langg->lang182 }}" required="">
                                  <i class="icofont-user-alt-5"></i>
                                  </div>
                             </div>
  
                             <div class="col-lg-6">
   <div class="form-input">
                                  <input type="email" class="User Name" name="email" placeholder="{{ $langg->lang183 }}" required="">
                                  <i class="icofont-email"></i>
                              </div>
  
                                 </div>
                             <div class="col-lg-6">
      <div class="form-input">
                                  <input type="text" class="User Name" name="phone" placeholder="{{ $langg->lang184 }}" required="">
                                  <i class="icofont-phone"></i>
                              </div>
  
                                 </div>
                             <div class="col-lg-6">
  
  <div class="form-input">
                                  <input type="text" class="User Name" name="address" placeholder="{{ $langg->lang185 }}" required="">
                                  <i class="icofont-location-pin"></i>
                              </div>
                                 </div>
  
                             <div class="col-lg-6">
   <div class="form-input">
                                  <input type="text" class="User Name" name="shop_name" placeholder="{{ $langg->lang238 }}" required="">
                                  <i class="icofont-cart-alt"></i>
                              </div>
  
                                 </div>
                             <div class="col-lg-6">
  
   <div class="form-input">
                                  <input type="text" class="User Name" name="owner_name" placeholder="{{ $langg->lang239 }}" required="">
                                  <i class="icofont-cart"></i>
                              </div>
                                 </div>
                             <div class="col-lg-6">
  
  <div class="form-input">
                                  <input type="text" class="User Name" name="shop_number" placeholder="{{ $langg->lang240 }}" required="">
                                  <i class="icofont-shopping-cart"></i>
                              </div>
                                 </div>
                             <div class="col-lg-6">
  
   <div class="form-input">
                                  <input type="text" class="User Name" name="shop_address" placeholder="{{ $langg->lang241 }}" required="">
                                  <i class="icofont-opencart"></i>
                              </div>
                                 </div>
                             <div class="col-lg-6">
  
  <div class="form-input">
                                  <input type="text" class="User Name" name="reg_number" placeholder="{{ $langg->lang242 }}" required="">
                                  <i class="icofont-ui-cart"></i>
                              </div>
                                 </div>
                             <div class="col-lg-6">
  
   <div class="form-input">
                                  <input type="text" class="User Name" name="shop_message" placeholder="{{ $langg->lang243 }}" required="">
                                  <i class="icofont-envelope"></i>
                              </div>
                                 </div>
  
                             <div class="col-lg-6">
    <div class="form-input">
                                  <input type="password" class="Password" name="password" placeholder="{{ $langg->lang186 }}" required="">
                                  <i class="icofont-ui-password"></i>
                              </div>
  
                                 </div>
                             <div class="col-lg-6">
                                   <div class="form-input">
                                  <input type="password" class="Password" name="password_confirmation" placeholder="{{ $langg->lang187 }}" required="">
                                  <i class="icofont-ui-password"></i>
                                  </div>
                                 </div>
  
                              @if($gs->is_capcha == 1)
  
  <div class="col-lg-6">
  
  
                              <ul class="captcha-area">
                                  <li>
                                       <p>
                                           <img class="codeimg1" src="{{asset("assets/images/capcha_code.png")}}" alt=""> <i class="fas fa-sync-alt pointer refresh_code "></i>
                                       </p>
  
                                  </li>
                              </ul>
  
  
  </div>
  
  <div class="col-lg-6">
  
   <div class="form-input">
                                  <input type="text" class="Password" name="codes" placeholder="{{ $langg->lang51 }}" required="">
                                  <i class="icofont-refresh"></i>
  
                              </div>
  
  
  
                            </div>
  
                            @endif
  
                              <input type="hidden" name="vendor"  value="1">
                              <input class="mprocessdata" type="hidden"  value="{{ $langg->lang188 }}">
                              <button type="submit" class="submit-btn">{{ $langg->lang189 }}</button>
  
                                 </div>
  
  
  
  
                          </form>
                      </div>
                  </div>
                      </div>
                  </div>
        </div>
      </div>
    </div>
  </div>
  <!-- VENDOR LOGIN MODAL ENDS -->