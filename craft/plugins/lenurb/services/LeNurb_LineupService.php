<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Le Nurb Lineup Service
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.2
 */

namespace Craft;

class LeNurb_LineupService extends BaseApplicationComponent
{
    private function validatePlayerCounts($goalkeeper, $defenders, $midfielders, $forwards)
    {
        if (empty($goalkeeper) || count($goalkeeper) > 1) {
            return false;
        }
        if (count($defenders) > 5 || count($defenders) < 3) {
            return false;
        }
        if (count($midfielders) > 5 || count($midfielders) < 3) {
            return false;
        }
        if (count($forwards) > 3 || count($forwards) < 1) {
            return false;
        }
        return true;
    }

    private function validateLineupFormation($defenders, $midfielders, $forwards)
    {
        if (count($defenders) === 3) {
            if (count($midfielders) === 3) {
                return false;
            }
            if (count($midfielders) === 4) {
                if (count($forwards) != 3 ) {
                    return false;
                } else {
                    return 13; // 3-4-3 ID
                }
            }
            if (count($midfielders) === 5) {
                if (count($forwards) != 2 ) {
                    return false;
                } else {
                    return 15; // return 3-5-2
                }
            }
        }
        if (count($defenders) === 4) {
            if (count($midfielders) === 5) {
                if (count($forwards) != 1 ) {
                    return false;
                } else {
                    return 16; // 4-5-1
                }
            }
            if (count($midfielders) === 4) {
                if (count($forwards) != 2 ) {
                    return false;
                } else {
                    return 10; // 4-4-2
                }
            }
            if (count($midfielders) === 3) {
                if (count($forwards) != 3 ) {
                    return false;
                } else {
                    return 11; // 3-4-3
                }
            }
        }
        if (count($defenders) === 5) {
            if (count($midfielders) === 5) {
                return false;
            }
            if (count($midfielders) === 4) {
                if (count($forwards) != 1 ) {
                    return false;
                } else {
                    return 12; // 5-4-1
                }
            }
            if (count($midfielders) === 3) {
                if (count($forwards) != 2 ) {
                    return false;
                } else {
                    return 14; // 5-3-2
                }
            }
        }
        return false;
    }

    public function saveParticipantLineup($lineup)
    {
        $participant = craft()->userSession->getUser();
        $currentGameweek = craft()->leNurb_helpers->getCurrentGameweek();
        // need to get the current gameweek and look for an entry related to the user and gameweek
        $existingLineupCriteria = craft()->elements->getCriteria(ElementType::Entry);
        $existingLineupCriteria->sectionId = 7;
        $existingLineupCriteria->relatedTo = $currentGameweek;
        $existingLineupCriteria->relatedTo = $participant;
        $existingLineup = $existingLineupCriteria->find();
        if ($existingLineup) {
            die('you already set a lineup');
        }
        // if not then do below:
        $entry = new EntryModel();
        $entry->sectionId = 7; // Lineups ID
        // need to get the fields depending on the entry type... unsure about this
        // need to save players
        $goalkeeper = [];
        $defenders = [];
        $midfielders = [];
        $forwards = [];
        foreach ($lineup as $player) {
            $playerToSort = craft()->leNurb_helpers->getPlayerFromCraftAndCheckOwnership($player, $participant);
            switch ($playerToSort->typeId) {
            case 3:
                $goalkeeper[] = $playerToSort->id;
                break;
            case 4:
                $defenders[] = $playerToSort->id;
                break;
            case 5:
                $midfielders[] = $playerToSort->id;
                break;
            case 6:
                $forwards[] = $playerToSort->id;
                break;
            default:
                return false;
            }
        }
        $isValid = craft()->leNurb_lineup->validatePlayerCounts($goalkeeper, $defenders, $midfielders, $forwards);
        if ($isValid) {
            $formation = craft()->leNurb_lineup->validateLineupFormation($defenders, $midfielders, $forwards);
            $defendersField = 'defendersLimit' . count($defenders);
            $midfieldersField = 'midfieldersLimit' . count($midfielders);
            $forwardsField = 'forwardsLimit' . count($forwards);
            if ($formation) {
                $entry->typeId = $formation;
                $entry->getContent()->setAttributes(array(
                    'title' => ($participant->username . ' - ' . $currentGameweek->title),
                    'gameweek' => array(
                        $currentGameweek->id
                    ),
                    'manager' => array(
                        $participant->id
                    ),
                    'goalkeeper' => $goalkeeper,
                    $defendersField => $defenders,
                    $midfieldersField => $midfielders,
                    $forwardsField => $forwards,
                ));
                craft()->entries->saveEntry($entry);
            }
        } else {
            return false;
        }
    }
}
