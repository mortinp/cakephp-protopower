<legend><?php echo __('Edit your profile'); ?></legend>
<div class="row">
    <div class="col-md-4">
        <?php echo $this->Form->create('User'); ?>
        <fieldset>
            <?php
            echo $this->Form->input('display_name', array('label' => 'Your name (for display)', 'type' => 'text'));
            echo $this->Form->input('password', array('placeholder'=>'Empty password means no change', 'required'=>false));
            echo $this->Form->input('id', array('type' => 'hidden'));
            echo $this->Form->input('username', array('type' => 'hidden'));
            echo $this->Form->input('created', array('type' => 'hidden'));
            echo $this->Form->submit(__('Save'));
            ?>
        </fieldset>
        <?php echo $this->Form->end(); ?>
    </div>
</div>