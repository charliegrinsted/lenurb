<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Le Nurb Helpers Service
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.2
 */

namespace Craft;

class LeNurb_HelpersService extends BaseApplicationComponent
{
    // Pass in a user and get their current squad back
    public function getParticipantSquad($participant)
    {
        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->sectionId = 5;
        $criteria->relatedTo = $participant;
        $participantSquad = $criteria->first();
        return $participantSquad;
    }

    // Pass in a URL and get a JSON object back
    public function getJSONwithGuzzle($url)
    {
        $cachedResponse = craft()->fileCache->get($url);
        if ($cachedResponse) {
            return $cachedResponse;
        }
        try {
            $client = new \Guzzle\Http\Client();
            $request = $client->get($url);
            $response = $request->send();
            if (!$response->isSuccessful()) {
                return;
            }
            $items = $response->json();
            craft()->fileCache->set($url, $items);
            return $items;
        } catch(\Exception $e) {
            return;
        }
    }

    public function getPlayerFromCraft($playerId)
    {
        $player = craft()->elements->getCriteria(ElementType::Entry);
        $player->sectionId = 3;
        $player->slug = $playerId;
        return $player->first();
    }

    public function getPlayerFromCraftAndCheckOwnership($playerId, $participant)
    {
        $player = craft()->elements->getCriteria(ElementType::Entry);
        $player->sectionId = 3;
        $player->relatedTo = $participant;
        $player->slug = $playerId;
        return $player->first();
    }

    public function getTeamFromCraft($teamFPLid)
    {
        $team = craft()->elements->getCriteria(ElementType::Entry);
        $team->sectionId = 4;
        $team->fplId = $teamFPLid;
        return $team->first();
    }

    public function getGameweekFromCraft($eventId)
    {
        $gameweek = craft()->elements->getCriteria(ElementType::Entry);
        $gameweek->sectionId = 8;
        $gameweek->slug = $eventId;
        return $gameweek->first();
    }

    public function getAllGameweeksFromCraft()
    {
        $gameweeks = craft()->elements->getCriteria(ElementType::Entry);
        $gameweeks->sectionId = 8;
        $gameweeks->order = 'gameweekDeadline asc';
        return $gameweeks->find();
    }

    public function getAllParticipantIDs()
    {
        $participantsToReturn = [];
        $participants = craft()->elements->getCriteria(ElementType::User);
        $participants->affiliateGroup = 'participants';
        $participantsToLoop = $participants->find();
        foreach ($participantsToLoop as $participant) {
           $participantsToReturn[] = $participant->id;
        }
        return $participantsToReturn;
    }

    public function getCurrentGameweek()
    {
        $status = craft()->globals->getSetByHandle('status');
        return $status->currentGameweek[0];
    }

    public function getMatrixTypeId($handle)
    {
        $matrix = craft()->fields->getFieldByHandle($handle);
        $matrixBlocks = craft()->matrix->getBlockTypesByFieldId($matrix->id);
        $matrixTypeId = $matrixBlocks[0]->id;
        return $matrixTypeId;
    }

    public function getMatrixFieldId($handle)
    {
        $matrix = craft()->fields->getFieldByHandle($handle);
        return $matrix->id;
    }

}
