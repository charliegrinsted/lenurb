<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Le Nurb Lineup Controller
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.1
 */

namespace Craft;

class LeNurb_LineupController extends BaseController
{
    protected $allowAnonymous = true;

    public function actionUpdateLineup()
    {
    	$this->requirePostRequest();
        $submittedLineup = craft()->request->getPost();
        die(var_export($submittedLineup));
        // craft()->leNurb_draft->assignPlayerToParticipant($playerId);
    }

}
