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
 * Class NavbarShortcode
 *
 * @author GravCMS Pro
 */
class NavbarShortcode extends BaseShortcode
{
    /**
     * {@inheritdoc}
     */
    public function shortcodeName()
    {
        return 'm-navbar';
    }

    /**
     * {@inheritdoc}
     */
    protected function childrenShortcodes()
    {
        return array(
            'm-navbar-item',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function template()
    {
        return 'navbar/navbar.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    protected function renderOutput(ShortcodeInterface $shortcode)
    {
        $this->addAssets();
        $childShortcodes = $this->shortcode->getStates($this->shortcode->getId($shortcode));

        $output = $this->grav['twig']->processTemplate($this->template(), [
            'values' => array(
                'sidebar_id' => $shortcode->getParameter('sidebar-id'),
                'transparent' => $shortcode->getParameter('transparent'),
                'fixed' => $shortcode->getParameter('fixed'),
                'extended' => $childShortcodes >= 2,
                'brand_url' => $shortcode->getParameter('brand-url'),
                'brand_link_attributes' => $shortcode->getParameter('brand-link-attributes'),
                'brand_image' => $shortcode->getParameter('brand-image'),
                'brand_image_path' => $shortcode->getParameter('brand-image-path'),
                'brand_image_attributes' => $shortcode->getParameter('brand-image-attributes'),
                'brand_align' => $shortcode->getParameter('brand-align'),
                'brand_name' => $shortcode->getParameter('brand-name'),
                'brand_icon' => $shortcode->getParameter('brand-icon'),
                'content' => $shortcode->getContent(),
                'childShortcodes' => $childShortcodes,
            ),
        ]);

        return $output;
    }

    private function addAssets()
    {
        $this->shortcode->addAssets('js', 'plugin://centre/js/material/navbar-init.js');
        $this->shortcode->addAssets('js', 'plugin://centre/js/material/sidenav-init.js');
        $this->shortcode->addAssets('js', 'plugin://centre/js/material/navbar-transparent.js');
    }
}
