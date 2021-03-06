<?php
/*
 * This file is part of the RedKiteLabsThemeEngineBundle and it is distributed
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

namespace RedKiteLabs\ThemeEngineBundle\Core\Rendering\SlotContent;

/**
 * SlotContent stores the information related to the content to replace
 * ona slot
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 */
class SlotContent
{
    private $slotName = null;
    private $content = null;
    private $replace = null;

    /**
     * Returns the name of the slot
     *
     * @return string
     */
    public function getSlotName()
    {
        return $this->slotName;
    }

    /**
     * Sets the name of the slot
     *
     * @return \RedKiteLabs\ThemeEngineBundle\Core\Rendering\SlotContent\SlotContent
     */
    public function setSlotName($slotName)
    {
        if (!is_string($slotName)) {
            throw new \InvalidArgumentException(sprintf('The slot name passed to "%s" must be a string', get_class($this)));
        }
        $this->slotName = $slotName;

        return $this;
    }

    /**
     * Returns the content to replace
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the content to replace
     *
     * @return \RedKiteLabs\ThemeEngineBundle\Core\Rendering\SlotContent\SlotContent
     */
    public function setContent($content)
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException('The content passed to "RedKiteLabs\FrontendBundle\Core\SlotContent\SlotContent" must be a string');
        }
        $this->content = $content;

        return $this;
    }

    /**
     * When true the content of the slot must be replaced, false injected
     *
     * @return Boolean
     */
    public function isReplacing()
    {
        return $this->replace;
    }

    /**
     * The slotContent is configured to replace the content on the slot
     *
     * @return \RedKiteLabs\ThemeEngineBundle\Core\Rendering\SlotContent\SlotContent
     */
    public function replace()
    {
        $this->replace = true;

        return $this;
    }

    /**
     * The slotContent is configured to inject the content into the slot
     *
     * @return \RedKiteLabs\ThemeEngineBundle\Core\Rendering\SlotContent\SlotContent
     */
    public function inject()
    {
        $this->replace = false;

        return $this;
    }
}
