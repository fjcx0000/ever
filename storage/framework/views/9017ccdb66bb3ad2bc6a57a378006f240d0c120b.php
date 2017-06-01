<?php $__env->startSection('heading'); ?>
    <h1>库存物品管理</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div id="pidDiv" class="row form-inline row-align-bottom">
        <div class="form-group col-sm-6 removeleft">
            <?php echo Form::label('product_id', '货号:', ['class' => 'control-label']); ?>

            <?php echo Form::text('product_id', null, ['class' => 'form-control']); ?>

        </div>
        <div class="form-group col-sm-4 group-align-bottom">
            <?php echo Form::button("查询", ['id' => 'search-form','class' => 'btn btn-info']); ?>

            <?php echo Form::button("增加", ['id' => 'addItem','class' => 'btn btn-primary']); ?>

            <?php echo Form::button("导入产品数据", ['id' => 'loadData','class' => 'btn btn-primary']); ?>

        </div>
    </div>

    <table class="table table-hover " id="items-table">
        <thead>
        <tr>
            <th>货号</th>
            <th>产品英文名</th>
            <th>颜色代码</th>
            <th>颜色英文名称</th>
            <th>尺码</th>
            <th>库存位置</th>
            <th>操作</th>
        </tr>
        </thead>
    </table>
    </div>

    <?php echo $__env->make('partials.productselect', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <div class="modal fade" id="additem-popup" tabindex="-1" role="dialog" aria-labelledby="addItemLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="addItemLabel">增加库存物品</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <label for="add_prd_id" class="col-sm-2 control-label">货号</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="add_prd_id" placeholder="请输入货号">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="add_prd_name" class="col-sm-2 control-label">名称</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="add_prd_name" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="add_color" class="col-sm-2 control-label">颜色</label>
                                    <div class="col-sm-4">
                                        <?php echo Form::select('add_color',array(''=>''), null, array('id'=>'add_color')); ?>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="add_size" class="col-sm-2 control-label">尺寸</label>
                                    <div class="col-sm-4">
                                        <?php echo Form::select('add_size',array(''=>''), null, array('id'=>'add_size')); ?>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-4">
                                        <button id="addItemConfirm" type="button" class="btn btn-primary">增加</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    var oTable=null;
    $(function () {
        $('#search-form').on('click', function(e) {
        oTable = $('#items-table').DataTable({
            processing: true,
            serverSide: true,
            bRetrieve: true,

            ajax: {
                //type: 'GET',
                url: '<?php echo route('storages.getitems'); ?>',

                data: function(d) {
                    d.products = $('#product_id').val();
                }

            },
            columns: [
                {data: 'product_id', name: 'product_id'},
                {data: 'product_ename', name: 'product_ename'},
                {data: 'color_id', name: 'color_id'},
                {data: 'color_ename', name: 'color_ename'},
                {data: 'size_value', name: 'size_value'},
                {data: 'isused', name: 'isused'},
                {data: 'delete', name: 'delete'},
            ]
        });
            oTable.draw();
            e.preventDefault();
            return false;
        });
        $('#addItem').on('click',function() {
            $('#additem-popup').modal();
            return false;
        });
        $('#add_prd_id').keydown(function (event){
            if (event.keyCode == "13") {
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
                });
                $.get('<?php echo e(route('products.productdetails')); ?>', {'product_id': $('#add_prd_id').val()}, function(data, status) {
                    console.log(data);
                    $('#add_prd_name').val(data.ename);
                    $.each(data.sizes,function(key, value) {
                        $("#add_size").append("<option></option>")
                            .attr("value",value)
                            .text(value);

                    });
                    $('#add_color').find('option').remove();
                    $('<option>').val('').text('').appendTo($('#add_color'));
                    $.each(data.colors, function(key, value) {
                        $('<option>').val(value.color_id).text(value.color).appendTo($('#add_color'));
                    });
                    console.log($('#add_color').html());
                    $('#add_size').find('option').remove();
                    $('<option>').val('').text('').appendTo($('#add_size'));
                    $.each(data.sizes, function(key, value) {
                        $('<option>').val(value).text(value).appendTo($('#add_size'));
                    });

                    //console.log($('#add_size').html());
                });
            }
        });
        $('#addItemConfirm').on('click',function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
            });
            $.ajax({
                url: '<?php echo e(route('storages.additem')); ?>',
                type: "post",
                data: {'product_id':$('#add_prd_id').val(), 'color_id':$('#add_color').val(), 'size_value':$('#add_size').val()},
                success: function(data) {
                    if (oTable != null) {
                        oTable.draw();
                    }
                    if (data.result) {
                        BootstrapDialog.show({
                            type: BootstrapDialog.TYPE_SUCCESS,
                            message: data.message,
                        });
                    } else {
                        BootstrapDialog.show({
                            type: BootstrapDialog.TYPE_DANGER,
                            message: data.message,
                        });
                    }
                }
            });
            return false;
        });
        $('#loadData').on('click',function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
            });
            $.ajax({
                url: '<?php echo e(route('storages.importproductdata')); ?>',
                type: "get",
                success: function(data) {
                    if (oTable != null) {
                        oTable.draw();
                    }
                    if (data.result) {
                        BootstrapDialog.show({
                            type: BootstrapDialog.TYPE_SUCCESS,
                            message: data.message,
                        });
                    } else {
                        BootstrapDialog.show({
                            type: BootstrapDialog.TYPE_DANGER,
                            message: data.message,
                        });
                    }
                }
            });
            return false;
        });
        $('#product_id').focus();
    });
    function del_item(id) {
        BootstrapDialog.confirm({
            title: 'WARNING',
            message: 'Warning! Drop this item ?',
            type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
            closable: true, // <-- Default value is false
            draggable: true, // <-- Default value is false
            btnCancelLabel: 'Do not drop it!', // <-- Default value is 'Cancel',
            btnOKLabel: 'Drop it!', // <-- Default value is 'OK',
            btnOKClass: 'btn-warning', // <-- If you didn't specify it, dialog type will be used,
            callback: function(result) {
                // result will be true if button was click, while it will be false if users close the dialog directly.
                if(!result) {
                    return false;
                }
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
                });
                $.ajax({
                    url: '<?php echo e(route('storages.delitem')); ?>',
                    type: "post",
                    data: {'id':id},
                    success: function(data) {
                        if (oTable != null) {
                            oTable.draw();
                        }
                        if (data.result) {
                            BootstrapDialog.show({
                                type: BootstrapDialog.TYPE_SUCCESS,
                                message: data.message,
                            });
                        } else {
                            BootstrapDialog.show({
                                type: BootstrapDialog.TYPE_DANGER,
                                message: data.message,
                            });
                        }
                    }
                });
                return false;
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>