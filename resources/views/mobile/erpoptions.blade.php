@extends('layouts.mobile')

@section('content')
    <div data-role="page" id="mainPage" data-theme="a">
        <div data-role="header">
            <a href="{{ route('mstorage.index') }}" data-ajax="false" class="ui-btn ui-btn-left ui-icon-home ui-btn-icon-left">返回主页面</a>
            <h1>ERP操作</h1>
        </div>
        <div data-role="content">
            <button id="btnLoadLocs">导入库位表</button>
            <button id="btnUpdateItems">同步库位存储数据</button>
            <button id="btnCheckItems">检查库位存储数据</button>
        </div>
        <div data-role="popup" id="popupOptResp">
            <h4 id="respMsg"></h4>
        </div>
    </div>
@stop

@push('links')
@endpush
@push('scripts')
<script>
    $(document).on("pageshow", function(e) {
        $('#btnLoadLocs').on('click', function () {
            $.ajax({
                url: '{{ route('mstorage.erploadlocs') }}',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                type: "post",
                beforeSend: function () {
                    showLoader();
                },
                complete: function () {
                    hideLoader();
                },
                success: function (data) {
                    $('#respMsg').empty();
                    $('#respMsg').text("New Inserted " + data.new + ", totally " + data.total);
                    $('#popupOptResp').popup();
                    $('#popupOptResp').popup('open');
                    setTimeout(function () {
                        $('#popupOptResp').popup('close');
                    }, 2000);
                }
            });
        });
        $('#btnUpdateItems').on('click', function () {
            $.ajax({
                url: '{{ route('mstorage.erpupdateitems') }}',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                type: "post",
                beforeSend: function () {
                    showLoader();
                },
                complete: function () {
                    hideLoader();
                },
                success: function (data) {
                    $('#respMsg').empty();
                    $('#respMsg').text("Updated successfully!");
                    $('#popupOptResp').popup();
                    $('#popupOptResp').popup('open');
                    setTimeout(function () {
                        $('#popupOptResp').popup('close');
                    }, 2000);
                }
            });
        });
        $('#btnCheckItems').on('click', function () {
            $.ajax({
                url: '{{ route('mstorage.erpcheckitems') }}',
                type: "get",
                beforeSend: function () {
                    showLoader();
                },
                complete: function () {
                    hideLoader();
                },
                success: function (data) {
                    $('#respMsg').empty();
                    $.each(data, function(i, item) {
                        $('#respMsg').append("<p>"+item.goodsno+"-"+item.goodsname+","+item.area+item.line+"-"+item.unit+"-"+item.level+"</p>");
                    });
                    $('#popupOptResp').popup();
                    $('#popupOptResp').popup('open');
                }
            });
        });
    });
    //显示加载器
    function showLoader() {
        //显示加载器.for jQuery Mobile 1.2.0
        $.mobile.loading('show', {
            text: '数据查询中...', //加载器中显示的文字
            textVisible: true, //是否显示文字
            theme: 'a',        //加载器主题样式a-e
            textonly: false,   //是否只显示文字
            html: ""           //要显示的html内容，如图片等
        });
    }

    //隐藏加载器.for jQuery Mobile 1.2.0
    function hideLoader()
    {
        //隐藏加载器
        $.mobile.loading('hide');
    }
</script>
@endpush
