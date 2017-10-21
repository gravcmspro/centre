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

namespace Grav;

use Grav\Console\ConsoleCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class WarmupCommand
 *
 * @author GravCMS Pro
 */
class WarmupCommand extends ConsoleCommand
{
    protected $source;
    protected $progress;

    protected function configure()
    {
        $this
            ->setName("warmup")
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'The website url to parse'

            )
            ->setDescription("Warms up a Grav website")
            ->setHelp('The <info>warmup</info> parses all Grav website pages to cache them.');
    }

    /**
     * @return int|null|void
     */
    protected function serve()
    {
        $grav = self::getGrav();
        $grav['debugger']->enabled(false);
        $pages = $grav['pages'];
        $pages->init();
        foreach($pages->all() as $page) {
            if ($page->modular() || !$page->visible()  || !$page->published()) {
                continue;
            }

            $route = $page->route();
            try{
                fopen($this->input->getArgument('url') . $route, 'r');
                echo "Fetched:" . $this->input->getArgument('url') . $route . "\n";
            }
            catch(\Exception $e){
                echo "NOT Fetched:" . $this->input->getArgument('url') . $route . "\n";
                // Handles module
            }
        }

        $this->output->writeln('Website cached');
    }
}
