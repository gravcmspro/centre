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

use Grav\Common\Page\Page;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

/**
 * Class MenuShortcode
 *
 * @author GravCMS Pro
 */
class MenuShortcode extends BaseShortcode
{
    /**
     * {@inheritdoc}
     */
    public function shortcodeName()
    {
        return 'm-menu';
    }

    /**
     * {@inheritdoc}
     */
    protected function template()
    {
        return 'material/menu/menu.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    protected function childrenShortcodes()
    {
        return array(
            'm-menu-item',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function renderOutput(ShortcodeInterface $shortcode)
    {
        require_once __DIR__.'/../../classes/Base/SiteMenu.php';
        $siteMenu = new SiteMenu($this->grav);
        $childShortcodes = $this->shortcode->getStates($this->shortcode->getId($shortcode));
        if (null === $childShortcodes) {
            $childShortcodes = array();
        }
        $menu = $siteMenu->buildMenu($shortcode, $childShortcodes, $shortcode->getParameter('root-page'));

        $templateName = 'menu/menu';
        if ($shortcode->getParameter('template') !== null) {
            $templateName = $shortcode->getParameter('template');
        }

        $template = sprintf('material/%s.html.twig', $templateName);
        $output = $this->grav['twig']->processTemplate($template, [
            'values' => array(
                'sidebar_id' => $shortcode->getParameter('sidebar-id'),
                'alignment' => $shortcode->getParameter('alignment'),
                'attributes' => $shortcode->getParameter('attributes'),
                'menu' => $menu,
            ),
        ]);

        return $output;
    }
}
