<h4>
<p>Your password has been changed and we already sent it to your email inbox. When you are ready you can 
<?php echo $this->Html->link('login', array('controller'=>'users', 'action'=>'login'))?> to the app using your new password.
</p>
</h4>
<div class="alert alert-warning">We recommend you to reset your password in <?php echo $this->Html->link(__('your setttings'), array('controller'=>'users', 'action'=>'profile'))?> when you are logged in.</div>