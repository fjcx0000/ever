<?php $__env->startSection('heading'); ?>
    <h1>Processed Successfully</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <a href="<?php echo e(url()->previous()); ?>">Back</a><br/>
    <a href="/">Home</a>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>