<?php
/**
 *
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2014 ifeelweb.de
 * @version   $Id: RecipientsLists.php 208 2014-05-01 17:23:27Z timoreithde $
 * @package
 */

class Psn_Module_Recipients_ListTable_RecipientsLists extends IfwPsn_Wp_Plugin_ListTable_Abstract
{
    protected $_mod = 'recipients';


    /**
     *
     */
    public function __construct(IfwPsn_Wp_Plugin_Manager $pm, $options = array())
    {
        $args = array('singular' => 'recipientslist', 'plural' => 'recipientslists');
        if (!empty($options)) {
            $args = array_merge($args, $options);
        }
        $data = new Psn_Module_Recipients_ListTable_Data_RecipientsLists();

        parent::__construct($args, $data, $pm);

        IfwPsn_Wp_Proxy_Action::add($this->_wpActionPrefix . 'after_display', array($this, 'afterDisplay'));
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'recipientslists';
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Name', 'ifw'),
        );

        if ($this->isMetaboxEmbedded()) {
            unset($columns['cb']);
        }

        return $columns;
    }

    /**
     * @return array
     */
    public function getSortableColumns()
    {
        return $sortable_columns = array(
            'name' => array('name', false),
        );
    }

    /**
     * Custom column handling for name
     *
     * @param unknown_type $item
     * @return string
     */
    public function getColumnName($item)
    {
        $result = $item['name'];

        if (!$this->isMetaboxEmbedded()) {
            //Build row actions
            $actions = array();
            $actions['edit'] = sprintf('<a href="?page=%s&mod=recipients&controller=recipientslists&appaction=edit&id=%s">'. __('Edit', 'psn') .'</a>', $_REQUEST['page'], $item['id']);
            $actions['copy'] = sprintf('<a href="?page=%s&mod=recipients&controller=recipientslists&appaction=copy&id=%s" class="copyConfirm">'. __('Copy', 'psn') .'</a>', $_REQUEST['page'], $item['id']);
            $actions['export'] = sprintf('<a href="?page=%s&mod=recipients&controller=recipientslists&appaction=export&id=%s">'. __('Export', 'psn') .'</a>', $_REQUEST['page'], $item['id']);
            $actions['delete'] = sprintf('<a href="?page=%s&mod=recipients&controller=recipientslists&appaction=delete&id=%s" class="delConfirm">'. __('Delete', 'psn') .'</a>', $_REQUEST['page'], $item['id']);

            //Return the title contents
            $result = sprintf('%1$s%2$s',
                /*$1%s*/ $item['name'],
                /*$2%s*/ $this->row_actions($actions)
            );
        }

        return $result;
    }

    /**
     *
     */
    public function getExtraControlsTop()
    {
        $this->search_box(__('Search'), 'name');
        $this->displayReloadButton();
    }

    /**
     * Renders the checkbox column (hard coded in class-wp-list-table.php)
     */
    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['id']
        );
    }

    /**
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = array();

        if (!$this->isMetaboxEmbedded()) {
            $actions = array(
                'delete' => __('Delete'),
                'export' => __('Export', 'psn'),
            );
        }

        return $actions;
    }

    public function process_bulk_action()
    {

    }

    /**
     * Init loadDetails link for jquery ui dialog
     *
     * @see WP_List_Table::display()
     */
    public function afterDisplay()
    {
        $ajaxDetails = new Psn_Module_Logger_ListTable_Ajax_Details();

        if (!$this->isMetaboxEmbedded()):
        ?>
        <script type="text/javascript">
        jQuery(".delConfirm").click(function(e) {
            e.preventDefault();
            var targetUrl = jQuery(this).attr("href");

            if (confirm('<?php _e('Are you sure you want to do this?'); ?>')) {
                document.location.href = targetUrl;
            }
        });
        jQuery(".copyConfirm").click(function(e) {
            e.preventDefault();
            var targetUrl = jQuery(this).attr("href");

            if (confirm('<?php _e('Do you want to copy this template?', 'psn_htm'); ?>')) {
                document.location.href = targetUrl;
            }
        });
        </script>
        <?php
        endif;
    }
}
