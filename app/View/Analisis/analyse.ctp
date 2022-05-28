<?php
// INITIALIZE
$isLoggedIn = AuthComponent::user('id') ? true : false;
?>

<?php if(!$isOwner):?>
<div class="alert alert-warning alert-dismissable" style="text-align:center">
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
    This page is <b>not showing all features</b> because you are not the owner of this analysis
</div>
<?php endif;?>
<div class="row">
    
    <div class='col-md-12'>
        
        <div class="pull-left">
            <ol class="breadcrumb" style="background-color: #FFFFFF; /*border-right: 1px solid #777*/">
                <li>
                    <b>
                    <?php echo (!$isOwner)? "<a class='info' data-type='project'>".$project['name'].'</a>':$this->Html->link($project['name'], array('controller' => 'projects', 'action' => 'view/' . $project['id']), array('class' => 'info', 'data-type'=>'project')); ?>
                    </b>
                </li>
                <li>
                    <b>
                    <?php echo (!$isOwner)? "<a class='info' data-type='powersource'>".$powersource['name'].'</a>':$this->Html->link($powersource['name'], array('controller' => 'projects', 'action' => 'view/' . $project['id'] . '/' . $powersource['id']), array('class' => 'info', 'data-type'=>'powersource')); ?>
                    </b>    
                </li>
                <li class="active"><?php echo $datablock['code']?></li>
                <li class="active"><?php echo $file['label'] ?></li>
                <li class="active"><b><?php echo $param ?></b></li>
            </ol>
        </div>

        <!--<h5>Viewing: 
            <?php /*echo (!$isOwner)? "<a class='info' data-type='project'>".$project['name'].'</a>':$this->Html->link($project['name'], array('controller' => 'projects', 'action' => 'view/' . $project['id']), array('class' => 'info', 'data-type'=>'project')); ?> /
            <?php echo (!$isOwner)? "<a class='info' data-type='powersource'>".$powersource['name'].'</a>':$this->Html->link($powersource['name'], array('controller' => 'projects', 'action' => 'view/' . $project['id'] . '/' . $powersource['id']), array('class' => 'info', 'data-type'=>'powersource')); ?> /
            <?php echo $datablock['code']/*$this->Html->link($datablock['code'], array('controller' => 'analisis', 'action' => 'analyse/' . $project['id'] . '/' . $powersource['id'] . '/' . $datablock['id'])); */?> /
            <?php /*echo $file['label'] ?> /
            <big><b><?php echo $param */?></b></big>
        </h5>-->
        <!--<div class="pull-left breadcrumb" style="background-color: #FFFFFF;">&mdash;</div> -->
        <?php if($isOwner) :?>
            <div class="pull-left breadcrumb" style="background-color: #FFFFFF;">
                <a id='tags' data-url='<?php echo $this->Html->url(array('controller' => 'analisis', 'action' => 'set_file_tags/' . $analisis['id'] . '/' . $param)) ?>' data-type="select2" data-pk="1" data-title="Enter tags" class="editable editable-click" style="display:inline;background-color:rgba(0, 0, 0, 0); ">
                    <?php
                    $tags = array();
                    if (isset($file['user_tags']))
                        $tags = $file['user_tags'];
                    $sep = '';
                    foreach ($tags as $t) {
                        echo $sep . $t;
                        $sep = ', ';
                    }
                    ?>
                </a>
            </div>
        <?php endif?>
            
        <div class='pull-left' style="background-color: #FFFFFF">
            <?php if (count($datablockFiles) > 1) : ?>
                <ul class="item-list-inline">
                <?php foreach ($datablockFiles as $f) :?>
                    <li>
                        <?php
                        if ($f['label'] != $file['label']) {
                            echo $this->Html->link("<i class='glyphicon glyphicon-file'></i> ".$f['label'], array('controller' => 'analisis',
                                'action' => 'analyse/' . $project['id'] . '/' . $powersource['id'] . '/' . $datablock['code'] . '/' . $f['label']), array('escape'=>false));
                        }
                        echo ' ';
                        ?>
                    </li>
                <?php endforeach;?>
                </ul> 
            <?php else: ?>
            <div class="pull-left breadcrumb text-muted" style="background-color: #FFFFFF;display:inline">( No more files in this datablock )</div>
            <?php endif ?>
        </div>
            
    </div>    
    
</div>

<div class="row">
    
    <div class='col-md-6'>
        <div class="container">
            <big><span class="text-muted">Parameters:</span>
                <?php
                $i = 0;
                foreach ($selectors as $s) {
                    $p = $label = $s;
                    if (is_array($s)) {
                        $label = $s['label'];
                        $p = $s['param'];
                    }

                    if ($p != $param)
                        echo '<b>'.
                        $this->Html->link($label, 
                                array('controller' => 'analisis', 
                                    'action' => 'analyse/' . $project['id'] . '/' . $powersource['id'] . '/' . $datablock['code'] . '/' . $file['label'] . '/' . $p), 
                                array('class'=>'parameter', 'data-toggle'=>'tooltip', 'title'=>$pretty_selectors[$i], 'style'=>'padding-right:5px;padding-left:5px')).
                            '</b>';
                    else
                        echo "<span class='text-muted' style='padding-right:5px;padding-left:5px'><b>".$label."</b></span>";
                    echo ' ';
                    
                    $i++;
                }
                ?>
            </big>
        </div>
        
    </div>
    
</div>

<div class="row">
    <div class="col-md-12">
        
        <div id="analisis-tabs"><ul></ul><!-- Charts go here --></div>
    </div>
</div>

<?php if(!$isLoggedIn):?>
<div class="alert alert-info alert-dismissable" style="text-align:center">
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
    <b>Don't have your own analysis</b> (like this one) because you haven't signed up to <em>ProtoPower</em> ? 
    <?php echo $this->Html->link('Sign up now', array('controller'=>'users', 'action'=>'register'))?>.
    You do? 
    <?php echo $this->Html->link('Login', array('controller'=>'users', 'action'=>'login'))?>.
</div>
<?php endif;?>

<section style="padding-top:20px">
    <div id="disqus_thread"></div>
    <script type="text/javascript">
        /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
        var disqus_shortname = 'protopower'; // required: replace example with your forum shortname
        var disqus_identifier = '<?php echo $disqusId?>';

        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
    
</section>


<?php

// CSS
$this->Html->css('amstockchart_3.4.0/amcharts/style', array('inline' => false));

//$this->Html->css('bootstrap3-editable-1.5.1/select2/select2', array('inline' => false));
//$this->Html->css('bootstrap3-editable-1.5.1/select2/select2-bootstrap', array('inline' => false));
$this->Html->css('bootstrap-select2', array('inline' => false));

$this->Html->css('common/toolbars_2', array('inline' => false)); // Nice toolbars

$this->Html->css('analisis-analyse', array('inline' => false));


// JS
$this->Html->script('jquery', array('inline' => false));

$this->Html->script('common/jquery.blockUI', array('inline' => false));

$this->Html->script('amstockchart_3.4.0/amcharts/amcharts', array('inline' => false));
$this->Html->script('amstockchart_3.4.0/amcharts/serial', array('inline' => false));
$this->Html->script('amstockchart_3.4.0/amcharts/amstock', array('inline' => false));


$this->Html->script('canvg/canvg', array('inline' => false));
$this->Html->script('canvg/rgbcolor', array('inline' => false));
$this->Html->script('canvg/amcanvg', array('inline' => false));

//$this->Html->css('charting/extamcharts', null, array('inline' => false));
$this->Html->script('charting/extamcharts', array('inline' => false));
$this->Html->script('charting/chart-analyser', array('inline' => false));
$this->Html->script('charting/chart-builder', array('inline' => false));


//$this->Html->script('bootstrap', array('inline' => false));
$this->Html->script('bootstrap-select2', array('inline' => false));
//$this->Html->script('bootstrap-editable', array('inline' => false));


// PHP - JS binding
$this->Js->set('project', $project);
$this->Js->set('powersource', $powersource);
$this->Js->set('analisisContext', $analisisContext);
echo $this->Js->writeBuffer(array('inline' => false));

$this->Html->script('handlebars/handlebars-v1.3.0', array('inline' => false));

$this->Html->script('analisis-analyse', array('inline' => false));
?>

<!-- TEMPLATES -->
<script id="analisis-tab-template" type="text/x-handlebars-template">
    <li class='temp'><a href='#panel-{{name}}' data-analisis-name="{{name}}">{{tab}}</a></li>
</script>
<script id="analisis-panel-template" type="text/x-handlebars-template">
    <div class='temp' id='panel-{{name}}' style='padding:4px 2px 2px 2px;'>
        <div class="panel panel-default">
            <div class="panel-heading">{{{title}}}</div>
                <div class="panel-body" style="padding:0px">
                    <div id='{{name}}' class='chart-panel'></div>
                </div>
            </div>
        </div>  
    </div>
</script>