<?php
/**
 *
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @copyright Copyright (c) 2014 ifeelweb.de
 * @version   $Id: PsnRecipientslistsController.php 359 2015-01-10 20:48:25Z timoreithde $
 * @package
 */
class Recipients_PsnRecipientslistsController extends PsnApplicationController
{
    /**
     * DB model class name
     */
    const MODEL = 'Psn_Module_Recipients_Model_RecipientsLists';

    /**
     * @var string
     */
    protected $_itemPostId = 'recipientslist';

    /**
     * @var array
     */
    protected $_exportOptions = array(
        'item_name_plural' => 'recipients_lists',
        'item_name_singular' => 'recipients_list',
        'filename' => 'PSN_recipients_lists_export_%s'
    );

    /**
     * @var IfwPsn_Vendor_Zend_Form
     */
    protected $_form;



    /**
     * (non-PHPdoc)
     * @see IfwPsn_Vendor_Zend_Controller_Action::preDispatch()
     */
    public function preDispatch()
    {
        if ($this->_request->getActionName() == 'index') {

            $this->enqueueScripts();

            if (isset($_POST['action']) && $_POST['action'] != '-1') {
                $action = $this->_request->getPost('action');
            } elseif (isset($_POST['action2']) && $_POST['action2'] != '-1') {
                $action = $this->_request->getPost('action2');
            } else {
                $action = false;
            }

            if ( $action == 'delete' && is_array($this->_request->getPost($this->_itemPostId)) ) {
                // bulk action delete
                $this->_bulkDelete($this->_request->getPost($this->_itemPostId));

            } else if ( $action == 'export' && is_array($this->_request->getPost($this->_itemPostId)) ) {
                // bulk action export
                $this->_bulkExport($this->_request->getPost($this->_itemPostId));
            }
        }
    }

    public function onBootstrap()
    {
        if ($this->_request->getActionName() == 'index') {
            $this->_perPage = new IfwPsn_Wp_Plugin_Screen_Option_PerPage($this->_pm, __('Items per page', 'ifw'), 'psn_recipientslists_per_page');
        }
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->_initHelp();

        $listTable = new Psn_Module_Recipients_ListTable_RecipientsLists($this->_pm);
        $listTable->setItemsPerPage($this->_perPage->getOption());

        $this->view->listTable = $listTable;



    }

    /**
     * Create new rule
     */
    public function createAction()
    {
        $this->_initFormView();

        if ($this->_request->isPost()) {
            if ($this->_form->isValid($this->_request->getPost())) {

                // request is valid, save the rule
                $rule = IfwPsn_Wp_ORM_Model::factory(self::MODEL)->create($this->_form->getValues());
                $rule->save();

                $this->getAdminNotices()->persistUpdated(
                    sprintf(__('Recipient list <b>%s</b> has been saved successfully.', 'psn_rec'), $rule->get('name')));

                $this->_gotoIndex();
            }
        }

        $this->view->form = $this->_form;
    }

    /**
     * Edit rules
     */
    public function editAction()
    {
        $this->_initFormView();

        $id = (int)$this->_request->get('id');

        $item = IfwPsn_Wp_ORM_Model::factory(self::MODEL)->find_one($id);
        $nameBefore = $item->get('name');

        $this->_form->setDefaults($item->as_array());

        if ($this->_request->isPost()) {
            if ($this->_form->isValid($this->_request->getPost())) {

                // request is valid, save the changes
                $item->hydrate($this->_form->getValues());
                $item->id = $id;
                $item->save();

                $this->getAdminNotices()->persistUpdated(
                    sprintf(__('Recipient list <b>%s</b> has been updated successfully.', 'psn_rec'), $nameBefore));

                $this->_gotoIndex();
            }
        }

        $this->view->form = $this->_form;
    }

    /**
     * Deletes a rule
     */
    public function deleteAction()
    {
        $id = (int)$this->_request->get('id');
        $list = IfwPsn_Wp_ORM_Model::factory(self::MODEL)->find_one($id);

        if (is_a($list, self::MODEL)) {

            if (!$this->_isInUse($list->get('id'))) {
                $rule = IfwPsn_Wp_ORM_Model::factory(self::MODEL)->find_one($id);
                $rule->delete();
            } else {
                $this->getAdminNotices()->persistError(
                    __('Recipients list could not be deleted. It is still in use by a notification rule.', 'psn_rec')
                );
            }
        }

        $this->_gotoIndex();
    }

    /**
     * @param array $items
     */
    protected function _bulkDelete(array $items)
    {
        foreach($items as $id) {

            $list = IfwPsn_Wp_ORM_Model::factory(self::MODEL)->find_one((int)$id);

            if (is_a($list, self::MODEL)) {

                if (!$this->_isInUse($list->get('id'))) {
                    $list->delete();
                } else {
                    $this->getAdminNotices()->persistError(
                        __('Some recipients lists could not be deleted. They are still in use by a notification rule.', 'psn_rec'), 'error'
                    );
                }
            }
        }
    }

    /**
     * @param $id
     * @return bool
     */
    protected function _isInUse($id)
    {
        $result = IfwPsn_Wp_ORM_Model::factory('Psn_Model_Rule')->find_many();

        foreach ($result as $r) {

            $ruleRecipient = $r->getRecipient();
            if (!is_array($ruleRecipient)) {
                $ruleRecipient = array($ruleRecipient);
            }
            $ruleCc = $r->getCcSelect();
            if (!is_array($ruleCc)) {
                $ruleCc = array($ruleCc);
            }
            $ruleBcc = $r->getBccSelect();
            if (!is_array($ruleBcc)) {
                $ruleBcc = array($ruleBcc);
            }

            $listToken = 'list_' . $id;

            if (in_array($listToken, $ruleRecipient) ||
                in_array($listToken, $ruleCc) ||
                in_array($listToken, $ruleBcc)) {

                return true;
            }
        }

        return false;
    }

    /**
     * Copies a template
     */
    public function copyAction()
    {
        IfwPsn_Wp_ORM_Model::duplicate(self::MODEL, $this->_request->get('id'));

        $this->_gotoIndex();
    }

    /**
     * Imports templates
     */
    public function importAction()
    {
        $items = $this->_getImportedItems($_FILES['importfile']['tmp_name'], $this->_exportOptions['item_name_singular']);

        $result = IfwPsn_Wp_ORM_Model::import(self::MODEL, $items, array(
            'prefix' => esc_attr($this->_request->get('import_prefix'))
        ));

        $this->_gotoIndex();
    }

    /**
     *
     */
    public function exportAction()
    {
        $this->_export($this->_request->get('id'));
    }

    /**
     * @param array $items
     */
    protected function _bulkExport($items)
    {
        $this->_export($items);
    }

    /**
     * @param $rules
     */
    protected function _export($rules)
    {
        $options = $this->_exportOptions;
        $options['filename'] = sprintf($options['filename'], date('Y-m-d_H_i_s'));

        IfwPsn_Wp_ORM_Model::export(self::MODEL, $rules, $options);
    }

    protected function _initFormView()
    {
        $this->_initHelp();

        $this->_form = new Psn_Module_Recipients_Admin_Form_RecipientsList();

        $this->_helper->viewRenderer('form');

        if ($this->_request->getActionName() == 'create') {
            $this->view->langHeadline = __('Create new recipients list', 'psn_rec');
        } else {
            $this->view->langHeadline = __('Edit recipients list', 'psn_rec');
            $this->_form->getElement('submit')->setLabel(__('Update', 'psn'));
        }
    }

    protected function _initHelp()
    {
        // set up contextual help
        $help = new IfwPsn_Wp_Plugin_Menu_Help($this->_pm);
        $help->setTitle(__('Recipients lists', 'psn_rec'))
            ->setHelp($this->_getHelpText())
            ->setSidebar($this->_getHelpSidebar())
            ->load();
    }

    /**
     *
     * @return string
     */
    protected function _getHelpText()
    {
        return sprintf(__('Please consider the documentation page <a href="%s" target="_blank">%s</a> for more information.', 'ifw'),
            'http://docs.ifeelweb.de/post-status-notifier/recipients_lists.html',
            __('Recipients lists', 'psn_rec'));
    }

    /**
     *
     * @return string
     */
    protected function _getHelpSidebar()
    {
        $sidebar = '<p><b>' . __('For more information:', 'ifw') . '</b></p>';
        $sidebar .= sprintf('<p><a href="%s" target="_blank">' . __('Plugin homepage', 'ifw') . '</a></p>',
            $this->_pm->getEnv()->getHomepage());
        if (!empty($this->_pm->getConfig()->plugin->docUrl)) {
            $sidebar .= sprintf('<p><a href="%s" target="_blank">' . __('Documentation', 'ifw') . '</a></p>',
                $this->_pm->getConfig()->plugin->docUrl);
        }
        return $sidebar;
    }

    public function enqueueScripts()
    {
        IfwPsn_Wp_Proxy_Script::loadAdmin('jquery-ui-dialog');
        IfwPsn_Wp_Proxy_Style::loadAdmin('wp-jquery-ui');
        IfwPsn_Wp_Proxy_Style::loadAdmin('wp-jquery-ui-dialog');
    }

    protected function _gotoIndex()
    {
        $this->_gotoRoute('recipientslists', 'index', 'post-status-notifier', array('mod' => 'recipients'));
    }
}

 