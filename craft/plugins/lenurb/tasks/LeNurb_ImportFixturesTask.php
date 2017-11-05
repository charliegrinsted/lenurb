<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Le Nurb Import Fixtures Task
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.1
 */

namespace Craft;

class LeNurb_ImportFixturesTask extends BaseTask
{
	private $allApiData = [];

	public function getDescription()
	{
		return 'Importing fixtures';
	}

	public function getTotalSteps()
	{
		$this->allApiData = craft()->leNurb_import->getAllFixtureData();
		return count( $this->allApiData );
	}

	public function runStep($step)
	{
        $fixture = $this->allApiData[$step];
        try {
            return craft()->leNurb_import->createFixtureEntry($fixture, 9);
        } catch (\Exception $e) {
            return false;
        }
	}
}
