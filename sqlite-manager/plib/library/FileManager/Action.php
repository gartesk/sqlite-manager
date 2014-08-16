<?php

class Modules_SQLiteManager_FileManager_Action extends pm_FileManager_Action
{
    public function getTitle()
    {
        pm_Context::init( 'sqlite-manager' );

        return pm_Locale::lmsg( 'actionTitle' );
    }

    public function getHref()
    {
        pm_Context::init( 'sqlite-manager' );

        $params = array(
            'dir=' . urlencode( $this->_item['currentDir'] ),
            'file=' . urlencode( $this->_item['name'] ),
        );
        return pm_Context::getBaseUrl() . '?' . implode( '&', $params );
    }

    public function isActive()
    {
        if ( $this->_item['isDirectory'] ) {
            return false;
        }

        return true;
    }
	
	public function isDefault()
    {
        return false;
    }
	
}
