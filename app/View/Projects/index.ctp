<?php
$user = AuthComponent::user();

if($user['display_name'] != null) {
    $splitName = explode('@', $user['display_name']);
    if(count($splitName) > 1) $pretty_user_name = $splitName[0];
    else $pretty_user_name = $user['display_name'];
} else {
    $splitEmail = explode('@', $user['username']);
    $pretty_user_name = $splitEmail[0];
}
$pretty_user_date = date('M j, Y', strtotime($user['created']));
?>

<div class="row">

    <div class="col-md-2">
        <div>
            <?php echo $this->Html->image('anonymous-user.png', array('alt' => 'Anonymous User')); ?>
            <h4 style="/*margin-left:24px;*/text-align: center"><?php echo $pretty_user_name ?> </h4>
            <h4 style="/*margin-left:24px;*/text-align: center"><small> (joined on <?php echo $pretty_user_date ?>)</small></h4>
        </div>
    </div>
    
    
        <?php if (empty($projects)): ?>
            <div class="col-md-5">        
                <legend><!--You don't have any projects yet. -->Create your first project</legend>
                <?php echo $this->element('project_form') ?>
            </div>
        <?php else: ?>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Your projects</h3>
                    </div>
                    <div class="panel-body">
                        <ul style="list-style-type:none">
                            <?php foreach ($projects as $pro): ?>
                                <li style="clear:both">

                                    <div style="width:80%;float:left">
                                        <div style="float:left"><i class="glyphicon glyphicon-folder-open" style="margin-left: -20px"></i></div>
                                        <a class="list-item" href="<?php echo $this->Html->url(array('controller' => 'projects', 'action' => 'view/' . $pro['id'])) ?>">
                                            <div class="list-item-ref"><big><?php echo $pro['name'] ?></big></div>
                                            <p style="color:#777">
                                                <small><?php if(strlen($pro['description']) < 50) echo $pro['description']; else echo substr($pro['description'], 0, 50).'...'?></small>
                                            </p>
                                        </a>
                                    </div>
                                    <div title='Delete this project' style="float:left;padding-left: 20px">
                                        <small>
                                            <a href="#!" class="delete-button" 
                                               data-project-id="<?php echo $pro['id']?>" 
                                               data-project-analisi-count="<?php echo $pro['analisi_count']?>"
                                               data-project-powersource-count="<?php echo $pro['power_source_count']?>"><i class='glyphicon glyphicon-trash'></i> Delete</a>
                                        </small>
                                    </div>

                                </li>

                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div style="margin:20px"><?php echo $this->Html->link("<i class='glyphicon glyphicon-folder-close'></i> New project", array('controller'=>'projects', 'action'=>'add'), array('escape'=>false))?></div>
                </div> 
                
            </div>
        <?php endif; ?>
    
        <div class="col-md-3 col-md-offset-1">
            <?php echo $this->element('use_projects_to')?>  
            <br/>          
            <legend>Things you might want to do</legend>
            <ul style="list-style-type: none">
                <li style="padding-bottom: 15px"><i class="glyphicon glyphicon-briefcase" style="margin-left: -20px"></i> Set a project as private to protect your clients data</li>
                <li style="padding-bottom: 15px"><i class="glyphicon glyphicon-user" style="margin-left: -20px"></i> Invite people to collaborate in a project</li>
            </ul>
        </div>
</div>




<?php
$this->Html->script('jquery', array('inline' => false));

/*$this->Html->script('common/jquery.blockUI', array('inline' => false));
$this->Html->script('common/jquery-ajax-extend', array('inline' => false));

$this->Html->script('flippant/flippant', array('inline' => false));
$this->Html->css('flippant/flippant', null, array('inline' => false));

$this->Js->set('projects', $projects);
$this->Js->set('html_project_form', $this->element('project_form', array('do_ajax' => true, 'is_modal' => true)));
echo $this->Js->writeBuffer(array('inline' => false));*/

//$this->Html->script('common/ajax-forms', array('inline' => false));


//$this->Html->script('handlebars/handlebars-v1.3.0', array('inline' => false));

$this->Html->script('bootbox/bootbox', array('inline' => false));

$this->Html->script('projects-index', array('inline' => false));
$this->Html->css('projects-index', null, array('inline' => false));
?>


<!-- TEMPLATES -->
<script id="project-template" type="text/x-handlebars-template">
    <tr id="project-row-{{id}}">
        <td><a href="<?php echo $this->Html->url(array('controller' => 'projects', 'action' => 'view')) ?>/{{id}}">{{name}}</a></td>
        <td>{{description}}</td>
        <td>
            <div id="actions-{{id}}">            
                <a href='#!' class='edit-project' data-obj='{{stringify}}' style="padding-right:20px"><i class='icon-pencil'> <?php echo __('Edit') ?></i></a>
                <a href='#!' class='delete-project' data-obj='{{stringify}}'><i class='icon-trash'> <?php echo __('Delete') ?></i></a>
            </div>
        </td>
    </tr>
</script>