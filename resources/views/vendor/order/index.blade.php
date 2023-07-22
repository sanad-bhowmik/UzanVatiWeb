@extends('layouts.vendor')

@section('content')
    <div class="content-area">
        <div class="mr-breadcrumb">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="heading">{{ $langg->lang443 }}</h4>
                    <ul class="links">
                        <li>
                            <a href="{{ route('vendor-dashboard') }}">{{ $langg->lang441 }} </a>
                        </li>
                        <li>
                            <a href="javascript:;">{{ $langg->lang442 }}</a>
                        </li>
                        <li>
                            <a href="{{ route('vendor-order-index') }}">{{ $langg->lang443 }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="product-area">

            {{-- <form method="post" action="{{ route('vendor.excel') }}">

                <div class="row">


                    @csrf

                    <div class="col-md-3">
                        <select id="status" name="status">

                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>

                        </select>

                    </div>
                    <div class="col-md-6">

                        <button id="exportExcel" class="btn btn-success">Export In Excel</button>
                    </div>


                </div>
            </form> --}}



            <div class="row">
                <div class="col-lg-12">
                    <div class="mr-table allproduct">
                        @include('includes.form-success')

                        <div class="table-responsiv">
                            <div class="gocover"
                                style="background: url({{ asset('assets/images/' . $gs->admin_loader) }}) no-repeat scroll center center rgba(45, 45, 45, 0.5);">
                            </div>
                            <table id="Dastable" class="table table-hover dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{ $langg->lang534 }}</th>
                                        <th>{{ $langg->lang535 }}</th>
                                        <th>{{ $langg->lang536 }}</th>
                                        <th>{{ $langg->lang537 }}</th>
                                        <th>Status</th>
                                        <th>Link</th>
                                        <th>Order Date</th>
                                        <th>{{ $langg->lang538 }}</th>
                                    </tr>
                                </thead>


                                <tbody>
                                    @foreach ($orders as $orderr)
                                        @php
                                            $qty = $orderr->sum('qty');
                                            $price = $orderr->sum('price');
                                        @endphp


                                        @foreach ($orderr as $order)
                                            @php
                                                
                                                if ($user->shipping_cost != 0) {
                                                    $price += round($user->shipping_cost * $order->order->currency_value, 2);
                                                }
                                                if (App\Models\Order::where('order_number', '=', $order->order->order_number)->first()->tax != 0) {
                                                    $price += ($price / 100) * App\Models\Order::where('order_number', '=', $order->order->order_number)->first()->tax;
                                                }
                                                
                                            @endphp
                                            <tr>
                                                <td> <a
                                                        href="{{ route('vendor-order-invoice', $order->order_number) }}">{{ $order->order->order_number }}</a>
                                                </td>
                                                <td>{{ $qty }}</td>
                                                <td>{{ $order->order->currency_sign }}{{ round($price * $order->order->currency_value, 2) }}
                                                </td>
                                                <td>{{ $order->order->method }}  {!! $order->order->payment_status == 'pending' ? "<span class='badge badge-warning'>". $order->order->payment_status ."</span>":"<span class='badge badge-success'>". $order->order->payment_status ."</span>" !!}</td>
                                                <td >
                                                    <span class="{{$order->status}}">
                                                    @if($order->status=='confirmed')
                                                        Picked
                                                        @elseif($order->status=='completed')
                                                        Delivered
                                                        @elseif($order->status=='declined')
                                                        Canceled
                                                        @else
                                                        {{$order->status}}
                                                    @endif     
                                                    </span>
                                                
                                                </td>
                                                <td>{{ $order->flag }}</td>
                                                <td>{{ date('d/m/Y h:i:s A', strtotime($order->created_at)) }}</td>

                                                <td>

                                                    <div class="action-list">

                                                        <a href="{{ route('vendor-order-show', $order->order->order_number) }}"
                                                            class="btn btn-primary product-btn"><i class="fa fa-eye"></i>
                                                            {{ $langg->lang539 }}</a>
                                                        <select id="vendor-status" class="vendor-btn {{ $order->status }}">
                                                            <option
                                                                value="{{ route('vendor-order-status', [$order->order->order_number, 'pending']) }}"
                                                                {{ $order->status == 'pending' ? 'selected' : '' }}>
                                                                {{ $langg->lang540 }}</option>
                                                      
                                                            <option
                                                                value="{{ route('vendor-order-status', [$order->order->order_number, 'processing']) }}"
                                                                {{ $order->status == 'processing' ? 'selected' : '' }}>
                                                                {{ $langg->lang541 }}</option>

                                                            <option
                                                                value="{{ route('vendor-order-status', [$order->order->order_number, 'confirmed']) }}"
                                                                {{ $order->status == 'confirmed' ? 'selected' : '' }}>
                                                                 Picked</option>

                                                            <option
                                                                value="{{ route('vendor-order-status', [$order->order->order_number, 'shipped']) }}"
                                                                {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped
                                                            </option>

                                                            <option
                                                                value="{{ route('vendor-order-status', [$order->order->order_number, 'completed']) }}"
                                                                {{ $order->status == 'completed' ? 'selected' : '' }}>
                                                                 Delivered</option>


                                                            <option
                                                                value="{{ route('vendor-order-status', [$order->order->order_number, 'declined']) }}"
                                                                {{ $order->status == 'declined' ? 'selected' : '' }}>
                                                                 Cancel</option>
                                                        </select>

                                                        <a href="javascript:;"
                                                            data-href="{{ route('vendor-order-track', $order->order->id) }}"
                                                            class="track" data-toggle="modal" data-target="#modal1"><i
                                                                class="fas fa-truck"></i> Track Order</a>


                                                    </div>

                                                </td>

                                            </tr>
                                        @break
                                    @endforeach
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th>{{ $langg->lang534 }}</th>
                                    <th>{{ $langg->lang535 }}</th>
                                    <th>{{ $langg->lang536 }}</th>
                                    <th>{{ $langg->lang537 }}</th>
                                    <th>Status</th>
                                    <th>Link</th>
                                    <th>Order Date</th>
                                    <th>{{ $langg->lang538 }}</th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add ORDER MODAL --}}

<div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="submit-loader">
                <img src="{{ asset('assets/images/' . $gs->admin_loader) }}" alt="">
            </div>
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>

</div>

{{-- ORDER MODAL --}}

{{-- <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="submit-loader">
                <img src="{{ asset('assets/images/' . $gs->admin_loader) }}" alt="">
            </div>
            <div class="modal-header d-block text-center">
                <h4 class="modal-title d-inline-block">{{ __('Update Status') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <p class="text-center">{{ __("You are about to update the order's Status.") }}</p>
                <p class="text-center">{{ __('Do you want to proceed?') }}</p>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Cancel') }}</button>
                <a class="btn btn-success btn-ok order-btn">{{ __('Proceed') }}</a>
            </div>

        </div>
    </div>
</div> --}}




{{-- ORDER MODAL ENDS --}}
@endsection

@section('scripts')
{{-- DATA TABLE --}}

<script type="text/javascript">
    $(document).on('click', '.track', function() {

        //   console.log("hello");




        $('#modal1').find('.modal-title').html('TRACK');
        $('#modal1 .modal-content .modal-body').html('').load($(this).attr('data-href'), function(response,
            status, xhr) {
            if (status == "success") {



            }

        });
    });


    $(document).on('submit', '#trackform', function(e) {
        e.preventDefault();

        $('button.addProductSubmit-btn').prop('disabled', true);
        $.ajax({
            method: "POST",
            url: $(this).prop('action'),
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                if ((data.errors)) {
                    $('#trackform .alert-success').hide();
                    $('#trackform .alert-danger').show();
                    $('#trackform .alert-danger ul').html('');
                    for (var error in data.errors) {
                        $('#trackform .alert-danger ul').append('<li>' + data.errors[error] +
                            '</li>')
                    }
                    $('#trackform input , #trackform select , #trackform textarea').eq(1).focus();
                } else {
                    $('#trackform .alert-danger').hide();
                    $('#trackform .alert-success').show();
                    $('#trackform .alert-success p').html(data);
                    $('#trackform input , #trackform select , #trackform textarea').eq(1).focus();
                    $('#track-load').load($('#track-load').data('href'));

                }


                $('button.addProductSubmit-btn').prop('disabled', false);
            }

        });

    });
    // TRACK OPERATION END


    $('.vendor-btn').on('change', function() {
        $('#confirm-delete2').modal('show');
        $('#confirm-delete2').find('.btn-ok').attr('href', $(this).val());

    });

    var table = $('#Dastable').DataTable({
        ordering: false,

        initComplete: function () {
            this.api()
                .columns()
                .every(function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
 
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
 
                    column
                        .data()
                        .unique()
                        .sort()
                        .each(function (d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        });
                });
        },
      
        createdRow: function(row, data, dataIndex) {

            // Set the data-status attribute, and add a class

            if (data[5] == 'visited') {
                $(row).addClass('visited');
            }

        }


    });


    // $('#exportExcel').on('click',function(){
    //     console.log('hello');
    //     $.ajax({
    //        type:'GET',
    //        url:'{{ route('vendor.excel') }}',
    //        success:function(data) {
    //           alert(data);
    //        }
    //     });

    // });













    
</script>

{{-- DATA TABLE --}}
@endsection
