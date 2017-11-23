<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Le Nurb Import Service
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.1
 */

namespace Craft;

class LeNurb_ImportService extends BaseApplicationComponent
{
    public function getAllPlayerData()
    {
        $url = 'https://fantasy.premierleague.com/drf/bootstrap-static';
        return craft()->leNurb_helpers->getJSONwithGuzzle($url);
    }

    public function getAllFixtureData()
    {
        $url = 'https://fantasy.premierleague.com/drf/fixtures';
        return craft()->leNurb_helpers->getJSONwithGuzzle($url);
    }

    public function getAllGameweekData()
    {
        $url = 'https://fantasy.premierleague.com/drf/events';
        return craft()->leNurb_helpers->getJSONwithGuzzle($url);
    }

    {
    }

    public function createAllParticipantFixtures()
    {
        $allGameweeks = craft()->leNurb_helpers->getAllGameweeksFromCraft();
        $allParticipants = craft()->leNurb_helpers->getAllParticipantIDs();
        $away = array_splice($allParticipants, (count($allParticipants) / 2));
        $home = $allParticipants;
        $totalWeeks = ( count($allGameweeks) - 3 );
        $allFixtures = [];
        for ($i = 0; $i < $totalWeeks; $i++) {
            for ($j = 0; $j < count($home); $j++){
                $allFixtures[$i][] = [$home[$j], $away[$j], $allGameweeks[$i]->id];
            }
            if(count($home)+count($away)-1 > 2){
                array_unshift($away, current(array_splice($home,1,1)));
                array_push($home,array_pop($away));
            }
        }
        return $allFixtures;
    }

    public function createParticipantFixtureBlocks($data)
    {
        $matrixFieldId = craft()->leNurb_helpers->getMatrixFieldId('gameweekFixtures');
        $matrixTypeId = craft()->leNurb_helpers->getMatrixTypeId('gameweekFixtures');
        LeNurbPlugin::log('Fixture total is ' . count($data));
        for ($i = 0; $i < count($data); $i++) {
            LeNurbPlugin::log($data[$i][0] . ' vs ' . $data[$i][1] . ' in GW ' . $data[$i][2]);
            $block = new MatrixBlockModel();
            $block->fieldId = $matrixFieldId;
            $block->typeId = $matrixTypeId;
            $block->ownerId = $data[$i][2];
            LeNurbPlugin::log('No errors yet 1');
            $block->getContent()->setAttributes(array(
                'home' => array(
                    $data[$i][0]
                ),
                'away' => array(
                    $data[$i][1]
                )
            ));
            LeNurbPlugin::log('No errors yet 2');
            $success = craft()->matrix->saveBlock($block, false);
            LeNurbPlugin::log('No errors yet 3');
        }
        return true;
    }

    public function createFixtureEntry($fixtureData, $sectionId)
    {
        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->sectionId = $sectionId;
        $criteria->slug = $fixtureData['id'];
        $existingFixture = $criteria->first();
        if ($existingFixture) {
            return true;
        }
        $entry = new EntryModel();
        $entry->slug = $fixtureData['id'];
        $entry->sectionId = $sectionId;
        $homeTeam = craft()->leNurb_helpers->getTeamFromCraft($fixtureData['team_h']);
        $awayTeam = craft()->leNurb_helpers->getTeamFromCraft($fixtureData['team_a']);
        $gameweek = craft()->leNurb_helpers->getGameweekFromCraft($fixtureData['event']);
        $entry->getContent()->setAttributes(array(
            'title' => ($homeTeam->title . ' v ' . $awayTeam->title),
            'postDate' => DateTimeHelper::formatTimeForDb($fixtureData['kickoff_time']),
            'associatedGameweek' => array(
                $gameweek->id
            ),
            'homeTeam' => array(
                $homeTeam->id
            ),
            'awayTeam' => array(
                $awayTeam->id
            ),
        ));
        craft()->entries->saveEntry($entry);
        return true;
    }

    public function createGameweekEntry($gameweekData, $sectionId)
    {
        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->sectionId = $sectionId;
        $criteria->slug = $gameweekData['id'];
        $existingFixture = $criteria->first();
        if ($existingFixture) {
            return true;
        }
        $entry = new EntryModel();
        $entry->slug = $gameweekData['id'];
        $entry->sectionId = $sectionId;
        $entry->getContent()->setAttributes(array(
            'title' => $gameweekData['name'],
            'gameweekDeadline' => DateTimeHelper::formatTimeForDb($gameweekData['deadline_time_epoch']),
        ));
        craft()->entries->saveEntry($entry);
        return true;
    }

    public function createPlayerEntry($playerData, $sectionId)
    {
        if ($playerData['status'] === 'u') {
            return true;
        }
        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->sectionId = $sectionId;
        $criteria->slug = $playerData['id'];
        LeNurbPlugin::log('Looking for: ' . $playerData['web_name']);
        $existingPlayer = $criteria->first();
        if ($existingPlayer) {
            LeNurbPlugin::log('Found: ' . $existingPlayer->title);
            return true;
        }
        $entry = new EntryModel();
        $entry->sectionId = $sectionId;
        switch ($playerData['element_type']) {
            case 1:
                $entry->typeId = 3;
                break;
            case 2:
                $entry->typeId = 4;
                break;
            case 3:
                $entry->typeId = 5;
                break;
            case 4:
                $entry->typeId = 6;
                break;
        }
        $entry->slug = $playerData['id'];
        $team = craft()->leNurb_helpers->getTeamFromCraft($playerData['team']);
        $entry->getContent()->setAttributes(array(
            'title' => ($playerData['web_name']),
            'fullName' => ($playerData['first_name'] . ' ' . $playerData['second_name']),
            'currentTeam' => array(
                $team->id
            ),
            'totalPoints' => ($playerData['total_points']),
        ));
        craft()->entries->saveEntry($entry);
        return true;
    }
}
