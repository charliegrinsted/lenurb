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

    public function actionImportAllRealFixtures()
    {
        craft()->tasks->createTask('LeNurb_ImportFixtures', Craft::t('Importing fixtures'));
    }

    public function actionGenerateParticipantFixtures()
    {
        craft()->tasks->createTask('LeNurb_GenerateParticipantFixtures', Craft::t('Generating participant fixtures'));
    }

    public function actionImportAllGameweeks()
    {
        craft()->tasks->createTask('LeNurb_ImportGameweeks', Craft::t('Importing gameweeks'));
    }

    public function actionDeleteAllRealFixtures()
    {
        $elementIds = craft()->db->createCommand()->select('id')
            ->from('entries')
            ->where('sectionId = :sectionId', array(':sectionId' => 9))
            ->queryColumn();
        $rowsAffected = craft()->db->createCommand()->delete(
           'elements',
            array('in', 'id', $elementIds)
        );
    }
    }
}
