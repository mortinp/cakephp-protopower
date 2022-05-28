<?php
if (!isset($do_ajax))
    $do_ajax = false;
if (!isset($form_action) && isset($project_id))
    $form_action = 'add/' . $project_id;

if (!isset($form_action))
    throw new InternalErrorException('Something bad happened loading element power_source_form!!!');
?>

<div>
    <div id='power_source-ajax-message' style='/*position:absolute*/'></div>
    <?php
    echo $this->Form->create("PowerSource", array('default' => !$do_ajax, 'url' => array('controller' => 'powersources', 'action' => $form_action)));
    ?>
    <fieldset>
    <?php
    echo $this->Form->input("name", array('type' => 'text', 'placeholder'=>'i.e. Facility Transformer'));
    echo $this->Form->input("kva", array('type' => 'text', 'label'=>'KVA'));
    echo $this->Form->input("reactance", array('type' => 'text', 'label' => 'Reactance (%)', 'placeholder'=>'usual value: 5.5'));
    echo $this->Form->input("voltage", array('type' => 'text', 'placeholder'=>'usual values: 230 or 480'/*, 'div' => 'input-prepend', 'between'=>'<span class="add-on">@</span>', 'label'=>false*/));
    //echo $this->Form->input("Isc", array('enabled'=>false));
    echo $this->Form->input('id', array('type' => 'hidden'));
    echo $this->Form->submit('Save');
    echo $this->Form->end();
    ?>
    </fieldset>
</div>


