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
use Grav\Common\Grav;
use Grav\Common\Page\Collection;

/**
 * Class SiteMenu
 *
 * @author GravCMS Pro
 */
class SiteMenu
{
    protected $grav;

    public function __construct(Grav $grav)
    {
        $this->grav = $grav;
    }

    public function buildMenu(ShortcodeInterface $shortcode, array $childShortcodes, $pageName = null, $showHidden = false)
    {
        $submenu = explode(',', $shortcode->getParameter('submenu'));

        $onePage = false;
        $page = $this->grav['pages']->root();
        if (null !== $pageName) {
            $page = $this->grav['page']->find('/'.$pageName);
            $onePage = true;
        }

        if (null === $page) {
            return;
        }

        $pages = $page->children();
        if ($onePage) {
            $header = $page->header();
            $pages = $this->sortCollection($header, $pages);
        }

        $menu = array();
        foreach ($pages as $page) {
            if (((!($showHidden || $page->visible())) && !$page->modular()) || !$page->published()) {
                continue;
            }

            $children = array();

            $tokens = explode('.', $page->folder());
            $folderName = (array_key_exists(1, $tokens)) ? $tokens[1] : $tokens[0];
            $addChildren = (null !== $submenu && in_array($folderName, $submenu));

            if (!$page->modular() && $addChildren && count($page->children()) > 0) {
                foreach ($page->children() as $child) {
                    if ($child->modular()) {
                        break;
                    }

                    if (!($child->visible()) || !$child->published()) {
                        continue;
                    }

                    $children[] = array(
                        'page' => $child,
                    );
                }
            }

            $menu[] = array(
                'page' => $page,
                'children' => $children,
            );
        }

        $extraItems = $this->processChildrenShortcodes($childShortcodes);
        $menu = array_merge($extraItems['before'], $menu, $extraItems['after']);

        return $menu;
    }

    private function sortCollection($header, Collection $pages)
    {
        if (null === $header || !property_exists($header, 'content')) {
            return $pages;
        }

        if (!array_key_exists('order', $header->content)) {
            return $pages;
        }
        $order = $header->content["order"];

        $by = array_key_exists('by', $order) ? $order["by"] : 'default';
        $dir = array_key_exists('dir', $order) ? $order["dir"] : 'asc';
        $custom = array_key_exists('custom', $order) ? $order["custom"] : null;
        $pages = $pages->order($by, $dir, $custom);

        return $pages;
    }

    private function processChildrenShortcodes(array $shortcodes)
    {
        $result = array(
            'before' => array(),
            'after' => array(),
        );
        foreach ($shortcodes as $shortcode) {
            $position = $shortcode->getParameter('position');
            if ($position == null) {
                $position = 'after';
            }

            if ($shortcode->getParameter('menu') === null) {
                $result[$position][] = $shortcode->getContent();

                continue;
            }

            $result[$position][] = array(
                'url' => $shortcode->getParameter('url'),
                'menu' => $shortcode->getParameter('menu'),
                'icon' => $shortcode->getParameter('icon'),
            );
        }

        return $result;
    }
}
