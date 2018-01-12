@extends('layouts.master')
@section('heading')
    <h1>生成零售渠道库存数据文件</h1>
@stop

@section('content')
    {!! Form::open(
    array(
        'route' => 'excel.getinventory',
        'novalidate' => 'novalidate',
        )) !!}

    <div class="form-group">
        <label for="sendnotices">配额编号列表（多个请使用,分隔）:</label>
        <textarea class="form-control" cols="50" rows="2" name="sendnotices" id="sendnotices"></textarea>
    </div>


    <div class="form-group">
        {!! Form::submit('提交') !!}
    </div>
    {!! Form::close() !!}
@stop
