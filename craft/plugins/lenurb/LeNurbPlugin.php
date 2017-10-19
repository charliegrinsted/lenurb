<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Fantasy draft football made sorta easy
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.1
 */

namespace Craft;

class LeNurbPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init()
    {
        require CRAFT_PLUGINS_PATH.'/lenurb/vendor/autoload.php';
        parent::init();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('Le Nurb League');
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Does Fantasy Football stuff');
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return '???';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return '???';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '0.0.2';
    }

    /**
     * @return string
     */
    public function getSchemaVersion()
    {
        return '0.0.1';
    }

    /**
     * @return string
     */
    public function getDeveloper()
    {
        return 'Charlie Grinsted';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return '';
    }

    /**
     * @return bool
     */
    public function hasCpSection()
    {
        return true;
    }

    /**
     */
    public function onBeforeInstall()
    {
    }

    /**
     */
    public function onAfterInstall()
    {
    }

    /**
     */
    public function onBeforeUninstall()
    {
    }

    /**
     */
    public function onAfterUninstall()
    {
    }

}