@extends('layouts.master')
@section('heading')
    <h1>Excel文件处理</h1>
@stop

@section('content')
    {!! Form::open(
    array(
        'route' => 'excel.processfile',
        'class' => 'form-horizontal',
        'novalidate' => 'novalidate',
        'files' => true)) !!}

    <div class="form-group">
        {!! Form::label('filetype', '文件类型:', ['class' => 'control-label']) !!}
        {!! Form::select('filetype', array(''=>'', 'EXCEL01'=>'已配未出清单', 'EXCEL02'=>'未配未出')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('uploadfile','Excel文件', ['class' => 'control-label']) !!}
        {!! Form::file('uploadfile') !!}
    </div>

    <div class="form-group">
        {!! Form::submit('转换') !!}
    </div>
    {!! Form::close() !!}
@stop


