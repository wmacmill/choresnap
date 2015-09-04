<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @version   $Id: Abstract.php 443 2015-07-25 15:32:39Z timoreithde $
 * @package   
 */
abstract class IfwPsn_Wp_Model_Mapper_Abstract implements IfwPsn_Wp_Model_Mapper_Interface
{
    /**
     * @param string $prefix
     * @param string $itemName
     * @return array
     */
    public function getExportOptions($prefix = '', $itemName = '')
    {
        if (!empty($itemName)) {
            $itemName = '_' . $itemName;
        }

        return array(
            'node_name_plural' => sprintf('%s%s', $prefix, $this->getPlural()),
            'node_name_singular' => sprintf('%s%s', $prefix, $this->getSingular()),
            'filename' => sprintf('%s%s%s_%s', $prefix, $this->getSingular(), $itemName, date('Y-m-d_H_i_s')),
            'filename_bundle' => sprintf('%s%s_bundle_%s', $prefix, $this->getPlural(), date('Y-m-d_H_i_s'))
        );
    }
}
