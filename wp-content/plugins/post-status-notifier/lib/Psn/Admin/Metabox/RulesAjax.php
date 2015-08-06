<?php
/**
 * AmazonSimpleAffiliate (ASA2)
 * For more information see http://www.wp-amazon-plugin.com/
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: RulesAjax.php 394 2015-06-21 21:40:04Z timoreithde $
 */ 
class Psn_Admin_Metabox_RulesAjax extends IfwPsn_Wp_Ajax_Request
{
    public $action = 'load-psn-rules';

    
    /**
     * @return IfwPsn_Wp_Ajax_Response_Abstract
     */
    public function getResponse()
    {
        $listTable = new Psn_Admin_ListTable_Rules(IfwPsn_Wp_Plugin_Manager::getInstance('Psn'), array('metabox_embedded' => true, 'ajax' => true));

        if (isset($_POST['refresh_rows'])) {
            $html = $listTable->ajax_response();
        } else {
            $html = '<p><a href="'.  IfwPsn_Wp_Proxy_Admin::getUrl() . IfwPsn_Wp_Proxy_Admin::getMenuUrl(IfwPsn_Wp_Plugin_Manager::getInstance('Psn'), 'rules', 'create') .'" class="ifw-wp-icon-plus" id="link_create_rule">'.
                __('Create new rule', 'psn') .'</a></p>';
            $html .= $listTable->fetch();
        }

        return new IfwPsn_Wp_Ajax_Response_Json(true, array(
            'html' => $html)
        );
    }
}
