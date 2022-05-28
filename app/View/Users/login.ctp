
<div class="row">
    <div class="col-md-4 col-md-offset-2">
        <?php echo $this->Session->flash('auth'); ?>
        <legend><?php echo __('Login (or ' . $this->Html->link('Sign Up', array('controller' => 'users', 'action' => 'register')) . ')'); ?></legend>
        <?php echo $this->Form->create('User'); ?>
        <fieldset>
            <?php
            echo $this->Form->input('username', array('label' => 'Email', 'type' => 'email'));
            echo $this->Form->input('password');
            echo $this->Form->submit(__('Login'));
            ?>
        </fieldset>
        <?php echo $this->Form->end(); ?>
        <br/>
        <?php echo $this->Html->link('Forgot your password?', array('controller'=>'users', 'action'=>'recover_password'))?>
    </div>
</div>