<?php $urlDef = array('controller' => 'users', 'action' => 'authorize/' . $user_id) ?>
<p>You have registered in <b>ProtoPower</b> and are almost ready to use its services. The only step left is to confirm your account in 
    <a href='<?php echo $this->Html->url($urlDef, true) ?>'>
        this link
    </a>.
</p>

<p>If this email got to you by mistake, simply delete it and forget this.</p>
<a href='<?php echo $this->Html->url($urlDef, true) ?>'>
	Click here to confirm
</a>
