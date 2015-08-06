<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Details.php 394 2015-06-21 21:40:04Z timoreithde $
 */ 
class Psn_Module_Logger_ListTable_Ajax_Details extends IfwPsn_Wp_Ajax_Request
{
    public $action = 'load-psn-log-detail';



    /**
     * @return IfwPsn_Wp_Ajax_Response_Abstract
     */
    public function getResponse()
    {
        $id = (int)$_POST['logId'];
        $log = IfwPsn_Wp_ORM_Model::factory('Psn_Module_Logger_Model_Log')->find_one($id);

        $extra = $log->get('extra');

        echo '<div class="log-detail-dialog">';
        if (strpos($extra, '{') === 0) {
            $extra = html_entity_decode($extra);
            $extra = json_decode($extra, true);
            echo '<p><b>TO:</b><br>' . $extra['to'] . '<br>';
            echo '<p><b>Headers:</b><br>';
            foreach ($extra['headers'] as $header) {
                echo htmlentities($header) . '<br>';
            }
            echo '</p>';
            echo '<p><b>Subject:</b><br>' . $extra['subject'] . '</p>';

            $message = $extra['message'];
            if (isset($extra['html']) && $extra['html'] == false) {
                $message = nl2br($extra['message']);
            }

            echo '<b>Text:</b><div class="log-detail-text">' . $message . '</div>';

        } else {
            echo nl2br(htmlspecialchars($log->get('extra')));
        }
        echo '<div>';
        exit;
    }
}