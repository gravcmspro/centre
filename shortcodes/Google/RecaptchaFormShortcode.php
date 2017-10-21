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
 * Class RecaptchaFormShortcode
 *
 * @author GravCMS Pro
 */
class RecaptchaFormShortcode extends BaseShortcode
{
    /**
     * {@inheritdoc}
     */
    public function shortcodeName()
    {
        return 'm-recaptcha-form';
    }

    /**
     * {@inheritdoc}
     */
    protected function template()
    {
        return 'google/recaptcha_form.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    protected function renderOutput(ShortcodeInterface $shortcode)
    {
        $this->addAssets();
        $output = $this->grav['twig']->processTemplate($this->template(), [
            'values' => array(
              'action' => $shortcode->getParameter('action'),
              'name_placeholder' => $shortcode->getParameter('name-placeholder'),
              'mail_placeholder' => $shortcode->getParameter('mail-placeholder'),
              'submit_text' => $shortcode->getParameter('submit-text'),
              'sitekey' => $shortcode->getParameter('site-key'),
              'sitesecret' => $shortcode->getParameter('site-secret'),
              'message' => strip_tags($shortcode->getContent()),
            )
        ]);

        return $output;
    }

    private function addAssets()
    {
        $this->shortcode->addAssets('js', 'https://www.google.com/recaptcha/api.js');
    }
}
