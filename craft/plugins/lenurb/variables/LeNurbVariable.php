<?php
/**
 * Le Nurb League plugin for Craft CMS
 *
 * Le Nurb variable
 *
 * @author    Charlie Grinsted
 * @copyright Copyright (c) 2017 Charlie Grinsted
 * @package   Le Nurb
 * @since     0.0.1
 */

namespace Craft;

class LeNurbVariable
{
    public function endUserSession()
    {
        craft()->httpSession->destroy();
    }
}
