<?php 
if(!isset($do_ajax)) $do_ajax = false;
if(!isset($form_action)) $form_action = 'add';
if(!isset($style)) $style = '';
if (!isset($is_modal)) $is_modal = false;

$buttonStyle = '';
if($is_modal) $buttonStyle = 'display:inline-block;float:left';
?>

<div>
    <div id='project-ajax-message' style='/*position:absolute*/'></div>
    <?php
    echo $this->Form->create('Project', array('default'=>!$do_ajax, 'url'=>array('controller'=>'projects', 'action'=>$form_action), 'style'=>$style));
    ?>
    <fieldset>
    <?php
    echo $this->Form->input("name", array('type'=>'text', 'placeholder'=>'i.e. the name of the facility you are analysing'));
    echo $this->Form->input("description", array('type'=>'textarea', 'placeholder'=>'i.e. details about the facility, or the project itself'));
    echo $this->Form->input('id', array('type' => 'hidden'));
    echo $this->Form->submit("Save", array('style'=>$buttonStyle));
    if($is_modal) echo $this->Form->button(__('Cancel'), array('id'=>'btn-cancel-project', 'style'=>'display:inline-block'));
    ?>
    </fieldset>
    <?php echo $this->Form->end();?>
</div>