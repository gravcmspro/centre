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
 * Class GoogleMapShortcode
 *
 * @author GravCMS Pro
 */
class GoogleMapShortcode extends BaseShortcode
{
    /**
     * {@inheritdoc}
     */
    protected function shortcodeName()
    {
        return 'm-google-map';
    }

    /**
     * {@inheritdoc}
     */
    protected function template()
    {
        return 'google/map.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    protected function childrenShortcodes()
    {
        return array(
            'm-google-map-marker'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function renderOutput(ShortcodeInterface $shortcode)
    {
        $this->addAssets();
        $id = $shortcode->getParameter('id');
        $output = $this->twig->processTemplate($this->template(), [
            'id' => $id,
            'zoom' => $shortcode->getParameter('zoom'),
            'center' => $this->convertChoords($shortcode->getParameter('center')),
            'markers' => $this->markers($shortcode),
            'api_key' => $shortcode->getParameter('api-key'),
        ]);
        $this->shortcode->addAssets('inlineJs', $output);

        return sprintf('<div id="%s" class="googlemap"></div>', $id);
    }

    private function addAssets()
    {
        $this->shortcode->addAssets('js', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBxhffX-WK4W2FXBKEE3RR1xafdZ3V9Rcc|-|group=header');
        $this->shortcode->addAssets('js', 'plugin://centre/js/google/googlemap.js');
    }

    private function markers(ShortcodeInterface $parentShortcode)
    {
        $shortcodes = $this->shortcode->getStates($this->shortcode->getId($parentShortcode));
        foreach($shortcodes as $shortcode) {
            $markers[] = sprintf('{%s}', implode(',', array(
                '"position": ' . $this->convertChoords($shortcode->getParameter('location')),
                '"title": ' . '"' . $shortcode->getParameter('title') . '"',
                '"info": ' . '"' . preg_replace('/[\n,\r]/s', '', $this->arrangeContent($shortcode->getContent())) . '"',
            )));
        }

        return sprintf('[%s]', implode(',', $markers));
    }

    private function convertChoords($value)
    {
        return json_encode(array_combine(array('lat', 'lng'), explode(",", $value)), JSON_NUMERIC_CHECK);
    }

    private function arrangeContent($content)
    {
        $content = trim($content);
        $content = preg_replace('/(^[\n,\r])/s', '', $content);
        $content = preg_replace('/($[\n,\r])/s', '', $content);
        $content = nl2br($content);
        $content = preg_replace('/([\n,\r])/s', '', $content);

        return $content;
    }
}
