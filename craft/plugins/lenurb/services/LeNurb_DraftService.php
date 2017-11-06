<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Le Nurb Draft Service
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.1
 */

namespace Craft;

class LeNurb_DraftService extends BaseApplicationComponent
{

    public function assignPlayerToParticipant($playerId)
    {
        $participant = craft()->userSession->getUser();
        if ($participant && $participant->isInGroup('participants')) {
            $entry = craft()->elements->getCriteria(ElementType::Entry);
            $entry->sectionId = 3;
            $entry->slug = $playerId;
            $playerToAssign = $entry->first();
            if ($playerToAssign) {
                // check if player has an owner and cancel if they do
                if (!craft()->leNurb_draft->checkIfPlayerIsAvailable($playerToAssign)) {
                    return false;
                }
                // check to see if they have space in their squad
                if (!craft()->leNurb_draft->addPlayerToParticipantRosterSlots($playerToAssign, $participant)) {
                    return false;
                }
                $playerToAssign->getContent()->setAttributes(array(
                    'currentOwner' => array(
                        $participant->id
                    ),
                ));
                craft()->entries->saveEntry($playerToAssign);
            }
        } else {
            return false;
        }
    }

    public function releasePlayerFromSquad($playerId)
    {
        $participant = craft()->userSession->getUser();
        if ($participant && $participant->isInGroup('participants')) {
            $entry = craft()->elements->getCriteria(ElementType::Entry);
            $entry->sectionId = 3;
            $entry->slug = $playerId;
            $playerToRelease = $entry->first();
            $playerToRelease->getContent()->setAttribute('currentOwner', []);
            craft()->entries->saveEntry($playerToRelease);
        }
        $participantSquad = craft()->leNurb_draft->getParticipantSquad($participant);
        switch ($playerToRelease->typeId) {
        case 3:
            $playerType = 'goalkeepers';
            break;
        case 4:
            $playerType = 'defenders';
            break;
        case 5:
            $playerType = 'midfielders';
            break;
        case 6:
            $playerType = 'forwards';
            break;
        default:
            return false;
        }
        craft()->leNurb_draft->removePlayerFromRoster($playerToRelease->id, $playerType, $participantSquad);
    }

    private function removePlayerFromRoster($playerId, $playerType, $participantSquad)
    {
        $rosterSlots = $participantSquad[$playerType]->ids();
        $benchSlots = $participantSquad->bench->ids();
        $rosterSlotKey = array_search($playerId, $rosterSlots);
        $benchSlotsKey = array_search($playerId, $benchSlots);
        if ($rosterSlotKey !== false) {
            unset($rosterSlots[$rosterSlotKey]);
            $participantSquad->getContent()->setAttribute($playerType, $rosterSlots);
        }
        else if ($benchSlotsKey !== false) {
            unset($benchSlots[$benchSlotsKey]);
            $participantSquad->getContent()->setAttribute('bench', $benchSlots);
        } else {
            return false;
        }
        craft()->entries->saveEntry($participantSquad);
    }

    private function checkIfPlayerIsAvailable($player)
    {
        $criteria = craft()->elements->getCriteria(ElementType::User);
        $criteria->relatedTo = $player;
        $attachedParticipant = $criteria->find();
        return empty($attachedParticipant);
    }

    private function addPlayerToParticipantRosterSlots($player, $participant)
    {
        $participantSquad = craft()->leNurb_draft->getParticipantSquad($participant);
        switch ($player->typeId) {
        case 3:
            $playerType = 'goalkeepers';
            $limit = 1;
            break;
        case 4:
            $playerType = 'defenders';
            $limit = 3;
            break;
        case 5:
            $playerType = 'midfielders';
            $limit = 3;
            break;
        case 6:
            $playerType = 'forwards';
            $limit = 2;
            break;
        default:
            return false;
        }
        $targetRosterSlot = craft()->leNurb_draft->checkIfRosterSlotIsAvailable($participantSquad, $playerType, $limit);
        if ($targetRosterSlot) {
            $currentRosterSlotIDs = $participantSquad[$targetRosterSlot]->ids();
            $currentRosterSlotIDs[] = $player->id;
            $participantSquad->getContent()->setAttribute($targetRosterSlot, $currentRosterSlotIDs);
            craft()->entries->saveEntry($participantSquad);
            return true;
        } else {
            return false;
        }
    }

    private function checkIfRosterSlotIsAvailable($participantSquad, $playerType, $limit)
    {
        if (count($participantSquad[$playerType]) < $limit) {
            return $playerType;
        }
        else if (count($participantSquad['bench']) < 4) {
            return 'bench';
        }
        else {
            return false;
        }
    }

    private function getParticipantSquad($participant)
    {
        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->sectionId = 5;
        $criteria->relatedTo = $participant;
        $participantSquad = $criteria->first();
        return $participantSquad;
    }
}
