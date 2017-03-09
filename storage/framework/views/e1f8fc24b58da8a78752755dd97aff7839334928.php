<?php $__env->startSection('heading'); ?>
    <h1>SKU分配和导出</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div id="pidDiv" class="row form-inline row-align-bottom">
        <div class="form-group col-sm-6 removeleft">
            <?php echo Form::label('product_id', '货号:', ['class' => 'control-label']); ?>

            <?php echo Form::text('product_id', null, ['class' => 'form-control']); ?>

        </div>
        <div class="form-group col-sm-4 group-align-bottom">
            <form id="export-form" action="<?php echo e(route('products.exportskufile')); ?>" method="POST">
            <?php echo Form::button("查询产品清单", ['id' => 'search-form','class' => 'btn btn-info']); ?>

            <?php echo Form::button("分配SKU", ['id' => 'allocateBtn','class' => 'btn btn-primary']); ?>

            <!--
        </div>
        <div class="form-group col-sm-2 group-align-bottom removeright">
        -->
                <?php echo e(csrf_field()); ?>

                <?php echo Form::submit("导出SKU文件", ['id' => 'exportSkuFile','class' => 'btn btn-primary']); ?>

            </form>
        </div>
    </div>

    <table class="table table-hover " id="items-table">
        <thead>
        <tr>
            <th>货号</th>
            <th>产品英文名</th>
            <th>产品中文名</th>
            <th>颜色代码</th>
            <th>颜色中文名称</th>
            <th>颜色英文名称</th>
            <th>尺码</th>
            <th>SKU</th>
        </tr>
        </thead>
    </table>
    </div>

    <?php echo $__env->make('partials.productselect', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(function () {
        var oTable;
        $('#search-form').on('click', function(e) {
        oTable = $('#items-table').DataTable({
            processing: true,
            serverSide: true,
            bRetrieve: true,

            ajax: {
                //type: 'GET',
                url: '<?php echo route('products.data'); ?>',

                data: function(d) {
                    d.products = $('#product_id').val();
                }

            },
            columns: [
                {data: 'product_id', name: 'product_id'},
                {data: 'prd_ename', name: 'prd_ename'},
                {data: 'prd_cname', name: 'prd_cname'},
                {data: 'color_id', name: 'color_id'},
                {data: 'color_ename', name: 'color_ename'},
                {data: 'color_cname', name: 'color_cname'},
                {data: 'size_value', name: 'size_value'},
                {data: 'sku_id', name: 'sku_id'},
            ]
        });
            oTable.draw();
            e.preventDefault();
            return false;
        });
        $('#allocateBtn').on('click',function(e){
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
            });
            $.post('<?php echo e(route('products.allocatesku')); ?>', {'products': $('#product_id').val()}, function(data, status) {
                oTable.draw();
                e.preventDefault();
                BootstrapDialog.show({
                    message: data,
                });
            });
            return false;
        });
        $('#export-form').submit(function(){ //listen for submit event
            $('<input />').attr('type', 'hidden')
                .attr('name', "products")
                .attr('value', $('#product_id').val())
                .appendTo('#export-form');
            return true;
        });
        $('#product_id').focus();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>