<?php
class Modules_SqliteManager_Element_CreateNewTable extends Zend_Form_Element {
	public function init() {
		$this->addPrefixPath( 'Modules_SqliteManager_Decorator' , 'Modules/SqliteManager/Decorator/' , 'decorator' );
		$decorator = new Modules_SqliteManager_Decorator_DCreateNewTable();
		$this->setDecorators( array( $decorator ) );
	}
	
	public function generateValue() {
        $this->setValue( null );
	}

}