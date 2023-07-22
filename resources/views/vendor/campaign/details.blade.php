@extends('layouts.load')

@section('content')
    <div class="content-area">

        <div class="add-product-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="product-description">
                        <div class="body-area" id="modalEdit">
                            @include('includes.vendor.form-error')


                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="img-upload full-width-img">
                                        <div id="image-preview" class="img-preview"
                                            style="background: url({{ asset('assets/images/banners/' . $data->banner) }});">

                                        </div>

                                    </div>

                                </div>



                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    CAMPAIGN TITLE
                                </div>
                                <div class="col-lg-6">
                                    {{ $data->title }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    CAMPAIGN NAME
                                </div>
                                <div class="col-lg-6">
                                    {{ $data->name }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    CAMPAIGN DETAILS
                                </div>
                                <div class="col-lg-6">
                                    {{ $data->vendor_note }}
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-lg-6">
                                    CAMPAIGN DATE
                                </div>
                                <div class="col-lg-6">
                                    {{ $data->start_date }} TO {{ $data->end_date }}
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-lg-12">
                                    FILE/NOTES: @if (!empty($data->file))
                                        <a class="btn btn-warning" href="{{ asset('assets/files/' . $data->file) }}">Click to
                                            show</a>
                                    @endif
                                </div>


                            </div>


                            <div class="row">
                                <div class="col-lg-12">
                                    YOUR NOTES: {{ $data->vendor_note }}
                                </div>


                            </div>


                            <div class="row">
                                <div class="col-lg-12">
                                    ADMIN NOTES: {{ $data->admin_note }}
                                </div>


                            </div>

                            
                            <div class="row">
                              <div class="col-lg-12">
                                  APPLICATION STATUS: {{ $data->status }}
                              </div>


                          </div>



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
