@extends('layouts.master')
@section('heading')
    <h1>订单处理</h1>
@stop

@section('content')
    <div class="row">
    <form class="form-horizontal">
        <div class="form-group col-sm-8 removeleft">
            <div class="row">
                <div class="col-sm-6">
                    {!! Form::label('file_startdate', '订单文件起始日期:', ['class' => 'control-label col-sm-6']) !!}
                    <div class="col-sm-6">
                        {!! Form::text('file_startdate', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-6 form-inline">
                    {!! Form::label('file_enddate', '订单文件截止日期:', ['class' => 'control-label col-sm-6']) !!}
                    <div class="col-sm-6">
                        {!! Form::text('file_enddate', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6 ">
                    {!! Form::label('order_id', '订单号:', ['class' => 'control-label col-sm-6']) !!}
                    <div class="col-sm-6">
                        {!! Form::text('order_id', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-sm-6 ">
                    {!! Form::label('product_id', '货号:', ['class' => 'control-label col-sm-6']) !!}
                    <div class="col-sm-6">
                        {!! Form::text('product_id', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 ">
                    {!! Form::label('isDespatched', '发货状态:', ['class' => 'control-label col-sm-6']) !!}
                    <div class="col-sm-6">
                        {!! Form::select('isDespatched', array(''=>'', 'TRUE'=>'已发货', 'FALSE'=>'未发货')) !!}
                    </div>
                </div>
                <div class="col-sm-6 ">
                    {!! Form::label('check_flag', '核对状态:', ['class' => 'control-label col-sm-6']) !!}
                    <div class="col-sm-6">
                        {!! Form::select('check_flag', array(''=>'', 'N'=>'未核对', '1'=>'核对成功', '2'=>'账单记录不存在', '3'=>'品种或数量不一致')) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 ">
           {!! Form::button("查询", ['id' => 'search-form','class' => 'btn btn-info']) !!}
           {!! Form::button("导入", ['id' => 'fileupload-btn','class' => 'btn btn-primary']) !!}
        </div>
    </form>
    </div>
    <div id="tableDiv" class="row">
        <div id="toolbar">
            <button id="remove" class="btn btn-danger" disabled>
                <i class="glyphicon glyphicon-remove"></i> Delete
            </button>
        </div>
        <table id="ordertable"></table>
    </div>

    @include('partials.productselect')
    @include('partials.fileupload',[
        'filetype' => 'order',
        'uploadurl' => route('smartchannel.importfile'),
    ])

@stop

@push('links')
<link href="{{ URL::asset('css/bootstrap-table.css') }}" rel="stylesheet" type="text/css">
@endpush
@push('scripts')
<script type="text/javascript" src="{{ URL::asset('js/bootstrap-table.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap-table-export.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap-table-editable.js') }}"></script>
<script src="//rawgit.com/hhurz/tableExport.jquery.plugin/master/tableExport.js"></script>
<script src="//rawgit.com/vitalets/x-editable/master/dist/bootstrap3-editable/js/bootstrap-editable.js"></script>
<script>
    $(document).ready(function() {
        $("#file_startdate").bootstrapDatepickr({date_format: "d-m-Y"});
        $("#file_enddate").bootstrapDatepickr({date_format: "d-m-Y"});
    });

    var $table = $('#ordertable'),
        $remove = $('#remove'),
        selections = [];
    function initTable() {
        $table.bootstrapTable({
            method: 'get',
            toolbar: '#toolbar',    //工具按钮用哪个容器
            striped: true,      //是否显示行间隔色
            cache: false,      //是否使用缓存，默认为true，所以一般情况下需要设置一下这个属性（*）
            pagination: true,     //是否显示分页（*）
            sortable: false,      //是否启用排序
            sortOrder: "asc",     //排序方式
            pageNumber:1,      //初始化加载第一页，默认第一页
            pageSize: 20,      //每页的记录行数（*）
            pageList: [10, 25, 50, 100],  //可供选择的每页的行数（*）
            url: "{{route('smartchannel.getorders')}}",//这个接口需要处理bootstrap table传递的固定参数
            //queryParams: queryParams,//前端调用服务时，会默认传递上边提到的参数，如果需要添加自定义参数，可以自定义一个函数返回请求参数
            sidePagination: "server",   //分页方式：client客户端分页，server服务端分页（*）
            //search: true,      //是否显示表格搜索，此搜索是客户端搜索，不会进服务端，所以，个人感觉意义不大
            strictSearch: true,
            //showColumns: true,     //是否显示所有的列
            //showRefresh: true,     //是否显示刷新按钮
            minimumCountColumns: 2,    //最少允许的列数
            clickToSelect: true,    //是否启用点击选中行
            searchOnEnterKey: true,
            detailView: true,
            //detailFormatter: detailFormatter,
            height: getHeight(),
            columns: [
                {
                    field: 'state',
                    checkbox: true,
                    align: 'center',
                    valign: 'middle'
                }, {
                    title: 'ID',
                    field: 'id',
                    align: 'center',
                    valign: 'middle',
                    sortable: true,
                    footerFormatter: totalTextFormatter
                }, {
                    field: 'file_date',
                    title: '订单文件日期',
                    sortable: true,
                    editable: {
                        type: 'date',
                        format: 'dd-mm-yyyy',
                        clear: false,
                        datepicker: {
                            format: 'dd-mm-yyyy',
                            autoclose: true,
                            language: 'en',
                        }
                    },
                    align: 'center'
                }, {
                    field: 'order_id',
                    title: '订单号',
                    sortable: true,
                    editable: true,
                    footerFormatter: totalNameFormatter,
                    align: 'center'
                }, {
                    field: 'product_id',
                    title: '货号',
                    sortable: true,
                    footerFormatter: totalNameFormatter,
                    align: 'center'
                }, {
                    field: 'color',
                    title: '颜色',
                    sortable: true,
                    footerFormatter: totalNameFormatter,
                    align: 'center'
                }, {
                    field: 'size_value',
                    title: '尺寸',
                    sortable: true,
                    footerFormatter: totalNameFormatter,
                    align: 'center'
                }, {
                    field: 'qty',
                    title: '数量',
                    sortable: true,
                    editable: {
                        type: 'text',
                        validate: function(value) {
                            if(parseInt(value) != value) {
                                return "必须是整数";
                            } else if (value == 0) {
                                return "不能为0";
                            }
                        },
                    },
                    footerFormatter: totalNameFormatter,
                    align: 'center'
                }, {
                    field: 'isDespatched',
                    title: '发货标志',
                    sortable: true,
                    editable: {
                        type: "select",
                        source: [{value: 0, text: "未发货"}, {value: 1, text: "已发货"}],
                        mode: "popup",
                        validate: function(value) {
                            if (!$.trim(value)) {
                                return "不能为空";
                            }
                        }
                    },
                    footerFormatter: totalNameFormatter,
                    align: 'center'
                }, {
                    field: 'check_flag',
                    title: '核对标志',
                    sortable: true,
                    editable: {
                        type: "select",
                        source: [
                            {value: 0, text: "未核对"},
                            {value: 1, text: "已核对"},
                            {value: 2, text: "账单记录不存在"},
                            {value: 3, text: "数量不一致"},
                        ],
                        mode: "popup",
                        validate: function(value) {
                            if (!$.trim(value)) {
                                return "不能为空";
                            }
                        }
                    },
                    footerFormatter: totalNameFormatter,
                    align: 'center'
                }
            ],
            queryParams : function (params) {
            var temp = {   //这里的键的名字和控制器的变量名必须一直，这边改动，控制器也需要改成一样的
                limit: params.limit,   //页面大小
                offset: params.offset,  //页码
                file_startdate: $("#file_startdate").val(),
                file_enddate: $("#file_enddate").val(),
                order_id: $("#order_id").val(),
                product_id: $("#product_id").val(),
                maxrows: params.limit,
                pageindex:params.pageNumber,
                isDespatched:$('#isDespatched').val(),
                check_flag:$('#check_flag').val(),
            };
            return temp;
            },
            onEditableSave: function(field, row, oldValue, $el) {
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                $.ajax({
                    url: '{{ route('smartchannel.updateorderfield') }}',
                    type: "post",
                    data: {'id':row['id'], 'fieldname': field, 'fieldvalue':row[field]},
                    success: function(data) {
                        if (!data.result) {
                            BootstrapDialog.show({
                                type: BootstrapDialog.TYPE_DANGER,
                                message: data.message,
                            });
                        }
                    }
                });
            },
        });
        /*
        setTimeout(function () {
            $table.bootstrapTable('resetView');
        }, 200);
        */
        $table.on('check.bs.table uncheck.bs.table ' +
            'check-all.bs.table uncheck-all.bs.table', function () {
            $remove.prop('disabled', !$table.bootstrapTable('getSelections').length);
            selections = getIdSelections();
        });
        $table.on('expand-row.bs.table', function (e, index, row, $detail) {
                $detail.html('Loading from ajax request...');
                $.get('{{route('smartchannel.getorderdetails')}}', {id: row['id']}, function (res) {
                    if (!res.result) {
                        BootstrapDialog.show({
                            type: BootstrapDialog.TYPE_DANGER,
                            message: data.message,
                        });
                    } else {
                        var html = [];
                        $.each(res.data, function (key, value) {
                         html.push('<p><b>' + key + ':</b> ' + value + '</p>');
                        });
                        $detail.html(html.join(''));
                    }
                });
        });
        /*
        $table.on('all.bs.table', function (e, name, args) {
            console.log(name, args);
        });
        */
        $remove.click(function () {
            var ids = getIdSelections();
            BootstrapDialog.confirm({
                title: 'WARNING',
                message: 'Warning! Drop orders ' + ids.join(',') + ' ?',
                type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                closable: true, // <-- Default value is false
                draggable: true, // <-- Default value is false
                btnCancelLabel: 'Cancel', // <-- Default value is 'Cancel',
                btnOKLabel: 'Confirm', // <-- Default value is 'OK',
                //btnOKClass: 'btn-warning', // <-- If you didn't specify it, dialog type will be used,
                callback: function (result) {
                    if(!result) {
                        return false;
                    }
                    $.ajaxSetup({
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                    });
                    $.ajax({
                        url: '{{ route('smartchannel.removeorders') }}',
                        type: "post",
                        data: {'idlist': ids.join(',')},
                        success: function (resp) {
                            if (resp.result) {
                                BootstrapDialog.show({
                                    type: BootstrapDialog.TYPE_SUCCESS,
                                    message: resp.message,
                                });
                                $table.bootstrapTable('remove', {
                                    field: 'id',
                                    values: ids
                                });
                                $remove.prop('disabled', true);
                            } else {
                                BootstrapDialog.show({
                                    type: BootstrapDialog.TYPE_DANGER,
                                    message: resp.message,
                                });
                            }
                        }
                    });
                },
            });
        });
        $(window).resize(function () {
            $table.bootstrapTable('resetView', {
                height: getHeight()
            });
        });
    }
    function getIdSelections() {
        return $.map($table.bootstrapTable('getSelections'), function (row) {
            return row.id
        });
    }
    function responseHandler(res) {
        $.each(res.rows, function (i, row) {
            row.state = $.inArray(row.id, selections) !== -1;
        });
        return res;
    }

    window.operateEvents = {
        'click .like': function (e, value, row, index) {
            alert('You click like action, row: ' + JSON.stringify(row));
        },
        'click .remove': function (e, value, row, index) {
            $table.bootstrapTable('remove', {
                field: 'id',
                values: [row.id]
            });
        }
    };
    function totalTextFormatter(data) {
        return 'Total';
    }
    function totalNameFormatter(data) {
        return data.length;
    }
    function getHeight() {
        return $(window).height() - $('h1').outerHeight(true);
    }
    $(function () {
        initTable();
        $('#search-form').on('click',function() {
            var opt = {
                url: '{{ route('smartchannel.getorders') }}',
                silent: true,
                query:{
                    file_startdate: $("#file_startdate").val(),
                    file_enddate: $("#file_enddate").val(),
                    order_id: $("#order_id").val(),
                    product_id: $("#product_id").val(),
                    isDespatched:$('#isDespatched').val(),
                    check_flag:$('#check_flag').val(),
                }
            };
            $table.bootstrapTable('refresh', opt);
            return false;
        });
        $('#fileupload-btn').on('click',function() {
            $('#fileupload-popup').modal();
            return false;
        });
    });
</script>
@endpush
