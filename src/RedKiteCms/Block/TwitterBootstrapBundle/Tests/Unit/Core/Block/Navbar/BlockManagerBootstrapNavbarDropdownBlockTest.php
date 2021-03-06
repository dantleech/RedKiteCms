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
 
namespace RedKiteCms\Block\TwitterBootstrapBundle\Tests\Unit\Core\Block\Navbar;

use RedKiteCms\Block\TwitterBootstrapBundle\Tests\Unit\Core\Block\Base\BaseTestBlock;
use RedKiteCms\Block\TwitterBootstrapBundle\Core\Block\Navbar\BlockManagerBootstrapNavbarDropdownBlock;

/**
 * BlockManagerBootstrapNavbarDropdownBlockTest
 *
 * @author RedKite Labs <info@redkite-labs.com>
 */
class BlockManagerBootstrapNavbarDropdownBlockTest extends BaseTestBlock
{  
    public function testDefaultValue()
    {
        $expectedValue = array(
            "Content" =>    
            '
            {
                "0": {
                    "button_text": "Dropdown Button 1",
                    "button_type": "",
                    "button_attribute": "",
                    "button_dropup" : "none",
                    "items": [
                        {
                            "data" : "Item 1",
                            "metadata" : {
                                "type": "link",
                                "href": "#"
                            }
                        },
                        {
                            "data" : "Item 2",
                            "metadata" : {
                                "type": "link",
                                "href": "#"
                            }
                        },
                        {
                            "data" : "Item 3",
                            "metadata" : {
                                "type": "link",
                                "href": "#"
                            }
                        }
                    ]
                }
            }'
        );
            
        $this->initContainer(); 
        $blockManager = new BlockManagerBootstrapNavbarDropdownBlock($this->container, $this->validator);
        $this->assertEquals($expectedValue, $blockManager->getDefaultValue());
    }
    
    public function testEditorParameters()
    {
        $value = '
            {
                "0": {
                    "button_text": "Dropdown Button 1",
                    "button_type": "",
                    "button_attribute": "",
                    "button_dropup" : "none",
                    "items": [
                        {
                            "data" : "Item 1", 
                            "metadata" : {  
                                "type": "link",
                                "href": "#"
                            }
                        },
                        { 
                            "data" : "Item 2", 
                            "metadata" : {  
                                "type": "link",
                                "href": "#"
                            }
                        },
                        { 
                            "data" : "Item 3", 
                            "metadata" : {  
                                "type": "link",
                                "href": "#"
                            }
                        }
                    ]
                }
            }';
        
        $block = $this->initBlock($value);
        $this->initContainer();
        
        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->at(0))
                    ->method('create')
                    ->will($this->returnValue($this->initForm()))
        ;
        
        $formType = $this->getMock('Symfony\Component\Form\FormTypeInterface');
        $this->container->expects($this->at(3))
                        ->method('get')
                        ->with('bootstrap_navbar_dropbown.form')
                        ->will($this->returnValue($formType))
        ;
        
        $this->container->expects($this->at(4))
                        ->method('get')
                        ->with('form.factory')
                        ->will($this->returnValue($formFactory))
        ;
        
        $seoRepository = $this->getMockBuilder('RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Repository\Propel\SeoRepositoryPropel')
                              ->disableOriginalConstructor()
                              ->getMock()
        ;
        
        $this->factoryRepository->expects($this->at(1))
             ->method('createRepository')
             ->with('Seo')
             ->will($this->returnValue($seoRepository))
        ;
        
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $this->container->expects($this->at(5))
                        ->method('get')
                        ->with('request')
                        ->will($this->returnValue($request))
        ;
        
        $blockManager = new BlockManagerBootstrapNavbarDropdownBlock($this->container, $this->validator);
        $blockManager->set($block);
        $result = $blockManager->editorParameters();
        $this->assertEquals('TwitterBootstrapBundle:Editor:DropdownButton/dropdown_editor.html.twig', $result["template"]);
    }
    
    protected function initRepository()
    {
        $this->factoryRepository = $this->getMock('RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Repository\Factory\FactoryRepositoryInterface');
        $this->factoryRepository->expects($this->at(0))
            ->method('createRepository')
            ->with('Block')
            ->will($this->returnValue($this->blockRepository));
    }
}