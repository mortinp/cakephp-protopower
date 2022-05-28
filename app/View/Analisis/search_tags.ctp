<?php if(empty($results)) :?>
    <h2>
        No results were found for tags:
        <?php 
        $sep = '';
        foreach ($tags as $t) {
            echo $sep.$t;
            $sep = ', ';
        }
        ?>
    </h2>
    
<?php else :?>

<div class="container-fluid"><div class="row">
    <legend>
        Found these results for tags:
        <?php 
        $sep = '';
        foreach ($tags as $t) {
            echo $sep.$t;
            $sep = ', ';
        }
        ?>
    </legend>
    <ul style="list-style-type:none">
        <?php $i = 0;?>
        <?php foreach ($results as $r): ?> 
            <?php $analisis = $r['Analisi']?>
            <?php $project = $r['Project']?>
            <?php $powersource = $r['PowerSource']?>
            <?php $datafile = $r['Datafile']?>
            <?php $analyseStr = $project['id'].'/'.$powersource['id'].'/'.$analisis['datablock_code'].'/'.$datafile['label'].'/'.$analisis['param'];?>
            <?php $prettyStr = $project['name'].' / '.$powersource['name'].' / '.$analisis['datablock_code'].' / '.$datafile['label'].' / '.$analisis['param'];?>
        <?php if($i%3 == 0):?> <div class="col-md-12" style="padding-bottom: 20px;"><?php endif;?>
        <div  class="col-md-4">
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
                        <div style="color:#777">
                            <small>
                                <b>matches:</b> 
                                <?php 
                                    $matches = $r['matches'];
                                    $sep = '';
                                    foreach ($matches as $m) {
                                        echo $sep.$m;
                                        $sep = ', ';
                                    }
                                ?>
                            </small>
                        </div>
                    </a>
                </div>
                
            </li>
        </div>
        <?php if($i%3 == 2):?> </div><?php endif;?>
            
        <?php $i++;?>

        <?php endforeach; ?>
    </ul> 
</div></div>
<?php endif?>

<?php $this->Html->css('search_tags', null, array('inline' => false));?>