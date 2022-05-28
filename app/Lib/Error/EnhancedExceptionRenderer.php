<?php
App::uses('ExceptionRenderer', 'Error');

class EnhancedExceptionRenderer extends ExceptionRenderer {
	
	protected function _outputMessage($template) {
		if($this->controller->request->is('ajax')) {
			echo $this->error->getMessage();
			$this->controller->response->send();
		} else {
			parent::_outputMessage($template);
		}
	}
}
?>