<legend>
    Create a new power source in project 
    <?php echo $this->Html->link($project['name'], array('controller'=>'projects', 'action'=>'view/'.$project['id']));?>
</legend>
<div class="row">
    <div class="col-md-4">        
        <?php echo $this->element('power_source_form', array('project_id'=>$project['id']));?>
    </div>
</div>
