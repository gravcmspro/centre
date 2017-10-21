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
use Grav\Common\Grav;

/**
 * Class ShortcodesNormalizer
 *
 * @author GravCMS Pro
 */
class ShortcodesNormalizer
{
    protected $grav = null;
    protected $parser = null;
    protected $page = null;

    public function __construct($grav)
    {
        $this->grav = $grav;
        $this->parser = new RegularParser(new CommonSyntax());
    }

    public function normalize($page)
    {
        $this->page = $page;
        $pageContent = $page->getRawContent();

        return $this->normalizeContent($pageContent);
    }

    public function normalizeContent($content) {
        $shortcodes = $this->parser->parse($content);
        $processedShortcodes = array();
        foreach ($shortcodes as $shortcode) {
            $shortcodeOriginalText = $shortcode->getText();
            $shortcodeText = $this->processShortcode($shortcode, $shortcodeOriginalText);
            $content = $this->normalizePage($content, $shortcodeOriginalText, $shortcodeText);
        }

        $content = $this->normalizeReplacements($content);

        return $content;
    }

    protected function findComponent($shortcode)
    {
        $folder = $this->findComponentFolder($shortcode);
        $componentName = $shortcode->getParameter('component');
        $componentFile = $this->page->path().'/'.$componentName.'.component';
        if (file_exists($componentFile)) {
            return $componentFile;
        }

        $componentFile = $this->grav["locator"]->findResource('user://') . '/components/'.$componentName.'.component';
        if (file_exists($componentFile)) {
            return $componentFile;
        }

        return $this->grav["locator"]->findResource('plugins://').'/centre/components/'.$folder.$componentName.'.component';
    }


    protected function findComponentFolder($shortcode)
    {
        $folder = '';
        $componentFolder = $shortcode->getParameter('folder');
        if ($componentFolder !== null) {
          $folder = $componentFolder . '/';
        }

        return $folder;
    }

    private function normalizeReplacements($pageContent)
    {
        return preg_replace_callback('/<<([a-z0-9_\.]+)>>/', function($matches) {
            return strtoupper($matches[1]);
        }, $pageContent);
    }

    private function normalizePage($pageContent, $shortcodeOriginalText, $shortcodeText)
    {
        $indentationSpacesRegex = '';
        $isCodeBlock = false;
        $lines = array('');
        foreach (preg_split("/((\r?\n)|(\r\n?))/", trim($shortcodeText)) as $line) {
            $isCodeBlockStarted = strpos($line, '```') !== false;
            if ($isCodeBlockStarted && !$isCodeBlock) {
                $isCodeBlock = true;
                preg_match('/[^`]+/', $line, $matches);
                if ($matches) {
                    $indentationSpacesRegex = sprintf('/^%s/', $matches[0]);
                }

                $lines = $this->parseCodeBlock($indentationSpacesRegex, $lines, $line);

                continue;
            }

            if ($isCodeBlockStarted && $isCodeBlock) {
                $lines = $this->parseCodeBlock($indentationSpacesRegex, $lines, $line);
                $isCodeBlock = false;

                continue;
            }

            if ($isCodeBlock) {
                $lines = $this->parseCodeBlock($indentationSpacesRegex, $lines, $line);

                continue;
            }

            $line = ltrim($line);
            $lines[] = $line;
        }

        return str_replace($shortcodeOriginalText, implode("\n", $lines), $pageContent);
    }

    private function processShortcode($shortcode, $shortcodeText)
    {
        $shortcodeProcessedText = $shortcodeText;
        if ($shortcode->getName() == 'm-component') {
            $shortcodeProcessedText = str_replace($shortcode->getText(), $this->processModel($shortcode), $shortcodeProcessedText);
        }

        $shortcodeProcessedText = $this->processShortcodeRecursive($shortcode, $shortcodeProcessedText);

        return $shortcodeProcessedText;
    }

    private function processShortcodeRecursive($shortcode, $shortcodeText)
    {
        $shortcodeName = $shortcode->getName();
        if ($shortcodeName === 'raw') {
            return $shortcodeText;
        }

        $shortcodeContent = $shortcode->getContent();
        $children = $this->parser->parse($shortcodeContent);
        foreach($children as $child) {
            if ($child->getName() == 'm-component') {
                $shortcodeText = str_replace($child->getText(), $this->processModel($child), $shortcodeText);
            }

            $shortcodeText = $this->processShortcodeRecursive($child, $shortcodeText);
        }

        return $shortcodeText;
    }

    private function processModel($shortcode)
    {
        $shortcodeContent = $shortcode->getContent();
        $componentFile = $this->findComponent($shortcode);
        if (!file_exists($componentFile)) {

            return $shortcodeContent;
        }

        $modelContent = $shortcodeContent = file_get_contents($componentFile);
        $children = $this->parser->parse($shortcode->getContent());
        foreach($children as $child) {
            if ($child->getName() != 'm-component-item') {
                continue;
            }
            $shortcodeContent = $this->processComponent($child, $shortcodeContent, $child->getParameter('name'));
        }

        $shortcodeContent = $this->processComponent($shortcode, $shortcodeContent);

        return $shortcodeContent;
    }

    private function processComponent($shortcode, $shortcodeContent, $itemName = null)
    {
        foreach ($shortcode->getParameters() as $name => $value) {
            if ($name == 'name') {
                continue;
            }

            if ($itemName !== null) {
                $name = $itemName . '.' . strtoupper($name);
            }

            $shortcodeContent = str_replace(strtoupper($name), $value, $shortcodeContent);
        }

        $contentPlaceholder = 'M_CONTENT';
        if ($itemName !== null) {
            $contentPlaceholder = $itemName . '.' . $contentPlaceholder;
        }

        $shortcodeContent = str_replace($contentPlaceholder, $shortcode->getContent(), $shortcodeContent);

        return $shortcodeContent;
    }

    private function parseCodeBlock($indentationSpacesRegex, $lines, $line)
    {
        if (!emptY($indentationSpacesRegex)) {
            $line = preg_replace($indentationSpacesRegex, '', $line);
        }
        $lines[] = $line;

        return $lines;
    }
}
