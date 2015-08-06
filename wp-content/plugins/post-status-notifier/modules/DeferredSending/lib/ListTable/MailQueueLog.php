<?php
/**
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: MailQueueLog.php 394 2015-06-21 21:40:04Z timoreithde $
 */ 
class Psn_Module_DeferredSending_ListTable_MailQueueLog extends IfwPsn_Wp_Plugin_ListTable_Abstract
{
    /**
     *
     */
    public function __construct(IfwPsn_Wp_Plugin_Manager $pm, $options = array())
    {
        $args = array('singular' => 'mailqueuelog', 'plural' => 'mailqueuelogs');
        if (!empty($options)) {
            $args = array_merge($args, $options);
        }
        $data = new Psn_Module_DeferredSending_ListTable_Data_MailQueueLog();


        parent::__construct($args, $data, $pm);

        IfwPsn_Wp_Proxy_Action::add($this->_wpActionPrefix . 'after_display', array($this, 'afterDisplay'));
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'mailqueuelog';
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'id' => __('ID', 'psn'),
            'subject' => __('Subject', 'psn'),
            'to' => __('TO', 'psn'),
            'added' => __('Added', 'psn_def'),
            'sent' => __('Sent', 'psn_def'),
            'tries' => __('Tries', 'psn_def'),
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
            'id' => array('id', false),
            'subject' => array('subject', false),
            'to' => array('to', false),
            'added' => array('added', false),
            'sent' => array('Sent', false),
            'tries' => array('tries', false),
        );
    }

    /**
     * Custom column handling for TO
     *
     * @param unknown_type $item
     * @return string
     */
    public function getColumnTo($item)
    {
        $result = $item['to'];

        if (strlen($result) > 200) {
            $result = substr($result, 0, 200) . ' ... ';
        }

        return $result;
    }

    /**
     * Custom column handling for name
     *
     * @param unknown_type $item
     * @return string
     */
    public function getColumnSubject($item)
    {
        $result = $item['subject'];

        if (!$this->isMetaboxEmbedded()) {
            //Build row actions
            $actions = array();
            $actions = array(
                'details' => sprintf('<a href="#%s" class="loadDetails">'. __('Show details', 'psn') .'</a>', $item['id']),
            );
//            $actions['export'] = sprintf('<a href="?page=%s&mod=htmlmails&controller=htmlmails&appaction=export&id=%s">'. __('Export', 'psn') .'</a>', $_REQUEST['page'], $item['id']);
            $actions['delete'] = sprintf('<a href="?page=%s&mod=deferredsending&controller=deferredsendinglog&appaction=delete&id=%s" class="delConfirm">'. __('Delete', 'psn') .'</a>', $_REQUEST['page'], $item['id']);

            //Return the title contents
            $result = sprintf('%1$s%2$s',
                /*$1%s*/ $item['subject'],
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
    public function getColumnAdded($items)
    {
        return IfwPsn_Wp_Date::format($items['added']);
    }

    /**
     * Custom column handling
     *
     * @param $items
     * @return string
     */
    public function getColumnSent($items)
    {
        return IfwPsn_Wp_Date::format($items['sent']);
    }

    /**
     *
     */
    public function getExtraControlsTop()
    {
        $this->search_box(__('Search'), 'subject');
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
                'reset' => __('Reset (delete all)', 'psn_def'),
                //'export' => __('Export', 'psn'),
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
        require_once dirname(__FILE__) . '/Ajax/DetailsLog.php';
        $ajaxDetails = new Psn_Module_DeferredSending_ListTable_Ajax_DetailsLog();

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

        jQuery(document).ready(function($) {
            if (typeof ajaxurl == 'undefined') {
                var ajaxurl = 'admin-ajax.php';
            }
            $('.loadDetails').each(function(index) {
                $(this).click(function(e) {
                    e.preventDefault();
                    var mailId = $(this).attr('href').substring(1);
                    var url = this.href;
                    // show ajax loading animation
                    var dialog = $('<div style="display:none" class="ifw-dialog-loading-default"></div>').appendTo('body');
                    var data = {
                        action: '<?php echo $ajaxDetails->getAction(); ?>',
                        nonce: '<?php echo $ajaxDetails->getNonce(); ?>',
                        mailId: mailId,
                        dataType: 'json'
                    };
                    // open the dialog
                    dialog.dialog({
                        dialogClass: 'wp-dialog',
                        // add a close listener to prevent adding multiple divs to the document
                        close: function(event, ui) {
                            // remove div with all data and events
                            dialog.remove();
                        },
                        modal: true,
                        resizable: true,
                        closeOnEscape: true,
                        width: 700,
                        height: 500
                    });
                    // load remote content
                    dialog.load(
                        ajaxurl,
                        data, // omit this param object to issue a GET request instead a POST request, otherwise you may provide post parameters within the object
                        function (responseText, textStatus, XMLHttpRequest) {
                            // remove the loading class
                            dialog.removeClass('ifw-dialog-loading-default');
                        }
                    );
                });
            });
        });
        </script>
        <?php
        endif;
    }
}
