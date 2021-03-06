<?php
/**
 * This file is part of the RedKiteCmsBaseBlocksBundle and it is distributed
 * under the MIT License. To use this application you must leave
 * intact this copyright notice.
 *
 * Copyright (c) RedKite Labs <info@redkite-labs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.redkite-labs.com
 *
 * @license    MIT License
 *
 */

namespace RedKiteCms\Block\RedKiteCmsBaseBlocksBundle\Core\Block\Script;

use RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Content\Block\BlockManagerContainer;

/**
 * BlockManagerScript handles a script block
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 */
class BlockManagerScript extends BlockManagerContainer
{
    /**
     * Defines the App-Block's default value
     *
     * @return array
     */
    public function getDefaultValue()
    {
        return array('Content' => $this->translator->translate("script_block_default_content", array(), 'RedKiteCmsBaseBlocksBundle'));
    }

    /**
     * Renders the App-Block's content view
     *
     * @return string|array
     */
    protected function renderHtml()
    {
        return array('RenderView' => array(
            'view' => 'RedKiteCmsBaseBlocksBundle:Content:Script/script.html.twig',
        ));
    }

    /**
     * Defines the parameters passed to the App-Block's editor
     *
     * @return array
     */
    public function editorParameters()
    {
        $formClass = $this->container->get('script.form');
        $form = $this->container->get('form.factory')->create($formClass, $this->alBlock);

        return array(
            "template" => "RedKiteCmsBaseBlocksBundle:Editor:Script/editor.html.twig",
            "title" => $this->translator->translate('script_block_editor_title', array(), 'RedKiteCmsBaseBlocksBundle'),
            "blockManager" => $this,
            "form" => $form->createView(),
            "jsFiles" => explode(",", $this->alBlock->getExternalJavascript()),
            "cssFiles" => explode(",", $this->alBlock->getExternalStylesheet()),
        );
    }

    /**
     * Defines when a content is rendered or not in edit mode
     *
     * @return boolean
     */
    public function getHideInEditMode()
    {
        return true;
    }

    /**
     * Edits the current block object
     *
     * @param  array
     * @return boolean
     * @codeCoverageIgnore
     */
    protected function edit(array $values)
    {
        $values["Content"] = str_replace('al_json_block[content]=', '', $values["Content"]);

        return parent::edit($values);
    }
}
