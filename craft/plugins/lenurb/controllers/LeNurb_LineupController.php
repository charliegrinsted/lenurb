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
        $playerIDsToAdd = [];
		foreach ($submittedLineup as $key => $val) {
			if (is_int($key)) {
        		// FEATURE REQUEST check that each player is valid and the whole lineup is legit
				$playerIDsToAdd[] = $val;
			}
		}
        if (count($playerIDsToAdd) != 11) {
            die('not 11 players');
        } else {
            craft()->leNurb_lineup->saveParticipantLineup($playerIDsToAdd);
        }
    }
}
