<?php
/**
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Limitations.php 353 2014-12-14 16:55:04Z timoreithde $
 */ 
class Psn_Module_Limitations_ListTable_Limitations extends IfwPsn_Wp_Plugin_ListTable_Abstract
{
    /**
     *
     */
    public function __construct(IfwPsn_Wp_Plugin_Manager $pm, $options = array())
    {
        $args = array('singular' => 'limitation', 'plural' => 'limitations');
        if (!empty($options)) {
            $args = array_merge($args, $options);
        }
        $data = new Psn_Module_Limitations_ListTable_Data_Limitations();

        parent::__construct($args, $data, $pm);

        IfwPsn_Wp_Proxy_Action::add($this->_wpActionPrefix . 'after_display', array($this, 'afterDisplay'));
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'limitations';
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'timestamp' => __('Timestamp', 'psn'),
            'rule_name' => __('Rule name', 'psn'),
            'post_title' => __('Post', 'psn'),
            'status_after' => __('Status after', 'psn'),
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
            'rule_id' => array('rule_id', false),
            'post_title' => array('post_title', false),
            'timestamp' => array('timestamp', false),
            'status_after' => array('status_after', false),
        );
    }

    /**
     * Custom column handling
     *
     * @param $item
     * @return string
     */
    public function getColumnTimestamp($item)
    {
        $result = IfwPsn_Wp_Date::format($item['timestamp']);

        if (!$this->isMetaboxEmbedded()) {
            //Build row actions
            $actions = array();
            $actions['delete'] = sprintf('<a href="?page=%s&mod=limitations&controller=limitations&appaction=delete&id=%s" class="delConfirm">'. __('Delete', 'psn') .'</a>', $_REQUEST['page'], $item['id']);

            //Return the title contents
            $result = sprintf('%1$s%2$s',
                /*$1%s*/ IfwPsn_Wp_Date::format($item['timestamp']),
                /*$2%s*/ $this->row_actions($actions)
            );
        }

        return $result;
    }

    /**
     * Custom column handling
     *
     * @param $items
     * @return string
     */
    public function getColumnType($items)
    {
        $result = '';

        switch((int)$items['type']) {
            case Psn_Module_HtmlMails_Model_MailTemplates::TYPE_PLAIN_TEXT:
                $value = __('Plain text', 'psn_htm');
                break;
            case Psn_Module_HtmlMails_Model_MailTemplates::TYPE_HTML:
                $value = 'HTML';
                break;
            default:
                $value = '';
        }

        if (!empty($value)) {
            $result = $value;
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
                'clear' => __('Clear'),
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
