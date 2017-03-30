@extends('layouts.master')
@section('heading')
@stop

@section('content')
    <div id="pidDiv" class="row form-inline row-align-bottom">
        <div class="form-group col-sm-6 removeleft">
            {!! Form::label('item_id', 'Ebay Item ID:', ['class' => 'control-label']) !!}
            {!!
                Form::text('item_id', null, ['class' => 'form-control'])
            !!}
        </div>
        <div class="form-group col-sm-6 group-align-bottom">
            {!! Form::button("Check SKU", ['id' => 'check-form','class' => 'btn btn-primary']) !!}
        </div>
    </div>

    <table class="table table-hover " id="ebayitem-table">
        <thead>
        <tr>
            <th>Ebay Item ID</th>
            <th>Item Name</th>
            <th>Ebay Color</th>
            <th>Ebay Size</th>
            <th>SKU</th>
            <th>Product ID</th>
            <th>ERP Color</th>
            <th>ERP Size</th>
            <th>Check Result</th>
        </tr>
        </thead>
    </table>
    </div>

@stop

@push('scripts')
<script>
    $(function () {
        var oTable;

        $('#check-form').on('click', function(e) {
            oTable = $('#ebayitem-table').DataTable({
            processing: true,
            serverSide: true,
            bRetrieve: true,

            ajax: {
                //type: 'GET',
                url: '{!! route('ebay.checksku') !!}',

                data: function(d) {
                    d.itemID = $('#item_id').val().trim();
                },
            },
            columns: [
                {data: 'ebay_itemid', name: 'ebay_itemid'},
                {data: 'ebay_title', name: 'ebay_title'},
                {data: 'ebay_color', name: 'ebay_color'},
                {data: 'ebay_size', name: 'ebay_size'},
                {data: 'ebay_sku', name: 'ebay_sku'},
                {data: 'product_id', name: 'product_id'},
                {data: 'color_ename', name: 'color_ename'},
                {data: 'size_value', name: 'size_value'},
                {data: 'check_result', name: 'check_result'},
            ]
            });
            oTable.draw();
            e.preventDefault();
            return false;
        });
    });
</script>
@endpush
