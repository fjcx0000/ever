<?php $__env->startSection('content'); ?>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); //Tooltip on icons top

$('.popoverOption').each(function() {
    var $this = $(this);
    $this.popover({
      trigger: 'hover',
      placement: 'left',
      container: $this,
      html: true,
  
    });
});
});
</script>

     

<div class="row">	


</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>