<?php $__env->startSection('heading'); ?>
    <h1>File Upload</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
        <?php echo Form::open(array('route' => 'products.fileupload', 'files'=>true)); ?>

            <div class="row">
                <div class="col-md-4">
                    <?php echo Form::label('filetype', 'Select File Type:', ['class' => 'control-label']); ?>

                    <?php echo e(Form::select('filetype', [
                        null => 'Please select file type',
                        'sku' => 'SKU Record File',
                        'product' => 'Product Excel File'
                    ]) ,['class' => 'form-control']); ?>

                </div>
                <div class="col-md-4">
                    <?php echo Form::file('excel', array('class'=>'form-contrl')); ?>

                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </div>
        <?php echo Form::close(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>