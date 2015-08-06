<?php
/**
 * AmazonSimpleAffiliate (ASA2)
 * For more information see http://www.wp-amazon-plugin.com/
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: LogsAjax.php 394 2015-06-21 21:40:04Z timoreithde $
 */ 
class Psn_Module_Logger_Metabox_LogsAjax extends IfwPsn_Wp_Ajax_Request
{
    public $action = 'load-psn-logs';

    /**
     * @return IfwPsn_Wp_Ajax_Response_Abstract
     */
    public function getResponse()
    {
        $listTable = new Psn_Module_Logger_ListTable_Log(IfwPsn_Wp_Plugin_Manager::getInstance('Psn'), array('metabox_embedded' => true, 'ajax' => true));

        if (isset($_POST['refresh_rows'])) {
            $html = $listTable->ajax_response();
        } else {
            $html = $listTable->fetch();
        }

        return new IfwPsn_Wp_Ajax_Response_Json(true, array(
            'html' => $html
        ));
    }
}
