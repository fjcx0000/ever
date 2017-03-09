<?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
    <option value="<?php echo e($product->product_id); ?>"><?php echo e($product->product_id); ?>-<?php echo e($product->ename); ?></option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>