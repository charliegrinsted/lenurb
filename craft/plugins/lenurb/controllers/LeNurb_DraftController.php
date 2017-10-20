<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Le Nurb Draft Controller
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.1
 */

namespace Craft;

class LeNurb_DraftController extends BaseController
{
    protected $allowAnonymous = true;

    public function actionDraftPlayerIntoSquad()
    {
    	$this->requirePostRequest();
        $playerId = craft()->request->getParam('playerId');
        craft()->leNurb_draft->assignPlayerToParticipant($playerId);
    }
}
