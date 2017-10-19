<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Le Nurb Player Model
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.1
 */

namespace Craft;

class LeNurb_PlayerModel extends BaseModel
{
    protected function defineAttributes()
    {
        return [
            'firstname' => [
                'type'      => AttributeType::String,
            ],
            'surname' => [
                'type'     => AttributeType::String,
            ],
            'team' => [
                'type'      => AttributeType::String,
            ],
        ];
    }

    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

}
