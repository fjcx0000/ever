@extends('layouts.mobile')

@section('content')
    <div data-role="page" id="mainPage" data-theme="a">
        <div data-role="header">
            <h1>Jquery Mobile Study</h1>
        </div>
        <div data-role="content">
            <a href="#customdialog" data-role="button" data-rel="dialog">
                Open Custom Dialog
            </a>
        </div>
        <div data-role="footer">
        </div>
    </div>
    <div data-role="dialog" id="customdialog">
        <div data-role="header" data-theme="d">
            <h1>
                Dialog</h1>
        </div>
        <div data-role="content" data-theme="c">
            <h1>
                Delete page?</h1>
            <p>
                This is a regular page, styled as a dialog. To create a dialog, just link to a normal
                page and include a transition and <code>data-rel="dialog"</code> attribute.</p>
            <a href="#" data-role="button" data-rel="back" data-theme="b" id="soundgood">Sounds
                good</a> <a href="#mainPage" data-role="button" data-rel="back" data-theme="c">Cancel</a>
        </div>
    </div>
@stop

@push('links')
@endpush
@push('scripts')
@endpush
