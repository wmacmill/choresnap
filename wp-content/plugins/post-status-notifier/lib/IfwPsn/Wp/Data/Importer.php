<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @version   $Id: Importer.php 428 2015-05-03 20:33:25Z timoreithde $
 * @package   
 */ 
class IfwPsn_Wp_Data_Importer 
{
    /**
     * @var string
     */
    protected $_file;

    /**
     * @var array (item_name_plural, item_name_singular)
     */
    protected $_xmlOptions = array();

    /**
     * @var string
     */
    protected $_error;


    /**
     * @param $file
     * @param $xmlOptions
     */
    public function __construct($file, $xmlOptions)
    {
        $this->_file = $file;
        if (is_array($xmlOptions)) {
            $this->_xmlOptions = $xmlOptions;
        }
    }

    /**
     * @param $modelname
     * @param $options
     * @return bool|int
     */
    public function import($modelname, $options = array())
    {
        if (empty($this->_file)) {
            $this->_error = __('Please select a valid import file.', 'ifw');
            return false;
        }

        $xml = simplexml_load_file($this->_file);

        // check for valid xml
        if (!$xml) {
            $this->_error = __('Please select a valid import file.', 'ifw');
            return false;
        }

        if (!isset($this->_xmlOptions['item_name_singular']) && !isset($this->_xmlOptions['node_name_singular'])) {
            $this->_error = __('Missing item singular name.', 'ifw');
            return false;
        }

        if (isset($this->_xmlOptions['node_name_singular'])) {
            $nodeNameSingular = $this->_xmlOptions['node_name_singular'];
        } else {
            $nodeNameSingular = $this->_xmlOptions['item_name_singular'];
        }

        $items = $this->_getItems($xml, $nodeNameSingular);

        if (count($items) == 0) {
            $this->_error = __('No items found in import file.', 'ifw');
            return;
        }

        // import
        return IfwPsn_Wp_ORM_Model::import($modelname, $items, $options);
    }

    /**
     * @param $xml
     * @param $itemNodeName
     * @return array
     */
    protected function _getItems($xml, $itemNodeName, $itemNameCol = 'name')
    {
        $items = array();

        // check if xml contains items
        if (count($xml->{$itemNodeName}) == 0) {
            // no items found
            return $items;
        }

        foreach($xml->{$itemNodeName} as $item) {

            $tmpItem = array();

            /**
             * @var SimpleXMLElement $col
             */
            foreach($item as $col) {

                $attr = $col->attributes();

                if (isset($attr[$itemNameCol])) {
                    $tmpItem[(string)$col[$itemNameCol]] = (string)$col;
                } else {
                    foreach (get_object_vars($col) as $colVar => $colVal) {
                        if (is_array($colVal) && !empty($colVal)) {
                            $tmpItem[$colVar] = $colVal;
                        }
                    }
                }
            }

            if (is_array($tmpItem)) {
                array_push($items, $tmpItem);
            }
        }

        return $items;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->_error;
    }
}
 