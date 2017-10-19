<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Le Nurb Import Task
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.1
 */

namespace Craft;

class LeNurb_ImportTask extends BaseTask
{
	private $allApiData = [];

	public function getDescription()
	{
		return 'Importing players';
	}

	public function getTotalSteps()
	{
		$this->allApiData = craft()->leNurb_import->getAllPlayerData();
		return count( $this->allApiData['elements'] );
	}

	public function runStep($step)
	{
        $player = $this->allApiData['elements'][$step];
        try {
            return craft()->leNurb_import->createPlayerEntry($player, 3);
        } catch (\Exception $e) {
            return false;
        }
	}
}
