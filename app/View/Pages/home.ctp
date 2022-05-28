<div class="row" style="text-align: center">
    <h1><big>Make better power networks analyses, and let others help you</big></h1>
    <smal>ProtoPower allows you to review other people's analyses, and let people review yours</small>
</div>

<br/>
<br/>

<div class="container">
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-md-4">
            <h2>Experts</h2>
            <p>Organize your analysis projects in a comprehensive way. Collaborate with colleagues by sharing analysis links with them. Provide your clients with better services and help them save money.</p>
            <!--<p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>-->
        </div>
        <div class="col-md-4">
            <h2>Teachers</h2>
            <p>Prepare sample analyses data of diverse power parameters and share them with your class. Encourage a collaborative behaviour among your students by letting them discuss the analyses, and come up with results.</p>
            <!--<p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>-->
        </div>
        <div class="col-md-4">
            <h2>Students</h2>
            <p>Learn by following analyses made by experts or your teachers. Get insightful comments from colleague students. Share your knowledge by coming up with conclusions and showing them.</p>
            <!--<p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>-->
        </div>
    </div>
</div>

<br/>
<br/>

<div class="container"><div class="row">
<?php if(count($rand_analises) > 0):?>
    <legend>Some analyses you can watch right now:</legend>
    <ul style="list-style-type:none">
        <?php $i = 0;?>
        <?php foreach ($rand_analises as $a): ?>
            <?php $analisis = $a['Analisi']?>
            <?php $project = $a['Project']?>
            <?php $powersource = $a['PowerSource']?>
            <?php $datafile = $a['Datafile']?>
            <?php $analyseStr = $project['id'].'/'.$powersource['id'].'/'.$analisis['datablock_code'].'/'.$datafile['label'].'/'.$analisis['param'];?>
            <?php $prettyStr = $project['name'].' / '.$powersource['name'].' / '.$analisis['datablock_code'].' / '.$datafile['label'].' / '.$analisis['param'];?>
        <?php if($i%2 == 0):?> <div class="col-md-12" style="padding-bottom: 20px;"><?php endif;?>
        <div  class="col-md-6">
            <li style="clear:both">

                <div style="float:left">
                    <div style="float:left"><i class="glyphicon glyphicon-stats" style="margin-left: -20px"></i></div>
                    <a class="list-item" href="<?php echo $this->Html->url(array('controller' => 'analisis', 'action' => 'analyse/' . $analyseStr)) ?>">
                        <div class="list-item-ref"><big><?php echo $analisis['param'] ?></big></div>
                        <div style="color:#777">
                            <small>
                                <b>in:</b> <?php echo $prettyStr?>
                            </small>
                        </div>
                    </a>
                </div>
                
            </li>
        </div>
        <?php if($i%2 != 0):?> </div><?php endif;?>
            
        <?php $i++;?>

        <?php endforeach; ?>
    </ul>
<?php endif?>    
</div></div>


<?php $this->Html->css('home', null, array('inline' => false));?>