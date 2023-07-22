@extends('layouts.load')

@section('content')
            <div class="content-area">
                @php 
                 $campaign_data = App\Models\Campaign::findOrFail($data->campaign_id);
               
                @endphp

              <div class="add-product-content">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="product-description">
                      <div class="body-area" id="modalEdit">
                        @include('includes.admin.form-error') 


                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">Title</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            {{$campaign_data->title}}

                          </div>
                        </div>

                        

                       


                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Name') }} </h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            {{$campaign_data->name}}
                          </div>
                        </div>


                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Notes from vendor') }} </h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            {{$data->vendor_note}}
                          </div>
                        </div>

                      <form id="Dasformdata" action="{{route('admin-join-request-update',$data->id)}}" method="POST" enctype="multipart/form-data">
                          {{csrf_field()}}




                          <div class="row">
                            <div class="col-lg-4">
                              <div class="left-area">
                                  <h4 class="heading">{{ __('Admin Note *') }} </h4>
                              </div>
                            </div>
                            <div class="col-lg-7">
                            <textarea  required class="form-group" name="admin_note">{{$data->admin_note}}</textarea>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4">
                              <div class="left-area">
                                  <h4 class="heading">{{ __('Application status *') }} </h4>
                              </div>
                            </div>
                            <div class="col-lg-7">
                              @php
                              $status = ['pending','approved','on-hold','declined']  
                                
                              @endphp
                           <select name='status' class="form-group">
                            @foreach ($status as $item)

                            <option  {{$item == $data->status ? "selected":""}} 
                              
                              
                              
                              value="{{$item}}">{{$item}}</option>
                                
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
                            <button class="addProductSubmit-btn" type="submit">{{ __('Proceed') }}</button>
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