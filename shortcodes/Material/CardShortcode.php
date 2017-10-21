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
 * Class CardShortcode
 *
 * @author GravCMS Pro
 */
class CardShortcode extends BaseShortcode
{
    /**
     * {@inheritdoc}
     */
    public function shortcodeName()
    {
        return 'm-card';
    }

    /**
     * {@inheritdoc}
     */
    protected function childrenShortcodes()
    {
        return array(
            'm-card-action',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function template()
    {
        return 'card/card.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    protected function renderOutput(ShortcodeInterface $shortcode)
    {
        $output = $this->grav['twig']->processTemplate($this->template(), [
            'values' => array(
                'name' => $shortcode->getParameter('name'),
                'image' => $shortcode->getParameter('image'),
                'image_title' => $shortcode->getParameter('image-title'),
                'image_alt' => $shortcode->getParameter('image-alt'),
                'image_path' => $shortcode->getParameter('image-path'),
                'size' => $shortcode->getParameter('size'),
                'content' => $shortcode->getContent(),
                'card_title' => $shortcode->getParameter('card-title'),
                'card_attributes' => $shortcode->getParameter('card-attributes'),
                'card_content_attributes' => $shortcode->getParameter('card-content-attributes'),
                'card_opacized_bg_attributes' => $shortcode->getParameter('card-opacized-bg-attributes'),
                'action' => $this->shortcode->getStates($this->shortcode->getId($shortcode))[0],
                'card_action_attributes' => $shortcode->getParameter('card-action-attributes'),
            ),
        ]);

        return $output;
    }
}
