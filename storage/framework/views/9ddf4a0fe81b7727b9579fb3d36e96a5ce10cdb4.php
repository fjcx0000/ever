<div class="form-group">
    <?php echo Form::label('name', 'Name:', ['class' => 'control-label']); ?>

    <?php echo Form::text('name',  
        isset($data['owners']) ? $data['owners'][0]['name'] : null, 
        ['class' => 'form-control']); ?>

</div>

<div class="form-inline">
    <div class="form-group col-sm-6 removeleft">
        <?php echo Form::label('vat', 'Vat:', ['class' => 'control-label']); ?>

        <?php echo Form::text('vat',
            isset($data['vat']) ?$data['vat'] : null,
            ['class' => 'form-control']); ?>

    </div>

    <div class="form-group col-sm-6 removeleft removeright">
        <?php echo Form::label('company_name', 'Company name:', ['class' => 'control-label']); ?>

        <?php echo Form::text('company_name',
            isset($data['name']) ? $data['name'] : null, 
            ['class' => 'form-control']); ?>

    </div>
</div>

<div class="form-group">
    <?php echo Form::label('email', 'Email:', ['class' => 'control-label']); ?>

    <?php echo Form::email('email',
        isset($data['email']) ? $data['email'] : null, 
        ['class' => 'form-control']); ?>

</div>

<div class="form-group">
    <?php echo Form::label('address', 'Address:', ['class' => 'control-label']); ?>

    <?php echo Form::text('address',
        isset($data['address']) ? $data['address'] : null, 
        ['class' => 'form-control']); ?>

</div>

<div class="form-inline">
    <div class="form-group col-sm-4 removeleft">
        <?php echo Form::label('zipcode', 'Zipcode:', ['class' => 'control-label']); ?>

        <?php echo Form::text('zipcode',
             isset($data['zipcode']) ? $data['zipcode'] : null, 
             ['class' => 'form-control']); ?>

    </div>

    <div class="form-group col-sm-8 removeleft removeright">
        <?php echo Form::label('city', 'City:', ['class' => 'control-label']); ?>

        <?php echo Form::text('city',
            isset($data['city']) ? $data['city'] : null,
            ['class' => 'form-control']); ?>

    </div>
</div>

<div class="form-inline">
    <div class="form-group col-sm-6 removeleft">
        <?php echo Form::label('primary_number', 'Primary Number:', ['class' => 'control-label']); ?>

        <?php echo Form::text('primary_number',  
            isset($data['phone']) ? $data['phone'] : null, 
            ['class' => 'form-control']); ?>

    </div>

    <div class="form-group col-sm-6 removeleft removeright">
        <?php echo Form::label('secondary_number', 'Secondary Number:', ['class' => 'control-label']); ?>

        <?php echo Form::text('secondary_number',  
            null, 
            ['class' => 'form-control']); ?>

    </div>
</div>
<div class="form-group">

    <?php echo Form::label('company_type', 'Company type:', ['class' => 'control-label']); ?>

    <?php echo Form::text('company_type',
        isset($data['companydesc']) ? $data['companydesc'] : null,
        ['class' => 'form-control']); ?>

</div>
<div class="form-group">
    <?php echo Form::label('industry', 'Industry:', ['class' => 'control-label']); ?>

    <?php echo Form::select('industry_id',
        $industries,
        null,
        ['class' => 'form-control ui search selection top right pointing search-select',
        'id' => 'search-select']); ?>

</div>


<div class="form-group">
    <?php echo Form::label('user_id', 'Assign user:', ['class' => 'control-label']); ?>

    <?php echo Form::select('user_id', $users, null, ['class' => 'form-control ui search selection top right pointing search-select', 'id' => 'search-select']); ?>


</div>


<?php echo Form::submit($submitButtonText, ['class' => 'btn btn-primary']); ?>