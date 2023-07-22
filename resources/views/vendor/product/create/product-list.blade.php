<div    class="col-md-12">

    <div class="row">
    @forelse($datas as $data)
        <div class="col-lg-3 col-md-3 mt-15">
            <div class="card card-shadow">

                <img class="card-img-top" alt="NO-IMAGE" src="{{ asset('assets/images/thumbnails/' . $data->thumbnail) }}">

                <div class="card-body text-center">
                    <p class="card-title">{{ $data->name }}</p>
                    <p class="card-text">BDT {{ $data->price }} TK</p>

                    <button data-href="{{ route('vendor-prod-store-from-list', $data->id) }}"
                        onclick="confirm('Sure you want to add this?')" class="btn btn-success add-to-shop-2">Add to Shop</button>
                </div>
            </div>

        </div>


    @empty
        <div class="col-md-12">
            <h5>No Items</h5>
        </div>
    @endforelse

    </div>
</div>
    <script>
        $(document).ready(function() {

            $(".add-to-shop-2").on('click', function(e) {

                var link = $(this).attr('data-href');

                $.get(link, function(data, status) {
                    if (data == 0) {

                        $.notify("Product is already in your shop..", "warning");
                    } else if (data == 2) {
                        $.notify("Faild to add this into shop..", "error");
                    } else {
                        $.notify("Successfuly added to shop..", "success");

                    }
                });

                e.preventDefault();

            });
        });
    </script>


