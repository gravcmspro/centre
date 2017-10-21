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
 * Class ModularCollectionShortcode
 *
 * @author GravCMS Pro
 */
class ModularCollectionShortcode extends BaseShortcode
{
    /**
     * {@inheritdoc}
     */
    protected function shortcodeName()
    {
        return 'm-modular-collection';
    }

    /**
     * {@inheritdoc}
     */
    protected function template()
    {
        return 'collection/modular-collection.html.twig';
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
        $output = $this->twig->processTemplate($this->template(), []);

        return $output;
    }
}