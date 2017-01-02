<div>
<div class="profilepic"><img class="profilepicsize"
  <?php if($contact->image_path != ""): ?>
      src="../images/<?php echo e($companyname); ?>/<?php echo e($contact->image_path); ?>"
  <?php else: ?>
      src="../images/default_avatar.jpg"
  <?php endif; ?> />
</div>

<h1><?php echo e($contact->name); ?> (<?php echo e($contact->department->first()->name); ?>) </h1>

         
<!--MAIL-->         
<p> <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> 
               <a href="mailto:<?php echo e($contact->email); ?>"><?php echo e($contact->email); ?></a></p>
<!--Work Phone-->            
<p> <span class="glyphicon glyphicon-headphones" aria-hidden="true"></span> 
               <a href="tel:<?php echo e($contact->work_number); ?>"><?php echo e($contact->work_number); ?></a></p>
               
<!--Personal Phone-->            
<p> <span class="glyphicon glyphicon-phone" aria-hidden="true"></span> 
               <a href="tel:<?php echo e($contact->personal_number); ?>"><?php echo e($contact->personal_number); ?></a></p>
               
<!--Address-->            
<p> <span class="glyphicon glyphicon-home" aria-hidden="true"></span> 
               <?php echo e($contact->address); ?>  </p>
  </div>