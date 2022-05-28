<div class="row">
    <div class="col-md-4 col-md-offset-2">
        <?php echo $this->Session->flash('auth'); ?>
        <legend>
            <div><?php echo __('Recover your password')?></div>
            <small class="text-muted"><small>Give us your email and we'll send a new password to your inbox</small></small>
        </legend>
        <?php echo $this->Form->create('User'); ?>
        <fieldset>
            <?php
            echo $this->Form->input('username', array('label' => 'Email', 'type' => 'email'));
            echo $this->Form->submit(__('Recover password'));
            ?>
        </fieldset>
        <?php echo $this->Form->end(); ?>
    </div>
</div>