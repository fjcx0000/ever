<?php $__env->startSection('heading'); ?>
    <h1>库存位置管理</h1>
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
            <th>区域</th>
            <th>行</th>
            <th>格</th>
            <th>层</th>
            <th>是否使用</th>
            <th>操作</th>
        </tr>
        </thead>
    </table>
    </div>
    <?php echo $__env->make('partials.fileupload',[
        'filetype' => 'location',
        'uploadurl' => route('storages.uploadlocfile'),
    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

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
                    url: '<?php echo route('storages.getlocations'); ?>',

                    data: function(d) {
                        d.area = $('#area').val();
                        d.line = $('#line').val();
                        d.unit = $('#unit').val();
                        d.level = $('#level').val();
                    }
                },
                columns: [
                    {data: 'code', name: 'code'},
                    {data: 'area', name: 'area'},
                    {data: 'line', name: 'line'},
                    {data: 'unit', name: 'unit'},
                    {data: 'level', name: 'level'},
                    {data: 'isused', name: 'isused'},
                    {data: 'delete', name: 'delete'},
                ]
            });
            oTable.draw();
            e.preventDefault();
            return false;
        });
        $('#addBtn').on('click',function(e){
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
            });
            $.ajax({
                url: '<?php echo e(route('storages.addlocation')); ?>',
                type: "post",
                data: {'area':$('#area').val(), 'line':$('#line').val(), 'unit':$('#unit').val(), 'level':$('#level').val()},
                success: function(data) {
                    if (oTable != null) {
                        oTable.draw();
                        e.preventDefault();
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
        $('#fileupload-btn').on('click',function() {
            $('#fileupload-popup').modal();
            return false;
        });
    });
    function del_location(id, code) {
        BootstrapDialog.confirm({
            title: 'WARNING',
            message: 'Warning! Drop location '+code+' ?',
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
                    url: '<?php echo e(route('storages.dellocation')); ?>',
                    type: "post",
                    data: {'id':id, 'code':code},
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