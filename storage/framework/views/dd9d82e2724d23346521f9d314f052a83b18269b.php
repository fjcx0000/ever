<?php $__env->startSection('heading'); ?>
    <h1>Create Product</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip(); //Tooltip on icons top

            $('.popoverOption').each(function () {
                var $this = $(this);
                $this.popover({
                    trigger: 'hover',
                    placement: 'left',
                    container: $this,
                    html: true
                });
            });
        });
    </script>
    <?php $__env->stopPush(); ?>

    <?php
    $data = Session::get('data');
    ?>

    <?php echo Form::open([
            'route' => 'products.store',
            'class' => 'ui-form'
            ]); ?>

    <?php echo $__env->make('products.form', ['submitButtonText' => Lang::get('product.titles.create')], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo Form::close(); ?>



<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>