<div class="row">
    <div class="col-md-4 col-md-offset-2">
        <?php echo $this->Session->flash('auth'); ?>
        <legend><?php echo __('Sign Up (or ' . $this->Html->link('Login', array('controller' => 'users', 'action' => 'login')) . ')'); ?></legend>
        <?php echo $this->Form->create('User', array('controller' => 'users', 'action' => 'register')); ?>
        <fieldset>
            <?php
            echo $this->Form->input('username', array('label' => 'Email', 'type' => 'email'));
            echo $this->Form->input('password');
            echo $this->Form->submit(__('Sign Up'));
            ?>
        </fieldset>
        <?php echo $this->Form->end(); ?>
    </div>
    <div class="col-md-4 col-md-offset-1">
        <legend>Collaborate by doing any of these:</legend>
        <ul style="list-style-type: none">
            <li style="padding-bottom: 15px"><i class="glyphicon glyphicon-share" style="margin-left: -20px"></i> Share your analysis links with other experts</li>
            <li style="padding-bottom: 15px"><i class="glyphicon glyphicon-comment" style="margin-left: -20px"></i> Comment on other people's analyses, or invite them to comment on yours</li>
            <li style="padding-bottom: 15px"><i class="glyphicon glyphicon-tags" style="margin-left: -20px"></i> Tag your analyses with keywords that indicate your analyses highlights</li>
            <li style="padding-bottom: 15px"><i class="glyphicon glyphicon-search" style="margin-left: -20px"></i> Find other people's analyses using tags or keywords</li>
        </ul>
    </div>
</div>