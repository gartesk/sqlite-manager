<?php

class IndexController extends pm_Controller_Action
{
    private $_fileManager = null;
	private $_file = null;
	private $_dir = null;

    public function init()
    {
	
        parent::init();
		
		$this->_getFileDirParam();
        $this->view->pageTitle = $this->lmsg( 'fileNameLabel' ) . ' for ' . $this->_file;
		
    }
	
	public function indexAction()
    {
		$externalBaseUrl = pm_Context::getBaseUrl() . 'externals';
		//array just in case of adding other js files
		$externalScripts = [ 'index_script.js' ];
        foreach ($externalScripts as $scriptFile) {
            $this->view->headScript()->appendFile( "$externalBaseUrl/$scriptFile" );
        }
		
		$this->view->uplevelLink = '/smb/file-manager/';
		
		$db = $this->_getDBAdapter();
		$list = $this->_getTablesList( $db );
		$this->view->list = $list;
		
		pm_Settings::set( 'dir' , $this->_dir );
		pm_Settings::set( 'file' , $this->_file );
		
        $params = array(
            'dir=' . urlencode( $this->_dir ),
            'file=' . urlencode( $this->_file ),
        );
        $parString = implode( '&', $params );

        $this->view->smallTools = array(
            array(
                'title' => 'New',
                'description' => 'Create new table',
				'class' => 'sb-add-new',
                'link' => pm_Context::getBaseUrl() . 'index.php/index/createtable?' . $parString,
            ),
			array(
                'title' => 'Remove',
                'description' => 'Remove selected tables',
				'class' => 'sb-remove-selected add-checks',
                'link' => pm_Context::getBaseUrl() . 'index.php/index/deletetable?' . $parString,
            ),
        );

    }
	
	public function indexDataAction() {
		
		$this->_dir = pm_Settings::get( 'dir' );
		$this->_file = pm_Settings::get( 'file' );
		$db = $this->_getDBAdapter();
        $list = $this->_getTablesList( $db );

        // Json data from pm_View_List_Simple
        $this->_helper->json( $list->fetchData() );
    }
	
	public function tableAction() {
		
		$externalBaseUrl = pm_Context::getBaseUrl() . 'externals';
		//array just in case of adding other js files
		$externalScripts = [ 'table_script.js' ];
        foreach ($externalScripts as $scriptFile) {
            $this->view->headScript()->appendFile( "$externalBaseUrl/$scriptFile" );
        }
		
		$table = $this->_getParam( 'table' );
		pm_Settings::set( 'table' , $table );
		
		$newUrl = pm_Context::getBaseUrl() . 'index.php/index/index'  . '?dir=' . urlencode( $this->_dir ) . '&file=' . urlencode( $this->_file );
		$this->view->uplevelLink = $newUrl;
		
		$db = $this->_getDBAdapter();
		
		$pk = $this->_getParam( 'pk' );
		if( $pk == null )
			$pk = $this->_getPrimaryKey( $db , $table );
		pm_Settings::set( 'pk' , $pk );
		
		$list = $this->_getTableData( $db , $table , $pk );
		$this->view->list = $list;
		
		
		$params = array(
				'dir=' . urlencode( $this->_dir ),
				'file=' . urlencode( $this->_file ),
				'table=' . urlencode( $table ),
				'pk=' . urlencode( $pk ),
		);
		$parString = implode( '&', $params );
		
		$this->view->smallTools = array(
            array(
                'title' => 'Edit',
                'description' => 'Edit selected records',
				'class' => 'sb-edit add-checks',
                'link' => pm_Context::getBaseUrl() . 'index.php/index/edit?' . $parString,
            ),
            array(
                'title' => 'Delete',
                'description' => 'Delete selected records',
				'class' => 'sb-remove-selected add-checks',
                'link' => pm_Context::getBaseUrl() . 'index.php/index/delete?' . $parString,
            ),
			array(
                'title' => 'New',
                'description' => 'Create new record',
				'class' => 'sb-add-new',
                'link' => pm_Context::getBaseUrl() . 'index.php/index/new?' . $parString,
            ),
        );

	}
	
	public function tableDataAction() {
		
		$this->_dir = pm_Settings::get( 'dir' );
		$this->_file = pm_Settings::get( 'file' );
		$table = pm_Settings::get( 'table' );
		$pk = pm_Settings::get( 'pk' );
		$db = $this->_getDBAdapter();
        $list = $this->_getTableData( $db , $table , $pk );

        // Json data from pm_View_List_Simple
        $this->_helper->json( $list->fetchData() );
    }
	
    public function editAction(){
        
		$externalBaseUrl = pm_Context::getBaseUrl() . 'externals';
		//array just in case of adding other js files
		$externalScripts = [ 'edit_script.js' ];
        foreach ($externalScripts as $scriptFile) {
            $this->view->headScript()->appendFile( "$externalBaseUrl/$scriptFile" );
        }
		
		$table = $this->_getParam( 'table' );
		$pk = $this->_getParam( 'pk' );
		pm_Settings::set( 'table' , $table );
		pm_Settings::set( 'pk' , $pk );
		pm_Settings::set( 'allValues' , $this->_getParam( 'values' ) );
		$values = explode( ',' , $this->_getParam( 'values' ) );
		
		$newUrl = '/index/table'  . '?dir=' . urlencode( $this->_dir ) . '&file=' . urlencode( $this->_file ) . '&table=' . urlencode( $table ) . '&pk=' . urlencode( $pk );
		
		if( $values[0] == null ) {
		
			$this->_status->addMessage( 'error' , 'No rows selected' );
			$this->_redirect( $newUrl );
		}
		else {
		
			$this->view->test = 'Edit ' . $table . ' rows:';
		
			$newUrl = pm_Context::getBaseUrl() . 'index.php/index/table'  . '?dir=' . urlencode( $this->_dir ) . '&file=' . urlencode( $this->_file ) . '&table=' . urlencode( $table ) . '&pk=' . urlencode( $pk );
			$this->view->uplevelLink = $newUrl;
		
			$db = $this->_getDBAdapter();
			$list = $this->_getTableSelectedData( $db , $table , $pk , $values );
			$keys = array_keys( $db->query( 'SELECT * FROM ' . $table . ' LIMIT 1' )->fetchAll()[0] );
		
			$this->view->list = $list;
		
			$form = new pm_Form_Simple();
			$form->addControlButtons( array(
				'cancelTitle' => 'Cancel',
				'cancelLink' => $newUrl,
				'sendTitle' => 'Apply',
				'hideLegend' => true,
			) );
		
			if ( $this->getRequest()->isPost() && $form->isValid( $this->getRequest()->getPost() ) ) {
				$errors = 0;
				foreach( $this->_getParam( 'names' ) as $name ) {
					$setRow = array();
					$tmp = 0;
					foreach( $this->_getParam( $name ) as $field ) {
						$setRow[$keys[$tmp]] = $field;
						$tmp++;
					}
					try { 
						$db->update( $table , $setRow , $pk . '=' . $name ); 
					}
					catch ( Exception $e ) { 
						++$errors;
					}
				}

				if( $errors == 0 )
					$this->_status->addMessage( 'info' , 'Table successfully updated' );
				else
					$this->_status->addMessage( 'error' , 'Error while updating ' . $errors . ' rows' ); 
				$this->_helper->json( array( 'redirect' => $newUrl ) );
			}
			$this->view->form = $form;
		}
    }

	public function editDataAction() {
		
		$this->_dir = pm_Settings::get( 'dir' );
		$this->_file = pm_Settings::get( 'file' );
		$table = pm_Settings::get( 'table' );
		$pk = pm_Settings::get( 'pk' );	
		$values = explode( ',' , pm_Settings::get( 'allValues' ) );
		$db = $this->_getDBAdapter();
        $list = $this->_getTableSelectedData( $db , $table , $pk , $values );

        // Json data from pm_View_List_Simple
        $this->_helper->json( $list->fetchData() );
    }
	
    public function deleteAction() {
		
		$table = $this->_getParam( 'table' );
		$pk = $this->_getParam( 'pk' );
		$values = explode( ',' , $this->_getParam( 'values' ) );
		
        $db = $this->_getDBAdapter();
        
        if( $values[0] == null ) {
			$this->_status->addMessage( 'error' , 'No rows selected' );
		}
		else {
			foreach( $values as $singleValue ) {
                $db->query( 'DELETE FROM ' . $table . ' WHERE ' . $pk . '=\'' . $singleValue . '\'' );
			}
			$this->_status->addMessage( 'info' , 'Rows were successfully removed' );
		}
		
		$newUrl = '/index/table'  . '?dir=' . urlencode( $this->_dir ) . '&file=' . urlencode( $this->_file ) . '&table=' . urlencode( $table ) . '&pk=' . urlencode( $pk );
        $this->_redirect( $newUrl );
    }

    public function newAction() {
		
		$this->view->test = 'Add new record:';
		
        $this->_file = $this->_getParam( 'file' );
        $this->_dir = $this->_getParam( 'dir' );
		$table = $this->_getParam( 'table' );
		$pk = $this->_getParam( 'pk' );		
        
		$newUrl = pm_Context::getBaseUrl() . 'index.php/index/table' . '?dir=' . urlencode( $this->_dir ) . '&file=' . urlencode( $this->_file ) . '&table=' . urlencode( $table ) . '&pk=' . urlencode( $pk );
		$this->view->uplevelLink = $newUrl;
		
		$db = $this->_getDBAdapter();
        $infoTable = $db->query( 'pragma table_info(' . $table . ');' )->fetchAll();
        foreach( $infoTable as $infoRow ) {
            foreach( $infoRow as $key => $val ) {
                if( $key == 'name' ) {
					$keys[] = $val;
                }
            }
        }
		
		$form = new pm_Form_Simple();
		
		foreach( $keys as $key ) {
			$form->addElement( 'text' , $key , array(
				'label' => $key,
			) );
		}
		
		$form->addControlButtons( array(
			'cancelTitle' => 'Cancel',
            'cancelLink' => $newUrl,
			'sendTitle' => 'Apply',
			'hideLegend' => true,
        ) );
		
        if ( $this->getRequest()->isPost() && $form->isValid( $this->getRequest()->getPost() ) ) {
			$noErrors = true;
			$insRow = array();
            foreach( $keys as $key ) {
                $insRow[$key] = $this->_getParam( $key );
            }
			
			try { 
				$db->insert( $table , $insRow );
			}
			catch ( Exception $e ) { 
				$noErrors = false;
			}
            
            if( $noErrors )
				$this->_status->addMessage( 'info' , 'Row successfully inserted' );
			else
				$this->_status->addMessage( 'error' , 'Error while inserting row' );
            $this->_helper->json( array( 'redirect' => $newUrl ) );
        }
				
        $this->view->form = $form;
    }

    public function createtableAction() {
	
		$externalBaseUrl = pm_Context::getBaseUrl() . 'externals';
		//array just in case of adding other js files
		$externalScripts = [ 'create_table_script.js' ];
        foreach ($externalScripts as $scriptFile) {
            $this->view->headScript()->appendFile( "$externalBaseUrl/$scriptFile" );
        }
		
        $newUrl = pm_Context::getBaseUrl() . 'index.php/index/index'  . '?dir=' . urlencode( $this->_dir ) . '&file=' . urlencode( $this->_file );
		$this->view->uplevelLink = $newUrl;
		
		$this->view->test = 'Create new table:';
		
        $form = new pm_Form_Simple();

        $form->addPrefixPath( 'Modules_SqliteManager_Element' , 'Modules/SqliteManager/Element/' , 'element' );
        $table = new Modules_SqliteManager_Element_CreateNewTable( 'Table' );
        $table->setLabel( 'table' );

        $table->generateValue();
        $table->addDecorator(
            array('div' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'table_wrapper')
        );
        $form->addElement( $table );
		
		$form->addControlButtons( array(
			'cancelTitle' => 'Cancel',
            'cancelLink' => $newUrl,
			'sendTitle' => 'Apply',
			'hideLegend' => true,
        ) );

        if ( $this->getRequest()->isPost() && $form->isValid( $this->getRequest()->getPost() ) ) {
			$noErrors = true;
            $db = $this->_getDBAdapter();
            $tmp = 0;
			$createString = '';
			$pkString = '';
			$pkNums = array();
			foreach( $this->_getParam( 'all_check' ) as $checkedNum )
				$pkNums[$checkedNum] = true;
            foreach( $this->_getParam( 'all_input' ) as $singleInput ){
                if( $tmp > 1 )
					$createString .= ', ';
                if( $tmp == 0 ) 
					$newTableName = $singleInput;
                else {
					$createString .= $singleInput . ' ' . $_POST['all_type'][( $tmp - 1 )];
					if( $pkNums[$tmp-1] ) {
						if( $pkString != '' )
							$pkString .= ', ';
						$pkString .= $singleInput;
					}
				}
                $tmp++;
            }
			try {
				if( $pkString != '' )
					$db->query( 'CREATE TABLE ' . $newTableName . '(' . $createString . ', PRIMARY KEY(' . $pkString . '))' );
				else
					$db->query( 'CREATE TABLE ' . $newTableName . '(' . $createString . ')' );
			}
			catch ( Exception $e ) {
				$noErrors = false;
			}
            if( $noErrors )
				$this->_status->addMessage( 'info', 'Table successfully created' );
			else
				$this->_status->addMessage( 'error' , 'Error while creating table' ); 
            $this->_helper->json( array( 'redirect' => $newUrl ) );
        }


        $this->view->form = $form;
    }

    public function deletetableAction() {
	
        $db = $this->_getDBAdapter();
		$tables = explode( ',' , $this->_getParam( 'tables' ) );
		if( $tables[0] == null ) {
			$this->_status->addMessage( 'error' , 'No tables selected' );
		}
		else {
			foreach( $tables as $currTable )
				$db->query( 'DROP TABLE ' . $currTable );
			$this->_status->addMessage( 'info' , 'Tables were successfully removed' );
		}
		$newUrl = '/index/index'  . '?dir=' . urlencode( $this->_dir ) . '&file=' . urlencode( $this->_file );
        $this->_redirect( $newUrl );
    }

    public function edittableAction(){
        $db = $this->_getDBAdapter();
        $stmt = $db->query( 'pragma table_info(' . $this->_getParam( 'table' ) . ');' );
        $testRes = $stmt->fetchAll();

        foreach( $testRes as $arr ) {
            foreach( $arr as $key => $val ) {
                if( $key == 'name' )
                    $names_arr[] = $val;
                if( $key == 'pk'  )
                    $pk_arr[] = $val;
                if( $key == 'type' )
                    $types_arr[] = $val;
            }
        }
        //***Нужно в таблице разместить эти массивы $names_arr , $types_arr , $pk_arr именно в таком порядке
        //***pk_arr если получиться сделать в виде чекбокса...

        /****
         *После POST сделать следующие запросы
         * ALTER TABLE имя_таблицы RENAME TO tmp_table_name;
         * CREATE TABLE имя_таблицы (
            имя_поля1 тип
            , имя_поля2 тип
            ....
            );
         * INSERT INTO имя_таблицы(имя_поля1, имя_поля2, ...)
            SELECT имя_поля1, имя_поля2, ...
            FROM tmp_table_name;
         * DROP TABLE tmp_table_name;
         *
         * P.S. CREATE TABLE можно посмотреть в createtableAction
         */

    }
///////////////////////////////////////////////////////////////////////////
	
    private function _getDBAdapter() {
        if( !$this->_file || !$this->_dir )
			$this->_getFileDirParam();
		$fileManager = $this->_getFileManager();
        $fileName = $fileManager->getFilePath( "$this->_dir/$this->_file" );
		$db = Zend_Db::factory( 'PDO_SQLITE' , array( 'dbname' => $fileName ) );
		return $db;
	}
	
	private function _getFileManager() {
        if ( !$this->_fileManager ) {
            $domainId = pm_Session::getCurrentDomain()->getId();
            $this->_fileManager = new pm_FileManager( $domainId );
        }
        return $this->_fileManager;
    }
	
	private function _getTablesList( $db ) {		
		$listData = array();
		$tables = $db->listTables();

		foreach( $tables as $table ) {
			$params = array(
				'dir=' . urlencode( $this->_dir ),
				'file=' . urlencode( $this->_file ),
				'table=' . urlencode( $table ),
			);
			$paramsString = implode( '&', $params );
			$listData[] = array(
				'check' => '<input type="checkbox" class="check" value="' . $table . '">',
				'table-name' => '<a href="/modules/sqlite-manager/index.php/index/table?' . $paramsString . '">' . $table . '</a>',
			);
		}
		$list = new pm_View_List_Simple( $this->view , $this->_request );
        $list->setData( $listData );
        $list->setColumns( array(
			'check' => array(
                'title' => ' ',
                'noEscape' => true,
            ),
			'table-name' => array(
                'title' => 'Table name',
                'noEscape' => true,
                //'searchable' => true,
				//search is not working properly, so without it for now
            ),
        ));
		//$newUrl = pm_Context::getBaseUrl() . 'index.php/index/index-data' . '?dir=' . urlencode( $this->_dir ) . '&file=' . urlencode( $this->_file );
		$list->setDataUrl( array( 'action' => 'index-data' ) );
		return $list;
	}
	
	private function _getTableData( $db , $table , $pk ) {
		
		$stmt = $db->query( 'SELECT rowid,* FROM ' . $table );
		$tableData = $stmt->fetchAll();
		
		$list = new pm_View_List_Simple( $this->view , $this->_request );
		if( !empty( $tableData ) ) {
			$n = count( $tableData );
			for( $i = 0 ; $i < $n ; ++$i ) {
			//possible error here: if table has column named 'rowid' which is not a pk
				$tableData[$i]['check'] = '<input type="checkbox" class="check" value="' . $tableData[$i][$pk] . '">';
			}
			
			$list->setData( $tableData );
			$keys = array_keys( $tableData[0] );
			$columns = array(
				'check' => array(
					'title' => ' ',
					'noEscape' => true,
				),
			);
			foreach( $keys as $key ) {
				if( $key != 'check' && $key != 'rowid' ) {
					$columns[$key] = array(
						'title' => $key,
						'sortable' => true,
						//'searchable' => true,
						//search is not working properly, so without it for now
					);
				}
			}
			$list->setColumns( $columns );
		}
		$list->setDataUrl( array( 'action' => 'table-data' ) );
        return $list;
	}
	
	private function _getPrimaryKey( $db , $table ) {
		$stmt = $db->query( 'pragma table_info(' . $table . ');' );
        $info = $stmt->fetchAll();
        $temp_counter = 0;
		$pk = null;
        foreach( $info as $colInfo ) {
            foreach( $colInfo as $key => $val ) {
                if( $key == 'name' ) 
					$temp_name = $val;
                if( $key == 'pk' && $val == 1 ) {
                    $pk = $temp_name;
					break;
				}
            }
            if ( $pk != null ) 
				break;
        }
		if( $pk == null )
			$pk = 'rowid';
		return $pk;
	}
	
	private function _getTableSelectedData( $db , $table , $pk , $values ) {
		$tmp = 0;
		$whereString = '';
        foreach( $values as $value )
        {
            if( $tmp > 0 ) 
				$whereString .= ' OR ';
            $whereString .= $pk . '=\'' . $value . '\'';
            $tmp++;
        }
        $whereString .= ';';
		$stmt = $db->query( 'SELECT rowid,* FROM ' . $table . ' WHERE ' . $whereString );
		$tableData = $stmt->fetchAll();
		
		$keys = array_keys( $tableData[0] );
		$n = count( $tableData );
		$temp_table = $tableData;
		for( $i = 0 ; $i < $n ; ++$i ) {
			$tableData[$i]['names'] = '<input type="hidden" name="names[]" value="' . $tableData[$i][$pk] . '">';
			foreach( $keys as $key )
				$tableData[$i][$key] = '<input type="text" name="' . $temp_table[$i][$pk] . '[]" value="' . $temp_table[$i][$key] . '">';
		}
		
		$list = new pm_View_List_Simple( $this->view , $this->_request );
        $list->setData( $tableData );
		
		$columns = array(
			'names' => array(
				'title' => ' ',
				'noEscape' => true,
			),
		);
		foreach( $keys as $key ) {
			if( $key != 'rowid' ) {
				$columns[$key] = array(
					'title' => $key,
					'noEscape' => true,
				);
			}
		}
		$list->setColumns( $columns );
		$list->setDataUrl( array( 'action' => 'edit-data' ) );
		return $list;
	}
	
	private function _getFileDirParam() {
		$this->_file = $this->_getParam( 'file' );
        $this->_dir = $this->_getParam( 'dir' );
	}
	

}
