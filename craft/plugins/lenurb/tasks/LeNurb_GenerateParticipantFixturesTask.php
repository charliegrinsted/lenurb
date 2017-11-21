<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Le Nurb Generate Participant Fixtures Task
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.1
 */

namespace Craft;

class LeNurb_GenerateParticipantFixturesTask extends BaseTask
{
	private $gameweekFixtureData = [];

	public function getDescription()
	{
		return 'Generating participant fixtures';
	}

	public function getTotalSteps()
	{
		$this->gameweekFixtureData = craft()->leNurb_import->createAllParticipantFixtures();
		return count( $this->gameweekFixtureData );
	}

	public function runStep($step)
	{
        LeNurbPlugin::log('Starting step ' . $step);
		$data = $this->gameweekFixtureData[$step];
        LeNurbPlugin::log($data[0][0] . ' vs ' . $data[0][1] . ' in GW ' . $data[0][2]);
        LeNurbPlugin::log($data[1][0] . ' vs ' . $data[1][1] . ' in GW ' . $data[1][2]);
        LeNurbPlugin::log($data[2][0] . ' vs ' . $data[2][1] . ' in GW ' . $data[2][2]);
        LeNurbPlugin::log($data[3][0] . ' vs ' . $data[3][1] . ' in GW ' . $data[3][2]);
		try {
			return craft()->leNurb_import->createParticipantFixtureBlocks($data);
		} catch (\Exception $e) {
			return false;
		}
	}
}
