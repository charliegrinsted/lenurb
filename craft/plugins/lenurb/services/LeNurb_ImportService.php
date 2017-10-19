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
            // Cache the response
            craft()->fileCache->set($url, $items);
            return $items;
        } catch(\Exception $e) {
            return;
        }
    }

    public function storeAllPlayerData($players)
    {
        foreach ($players as $player) {
            craft()->leNurb_import->createPlayerEntry($player, 3);
        }
    }

    public function createPlayerEntry($playerData, $sectionId)
    {
        $entry = new EntryModel();
        $entry->sectionId = $sectionId;
        $entry->enabled = true;
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
        $team = craft()->elements->getCriteria(ElementType::Entry);
        $team->fplId = $playerData['team'];
        $teamToAssign = $team->first();
        $entry->getContent()->setAttributes(array(
            'title' => ($playerData['first_name'] . ' ' . $playerData['second_name']),
            'firstName' => ($playerData['first_name']),
            'surname' => ($playerData['second_name']),
            'currentTeam' => array(
                $teamToAssign->id
            ),
        ));
        craft()->entries->saveEntry($entry);
    }
}
