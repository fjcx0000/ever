<?php $__env->startSection('heading'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div id="pidDiv" class="row form-inline row-align-bottom">
        <div class="form-group col-sm-6 removeleft">
            <?php echo Form::label('product_id', 'Product ID:', ['class' => 'control-label']); ?>

            <?php echo Form::text('product_id', null, ['class' => 'form-control']); ?>

        </div>
        <div class="form-group col-sm-3 removeright group-align-bottom">
            <?php echo Form::button("Enquiry", ['id' => 'search-form','class' => 'btn btn-primary']); ?>

        </div>
    </div>
    <div class="row form-inline">
        <div class="form-group col-sm-6 removeleft">
            <?php echo Form::label('ename', 'English Name:', ['class' => 'control-label']); ?>

            <?php echo Form::text('ename', null, ['class' => 'form-control']); ?>

        </div>

        <div class="form-group col-sm-6 removeleft removeright">
            <?php echo Form::label('cname', 'Chinese Name:', ['class' => 'control-label']); ?>

            <?php echo Form::text('cname', null, ['class' => 'form-control']); ?>

        </div>
    </div>

    <table class="table table-hover " id="products-table">
        <thead>
        <tr>
            <th><?php echo app('translator')->get('product.headers.product_id'); ?></th>
            <th><?php echo app('translator')->get('product.headers.cname'); ?></th>
            <th><?php echo app('translator')->get('product.headers.ename'); ?></th>
            <th><?php echo app('translator')->get('product.headers.brand'); ?></th>
            <th><?php echo app('translator')->get('product.headers.supplier'); ?></th>
            <th><?php echo app('translator')->get('product.headers.updated_at'); ?></th>
            <th></th>
            <th></th>
        </tr>
        </thead>
    </table>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(function () {
        var oTable = $('#products-table').DataTable({
            processing: true,
            serverSide: true,

            ajax: {
                //type: 'GET',
                url: '<?php echo route('products.data'); ?>',

                data: function(d) {
                    d.product_id = $('#product_id').val();
                    d.ename = $('#ename').val();
                    d.cname = $('#cname').val();
                }

            },
            columns: [

                {data: 'product_id', name: 'product_id'},
                {data: 'cname', name: 'cname'},
                {data: 'ename', name: 'ename'},
                {data: 'brand', name: 'brand'},
                {data: 'supplier_name', name: 'supplier'},
                {data: 'updated_at', name: 'updated_at'},
                <?php if(Entrust::can('client-update')): ?>   
                { data: 'edit', name: 'edit', orderable: false, searchable: false},
                <?php endif; ?>
                <?php if(Entrust::can('client-delete')): ?>   
                { data: 'delete', name: 'delete', orderable: false, searchable: false},
                <?php endif; ?>

            ]
        });

        $('#search-form').on('click', function(e) {
            oTable.draw();
            e.preventDefault();
        })
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>