<legend>
    Edit project
    <?php 
        $project = $this->request->data['Project'];
        echo $this->Html->link($project['name'], array('controller'=>'projects', 'action'=>'view/'.$project['id']));
    ?>
</legend>
<div class="row">
    <div class="col-md-4">
        <?php echo $this->element('project_form', array('form_action'=>'edit/'.$project['id'])); ?>
    </div>
</div>
