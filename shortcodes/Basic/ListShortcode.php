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
 * Class ListShortcode
 *
 * @author GravCMS Pro
 */
class ListShortcode extends BaseShortcode
{
    /**
     * {@inheritdoc}
     */
    public function shortcodeName()
    {
        return 'm-list';
    }

    /**
     * {@inheritdoc}
     */
    protected function template()
    {
        return 'basic/list.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    protected function childrenShortcodes()
    {
        return array(
            'm-list-item',
        );
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
        $tag = null !== $shortcode->getParameter('tag') ? $shortcode->getParameter('tag') : 'ul';
        $output = $this->grav['twig']->processTemplate($this->template(), [
            'list_attributes' => $shortcode->getParameter('attributes'),
            'items' => $this->shortcode->getStates($this->shortcode->getId($shortcode)),
            'tag' => $tag,
        ]);

        return $output;
    }
}
