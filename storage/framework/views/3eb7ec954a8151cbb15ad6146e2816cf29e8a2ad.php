<?php $__env->startSection('heading'); ?>
    <h1><?php echo app('translator')->get('setting.headers.settings'); ?></h1>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <table class="table table-responsive table-hover table_wrapper" id="clients-table">
            <thead>
            <tr>
                <th></th>
                <?php $__currentLoopData = $permission; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                    <th><?php echo e($perm->display_name); ?></th>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                <th></th>
            </tr>


            </thead>
            <tbody>

            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                <tr>
                    <div class="col-lg-4">
                        <?php echo Form::model($permission, [
                        'method' => 'PATCH',
                        'url'    => 'settings/permissionsUpdate',
                        ]); ?>


                        <th><?php echo e($role->display_name); ?></th>


                        <input type="hidden" name="role_id" value="<?php echo e($role->id); ?>"/>
                        <?php $__currentLoopData = $permission; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <?php $isEnabled = !current(
                                    array_filter(
                                            $role->permissions->toArray(),
                                            function ($element) use ($perm) {
                                                return $element['id'] === $perm->id;
                                            }
                                    )
                            );  ?>

                            <td><input type="checkbox"
                                       <?php if (!$isEnabled) echo 'checked' ?> name="permissions[ <?php echo e($perm->id); ?> ]"
                                       value="1">
                                <span class="perm-name"></span><br/></td>

                
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                    </div>
                
        
    <td><?php echo Form::submit(Lang::get('setting.headers.save_role'), ['class' => 'btn btn-primary']); ?></td>
    <?php echo Form::close(); ?>

    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
    </tbody>
    </table>
</div>



    <div class="row">
        <div class="col-lg-12">
            <div class="sidebarheader movedown"><p><?php echo app('translator')->get('setting.headers.overall'); ?></p></div>


            <?php echo Form::model($settings, [
               'method' => 'PATCH',
               'url' => 'settings/overall'
               ]); ?>


                    <!-- *********************************************************************
     *                     Task complete       
     *********************************************************************-->
            <div class="panel panel-default movedown">
                <div class="panel-heading"><?php echo app('translator')->get('setting.overall.task.completion'); ?></div>
                <div class="panel-body">

                    <?php echo app('translator')->get('setting.overall.task.completion_allowed'); ?> <br/>
                    <?php echo app('translator')->get('setting.overall.task.completion_not_allowed'); ?>
                </div>
            </div>
            <?php echo Form::select('task_complete_allowed', [1 => Lang::get('setting.headers.allowed'), 2 => Lang::get('setting.headers.not_allowed')], $settings->task_complete_allowed, ['class' => 'form-control']); ?>

                    <!-- *********************************************************************
     *                     Task assign       
     *********************************************************************-->
            <div class="panel panel-default movedown">
                <div class="panel-heading"><?php echo app('translator')->get('setting.overall.task.assigned'); ?></div>
                <div class="panel-body">

                    <?php echo app('translator')->get('setting.overall.task.assigned_allowed'); ?> <br/>
                    <?php echo app('translator')->get('setting.overall.task.assigned_not_allowed'); ?>
                </div>
            </div>
            <?php echo Form::select('task_assign_allowed', [1 => Lang::get('setting.headers.allowed'), 2 => Lang::get('setting.headers.not_allowed')], $settings->task_assign_allowed, ['class' => 'form-control']); ?>

                    <!-- *********************************************************************
     *                     Lead complete       
     *********************************************************************-->

            <div class="panel panel-default movedown">
                <div class="panel-heading"><?php echo app('translator')->get('setting.overall.lead.completion'); ?></div>
                <div class="panel-body">

                    <?php echo app('translator')->get('setting.overall.lead.completion_allowed'); ?><br/>
                    <?php echo app('translator')->get('setting.overall.lead.completion_not_allowed'); ?>
                </div>
            </div>
            <?php echo Form::select('lead_complete_allowed', [1 => Lang::get('setting.headers.allowed'), 2 => Lang::get('setting.headers.not_allowed')], $settings->lead_complete_allowed, ['class' => 'form-control']); ?>

                    <!-- *********************************************************************
     *                     Lead assign       
     *********************************************************************-->
            <div class="panel panel-default movedown">
                <div class="panel-heading"><?php echo app('translator')->get('setting.overall.lead.assigned'); ?></div>
                <div class="panel-body">

                    <?php echo app('translator')->get('setting.overall.lead.assigned_allowed'); ?><br/>
                    <?php echo app('translator')->get('setting.overall.lead.assigned_not_allowed'); ?>
                </div>
            </div>
            <?php echo Form::select('lead_assign_allowed', [1 => Lang::get('setting.headers.allowed'), 2 => Lang::get('setting.headers.not_allowed')], $settings->lead_assign_allowed, ['class' => 'form-control']); ?>

            <br/>
            <?php echo Form::submit(Lang::get('setting.headers.save_overall'), ['class' => 'btn btn-primary']); ?>

            <?php echo Form::close(); ?>

        </div>
    </div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>