
    <?php echo Form::open(array('route' => 'mobile.convertfile', 'files'=>true)); ?>

    <div class="row">
        <div class="col-md-4">
            <?php echo Form::file('excel', array('class'=>'form-contrl')); ?>

        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-success">Upload</button>
        </div>
    </div>
    <?php echo Form::close(); ?>

