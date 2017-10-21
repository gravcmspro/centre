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
 * Class CardRevealedShortcode
 *
 * @author GravCMS Pro
 */
class CardRevealedShortcode extends BaseShortcode
{
    /**
     * {@inheritdoc}
     */
    public function shortcodeName()
    {
        return 'm-card-revealed';
    }

    /**
     * {@inheritdoc}
     */
    protected function childrenShortcodes()
    {
        return array(
            'm-card-revealed-full-content',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function template()
    {
        return 'card/card_revealed.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    protected function renderOutput(ShortcodeInterface $shortcode)
    {
        $children = $this->shortcode->getStates($this->shortcode->getId($shortcode));
        $fullContent = '';
        $action = '';
        if (null !== $children) {
            if (array_key_exists(0, $children)) {
                $fullContent = $children[0];
            }

            if (array_key_exists(1, $children)) {
                $action = $children[1];
            }
        }

        $output = $this->grav['twig']->processTemplate($this->template(), [
            'values' => array(
                'image' => $shortcode->getParameter('image'),
                'image_path' => $shortcode->getParameter('image-path'),
                'size' => $shortcode->getParameter('size'),
                'content' => $shortcode->getContent(),
                'card_title' => $shortcode->getParameter('title'),
                'sticky' => $shortcode->getParameter('sticky'),
                'full_content' => $fullContent,
                'action' => $action,
                'card_attributes' => $shortcode->getParameter('card-attributes'),
                'card_content_attributes' => $shortcode->getParameter('card-content-attributes'),
            ),
        ]);

        return $output;
    }
}
