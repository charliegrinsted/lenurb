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
            $entry->slug = $playerId;
            $playerToAssign = $entry->first();
            if ($playerToAssign) {
                // check if player has an owner and cancel if they do
                if (!craft()->leNurb_draft->checkIfPlayerIsAvailable($playerToAssign)) {
                    return false;
                }
                // check to see if they have space in their squad

                $playerToAssign->getContent()->setAttributes(array(
                    'currentOwner' => array(
                        $participant->id
                    ),
                ));
                craft()->entries->saveEntry($playerToAssign);
            }
        } else {
            return true;
        }
    }

    private function checkIfPlayerIsAvailable($player)
    {
        $criteria = craft()->elements->getCriteria(ElementType::User);
        $criteria->relatedTo = $player;
        $attachedParticipant = $criteria->find();
        return empty($attachedParticipant);
    }
}
