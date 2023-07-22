@extends('layouts.load')

@section('content')

            <div class="content-area">

              <div class="add-product-content">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="product-description">
                      <div class="body-area" id="modalEdit">
                        @include('includes.admin.form-error')  
                      <form id="Dasformdata" action="{{route('admin-campaign-store')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Campaign Banner') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <div class="img-upload full-width-img">
                                <div id="image-preview" class="img-preview" style="background: url({{ asset('assets/admin/images/upload.png') }});">
                                    <label for="image-upload" class="img-label" id="image-label"><i class="icofont-upload-alt"></i>{{ __('Upload Image') }}</label>
                                    <input type="file" name="photo" class="img-upload" id="image-upload">
                                  </div>
                                  <p class="text">{{ __('Prefered Size: (1280x600) or Relevant Sized Image') }}</p>
                            </div>

                          </div>
                        </div>

                        

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Title') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <input type="text" class="input-field" name="title" placeholder="{{ __('Campaign Title') }}" value="">
                          </div>
                        </div>


                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Name') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <input type="text" class="input-field" name="name" placeholder="{{ __('Campaign Name') }}" value="">
                          </div>
                        </div>

                        <div class="row">
                          
                          <div class="col-lg-3">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Start Date') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-3">
                            <input type="date" class="input-field" name="start_date"  value="">
                          </div>


                          <div class="col-lg-3">
                            <div class="left-area">
                                <h4 class="heading">{{ __('End Date') }} *</h4>
                            </div>
                          </div>

                          <div class="col-lg-3">
                            <input type="date" class="input-field" name="end_date"  value="">
                          </div>


                        </div>


                        <div class="row">
                          
                          <div class="col-lg-3">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Start Time') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-3">
                            <select class="input-field" name="start_time">
                              @foreach($times as $time)
                              <option value="{{$time->time}}">{{$time->am_pm_time}}</option>
                              @endforeach
                            </select>
                          </div>


                          <div class="col-lg-3">
                            <div class="left-area">
                                <h4 class="heading">{{ __('End Time') }} *</h4>
                            </div>
                          </div>

                          <div class="col-lg-3">
                            <select class="input-field" name="end_time">
                              @foreach($times as $time)
                              <option value="{{$time->time}}">{{$time->am_pm_time}}</option>
                              @endforeach
                            </select>
                          </div>


                        </div>











                        

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                              
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <button class="addProductSubmit-btn" type="submit">{{ __('Create Campaign') }}</button>
                          </div>
                        </div>
                      </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

@endsection