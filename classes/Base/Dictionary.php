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
use Thunder\Shortcode\Syntax\CommonSyntax;
use Thunder\Shortcode\Parser\RegularParser;
use Thunder\Shortcode\Processor\Processor;
use Grav\Common\Grav;
use Grav\Common\Markdown\ParsedownExtra;

/**
 * Class Dictionary
 *
 * @author GravCMS Pro
 */
class Dictionary
{
    protected $grav = null;
    protected $parser = null;
    protected $page = null;
    private $shortcodes = array();

    public function __construct($grav)
    {
        $this->grav = $grav;
        $this->parser = new RegularParser(new CommonSyntax());
    }

    public function translate($page, $pageContent = null)
    {
        $this->page = $page;
        $shortcodes = $this->init($page);
        if (null === $pageContent) {
          $pageContent = $page->getRawContent();
        }
        foreach($shortcodes as $shortcode) {
            $name = strtoupper($shortcode->getParameter('name'));
            if ($name == "" || false === strpos($pageContent, $name)) {
                continue;
            }

            $content = trim($shortcode->getContent());
            $parsedContent = $this->parseContent($content);
            $this->shortcodes[$name] = $parsedContent;
            $pageContent = str_replace($name, $parsedContent["content"], $pageContent);
        }

        return $pageContent;
    }

    public function fromName($name, $page = null, $summary = true)
    {
        if (null === $page) {
            $content = $this->getContent($name);
            $page = $this->page;
        } else {
            $content = $this->getContentFromName($name, $page);
            $content = $this->translate($page, $content);
        }

        if (empty($content)) {
            return $content;
        }

        $parsedContent = $this->parseContent($content);
        $content = ($summary) ? $parsedContent["summary"] : $parsedContent["content"];

        $config = $this->grav['config'];
        $defaults = (array)$config->get('system.pages.markdown');
        $parsedown = new ParsedownExtra($page, $defaults);
        $processedContent = $parsedown->text($content);

        $processor = new Processor($this->parser, $this->grav['shortcode']->getHandlers());
        $processedContent = $processor->process($processedContent);

        return $processedContent;
    }

    private function parseContent($content)
    {
        $result = array(
          'summary' => '',
          'content' => $content,
        );
        $regex = '/(\|==[\r\n]*)(.*?)(==\|[\r\n]*)/s';
        if (preg_match($regex, $content, $matches)) {
            $content = str_replace($matches[1], '', $content);
            $content = str_replace($matches[3], '', $content);

            $summary = $this->grav['shortcodes_normalizer']->normalizeContent('[tmp]' . $matches[2] . '[/tmp]');
            $summary = str_replace('[tmp]', '', $summary);
            $summary = trim(str_replace('[/tmp]', '', $summary));

            $result = array(
              'summary' => $summary,
              'content' => $content,
            );
        }

        return $result;
    }

    private function getContent($name)
    {
        if (!array_key_exists($name, $this->shortcodes)) {
            return '';
        }

        return $this->shortcodes[$name];
    }

    private function getContentFromName($name, $page)
    {
        $content = '';
        $shortcodes = $this->init($page);
        foreach($shortcodes as $shortcode) {
            $key = strtoupper($shortcode->getParameter('name'));
            if ($name == $key) {
                $content = trim($shortcode->getContent());
              //  dump($this->init($page, $content));
          //      exit;
/*
                $parsedContent = $this->parseContent($content);
                                //dump($parsedContent);exit;
                $this->shortcodes[$name] = $parsedContent;
                $pageContent = str_replace($name, $parsedContent["content"], $pageContent);*/

                break;
            }
        }

        return $content;
    }

    private function init($page)
    {
        $shortcodes = array();
        $dictionaries = $this->dictionaries($page);
        $language = $this->grav["language"]->getLanguage();
        if(false === $language) {
            return $shortcodes;
        }

        $userFolder = $this->grav["locator"]->findResource('user://');
        foreach($dictionaries as $dictionary) {
            $dictionary = str_replace('.', '/', $dictionary);
            $shortcodes = $this->addDictionary($dictionary, $shortcodes, $language, $userFolder);
            $shortcodes = $this->addDictionary($dictionary, $shortcodes, 'all', $userFolder);
        }

        return $shortcodes;
    }

    private function addDictionary($dictionary, $shortcodes, $language, $userFolder)
    {
        $dictionayFile = sprintf('%s/dictionaries/%s.%s.md', $userFolder, $dictionary, $language);
        if (!file_exists($dictionayFile)) {
            return $shortcodes;
        }
        $dictionayContent = file_get_contents($dictionayFile);

        return array_merge($shortcodes, $this->parser->parse($dictionayContent));
    }

    private function dictionaries($page)
    {
        $header = $page->header();
        $dictionary = $this->getDictionaries($header);
        if (!empty($dictionary)) {
            return $dictionary;
        }

        if ($page->modular()) {
            $header = $page->parent()->header();

            return $this->getDictionaries($header);
        }

        return array();
    }

    private function getDictionaries($header)
    {
        $dictionaries = array();
        if (null !== $header && property_exists($header, 'dictionaries')) {
            $dictionaries = $header->dictionaries;
            if (!is_array($dictionaries)) {
                $dictionaries = array($dictionaries);
            }
        }

        return $dictionaries;
    }
}
