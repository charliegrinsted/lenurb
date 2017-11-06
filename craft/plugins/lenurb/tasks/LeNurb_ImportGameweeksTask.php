<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Le Nurb Import Gameweeks Task
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.1
 */

namespace Craft;

class LeNurb_ImportGameweeksTask extends BaseTask
{
	private $allApiData = [];

	public function getDescription()
	{
		return 'Importing gameweeks';
	}

	public function getTotalSteps()
	{
		$this->allApiData = craft()->leNurb_import->getAllGameweekData();
		return count( $this->allApiData );
	}

	public function runStep($step)
	{
        $gameweek = $this->allApiData[$step];
        try {
            return craft()->leNurb_import->createGameweekEntry($gameweek, 8);
        } catch (\Exception $e) {
            return false;
        }
	}
}
