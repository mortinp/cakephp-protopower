<?php
// Initialize
$powersources = array();
$hasPowerSources = false;
if (isset($project["powersources"]))
    $powersources = $project["powersources"];
if (!empty($powersources)) {
    $currentPowerSource = $this->request->data['PowerSource'];
    $hasPowerSources = true;
} else {
    $this->request->data['Project'] = $project; // TODO: [HACK] Esto es para hacer que cuando se acaba de crear un project, en el formulario se pueda editar.
}
?>
<div class='row'>
    <div class="col-md-3">
        <div class='box'>
            <div id='project-header'>
                <h1>
                    <!--Project: --><span id='project-name-label'><?php echo $project["name"]; ?></span>
                    <small><small><a title="Edit this project" href="#!" class="edit-project">&ndash; Edit</a></small></small>
                </h1>
            </div>
            <div id='project-form' style="display:none">
                <legend>Edit this project or <a href="#!" class="cancel-edit-project"><!--<i class='icon-arrow-up'></i>-->&ndash; cancel</a></legend>
                <?php echo $this->element('project_form', array('do_ajax' => true, 'form_action' => 'edit/' . $project['id'])); ?>
                <br/>
            </div>
            <div class='tools'>
                <!--<div title='Delete this project' class='bouton small'><?php /*echo $this->Html->link("<i class='icon-trash'></i> Delete", array('controller' => 'projects', 'action' => 'remove/' . $project['id']), array('confirm' => 'Are you sure you want to delete this project?', 'class' => 'delete-button', 'escape' => false))*/ ?></div>-->
                <div title='Edit this project' class='bouton'><a href="#!" class="edit-project"><i class='glyphicon glyphicon-pencil'></i> Edit</a></div>
                <div title='Create a new project' class='bouton'><?php echo $this->Html->link("<i class='glyphicon glyphicon-plus-sign'></i> Create new", array('controller' => 'projects', 'action' => 'add'), array('escape' => false)) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-9"></div>
</div>

<br/>

<div class="row">
    
    <div class='col-md-3'>        
        <?php if ($hasPowerSources) : ?>
            <div class='box' id='powersource-header'>
                <legend>Current power source</legend>
                <?php echo $this->element('power_source_form', array('do_ajax' => true, 'form_action' => 'edit/' . $project['id'] . '/' . $currentPowerSource['id'])); ?>    
                <br/>
                <div class='tools'>
                    <div title='Delete this power source' class='bouton'><?php echo $this->Html->link("<i class='glyphicon glyphicon-trash'></i> Delete", array('controller' => 'powersources', 'action' => 'remove/' . $project['id'] . '/' . $currentPowerSource['id']), array('confirm' => 'Are you sure you want to delete this power source?', 'escape' => false)); ?></div>
                    <div title='Create a new power source' class='bouton'><?php echo $this->Html->link("<i class='glyphicon glyphicon-plus-sign'></i> Create new", array('controller' => 'powersources', 'action' => 'add/' . $project['id']), array('escape' => false)); ?></div>
                </div>
            </div>
        <?php else : ?>
            <legend>Enter the power source data</legend>
            <?php echo $this->element('power_source_form', array('do_ajax' => false, 'form_action' => 'add/' . $project['id'])); ?>   
        <?php endif ?>

        <?php if ($hasPowerSources) : ?>
            <?php if (count($powersources) > 1) : ?>
            <br><legend><small>Other power sources in this project</small></legend>
            <ul style='list-style-type:none;margin:10px;padding:0px'>
                <?php foreach ($powersources as $d) : ?>
                    <?php if ($d['id'] != $currentPowerSource['id']) : ?>
                        <li>
                            
                            <div class="col-md-6">
                                <div style="float:left"><i class="glyphicon glyphicon-folder-open" style="margin-left: -20px"></i></div>
                                <a class="list-item" href="<?php echo $this->Html->url(array('controller' => 'projects', 'action' => 'view/' . $project['id'] . '/' . $d['id'])) ?>">
                                    <div class="list-item-ref"><big><?php echo $d['name'] ?></big></div>
                                    <p style="color:#777">
                                        <small>KVA: <?php echo $d['kva']?></small>
                                    </p>
                                </a>
                            </div>
                            
                        </li>
                    <?php endif ?>
                <?php endforeach ?>
            </ul>
            <?php else: ?>
            <br/>
            <div class="text-muted">( No more power sources in this project )</div>
            <?php endif ?>            

            <div style="clear:both;padding-top: 10px"><?php echo $this->Html->link("<i class='glyphicon glyphicon-folder-close'></i> New power source", array('controller' => 'powersources', 'action' => 'add/' . $project['id']), array('escape'=>false)) ?></div>
        <?php endif ?>
    </div>

    <?php if ($hasPowerSources) : ?>
        <div class='col-md-9'><legend>Datablocks</legend>
            <div id="datablocks-outerframe">
                <div id="datablocks-innerframe" ><!-- Datablocks are generated here --></div>
            </div>
        </div>
    <?php endif ?>
    
</div>

<?php

// CSS
$this->Html->css('dropzone', null, array('inline' => false));
$this->Html->css('common/toolbars_2', null, array('inline' => false));
$this->Html->css('projects-view', null, array('inline' => false));
//$this->Html->css('dropdatablock', null, array('inline' => false));


// JS
$this->Html->script('jquery', array('inline' => false));
$this->Html->script('dropzone', array('inline' => false));

if (isset($currentPowerSource)) {
    $this->Js->set('project', $project);
    $this->Js->set('current_power_source', $currentPowerSource);
    echo $this->Js->writeBuffer(array('inline' => false));
}

$this->Html->script('common/ajax-forms', array('inline' => false));

$this->Html->script('doubly-linked-list', array('inline' => false));
$this->Html->script('dropdatablock', array('inline' => false));
$this->Html->script('projects-view', array('inline' => false));
?>