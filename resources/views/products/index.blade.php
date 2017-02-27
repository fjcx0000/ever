@extends('layouts.master')
@section('heading')

@stop

@section('content')
    <div id="pidDiv" class="row form-inline row-align-bottom">
        <div class="form-group col-sm-6 removeleft">
            {!! Form::label('product_id', 'Product ID:', ['class' => 'control-label']) !!}
            {!!
                Form::text('product_id', null, ['class' => 'form-control'])
            !!}
        </div>
        <div class="form-group col-sm-3 removeright group-align-bottom">
            {!! Form::button("Enquiry", ['id' => 'search-form','class' => 'btn btn-primary']) !!}
        </div>
    </div>
    <div class="row form-inline">
        <div class="form-group col-sm-6 removeleft">
            {!! Form::label('ename', 'English Name:', ['class' => 'control-label']) !!}
            {!!
                Form::text('ename', null, ['class' => 'form-control'])
            !!}
        </div>

        <div class="form-group col-sm-6 removeleft removeright">
            {!! Form::label('cname', 'Chinese Name:', ['class' => 'control-label']) !!}
            {!!
                Form::text('cname', null, ['class' => 'form-control'])
            !!}
        </div>
    </div>

    <table class="table table-hover " id="products-table">
        <thead>
        <tr>
            <th>@lang('product.headers.product_id')</th>
            <th>@lang('product.headers.cname')</th>
            <th>@lang('product.headers.ename')</th>
            <th>@lang('product.headers.brand')</th>
            <th>@lang('product.headers.supplier')</th>
            <th>@lang('product.headers.updated_at')</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
    </table>
    </div>

@stop

@push('scripts')
<script>
    $(function () {
        var oTable = $('#products-table').DataTable({
            processing: true,
            serverSide: true,

            ajax: {
                //type: 'GET',
                url: '{!! route('products.data') !!}',

                data: function(d) {
                    d.product_id = $('#product_id').val();
                    d.ename = $('#ename').val();
                    d.cname = $('#cname').val();
                }

            },
            columns: [

                {data: 'product_id', name: 'product_id'},
                {data: 'cname', name: 'cname'},
                {data: 'ename', name: 'ename'},
                {data: 'brand', name: 'brand'},
                {data: 'supplier_name', name: 'supplier'},
                {data: 'updated_at', name: 'updated_at'},
                @if(Entrust::can('client-update'))   
                { data: 'edit', name: 'edit', orderable: false, searchable: false},
                @endif
                @if(Entrust::can('client-delete'))   
                { data: 'delete', name: 'delete', orderable: false, searchable: false},
                @endif

            ]
        });

        $('#search-form').on('click', function(e) {
            oTable.draw();
            e.preventDefault();
        })
    });
</script>
@endpush
