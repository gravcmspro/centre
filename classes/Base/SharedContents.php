<?php
/**
 * This file is part of "Centre" Material plugin developed by GravCMS Pro and
 * it is distributed under a proprietary license, summarized down below.
 *
 *  - Can be distributed and used in a single project
 *  - Non-commercial use only
 *  - A backlink to Grav CMS Pro website is required
 *  - Can modify source-code but cannot distribute modifications (derivative works)
 *  - Attribution to software creator must be made
 *
 * The full license is distributed with this package. If you do not receive your
 * license copy, write to info@gravcmspro.com. To use this software you must agree
 * with this license.
 *
 * For commercial projects you can get a Commercial License. Leaan more on this
 * topic at https://gravcmspro.com/#prices
 *
 * Copyright (c) Grav CMS Pro - All rights reserved
 * @license   GravCMS Pro License
 */

namespace Grav\Plugin\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Thunder\Shortcode\Syntax\CommonSyntax;
use Thunder\Shortcode\Parser\RegularParser;
use Grav\Common\Grav;

/**
 * Class SharedContents
 *
 * @author GravCMS Pro
 */
class SharedContents
{
    protected $grav = null;

    public function __construct($grav)
    {
        $this->grav = $grav;
    }

    public function match($page)
    {
        $pageContent = $page->getRawContent();
        preg_match_all('/\<\|([^\.]+).([^\|]+)\|\>/i', $pageContent, $matches, PREG_SET_ORDER);
        foreach($matches as $match) {
            if (count($match) < 3) {
                continue;
            }

            $modelFile = sprintf('%s/components/%s/%s.component',  $this->grav["locator"]->findResource('user://'), strtolower(trim($match[1])), strtolower(trim($match[2])));
            if (!file_exists($modelFile)) {
                continue;
            }

            $modelContent = file_get_contents($modelFile);
            $pageContent = str_replace($match[0], $modelContent, $pageContent);
        }

        return $pageContent;
    }
}
