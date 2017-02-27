@extends('layouts.master')
@section('heading')
    <h1>File Upload</h1>
@stop

@section('content')
        {!! Form::open(array('route' => 'products.fileupload', 'files'=>true)) !!}
            <div class="row">
                <div class="col-md-4">
                    {!! Form::label('filetype', 'Select File Type:', ['class' => 'control-label']) !!}
                    {{ Form::select('filetype', [
                        null => 'Please select file type',
                        'sku' => 'SKU Record File',
                        'product' => 'Product Excel File'
                    ]) ,['class' => 'form-control']}}
                </div>
                <div class="col-md-4">
                    {!! Form::file('excel', array('class'=>'form-contrl')) !!}
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </div>
        {!! Form::close() !!}
@stop
