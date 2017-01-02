<?php $__env->startSection('content'); ?>
<?php echo Form::open([
        'route' => 'departments.store',
        ]); ?>

<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
<div class="form-group">
    <?php echo Form::label(Lang::get('department.titles.name'), 'Department name:', ['class' => 'control-label']); ?>

    <?php echo Form::text('name', null,['class' => 'form-control']); ?>

</div>

<div class="form-group">
    <?php echo Form::label(Lang::get('department.titles.description'), 'Department description:', ['class' => 'control-label']); ?>

    <?php echo Form::textarea('description', null, ['class' => 'form-control']); ?>

</div>
<?php echo Form::submit(Lang::get('department.titles.create'), ['class' => 'btn btn-primary']); ?>


<?php echo Form::close(); ?>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>