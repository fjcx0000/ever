<?php $__env->startSection('heading'); ?>
    <h1>Create Client</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip(); //Tooltip on icons top

            $('.popoverOption').each(function () {
                var $this = $(this);
                $this.popover({
                    trigger: 'hover',
                    placement: 'left',
                    container: $this,
                    html: true
                });
            });
        });
    </script>

    <?php
    $data = Session::get('data');
    ?>

    <?php echo Form::open([
            'url' => '/clients/create/cvrapi'

            ]); ?>

    <div class="form-group">
        <div class="input-group">

            <?php echo Form::text('vat', null, ['class' => 'form-control', 'placeholder' => 'Insert company VAT']); ?>

            <div class="popoverOption input-group-addon"
                 rel="popover"
                 data-placement="left"
                 data-html="true"
                 data-original-title="<span>Only for DK, atm.</span>">?
            </div>

        </div>
        <?php echo Form::submit('Get client info', ['class' => 'btn btn-primary clientvat']); ?>


    </div>

    <?php echo Form::close(); ?>


    <?php echo Form::open([
            'route' => 'clients.store',
            'class' => 'ui-form'
            ]); ?>

    <?php echo $__env->make('clients.form', ['submitButtonText' => Lang::get('client.titles.create')], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo Form::close(); ?>



<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>