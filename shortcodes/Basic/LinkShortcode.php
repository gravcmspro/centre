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

/**
 * Class LinkShortcode
 *
 * @author GravCMS Pro
 */
class LinkShortcode extends BaseShortcode
{
    /**
     * {@inheritdoc}
     */
    public function shortcodeName()
    {
        return 'm-link';
    }

    /**
     * {@inheritdoc}
     */
    protected function template()
    {
        return 'basic/link.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    protected function aliases()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    protected function renderOutput(ShortcodeInterface $shortcode)
    {
        $output = $this->grav['twig']->processTemplate($this->template(), [
            'values' => array(
              'url' => $shortcode->getParameter('url'),
              'menu' => $shortcode->getParameter('menu'),
              'link_attributes' => $shortcode->getParameter('link-attributes'),
              'icon' => $shortcode->getParameter('icon'),
              'icon_container' => $shortcode->getParameter('icon-container'),
              'icon_attributes' => $shortcode->getParameter('icon-attributes'),
              'stacked' => $this->stringToBoolean($shortcode->getParameter('stacked')),
              'large_icon' => $this->stringToBoolean($shortcode->getParameter('large-icon')),
              ),
        ]);

        return $output;
    }
}
