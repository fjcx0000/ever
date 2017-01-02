<?php $__env->startSection('heading'); ?>
<script>$('#pagination a').on('click', function(e){
    e.preventDefault();
    var url = $('#search').attr('action')+'?page='+page;
    $.post(url, $('#search').serialize(), function(data){
        $('#posts').html(data);
    });
});</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.userheader', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
  <!-- *********************************************************************
     *                 Header end and top task start                   
     *********************************************************************-->
      <div class="row">
          <div class="col-lg-6 currenttask">
          

   <table class="table table-hover" id="opentask-table">
   <h3><?php echo app('translator')->get('task.status.open'); ?></h3>
        <thead>
            <tr>
                
                <th><?php echo app('translator')->get('task.headers.title'); ?></th>
                <th><?php echo app('translator')->get('task.headers.created_at'); ?></th>
                <th><?php echo app('translator')->get('task.headers.deadline'); ?></th>
                
            </tr>
        </thead>
    </table>

             
          </div>
  <!-- *********************************************************************
     *                     Open task end, Closed task start       
     *********************************************************************-->
          <div class="col-lg-6 currenttask">
          

   <table class="table table-hover" id="closedtask-table">
   <h3><?php echo app('translator')->get('task.status.closed'); ?></h3>
        <thead>
            <tr>
                
                <th><?php echo app('translator')->get('task.headers.title'); ?></th>
                <th><?php echo app('translator')->get('task.headers.created_at'); ?></th>
                <th><?php echo app('translator')->get('task.headers.deadline'); ?></th>
                
            </tr>
        </thead>
    </table>

          </div>
  <!-- *********************************************************************
     *               Closed task end assigned clients start    
     *********************************************************************-->


          <div class="col-lg-8 currenttask">
          


              
        
<table class="table table-hover" id="clients-table">
   <h3><?php echo app('translator')->get('client.status.assigned'); ?></h3>
        <thead>
            <tr>
                
                <th><?php echo app('translator')->get('client.headers.name'); ?></th>
                <th><?php echo app('translator')->get('client.headers.company'); ?></th>
                <th><?php echo app('translator')->get('client.headers.primary_number'); ?></th>
              
                
            </tr>
        </thead>
    </table>
   </div>
   <!-- *********************************************************************
 *               assigned clients end, Last 10 created task start    
 *********************************************************************-->

                <div class="col-lg-4 currenttask">
          
                    <table class="table table-hover">
         <h3><?php echo app('translator')->get('task.status.created'); ?></h3>
            <thead>
    <thead>
      <tr>
        <th><?php echo app('translator')->get('task.headers.title'); ?></th>
        <th><?php echo app('translator')->get('task.headers.created_at'); ?></th>
        <th><?php echo app('translator')->get('task.headers.deadline'); ?></th> 
      </tr>
    </thead>
    <tbody>

<?php $__currentLoopData = $user->tasksCreated; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>

       <tr>
<td>
<a href="<?php echo e(route('tasks.show', $task->id)); ?>">
<?php echo e($task->title); ?>

</a> </td>
<td><?php echo e(date('d, M Y', strTotime($task->created_at))); ?> </td>
<td><?php echo e(date('d, M Y', strTotime($task->deadline))); ?></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
              </tbody>
              </table>

          </div>
         

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
$(function() {
    $('#opentask-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo route('users.taskdata', ['id' => $user->id]); ?>',
        columns: [
            
            { data: 'titlelink', name: 'title' },
            { data: 'created_at', name: 'created_at' },
            { data: 'deadline', name: 'deadline' },
        ]
    });
});
</script>

<script>
$(function() {
    $('#clients-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo route('users.clientdata', ['id' => $user->id]); ?>',
        columns: [
            
            { data: 'clientlink', name: 'name' },
            { data: 'company_name', name: 'company_name' },
             { data: 'primary_number', name: 'primary_number' },

        ]
    });
});
</script>
<script>
$(function() {
    $('#closedtask-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo route('users.closedtaskdata', ['id' => $user->id]); ?>',
        columns: [
            
            { data: 'titlelink', name: 'title' },
            { data: 'created_at', name: 'created_at' },
            { data: 'deadline', name: 'deadline' },
        ]
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>