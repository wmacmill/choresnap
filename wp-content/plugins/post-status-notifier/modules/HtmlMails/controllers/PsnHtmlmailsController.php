<?php
/**
 * Index controller
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: PsnHtmlmailsController.php 381 2015-04-24 21:50:20Z timoreithde $
 * @package  IfwPsn_Wp
 */
class Htmlmails_PsnHtmlmailsController extends PsnApplicationController
{
    /**
     * DB model class name
     */
    const MODEL = 'Psn_Module_HtmlMails_Model_MailTemplates';

    /**
     * @var string
     */
    protected $_itemPostId = 'htmlmail';

    /**
     * @var array
     */
    protected $_exportOptions = array(
        'item_name_plural' => 'mail_templates',
        'item_name_singular' => 'mail_template',
        'filename' => 'PSN_mail_templates_export_%s'
    );

    /**
     * @var IfwPsn_Zend_Form
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
            $this->_perPage = new IfwPsn_Wp_Plugin_Screen_Option_PerPage($this->_pm, __('Items per page', 'ifw'), 'psn_mailtemplates_per_page');
        }
    }

    /**
     * 
     */
    public function indexAction()
    {
        // set up contextual help
        $help = new IfwPsn_Wp_Plugin_Menu_Help($this->_pm);
        $help->setTitle(__('Mail templates', 'psn_htm'))
            ->setHelp($this->_getHelpText())
            ->setSidebar($this->_getHelpSidebar())
            ->load();

        $listTable = new Psn_Module_HtmlMails_ListTable_MailTemplates($this->_pm);
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

            if (!$this->_form->isValidNonce()) {

                $this->getAdminNotices()->persistError(__('Invalid access.', 'psn'));
                $this->_gotoIndex();

            } elseif ($this->_form->isValid($this->_request->getPost())) {

                // request is valid, save the rule
                $rule = IfwPsn_Wp_ORM_Model::factory(self::MODEL)->create($this->_form->removeNonceAndGetValues());
                $rule->save();

                $this->getAdminNotices()->persistUpdated(
                    sprintf(__('Mail template "%s" has been saved successfully.', 'psn_htm'), $rule->get('name')));

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

        $template = IfwPsn_Wp_ORM_Model::factory(self::MODEL)->find_one($id);
        $templateNameBefore = $template->get('name');


        $this->_form->setDefaults($template->as_array());

        if ($this->_request->isPost()) {

            if (!$this->_form->isValidNonce()) {

                $this->getAdminNotices()->persistError(__('Invalid access.', 'psn'));
                $this->_gotoIndex();

            } elseif ($this->_form->isValid($this->_request->getPost())) {

                // request is valid, save the changes
                $template->hydrate($this->_form->removeNonceAndGetValues());
                $template->id = $id;
                $template->save();

                $this->getAdminNotices()->persistUpdated(
                    sprintf(__('Mail template "%s" has been updated successfully.', 'psn_htm'), $templateNameBefore));

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
        $tplId = (int)$this->_request->get('id');

        if (!wp_verify_nonce($this->_request->get('nonce'), 'tpl-delete-' . $this->_request->get('id'))) {
            $this->getAdminNotices()->persistError(__('Invalid access.', 'psn'));
            $this->_gotoIndex();
        }

        if (!$this->_isTemplateInUse($tplId)) {
            $rule = IfwPsn_Wp_ORM_Model::factory(self::MODEL)->find_one((int)$this->_request->get('id'));
            $rule->delete();
        } else {
            $this->getAdminNotices()->persistError(
                __('Mail template could not be deleted. It is still in use by a notification rule.', 'psn_htm')
            );
        }
        $this->_gotoIndex();
    }

    /**
     * @param array $items
     */
    protected function _bulkDelete(array $items)
    {
        foreach($items as $tplId) {
            if (!$this->_isTemplateInUse($tplId)) {
                IfwPsn_Wp_ORM_Model::factory(self::MODEL)->find_one((int)$tplId)->delete();
            } else {
                $this->getAdminNotices()->persistError(
                    __('Some mail templates could not be deleted. They are still in use by a notification rule.', 'psn_htm')
                );
            }
        }
    }

    /**
     * @param $id
     * @return bool
     */
    protected function _isTemplateInUse($id)
    {
        $result = IfwPsn_Wp_ORM_Model::factory('Psn_Model_Rule')->where_equal('mail_tpl', $id)->find_array();

        if (!is_array($result)) {
            $result = array();
        }

        return count($result) > 0;
    }

    /**
     * Copies a template
     */
    public function copyAction()
    {
        $id = (int)$this->_request->get('id');

        if (IfwPsn_Wp_ORM_Model::duplicate(self::MODEL, $id)) {

            $model = IfwPsn_Wp_ORM_Model::factory(self::MODEL)->find_one($id);
            $this->getAdminNotices()->persistUpdated(
                sprintf(__('Email template "%s" copied successfully.', 'psn_htm'), $model->get('name'))
            );
        } else {
            $this->getAdminNotices()->persistError(
                __('Sorry, could not copy email template.', 'psn_htm')
            );
        }

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
        $mod = $this->_pm->getBootstrap()->getModuleManager()->getModule('psn_mod_htm');

        IfwPsn_Wp_Proxy_Script::loadAdmin('ckeditor', $mod->getEnv()->getUrlJs() . 'ckeditor/ckeditor.js', array('jquery'), $mod->getEnv()->getVersion());
        IfwPsn_Wp_Proxy_Script::localize('ckeditor', 'ckconfig', array(
            'lang' => IfwPsn_Wp_Proxy_Blog::getLanguageShort()
        ));
        IfwPsn_Wp_Proxy_Script::loadAdmin('ckeditor-adapter', $mod->getEnv()->getUrlJs() . 'ckeditor/adapters/jquery.js', array('ckeditor'), $mod->getEnv()->getVersion());


        $this->_form = new Psn_Module_HtmlMails_Admin_Form_MailTemplate();

        $this->_helper->viewRenderer('form');

        $placeholders = new Psn_Notification_Placeholders();

        $help = new IfwPsn_Wp_Plugin_Menu_Help($this->_pm);
        $help->setTitle(__('Placeholders', 'psn'))
            ->setId('placeholders')
            ->setHelp($placeholders->getOnScreenHelp())
            ->setSidebar($this->_getHelpSidebar())
            ->load();
        $help = new IfwPsn_Wp_Plugin_Menu_Help($this->_pm);
        $help->setTitle(__('Conditions', 'psn'))
            ->setId('conditions')
            ->setHelp(IfwPsn_Wp_Tpl::getFilesytemInstance($this->_pm)->render('admin_help_conditions.html.twig', array('pm' => $this->_pm)))
            ->setSidebar($this->_getHelpSidebar())
            ->load();

        if ($this->_request->getActionName() == 'create') {
            $this->view->langHeadline = __('Create new mail template', 'psn_htm');
        } else {
            $this->view->langHeadline = __('Edit mail template', 'psn_htm');
            $this->_form->getElement('submit')->setLabel(__('Update', 'psn'));
        }
    }

    /**
     *
     * @return string
     */
    protected function _getHelpText()
    {
        return sprintf(__('Please consider the documentation page <a href="%s" target="_blank">%s</a> for more information.', 'ifw'),
            'http://docs.ifeelweb.de/post-status-notifier/mail_templates.html',
            __('Mail templates', 'psn_htm'));
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
        $this->_gotoRoute('htmlmails', 'index', 'post-status-notifier', array('mod' => 'htmlmails'));
    }
}
