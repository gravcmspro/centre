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
 * Class BaseShortcode
 *
 * @author GravCMS Pro
 */
abstract class BaseShortcode extends Shortcode
{
    /**
     * Returns the sortcode tag name
     *
     * @return string
     */
    abstract protected function shortcodeName();

    /**
     * Returns the name of the template to use
     *
     * @return string
     */
    abstract protected function template();

    /**
     * Renders the shortcode output
     *
     * @param ShortcodeInterface $shortcode
     * @return string
     */
    abstract protected function renderOutput(ShortcodeInterface $shortcode);

    /**
     * Initializes the shortcode
     */
    public function init()
    {
        $this->shortcode->getHandlers()->add($this->shortcodeName(), function(ShortcodeInterface $shortcode) {
            /*
            foreach($this->assets() as $type => $assets) {
                $this->grav['assets']->add($assets);
            }*/

            $output = $this->renderOutput($shortcode);

            return $output;
        });


        foreach($this->childrenShortcodes() as $childShortcode) {
            $this->registerChildShortcode($childShortcode);
        }

        foreach($this->aliases() as $aliasShortcode => $alias) {
            if (!is_array($alias)) {
                $this->shortcode->getHandlers()->addAlias($alias, $this->shortcodeName());

                continue;
            }

            foreach($alias as  $aliasName) {
                $this->shortcode->getHandlers()->addAlias($aliasName, $aliasShortcode);
            }
        }
    }

    /**
     * Add aliases to shortcode.
     *
     * Aliases can be defined as follows:
     *
     * array(
     *      'm-jumbotron',
     * );
     *
     * or
     *
     * array(
     *      'material-navbar' => array(
     *         'm-navbar'
     *      ),
     *      'material-navbar-item' => array(
     *         'm-navbar-item'
     *      ),
     * );
     *
     *
     * @return array
     */
    protected function aliases()
    {
        return array();
    }

    /**
     * Initializes children shortcodes.
     *
     * @see Grav\Plugin\Shortcodes\CarouselShortcode
     * @return array
     */
    protected function childrenShortcodes()
    {
        return array();
    }

    /**
     * Looks for a parameter in the given shortcode first, then looks to its parent when it is not found
     *
     * @param ShortcodeInterface $shortcode
     * @param string $parameterName
     * @param ShortcodeInterface $parent
     * @return mixed s$value
     */
    protected function findParameterInCascade(ShortcodeInterface $shortcode, $parameterName, ShortcodeInterface $parent = null)
    {
        if (null === $parent) {
            $parent = $shortcode->getParent();
        }

        $value = $shortcode->getParameter($parameterName);
        if (null === $value && null !== $parent) {
            $value = $parent->getParameter($parameterName);
        }

        return $value;
    }

    /**
     * Register a shortcode section
     *
     * @param ShortcodeInterface $shortcode
     * @param strign|null $content
     */
    protected function registerSection(ShortcodeInterface $shortcode, $content = null)
    {
        if (null === $content) {
            $content = $shortcode->getContent();
        }

        $name = $shortcode->getParameter('name');
        $object = new ShortcodeObject($name, $content);
        $this->shortcode->addObject($shortcode->getName(), $object);
    }

    /**
     * Registers a child shortcode
     *
     * @param string $shortcodeName
     */
    protected function registerChildShortcode($shortcodeName)
    {
        $this->shortcode->getHandlers()->add($shortcodeName, function(ShortcodeInterface $shortcode) {
            if (null === $shortcode->getParent()) {
                return;
            }

            $hash = $this->shortcode->getId($shortcode->getParent());
            $this->shortcode->setStates($hash, $shortcode);

            return;
        });
    }

    /**
     * Returns the given default value when given value is null
     *
     * @param mixed $value
     * @param mixed $default
     * @return mixed
     */
    protected function defaultValue($value, $default)
    {
        return (null !== $value) ? $value : $default;
    }

    /**
     * Converts a string to boolean
     *
     * @param string $value
     * @return boolean
     */
    protected function stringToBoolean($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
