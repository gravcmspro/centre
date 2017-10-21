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
 * Class ImageShortcode
 *
 * @author GravCMS Pro
 */
class ImageShortcode extends BaseShortcode
{
    /**
     * {@inheritdoc}
     */
    protected function shortcodeName()
    {
        return 'm-image';
    }

    /**
     * {@inheritdoc}
     */
    protected function template()
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function renderOutput(ShortcodeInterface $shortcode)
    {
        $imagesPath = (null != $shortcode->getParameter('image-path')) ? $shortcode->getParameter('image-path') : '/images';
        if (substr($imagesPath, 0, 1) != '/') {
            $imagesPath = '/'.$imagesPath;
        }

        $page = $this->grav["page"]->find($imagesPath);
        if (null === $page){
            return;
        }

        $name = $shortcode->getParameter('image');
        $images = $page->media()->all();
        if (!array_key_exists($name, $images)) {
            return '';
        }

        $image = $images[$name];
        $actions = $shortcode->getParameter('media-actions');
        if (null !== $actions) {
            //call_user_func(array($image, 'resize'), 200,200);
            $actions = explode('|', $actions);//dump($actions);exit;
            foreach($actions as $action) {
                $tokens = explode(':', $action);
                $arguments = (array_key_exists(1, $tokens)) ? explode(',', $tokens[1]) : array();
                call_user_func_array(array($image, $tokens[0]), $arguments);
            }
        }

        return $image->html($shortcode->getParameter('alt'), $shortcode->getParameter('title'), $shortcode->getParameter('classes'));
    }
}
