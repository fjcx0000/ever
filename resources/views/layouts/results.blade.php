@extends('layouts.master')
@section('heading')
    <h1>Processed Successfully</h1>
@stop

@section('content')
    <a href="{{ url()->previous() }}">Back</a><br/>
    <a href="/">Home</a>

@stop
