<?php
// INITIALIZE
$isLoggedIn = AuthComponent::user('id') ? true : false;

if($isLoggedIn) {
    $user = AuthComponent::user();

    if($user['display_name'] != null) {
        $splitName = explode('@', $user['display_name']);
        if(count($splitName) > 1) $pretty_user_name = $splitName[0];
        else $pretty_user_name = $user['display_name'];
    } else {
        $splitEmail = explode('@', $user['username']);
        $pretty_user_name = $splitEmail[0];
    }
    //$pretty_user_date = date('M j, Y', strtotime($user['created']));
}

?>
<!DOCTYPE html>
<html>
    <head>        
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php echo "ProtoPower" ?>
        </title>
        <?php
        // CSS
        //$this->Html->css('prettify', array('inline' => false));
        $this->Html->css('bootstrap', array('inline' => false));        
        $this->Html->css('jquery-ui', array('inline' => false));
        $this->Html->css('common/font-awesome.min', array('inline' => false));
        //$this->Html->script('default', array('inline' => false));
        $this->Html->css('default', array('inline' => false));
        
        
        //JS
        $this->Html->script('jquery', array('inline' => false));
        //$this->Html->script('prettify', array('inline' => false));        
        $this->Html->script('bootstrap', array('inline' => false));
        $this->Html->script('jquery-ui', array('inline' => false));
        

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        ?>
    </head>
    <body>
        <div id="container">
            <nav id="myNavbar" class="navbar navbar-default" role="navigation">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <?php echo $this->Html->link('<i class="icon-bolt"></i> ProtoPower', array('controller' => 'pages', 'action' => 'home'), array('class' => 'navbar-brand', 'escape' => false)); ?>
                    </div>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li>
                                <?php
                                if ($isLoggedIn) {
                                    echo $this->Html->link('My Projects', array('controller' => 'projects', 'action' => 'index'));
                                } else {
                                    echo $this->Html->link('Home', array('controller' => 'pages', 'action' => 'home'));
                                }
                                ?>
                            </li>
                        </ul>

                        <form class="navbar-form navbar-left" role="search" action='<?php echo $this->Html->url(array('controller' => 'analisis', 'action' => 'search_tags')) ?>'>
                            <div class="form-group">
                                <input type="text" name='tags' class="form-control" placeholder="Search tags (comma separated)" style="width:200px" required="required"/>
                            </div>
                            <button type="submit" class="btn btn-default">Search</button>
                        </form>

                        <ul class="nav navbar-nav navbar-right">
                            <?php if ($isLoggedIn): ?>
                                <li class="dropdown">
                                    <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                                        <?php echo $pretty_user_name;?>
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><?php echo $this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout')) ?></li>
                                        <li><?php echo $this->Html->link('Settings', array('controller' => 'users', 'action' => 'profile')) ?></li>
                                        <!--<li class="divider"></li>
                                        <li><a href="#">Settings</a></li>-->
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li>
                                    <?php echo $this->Html->link('Login', array('controller' => 'users', 'action' => 'login')) ?>
                                </li>
                                <li>
                                    <?php echo $this->Html->link('Sign Up', array('controller' => 'users', 'action' => 'register')) ?>
                                </li>
                            <?php endif ?>

                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div>
            </nav>




            <div id="content" class="container-fluid">
                <?php echo $this->Session->flash(); ?>
                <?php echo $this->fetch('content'); ?>
            </div>

            <div id="footer">
                <div class="container-fluid">
                    <p class="text-muted" style="margin: 20px 0;">Created by <?php echo $this->Html->link('ProtoPower&trade;', array('controller' => 'pages', 'action' => 'home'), array('escape' => false)); ?></p>
                </div>
            </div>
        </div>
        <?php /* echo $this->element('sql_dump'); */ ?>
    </body>
</html>
