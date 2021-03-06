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
          
namespace RedKiteCms\Block\RedKiteCmsBaseBlocksBundle\Tests\Unit\Core\Block\Menu;

use RedKiteCms\Block\RedKiteCmsBaseBlocksBundle\Core\Block\Menu\BlockManagerMenuVertical;


/**
 * BlockManagerMenuVerticalTest
 *
 * @author RedKite Labs <info@redkite-labs.com>
 */
class BlockManagerMenuVerticalTest extends BaseBlockManagerMenu
{
    protected function setUp()
    {
        parent::setUp();
        
        $this->blocksTemplate = 'RedKiteCmsBaseBlocksBundle:Content:Menu/menu_vertical.html.twig';
    }
    
    protected function getBlockManager()
    {
        return new BlockManagerMenuVertical($this->container, $this->validator);
    }
}
