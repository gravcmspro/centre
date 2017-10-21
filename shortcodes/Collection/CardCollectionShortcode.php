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
 * Class CardCollectionShortcode
 *
 * @author GravCMS Pro
 */
class CardCollectionShortcode extends BaseShortcode
{
    /**
     * {@inheritdoc}
     */
    protected function shortcodeName()
    {
        return 'm-card-collection';
    }

    /**
     * {@inheritdoc}
     */
    protected function template()
    {
        return 'collection/card-collection.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    protected function renderOutput(ShortcodeInterface $shortcode)
    {
        $this->addAssets();
        $output = $this->twig->processTemplate($this->template(), [
            'values' => $this->normlizeParametersForTwig($shortcode->getParameters()),
        ]);

        return $output;
    }

    private function addAssets()
    {
        $this->shortcode->addAssets('js', 'plugin://centre-pro/js/masonry/masonry.min.js');
        $this->shortcode->addAssets('js', 'plugin://centre-pro/js/masonry/masonry-init.js');
        $this->shortcode->addAssets('js', 'plugin://centre-pro/js/masonry/images-loaded.js');
    }

    private function normlizeParametersForTwig(array $values)
    {
        $result = array();
        foreach($values as $key => $value) {
            $key = str_replace('-', '_', $key);
            $result[$key] = $value;
        }

        return $result;
    }
}
