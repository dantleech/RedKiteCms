<?php
/**
 * This file is part of the RedKiteCmsBunde Application and it is distributed
 * under the MIT License. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) RedKite Labs <webmaster@redkite-labs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.redkite-labs.com
 *
 * @license    MIT License
 *
 */

namespace RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Controller;

use RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Content\PageBlocks\PageBlocks;
use RedKiteLabs\ThemeEngineBundle\Core\Theme\Theme;

class ThemePreviewController extends CmsController
{
    protected $pageTree;
    protected $themes;
    protected $blocksFactory;
    protected $factoryRepository;
    protected $activeTheme;

    public function previewThemeAction($languageName, $pageName, $themeName, $templateName)
    {
        $this->kernel = $this->container->get('kernel');
        $this->themes = $this->container->get('red_kite_labs_theme_engine.themes');
        $this->factoryRepository = $this->container->get('red_kite_cms.factory_repository');
        $this->blocksFactory = $this->container->get('red_kite_cms.block_manager_factory');
        $this->activeTheme = $this->container->get('red_kite_cms.active_theme');
        $templateManager = $this->container->get('red_kite_cms.template_manager');
        $assetsManager = $this->container->get('red_kite_cms.template_assets_manager');
        $this->blocksRepository = $this->factoryRepository->createRepository('Block');
        $bootstrapVersion = $this->activeTheme->getThemeBootstrapVersion($themeName);

        $theme = $this->themes->getTheme($themeName);
        $template = ($templateName == 'none') ? $theme->getHomeTemplate() : $theme->getTemplate($templateName);

        $this->pageTree = $this->container->get('red_kite_cms.page_tree_preview');
        $this->pageTree->setTemplateAssetsManager($assetsManager);
        $slotContents = $this->fetchSlotContents($theme);
        $pageBlocks = new PageBlocks($this->factoryRepository);
        $pageBlocks->addRange($slotContents);
        $this->pageTree->setUp($theme, $templateManager, $pageBlocks, $template);
        $this->container->set('red_kite_cms.page_tree', $this->pageTree);

        $twigTemplate = $this->findTemplate($this->pageTree);
        $baseParams = array(
            'template' => $twigTemplate,
            'skin_path' => $this->getSkin(),
            'theme_name' => $themeName,
            'template_name' => $template->getTemplateName(),
            'available_languages' => $this->container->getParameter('red_kite_cms.available_languages'),
            'base_template' => $this->container->getParameter('red_kite_labs_theme_engine.base_template'),
            'internal_stylesheets' => $this->pageTree->getInternalStylesheets(),
            'internal_javascripts' => $this->pageTree->getInternalJavascripts(),
            'templateStylesheets' => $this->pageTree->getExternalStylesheets(),
            'templateJavascripts' => $this->fixAssets($this->pageTree->getExternalJavascripts()),
            'templates' => array_keys($theme->getTemplates()),
            'frontController' => $this->getFrontcontroller(),
            'language_name' => $languageName,
            'page_name' => $pageName,
        );

        return $this->render(sprintf('RedKiteCmsBundle:Bootstrap:%s/Template/Preview/template.html.twig', $bootstrapVersion), $baseParams);
    }

    protected function fetchSlotContents(Theme $theme)
    {
        $slots = $theme->getThemeSlots()->getSlots();
        $slotContents = array();
        foreach ($slots as $slot) {
            $slotName = $slot->getSlotName();
            $blockType = $slot->getBlockType();
            $content = $slot->getContent();
            $blockManager = $this->blocksFactory->createBlockManager($blockType);

            $blockClass = $this->blocksRepository->getRepositoryObjectClassName();
            $block = new $blockClass();
            $block->setType($blockType);
            $block->setSlotName($slotName);
            if (null === $content) {
                $defaultValue = $blockManager->getDefaultValue();
                $content = $defaultValue['Content'];
            }

            $block->setContent($content);
            $blockManager->set($block);

            $slotContents[$slotName] = array($block);
            $this->pageTree->addBlockManager($slotName, $blockManager);
        }

        return $slotContents;
    }
}
