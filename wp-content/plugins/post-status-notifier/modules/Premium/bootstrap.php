<?php
/**
 * Premium module
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: bootstrap.php 397 2015-08-16 20:09:46Z timoreithde $
 */
class Psn_Premium_Bootstrap extends IfwPsn_Wp_Module_Bootstrap_Abstract
{
    /**
     * The module ID
     * @var string
     */
    protected $_id = 'psn_mod_prm';

    /**
     * The module name
     * @var string
     */
    protected $_name = 'Premium';

    /**
     * The module description
     * @var string
     */
    protected $_description = 'Activates premium version';

    /**
     * The module text domain
     * @var string
     */
    protected $_textDomain = 'psn_prm';

    /**
     * The module version
     * @var string
     */
    protected $_version = '1.1';

    /**
     * The module author
     * @var string
     */
    protected $_author = 'Timo';

    /**
     * The author's homepage
     * @var string
     */
    protected $_authorHomepage = 'http://www.ifeelweb.de/';

    /**
     * The module homepage
     * @var string
     */
    protected $_homepage = 'http://www.ifeelweb.de/wp-plugins/post-status-notifier/';

    /**
     * The module dependencies
     * @var array
     */
    protected $_dependencies = array();



    /**
     * @see IfwPsn_Wp_Module_Bootstrap_Abstract::bootstrap()
     */
    public function bootstrap()
    {
        $this->addGlobalCallbacks();

        if ($this->_pm->getAccess()->isPlugin()) {
            $this->addPluginCallbacks();
        }

        if ($this->_pm->getAccess()->isAdmin()) {
            require_once $this->getPathinfo()->getRootLib() . 'Options.php';
            $options = new Psn_Module_Premium_Options($this->_pm, $this);
            $options->load();

            add_filter('envato_license_code', array($this, 'getEnvatoLicenseCode'));
        }

        require_once $this->getPathinfo()->getRootLib() . 'PostSubmitboxHandler.php';
        new Psn_Module_Premium_PostSubmitboxHandler($this);
    }

    public function addGlobalCallbacks()
    {
        IfwPsn_Wp_Proxy_Filter::addPlugin($this->_pm, 'is_premium', array($this, 'setPremium'));

        add_action('psn_add_feature', array($this, 'addFeatures'));
    }

    /**
     * Load premium features
     *
     * @param Psn_Feature_Loader $loader
     */
    public function addFeatures(Psn_Feature_Loader $loader)
    {
        require_once $this->getPathinfo()->getRootLib() . '/Mandrill/Feature.php';
        $loader->addFeature(new Psn_Module_Premium_Mandrill_Feature($this->_pm, $this));

        require_once $this->getPathinfo()->getRootLib() . '/Features/Rules.php';
        $loader->addFeature(new Psn_Module_Premium_Features_Rules($this->_pm, $this));
    }

    public function addPluginCallbacks()
    {
        IfwPsn_Wp_Proxy_Action::add('psn-service-metabox-col3', array($this, 'addServiceCol3Metabox'));
        IfwPsn_Wp_Proxy_Action::add('PsnServiceController_init', array($this, 'initPsnController'));
        IfwPsn_Wp_Proxy_Action::add('PsnOptionsController_init', array($this, 'initPsnController'));
    }

    /**
     * @param IfwPsn_Wp_Plugin_Metabox_Container $container
     */
    public function addServiceCol3Metabox(IfwPsn_Wp_Plugin_Metabox_Container $container)
    {
        require_once $this->getPathinfo()->getRootLib() . '/Metabox/ModuleFrontend.php';

        $container->addMetabox(new Psn_Module_Premium_Metabox_ModuleFrontend($this->_pm));
    }

    /**
     * @param IfwPsn_Zend_Controller_Default $controller
     */
    public function initPsnController(IfwPsn_Zend_Controller_Default $controller)
    {
        IfwPsn_Wp_Proxy_Style::loadAdmin('psn-service-prm', $this->getEnv()->getUrlCss() . 'admin.css');
    }

    /**
     * Sets plugin to premium
     * @param $premium
     * @return bool
     */
    public function setPremium($premium)
    {
        return true;
    }

    /**
     * @param $max
     * @return int
     */
    public function unsetMaxRules($max)
    {
        return 0;
    }

    /**
     * @param $license_code
     * @return string
     */
    public function getEnvatoLicenseCode($license_code)
    {
        if (IfwPsn_Util_Encryption::isEncryptedString($license_code)) {
            return IfwPsn_Util_Encryption::decrypt($license_code, Psn_Module_Premium_Options::LICENSE_CODE_SALT);
        }
        return $license_code;
    }
}