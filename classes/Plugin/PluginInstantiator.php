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

namespace Grav\Plugin;

use Grav\Common\Plugin;

use Grav\Plugin\Shortcodes\ShortcodesNormalizer;

/**
 * Class PluginInstantiator
 *
 * @author GravCMS Pro
 */
class PluginInstantiator
{
    protected $grav = null;

    function __construct($grav)
    {
        $this->grav = $grav;
    }

    public function initShortcodes()
    {
        require_once __DIR__.'/../Base/BaseShortcode.php';
        $path = __DIR__.'/../../shortcodes';
        $this->grav['shortcode']->registerAllShortcodes($path.'/Basic');
        $this->grav['shortcode']->registerAllShortcodes($path.'/Collection');
        $this->grav['shortcode']->registerAllShortcodes($path.'/Component');
        $this->grav['shortcode']->registerAllShortcodes($path.'/Elements');
        $this->grav['shortcode']->registerAllShortcodes($path.'/Google');
        $this->grav['shortcode']->registerAllShortcodes($path.'/Material');
        $this->grav['shortcode']->registerAllShortcodes($path.'/Vendor');
    }

    public function initTemplatePaths()
    {
        $path = __DIR__.'/../../templates';
        $this->grav['twig']->twig_paths[] = $path;
        $this->grav['twig']->twig_paths[] = $path.'/material';
    }

    public function initShortcodesNormalizer()
    {
        require_once __DIR__.'/../../classes/Base/ShortcodesNormalizer.php';
        return new ShortcodesNormalizer($this->grav);
    }
}
