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

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;
use Grav\Common\Page\Page;
use Grav\Plugin\Shortcodes\Dictionary;
use Grav\Plugin\Shortcodes\SharedContents;

class CentrePlugin extends Plugin
{
    private $dictionary = null;
    private $pluginInstantiator = null;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onShortcodeHandlers' => ['onShortcodeHandlers', 0],
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
            'onTwigSiteVariables' => ['onTwigSiteVariables', 0],
            'onPageContentRaw' => ['onPageContentRaw', 0],
            'onPageContentProcessed' => ['onPageContentProcessed', 10000],
            'onPageInitialized' => ['onPageInitialized', 0],
            'onTwigExtensions' => ['onTwigExtensions', 1000],
        ];
    }

    public function onPluginsInitialized()
    {
        $proFolder = sprintf('%s/centre-pro', $this->grav["locator"]->findResource('plugins://'));
        $this->grav['is_pro'] = is_dir($proFolder);
        $this->grav['shortcodes_assets'] = array();
        $this->pluginInstantiator = $this->initPluginInstantiator();
    }

    private function initPluginInstantiator()
    {
      if ($this->grav['is_pro']) {
          require_once __DIR__.'/../centre-pro/classes/Plugin/PluginInstantiatorPro.php';

          return new PluginInstantiatorPro($this->grav);
      }

      require_once __DIR__.'/classes/Plugin/PluginInstantiator.php';

      return new PluginInstantiator($this->grav);
    }

    public function onPageContentProcessed(Event $e)
    {
        $page = $e['page'];
        $pageContent = $page->getRawContent();
        $pageContent = str_replace('<p>[', '[', $pageContent);
        $pageContent = str_replace(']</p>', ']', $pageContent);
        $page->setRawContent($pageContent);
    }

    public function onPageContentRaw(Event $e)
    {
        require_once __DIR__.'/classes/Base/SharedContents.php';
        require_once __DIR__.'/classes/Base/Dictionary.php';

        $page = $e['page'];

        $sharedContents = new SharedContents($this->grav);
        $page->setRawContent($sharedContents->match($page));

        $this->grav['dictionary'] = $dictionary = new Dictionary($this->grav);
        $this->grav['shortcodes_normalizer'] = $normalizer = $this->pluginInstantiator->initShortcodesNormalizer();
        $page->setRawContent($dictionary->translate($page));
        $pageContent = $normalizer->normalize($page);
        $page->setRawContent($pageContent);
    }

    /**
     * Add the Admin Twig Extensions.
     */
    public function onTwigExtensions()
    {
        require_once __DIR__.'/twig/MaterialTwigExtension.php';
        require_once __DIR__.'/twig/TruncateExtension.php';
        $this->grav['twig']->twig->addExtension(new MaterialTwigExtension());
        $this->grav['twig']->twig->addExtension(new TruncateExtension());
    }

    /**
     * Initializes the shortcodes.
     *
     * @param Event $e
     */
    public function onShortcodeHandlers()
    {
        $this->pluginInstantiator->initShortcodes();
    }

    /**
     * Processes the page when it has children. This allows to use shortcodes in a page who has children.
     */
    public function onPageInitialized()
    {
        $page = $this->grav['page'];
        if (isset($page->header()->content['items'])) {
            $page->content();
        }
    }

    /**
     * Add current directory to twig lookup paths.
     */
    public function onTwigTemplatePaths()
    {
        $this->pluginInstantiator->initTemplatePaths();

        $this->grav['assets']->addJs('plugin://centre/js/document-ready.js');
        $this->grav['assets']->addJs('plugin://centre/js/smootscroll.js');
    }

    public function onTwigSiteVariables()
    {
        $page = $this->grav['page'];
        $this->manageModularPage($page);
    }

    private function manageModularPage(Page $page)
    {
        $children = $page->collection();
        foreach ($children as $child) {
            if (!$child->modular()) {
                return;
            }

            //$this->addAssets($child);
        }
    }

    private function addAssets(Page $page)
    {
        // get the meta and check for assets
        $meta = $page->getContentMeta('shortcodeMeta');
        if (!isset($meta['shortcodeAssets'])) {
            return;
        }

        $assets = (array) $meta['shortcodeAssets'];
        foreach ($assets as $type => $asset) {
            switch ($type) {
                case 'css':
                    $this->grav['assets']->addCss($asset);
                    break;
                case 'js':
                    $this->grav['assets']->addJs($asset);
                    break;
                case 'js-header':
                    $this->grav['assets']->addJs($asset, null, true, null, 'header');
                    break;
            }
        }
    }
}
