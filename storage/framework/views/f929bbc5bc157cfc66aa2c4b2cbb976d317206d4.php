<?php $__env->startSection('heading'); ?>
    <h1>账单处理</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <form class="form-horizontal">
            <div class="form-group col-sm-8 removeleft">
                <div class="row">
                    <div class="col-sm-6">
                        <?php echo Form::label('start_date', '账单起始日期:', ['class' => 'control-label col-sm-6']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::text('start_date', null, ['class' => 'form-control']); ?>

                        </div>
                    </div>
                    <div class="col-sm-6 form-inline">
                        <?php echo Form::label('end_date', '账单截止日期:', ['class' => 'control-label col-sm-6']); ?>

                        <div class="col-sm-6">
                            <?php echo Form::text('end_date', null, ['class' => 'form-control']); ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 ">
                <?php echo Form::button("查询", ['id' => 'search-form','class' => 'btn btn-info']); ?>

                <?php echo Form::button("导入", ['id' => 'fileupload-btn','class' => 'btn btn-primary']); ?>

            </div>
        </form>
    </div>
    <div id="tableDiv" class="row">
        <div id="toolbar">
            <button id="remove" class="btn btn-danger" disabled>
                <i class="glyphicon glyphicon-remove"></i> Delete
            </button>
            <button id="check" class="btn btn-info" disabled>
                <i class="glyphicon glyphicon-check"></i> Check
            </button>
        </div>
        <table id="payfiletable"></table>
    </div>

    <?php echo $__env->make('partials.fileupload',[
        'filetype' => 'payment',
        'uploadurl' => route('smartchannel.importfile'),
    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


    <div class="modal fade" id="paylist-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width:auto">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">账单明细</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-hover " id="paylist-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>SC_SKU</th>
                            <th>SKU</th>
                            <th>SC_ORDER_ID</th>
                            <th>DATE</th>
                            <th>QTY</th>
                            <th>PRICE</th>
                            <th>AMOUNT</th>
                            <th>CHECK_FLAG</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('links'); ?>
<link href="<?php echo e(URL::asset('css/bootstrap-table.css')); ?>" rel="stylesheet" type="text/css">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript" src="<?php echo e(URL::asset('js/bootstrap-table.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(URL::asset('js/bootstrap-table-export.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(URL::asset('js/bootstrap-table-editable.js')); ?>"></script>
<script src="//rawgit.com/hhurz/tableExport.jquery.plugin/master/tableExport.js"></script>
<script src="//rawgit.com/vitalets/x-editable/master/dist/bootstrap3-editable/js/bootstrap-editable.js"></script>
<script>
    $(document).ready(function() {
        $("#start_date").bootstrapDatepickr({date_format: "d-m-Y"});
        $("#end_date").bootstrapDatepickr({date_format: "d-m-Y"});
    });

    var $table = $('#payfiletable'),
        $remove = $('#remove'),
        $check = $('#check'),
        file_id,
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
            url: "<?php echo e(route('smartchannel.getpayfiles')); ?>",//这个接口需要处理bootstrap table传递的固定参数
            //queryParams: queryParams,//前端调用服务时，会默认传递上边提到的参数，如果需要添加自定义参数，可以自定义一个函数返回请求参数
            sidePagination: "server",   //分页方式：client客户端分页，server服务端分页（*）
            //search: true,      //是否显示表格搜索，此搜索是客户端搜索，不会进服务端，所以，个人感觉意义不大
            strictSearch: true,
            //showColumns: true,     //是否显示所有的列
            //showRefresh: true,     //是否显示刷新按钮
            minimumCountColumns: 2,    //最少允许的列数
            //clickToSelect: true,    //是否启用点击选中行
            searchOnEnterKey: true,
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
                    field: 'start_date',
                    title: '账单起始日期',
                    sortable: true,
                    editable: {
                        type: 'date',
                        format: 'yyyy-mm-dd',
                        clear: false,
                        datepicker: {
                            format: 'yyyy-mm-dd',
                            autoclose: true,
                            language: 'en',
                        }
                    },
                    footerFormatter: totalNameFormatter,
                    align: 'center'
                }, {
                    field: 'end_date',
                    title: '账单截止日期',
                    sortable: true,
                    editable: {
                        type: 'date',
                        format: 'yyyy-mm-dd',
                        clear: false,
                        datepicker: {
                            format: 'yyyy-mm-dd',
                            autoclose: true,
                            language: 'en',
                        }
                    },
                    footerFormatter: totalNameFormatter,
                    align: 'center'
                }, {
                    field: 'filename',
                    title: '账单文件名',
                    sortable: true,
                    editable: true,
                    footerFormatter: totalNameFormatter,
                    align: 'center'
                }, {
                    field: 'rec_number',
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
                    field: 'check_flag',
                    title: '核对标志',
                    sortable: true,
                    editable: {
                        type: "select",
                        source: [{value: 0, text: "未核对"}, {value: 1, text: "已核对"}],
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
                    field: 'paylist',
                    title: '账单明细',
                    align: 'center',
                    formatter: paylistFormatter,
                }
            ],
            queryParams : function (params) {
                var temp = {   //这里的键的名字和控制器的变量名必须一直，这边改动，控制器也需要改成一样的
                    limit: params.limit,   //页面大小
                    offset: params.offset,  //页码
                    start_date: $("#start_date").val(),
                    end_date: $("#end_date").val(),
                    maxrows: params.limit,
                    pageindex:params.pageNumber,
                };
                return temp;
            },
            onEditableSave: function(field, row, oldValue, $el) {
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
                });
                $.ajax({
                    url: '<?php echo e(route('smartchannel.updatepayfilefield')); ?>',
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
            $check.prop('disabled', !$table.bootstrapTable('getSelections').length);
            selections = getIdSelections();
        });
        /*
        $table.on('expand-row.bs.table', function (e, index, row, $detail) {
            $detail.html('Loading from ajax request...');
            $.get('<?php echo e(route('smartchannel.getorderdetails')); ?>', {id: row['id']}, function (res) {
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
        */
        /*
         $table.on('all.bs.table', function (e, name, args) {
         console.log(name, args);
         });
         */
        $remove.click(function () {
            var ids = getIdSelections();
            BootstrapDialog.confirm({
                title: 'WARNING',
                message: 'Warning! Drop payment files ' + ids.join(',') + ' ?',
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
                        headers: {'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'}
                    });
                    $.ajax({
                        url: '<?php echo e(route('smartchannel.removepayfiles')); ?>',
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
        $check.click(function () {
            var ids = getIdSelections();
            BootstrapDialog.show({
                title: '账单核对',
                message: function(dialog) {
                    var message;
                    if (ids.length > 1) {
                        dialog.enableButtons(false);
                        message = '每次只能核对一个账单文件，请退出重新选择';
                    } else {
                        message= '准备核对';
                    }
                    return message;
                },
                buttons: [{
                    icon: 'glyphicon glyphicon-send',
                    label: '核对',
                    cssClass: 'btn-primary',
                    autospin: true,
                    action: function(dialogRef){
                        var $button = this;
                        if (ids.length > 1) {
                            $button.disable();
                            dialogRef.getModalBody().html('每次只能核对一个账单文件，请退出重新选择');
                        }
                        $.ajax({
                            url: '<?php echo e(route('smartchannel.checkpayrecords')); ?>',
                            type: "post",
                            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
                            data: {'file_id':ids[0]},
                            success: function(resp) {
                                $button.stopSpin();
                                if (resp.result) {
                                    var html = [];
                                    for(var item in resp.data) {
                                        html.push('<p><b>' + item + ':</b> ' + resp.data[item] + '</p>');
                                    }
                                    html.join('');
                                    dialogRef.getModalBody().html(html);
                                } else {
                                    BootstrapDialog.show({
                                        type: BootstrapDialog.TYPE_DANGER,
                                        message: resp.message,
                                    });
                                }
                            }
                        });
                    }
                }, {
                    label: '退出',
                    action: function(dialogRef){
                        dialogRef.close();
                    }
                }]
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
    function paylistFormatter(value, row, index) {
        return [
            '<button onclick=\"showPaylist(\''+value+'\')\">账单明细</button>',
        ].join('');
    }
    function showPaylist(id) {
        file_id = id;
        $('#paylist-popup').modal();
    }
    function getHeight() {
        return $(window).height() - $('h1').outerHeight(true);
    }
    $(function () {
        initTable();
        $('#search-form').on('click',function() {
            var opt = {
                url: '<?php echo e(route('smartchannel.getpayfiles')); ?>',
                silent: true,
                query:{
                    file_startdate: $("#start_date").val(),
                    file_enddate: $("#end_date").val(),
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
    $(function(){
        $('#paylist-popup').on('shown.bs.modal', function(){
            var oTable = $('#paylist-table').DataTable({
                processing: true,
                serverSide: true,
                bRetrieve: true,

                ajax: {
                    //type: 'GET',
                    url: '<?php echo route('smartchannel.getpaylist'); ?>',

                    data: function(d) {
                        d.file_id = file_id;
                    }

                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'sc_sku', name: 'sc_sku'},
                    {data: 'international_sku', name: 'international_sku'},
                    {data: 'source_order_id', name: 'source_order_id'},
                    {data: 'date', name: 'date'},
                    {data: 'qty', name: 'qty'},
                    {data: 'price', name: 'price'},
                    {data: 'amount', name: 'amount'},
                    {data: 'check_flag', name: 'check_flag'},
                ]
            });
            oTable.draw();
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>