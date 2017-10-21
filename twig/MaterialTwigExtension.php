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

namespace Grav\Plugin;

use Grav\Common\Grav;

/**
 * Class MaterialTwigExtension
 *
 * @author GravCMS Pro
 */
class MaterialTwigExtension extends \Twig_Extension
{
    /**
     * @var Grav
     */
    protected $grav;

    public function __construct()
    {
        $this->grav = Grav::instance();
    }

    /**
     * Returns extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'MaterialTwigExtension';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFunction('instanceof', [$this, 'instance_of']),
            new \Twig_SimpleFilter('rmerge', [$this, 'recursiveMerge']),
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'get_media' => new \Twig_Function_Method($this, 'getMedia', array('is_safe' => array('html'))),
            'instanceof' => new \Twig_SimpleFunction('instanceof', [$this, 'instance_of']),
            'parse_attributes' => new \Twig_SimpleFunction('parse_attributes', [$this, 'parseAttributes']),
        );
    }

    /**
     * Implements PHP instanceof function for Twig.
     *
     * @param mixed  $object
     * @param string $class
     *
     * @return type
     */
    public function instance_of($object, $class)
    {
        return $object instanceof $class;
    }

    /**
     * Parses attributes given as string and returns an array
     * The given input attributes string is given as a comma separated values string and each keypair combination is separated by a colon. An example is class:my-class,rel=my-rel.
     *
     * @param string $tagsString
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function parseAttributes($tagsString)
    {
        $res = array();
        if (null === $tagsString || !is_string($tagsString)) {
            return $res;
        }

        $tags = explode(',', $tagsString);
        foreach ($tags as $tag) {
            $tokens = explode(':', $tag);
            if (count($tokens) != 2) {
              continue;
                throw new \InvalidArgumentException(sprintf('The attribute "%s" is not valid. Attribute string must be made by key:value pairs, separated by colons', $tagsString));
            }

            $res[$tokens[0]] = $tokens[1];
        }

        return $res;
    }

    public function recursiveMerge($a, $b)
    {
        $new = &$b;
        $this->deepMerge($a, $b);

        return $new;
    }

    protected function deepMerge($a, &$new)
    {
        foreach ($a as $kA1 => $vA1) {
            if (!isset($new[$kA1])) {
                $new[$kA1] = $vA1;
            }
            if (is_array($vA1) && is_array($new[$kA1])) {
                $this->deepMerge($vA1, $new[$kA1]);
            }
        }
    }

    /**
     * Returns the left part of a string according the given length.
     *
     * @param string $text
     * @param int    $length
     *
     * @return string
     */
    public function getMedia($name = null, $imagesFolder = null, $page = null)
    {
        if (null === $page) {
            $page = $this->grav['page'];
        }

        if (null === $name) {
            $images = array_values($page->media()->images());
            if (count($images) == 0) {
                return null;
            }

            return $images[0];
        }

        if (null === $imagesFolder) {
            $header = $page->header();
            $imagesFolder = property_exists($header, 'image.folder') ? $header->image->folder : '/images';
        }

        if (substr($imagesFolder, 0, 1) != '/') {
            $imagesFolder = '/'.$imagesFolder;
        }
        $imagesPage = $page->find($imagesFolder);
        if (null === $imagesPage) {
            return null;
        }

        $images = $imagesPage->media()->all();
        if (!array_key_exists($name, $images)) {
            return null;
        }

        return $images[$name];
    }
}
