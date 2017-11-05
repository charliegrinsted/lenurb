<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Le Nurb Import Controller
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.1
 */

namespace Craft;

class LeNurb_ImportController extends BaseController
{
    protected $allowAnonymous = false;

    public function actionImportAllPlayers()
    {
        craft()->tasks->createTask('LeNurb_ImportPlayers', Craft::t('Importing players'));
    }

    public function actionImportAllFixtures()
    {
        craft()->tasks->createTask('LeNurb_ImportFixtures', Craft::t('Importing fixtures'));
    }

    }
}
