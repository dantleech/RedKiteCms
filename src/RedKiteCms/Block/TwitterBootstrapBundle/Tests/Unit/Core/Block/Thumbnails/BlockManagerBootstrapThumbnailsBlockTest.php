<?php
/*
 * This file is part of the TwitterBootstrapBundle and it is distributed
 * under the MIT LICENSE. To use this application you must leave intact this copyright 
 * notice.
 *
 * Copyright (c) RedKite Labs <info@redkite-labs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.redkite-labs.com
 * 
 * @license    MIT LICENSE
 * 
 */

namespace RedKiteCms\Block\TwitterBootstrapBundle\Tests\Unit\Core\Block\Thumbnails;

use RedKiteCms\Block\TwitterBootstrapBundle\Tests\Unit\Core\Block\Base\BaseTestBlock;
use RedKiteCms\Block\TwitterBootstrapBundle\Core\Block\Thumbnails\BlockManagerBootstrapThumbnailsBlock;


/**
 * BlockManagerBootstrapThumbnailsBlockTest
 *
 * @author RedKite Labs <info@redkite-labs.com>
 */
class BlockManagerBootstrapThumbnailsBlockTest extends BaseTestBlock
{  
    public function testDefaultValue()
    {
        $expectedValue = array(
            "Content" =>    '
            {
                "0" : {
                    "type": "BootstrapThumbnailBlock"
                },
                "1" : {
                    "type": "BootstrapThumbnailBlock"
                }
            }'
        );
            
        $this->initContainer(); 
        $blockManager = new BlockManagerBootstrapThumbnailsBlock($this->container, $this->validator);
        $this->assertEquals($expectedValue, $blockManager->getDefaultValue());
    }
    
    /**
     * @dataProvider bootstrapVersionsProvider
     */
    public function testGetHtml($bootstrapVersion)
    {
        $value = '
        {
            "0" : {
                "type": "BootstrapThumbnailBlock"
            },
            "1" : {
                "type": "BootstrapThumbnailBlock"
            }
        }';
            
        $block = $this->initBlock($value);
        $this->initContainer();
        $this->initBootstrapversion($bootstrapVersion);
        
        $blockManager = new BlockManagerBootstrapThumbnailsBlock($this->container, $this->validator);
        $blockManager->set($block);
        
        $expectedResult = array('RenderView' => array(
            'view' => 'TwitterBootstrapBundle:Content:Thumbnails/' . $bootstrapVersion . '/thumbnails.html.twig',
            'options' => array(
                'values' => array(
                    array(
                        "type" => "BootstrapThumbnailBlock",
                    ),
                    array(
                        "type" => "BootstrapThumbnailBlock",
                    ),
                ),
                'block_manager' => $blockManager,
            ),
        ));
        
        $this->assertEquals($expectedResult, $blockManager->getHtml());
    }
}