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
 * Class SideNavShortcode
 *
 * @author GravCMS Pro
 */
class SideNavShortcode extends BaseShortcode
{
    /**
     * {@inheritdoc}
     */
    public function shortcodeName()
    {
        return 'm-sidenav';
    }

    /**
     * {@inheritdoc}
     */
    protected function childrenShortcodes()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    protected function template()
    {
        return 'material/sidenav/sidenav.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    protected function renderOutput(ShortcodeInterface $shortcode)
    {
        if (!$this->visibility($shortcode)) {
            return '';
        }

        $this->addAssets();
        $output = $this->grav['twig']->processTemplate($this->template(), [
            'values' => array(
                'sidebar_id' => $shortcode->getParameter('sidebar-id'),
                'content' => $shortcode->getContent(),
            ),
        ]);

        return $output;
    }

    private function visibility(ShortcodeInterface $shortcode) {
        $visibility = $shortcode->getParameter('visible');
        if (null === $visibility) {
            return true;
        }

        $menuName = strtolower($this->grav['page']->menu());
        $tokens = explode(',', $visibility);
        if (in_array($menuName, $tokens)) {
            return true;
        }

        $parent = $this->searchParent($tokens);
        if (null !== $parent) {
            $menuName = strtolower($this->grav['page']->parent()->menu());
            if (in_array($menuName, $tokens)) {
                return true;
            }
        }

        return false;
    }

    private function searchParent(array $tokens) {
        foreach($tokens as $token) {
            $subtokens = explode(":", $token);
            if(count($subtokens) > 1 && $subtokens[0] === 'parent') {
                return $subtokens[1];
            }
        }
    }

    private function addAssets()
    {
        $this->shortcode->addAssets('js', 'plugin://centre/js/material/sidenav-init.js');
    }
}
