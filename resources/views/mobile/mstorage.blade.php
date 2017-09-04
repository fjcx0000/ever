@extends('layouts.mobile')

@section('content')
    <div data-role="page" id="mainPage" data-theme="a">
        <div data-role="header" data-position="fixed">
            <h1>库位巡检</h1>
        </div>
        <div data-role="content">
            <h4>库存位置</h4>
            <div class="ui-grid-b">
                <div class="ui-block-a">
                    <label for="area">区</label>
                    <input type="text" id="area" readonly="yes" value="A">
                </div>
                <div class="ui-block-b">
                    <label for="line">列</label>
                    <input type="text" id="line" readonly="yes" value="01">
                </div>
                <div class="ui-block-c">
                    <label for="unit">间</label>
                    <input type="text" id="unit" readonly="yes" value="3">
                </div>
            </div>
            <h4>存放物品</h4>
            <ul data-role="listview" data-inset="true" id="levels">
                <li data-role="list-divider">第1层</li>
                <li>
                    <a>
                        11602-2 Pieces Scuff-Chestnut
                    </a>
                    <a href="#" data-icon="delete">Delete</a>
                </li>
                <li>
                    <a>
                        11602-2 Pieces Scuff-Pink
                    </a>
                    <a href="#" data-icon="delete">Delete</a>
                </li>
            </ul>
        </div>
        <div data-role="popup" id="itemcontent">
        </div>
        <div data-role="popup" id="confirm-itemdel">
            <div role="main" class="ui-content">
                <h3 id="notice-itemdel"></h3>
                <div style="display:none;" id="item-id"></div>
                <div align="center">
                    <a data-role="button" data-rel="back" onclick="deleteitem();" data-inline="true" class="ui-btn-active">确认</a>
                    <a data-role="button" data-rel="back" data-inline="true">取消</a>
                </div>
            </div>
        </div>
        <div data-role="popup" id="locInput" data-dismissible="false" class="ui-content">
            <h4>库位选择：</h4>
            <div data-role="fieldcontain">
                <table>
                    <tr>
                        <td>
                            <label for="areainput">区：</label>
                        </td>
                        <td>
                            <select name="areainput" id="areainput">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="lineinput">列：</label>
                        </td>
                        <td>
                            <select name="lineinput" id="lineinput">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="unitinput">间：</label>
                        </td>
                        <td>
                            <select name="unitinput" id="unitinput">
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="text-align: center;">
                <a href="#" data-role="button" data-ajax="false" data-rel="back" data-transition="flow"
                   onclick="setLocation();" data-theme="c">跳转</a>
            </div>
        </div>
        <div data-role="footer" data-position="fixed" data-id="footernav" data-theme="a">
            <div data-role="navbar">
                <ul>
                    <li><button id="nextPosi">下一库位</button></li>
                    <li><button id="gotoPosi">跳转位置</button></li>
                    <li><button id="addItem">增加物品</button></li>
                </ul>
            </div>
        </div>
    </div>
@stop

@push('links')
@endpush
@push('scripts')
<script>
    $(document).on('pageinit', function(event){
        $.ajax({
            url: '{{ route('mstorage.firstloc') }}',
            type: "get",
            success: function(data) {
                loadLocdata(data);
            }
        });
    });
    $('#nextPosi').on('click',function(event) {
        $.ajax({
            url: '{{ route('mstorage.nextloc') }}',
            type: "get",
            data: {'area':$('#area').val(), 'line':$('#line').val(), 'unit':$('#unit').val()},
            beforeSend: function () {
                showLoader();
            },
            complete:function(){
                hideLoader();
            },
            success: function(data) {
                loadLocdata(data);
            }
        });
    });
    $('#gotoPosi').on('click', function(event){
        $('#locInput').popup();
        $('#locInput').popup('open');
        $('#areainput').empty();
        $('#areainput').selectmenu('refresh');
        $('#lineinput').empty();
        $('#lineinput').selectmenu('refresh');
        $('#unitinput').empty();
        $('#unitinput').selectmenu('refresh');
        $.ajax({
            url: '{{ route('mstorage.arealist') }}',
            type: "get",
            success: function(data) {
                $('#areainput').append("<option></option>");
                $.each(data, function(i, value){
                    $('#areainput').append("<option value='"+value.area+"'>"+value.area+"</option>");
                });
                $('#areainput').attr("selectedIndex",0);
                $('#areainput').selectmenu('refresh');
            }
        });
    });
    $('#areainput').change(function(){
        $.ajax({
            url: '{{ route('mstorage.linelist') }}',
            type: "get",
            data: {'area': $('#areainput').val()},
            success: function(data) {
                $('#lineinput').append("<option></option>");
                $.each(data, function(i, value){
                    $('#lineinput').append("<option value='"+value.line+"'>"+value.line+"</option>");
                });
                $('#lineinput').attr("selectedIndex",0);
                $('#lineinput').selectmenu('refresh');
            }
        });
    });
    $('#lineinput').change(function(){
        $.ajax({
            url: '{{ route('mstorage.unitlist') }}',
            type: "get",
            data: {'area': $('#areainput').val(), 'line': $('#lineinput').val()},
            success: function(data) {
                $('#unitinput').append("<option></option>");
                $.each(data, function(i, value){
                    $('#unitinput').append("<option value='"+value.unit+"'>"+value.unit+"</option>");
                });
                $('#unitinput').attr("selectedIndex",0);
                $('#unitinput').selectmenu('refresh');
            }
        });

    });

    function loadLocdata(locdata)
    {
        $('#area').val(locdata.area);
        $('#line').val(locdata.line);
        $('#unit').val(locdata.unit);
        $('#levels').empty();
        $.each(locdata.levels, function(i, level) {
            $('#levels').append("<li data-role='list-divider'>第"+level.level+"层</li>");
            $.each(level.items, function(i, item){
                if (item.comments == null) item.comments = " ";
                itemcontent=item.goodsno+"-"+item.goodsname+"-"+item.colordesc+". "+item.comments;
                $('#levels').append("<li><a onclick='showItemContent(\""+itemcontent+"\")'>"+itemcontent+
                "</a><a data-icon='delete' onclick='confirmItemdelete(\""+item.id+"\",\""+itemcontent+"\")'>Delete</a></li>");
            });
        });
        $('#levels').listview('refresh');
    }
    function setLocation()
    {
        $('#area').val($('#areainput').val());
        $('#line').val($('#lineinput').val());
        $('#unit').val($('#unitinput').val());
        refreshLocation();
    }
    function showItemContent(itemcontent)
    {
        $('#itemcontent').empty();
        $('#itemcontent').append("<p>"+itemcontent+"</p>");
        $('#itemcontent').popup();
        $('#itemcontent').popup('open');
    }
    function confirmItemdelete(itemid,itemcontent)
    {
        $("#notice-itemdel").empty();
        $("#notice-itemdel").text("删除"+itemcontent+"？");
        $("#item-id").empty();
        $("#item-id").text(itemid);
        $('#confirm-itemdel').popup();
        $('#confirm-itemdel').popup('open');
    }
    function deleteitem()
    {
        $.ajax({
            url: '{{ route('mstorage.deleteitem') }}',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: "post",
            data: {'id': $('#item-id').text()},
            success: function() {
                refreshLocation();
            }
        });

    }

    function refreshLocation()
    {
        $.ajax({
            url: '{{ route('mstorage.locdata') }}',
            type: "get",
            data: {'area': $('#area').val(), 'line': $('#line').val(), 'unit': $('#unit').val()},
            beforeSend: function () {
                showLoader();
            },
            complete:function(){
                hideLoader();
            },
            success: function(data) {
                loadLocdata(data);
            }
        });
    }

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
