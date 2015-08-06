<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Networkwide task executor
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @version   $Id: Task.php 320 2014-08-13 20:35:07Z timoreithde $
 * @package   
 */ 
abstract class IfwPsn_Wp_Network_Task
{
    /**
     * @param bool $networkwide
     */
    public function execute($networkwide = false)
    {
        if (IfwPsn_Wp_Proxy_Blog::isMultisite() && $networkwide == true) {

            // multisite installation
            // get the current blog id
            $currentBlogId = IfwPsn_Wp_Proxy_Blog::getBlogId();

            // loop through all blogs
            foreach (IfwPsn_Wp_Proxy_Blog::getMultisiteBlogIds() as $blogId) {

                IfwPsn_Wp_Proxy_Blog::switchToBlog($blogId);

                // execute networkwide task
                $this->_execute();
            }

            // switch back to current blog
            IfwPsn_Wp_Proxy_Blog::switchToBlog($currentBlogId);

        } else {
            // no network found or no networkwide execution requested
            // execute single blog task
            $this->_execute();
        }
    }

    public function executeNetworkwide()
    {
        $this->execute(true);
    }

    /**
     * The task to be executed must be implemented by the concrete class
     * @return mixed
     */
    abstract protected function _execute();
}
 