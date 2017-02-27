<div id="pidDiv" class="row form-inline row-align-bottom">
    <div class="form-group col-sm-3 removeleft">
        <?php echo Form::label('product_id', 'Product ID:', ['class' => 'control-label']); ?>

        <?php echo Form::text('product_id',
            isset($data['owners']) ? $data['owners'][0]['product_id'] : null,
            ['class' => 'form-control']); ?>

    </div>
    <div class="form-group col-sm-3 removeleft group-align-bottom">
        <span id="pidChkResult" class="help-block"></span>
    </div>
</div>
<div class="row form-inline">
    <div class="form-group col-sm-6 removeleft">
        <?php echo Form::label('ename', 'English Name:', ['class' => 'control-label']); ?>

        <?php echo Form::text('ename',
             isset($data['ename']) ? $data['ename'] : null,
             ['class' => 'form-control']); ?>

    </div>

    <div class="form-group col-sm-6 removeleft removeright">
        <?php echo Form::label('cname', 'Chinese Name:', ['class' => 'control-label']); ?>

        <?php echo Form::text('cname',
            isset($data['cname']) ? $data['cname'] : null,
            ['class' => 'form-control']); ?>

    </div>
</div>
<div class="row form-inline">
    <div class="form-group col-sm-3 removeleft">
        <?php echo Form::label('brands', 'Brand:', ['class' => 'control-label']); ?>

        <?php echo Form::select('brand_id',
            $brands,
            null,
            ['class' => 'form-control ui search selection top right pointing search-select',
            'id' => 'search-select']); ?>

    </div>
    <div class="form-group col-sm-3 removeleft removeright">
        <?php echo Form::label('Suppliers', 'Supplier:', ['class' => 'control-label']); ?>

        <?php echo Form::select('supplier_id',
            $suppliers,
            null,
            ['class' => 'form-control ui search selection top right pointing search-select',
            'id' => 'search-select']); ?>

    </div>
</div>

<div class="form-group">
    <?php echo Form::label('description', 'Description:', ['class' => 'control-label']); ?>

    <?php echo Form::text('description',
        isset($data['description']) ? $data['description'] : null,
        ['class' => 'form-control']); ?>

</div>

<?php echo Form::submit($submitButtonText, ['class' => 'btn btn-primary']); ?>


<?php $__env->startPush('scripts'); ?>
<script>
    $(function(){
        $("#product_id").focusout(function(){
            var product_id = $('#product_id').val();
            $this = this;

            $.ajax({
                type: 'POST',
                url: '/products/product_exists',
                data: {product_id: product_id},
                success: function(data) {
                    if (data != "EXIST") {
                        $('#pidDiv').removeClass('has-error');
                        $('#pidChkResult').text("产品编号尚未使用");
                    } else {
                        $('#pidDiv').addClass('has-error');
                        $('#pidChkResult').text('产品编号已使用，请检查');
                        $('#product_id').focus();
                    }
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
