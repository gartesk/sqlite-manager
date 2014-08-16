<?php

class Modules_SqliteManager_Decorator_DCreateNewTable extends Zend_Form_Decorator_Abstract {

	public function render( $content = null ) {
	
		$separator = $this->getSeparator();
        $output = '
            <div id="inputi" name="div"><p>Name of table:</p><input type="text" name="all_input[]" value></div>
			<input type="button" onclick="add_input()" value="Add field" name="a_btn" style="margin-top: 1em;">
        ';

		return $content . $separator . $output;
		
	}

}