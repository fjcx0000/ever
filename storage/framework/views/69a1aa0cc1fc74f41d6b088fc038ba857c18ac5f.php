<?php $__env->startSection('content'); ?>
    <div class="col-lg-12 currenttask">

        <table class="table table-hover">
            <h3><?php echo app('translator')->get('role.headers.roles'); ?></h3>
            <thead>
            <thead>
            <tr>
                <th><?php echo app('translator')->get('role.headers.name'); ?></th>
                <th><?php echo app('translator')->get('role.headers.description'); ?></th>
                <th><?php echo app('translator')->get('role.headers.action'); ?></th>
            </tr>
            </thead>
            <tbody>

            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                <tr>
                    <td><?php echo e($role->display_name); ?></td>
                    <td><?php echo e(Str_limit($role->description, 50)); ?></td>

                    <td>   <?php echo Form::open([
            'method' => 'DELETE',
            'route' => ['roles.destroy', $role->id]
        ]);; ?>

                        <?php if($role->id !== 1): ?>
                            <?php echo Form::submit(Lang::get('role.headers.delete'), ['class' => 'btn btn-danger', 'onclick' => 'return confirm("Are you sure?")']);; ?>

                        <?php endif; ?>
                        <?php echo Form::close();; ?></td>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>

            </tbody>
        </table>

        <a href="<?php echo e(route('roles.create')); ?>">
            <button class="btn btn-success"><?php echo app('translator')->get('role.headers.add_new'); ?>e</button>
        </a>

    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>