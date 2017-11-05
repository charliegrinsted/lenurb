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
            craft()->fileCache->set($url, $items);
            return $items;
        } catch(\Exception $e) {
            return;
        }
    }

    public function getAllFixtureData()
    {
        $url = 'https://fantasy.premierleague.com/drf/fixtures';
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

    public function downloadPlayerPhoto($playerId)
    {
        $fileName = $playerId . '.png';
        $criteria = craft()->elements->getCriteria(ElementType::Asset);
        $criteria->filename = $fileName;
        $file = $criteria->first();
        if ($file) {
            return $file->id;
        }
        $fileUrl = 'https://platform-static-files.s3.amazonaws.com/premierleague/photos/players/250x250/p' . $playerId . '.png';
        $assetSourceId = 1;
        $assetFolderId = (int)craft()->assets->getRootFolderBySourceId($assetSourceId)->id;
        $tempFilePath = AssetsHelper::getTempFilePath($fileName);
        $fileContents = file_get_contents($fileUrl);
        file_put_contents($tempFilePath, $fileContents);
        $oAssetOperationResponse = craft()->assets->insertFileByLocalPath(
            $tempFilePath,
            $fileName,
            $assetFolderId,
            AssetConflictResolution::Replace
        );
        return (int)$oAssetOperationResponse->responseData["fileId"];
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
        $homeTeam = craft()->leNurb_import->getTeamFromCraft($fixtureData['team_h']);
        $awayTeam = craft()->leNurb_import->getTeamFromCraft($fixtureData['team_a']);
        $entry->getContent()->setAttributes(array(
            'title' => ($homeTeam->title . ' v ' . $awayTeam->title),
            'postDate' => $fixtureData['kickoff_time'],
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

    public function getTeamFromCraft($teamFPLid)
    {
        $team = craft()->elements->getCriteria(ElementType::Entry);
        $team->fplId = $teamFPLid;
        return $team->first();
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
        $team = craft()->leNurb_import->getTeamFromCraft($playerData['team']);
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
