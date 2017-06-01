<?php $__env->startSection('heading'); ?>
    <h1>库位使用管理</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div id="pidDiv" class="row form-inline row-align-bottom">
        <div class="form-group col-sm-6 removeleft">
            <div class="row" style="border-style:solid">
                <div class="col-sm-3">
                    <?php echo Form::label('area', '区:', ['class' => 'control-label']); ?>

                    <?php echo Form::select('area', array(''=>'', 'A'=>'A区', 'B'=>'B区', 'C'=>'C区', 'D'=>'D区')); ?>

                </div>
                <div class="col-sm-3">
                    <?php echo Form::label('line', '行:', ['class' => 'control-label']); ?>

                    <?php echo Form::select('line',array(''=>'','01'=>'01','02'=>'02','03'=>'03','04'=>'04',
                                    '05'=>'05','06'=>'06','07'=>'07','08'=>'08','09'=>'09','10'=>'10','11'=>'11')); ?>

                </div>
                <div class="col-sm-3">
                    <?php echo Form::label('unit', '格:', ['class' => 'control-label']); ?>

                    <?php echo Form::select('unit',array(''=>'','1'=>'1','2'=>'2','3'=>'3','4'=>'4',
                                    '5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9')); ?>

                </div>
                <div class="col-sm-3">
                    <?php echo Form::label('level', '层:', ['class' => 'control-label']); ?>

                    <?php echo Form::select('level',['','1','2','3']); ?>

                </div>
            </div>
        </div>
        <div class="form-group col-sm-4 ">
            <form id="export-form" action="<?php echo e(route('products.exportskufile')); ?>" method="POST">
                <?php echo Form::button("查询", ['id' => 'search-form','class' => 'btn btn-info']); ?>

                <?php echo Form::button("增加", ['id' => 'addBtn','class' => 'btn btn-primary']); ?>

                <?php echo Form::button("导入", ['id' => 'fileupload-btn','class' => 'btn btn-primary']); ?>

            </form>
        </div>
    </div>

    <table class="table table-hover " id="locations-table">
        <thead>
        <tr>
            <th>库位编号</th>
            <th>货品编号</th>
            <th>货品名称</th>
            <th>颜色</th>
            <th>尺寸</th>
            <th>备注信息</th>
            <th>操作</th>
        </tr>
        </thead>
    </table>
    </div>
    <?php echo $__env->make('partials.fileupload',[
        'filetype' => 'relation',
        'uploadurl' => route('storages.uploadlocfile'),
    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


    <div class="modal fade" id="addlocitem-popup" tabindex="-1" role="dialog" aria-labelledby="addLocitemLabel" aria-hidden="true">
        <?php $__env->startPush('links'); ?>
        <link href="<?php echo e(URL::asset('css/textext.core.css')); ?>" rel="stylesheet" type="text/css">
        <link href="<?php echo e(URL::asset('css/textext.plugin.autocomplete.css')); ?>" rel="stylesheet" type="text/css">
        <?php $__env->stopPush(); ?>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="addLocitemLabel">库位物品关联</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div>
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <label for="add_location" class="col-sm-2 control-label">库位</label>
                                    <div  class="col-sm-6">
                                        <textarea id="add_location" class="form-control" rows="1" placeholder="请输入库位（区-列-单元-层）"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="add_item" class="col-sm-2 control-label">物品</label>
                                    <div  class="col-sm-6">
                                        <textarea id="add_item" class="form-control" rows="1" placeholder="请输入货号"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="add_item" class="col-sm-2 control-label">备注</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="add_comment">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-4">
                                        <button id="addRelationConfirm" type="button" class="btn btn-primary">增加</button>
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
        <?php $__env->startPush('scripts'); ?>
        <script type="text/javascript" src="<?php echo e(URL::asset('js/textext.core.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(URL::asset('js/textext.plugin.ajax.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(URL::asset('js/textext.plugin.autocomplete.js')); ?>"></script>
        <script type="text/javascript">
            $('#add_location')
                .textext({
                    plugins : 'autocomplete ajax',
                    ajax : {
                        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
                        url: '<?php echo e(route('storages.searchlocations')); ?>',
                        dataType : 'json',
                        cacheResults : false
                    }
                })
            ;
            $('#add_item')
                .textext({
                    plugins : 'autocomplete ajax',
                    ajax : {
                        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
                        url: '<?php echo e(route('storages.searchitems')); ?>',
                        dataType : 'json',
                        cacheResults : false
                    }
                })
            ;
            //change style as conflict with bootstrap
            $('div.text-wrap').attr("style","width: 100%");
            $('#add_location').attr("style","width: 100%");
            $('#add_item').attr("style","width: 100%");
            $('#addRelationConfirm').on('click',function() {
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
                });
                $.ajax({
                    url: '<?php echo e(route('storages.addrelation')); ?>',
                    type: "post",
                    data: {'location':$('#add_location').val(),
                        'item':$('#add_item').val(),
                        'comment':$('#add_comment').val()},
                    success: function(data) {
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
        </script>
        <?php $__env->stopPush(); ?>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    var oTable=null;
    $(function () {
        $('#search-form').on('click', function(e) {
            oTable = $('#locations-table').DataTable({
                processing: true,
                serverSide: true,
                bRetrieve: true,

                ajax: {
                    type: 'GET',
                    url: '<?php echo route('storages.getrelations'); ?>',

                    data: function(d) {
                        d.area = $('#area').val();
                        d.line = $('#line').val();
                        d.unit = $('#unit').val();
                        d.level = $('#level').val();
                    }
                },
                columns: [
                    {data: 'code', name: 'code'},
                    {data: 'product_id', name: 'product_id'},
                    {data: 'product_name', name: 'product_name'},
                    {data: 'color', name: 'color'},
                    {data: 'size_value', name: 'size_value'},
                    {data: 'comment', name: 'comment'},
                    {data: 'delete', name: 'delete'},
                ]
            });
            oTable.draw();
            e.preventDefault();
            return false;
        });
        $('#addBtn').on('click',function() {
            $('#addlocitem-popup').modal();
            return false;
        });
        $('#fileupload-btn').on('click',function() {
            $('#fileupload-popup').modal();
            return false;
        });
    });
    function del_relation(id) {
        BootstrapDialog.confirm({
            title: 'WARNING',
            message: 'Warning! Drop relation ?',
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
                    url: '<?php echo e(route('storages.delrelation')); ?>',
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