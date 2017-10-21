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
 * Class ModalShortcode
 *
 * @author GravCMS Pro
 */
class ModalShortcode extends BaseShortcode
{
    /**
     * {@inheritdoc}
     */
    public function shortcodeName()
    {
        return 'm-modal';
    }

    /**
     * {@inheritdoc}
     */
    protected function template()
    {
        return 'modal/modal.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    protected function renderOutput(ShortcodeInterface $shortcode)
    {
        $this->addAssets();
        $output = $this->grav['twig']->processTemplate($this->template(), [
            'values' => array(
              'id' => $shortcode->getParameter('id'),
              'closer_label' => $shortcode->getParameter('closer-label'),
              'content' => $shortcode->getContent(),
            ),
        ]);

        return $output;
    }

    private function addAssets()
    {
        $this->shortcode->addAssets('js', 'plugin://centre/js/vendor/modal-init.js');
    }
}
