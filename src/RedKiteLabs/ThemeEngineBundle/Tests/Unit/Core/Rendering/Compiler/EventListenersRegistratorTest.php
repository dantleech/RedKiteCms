<?php
/**
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

namespace RedKiteLabs\ThemeEngineBundle\Tests\Unit\Core\Rendering\Compiler;

use RedKiteLabs\ThemeEngineBundle\Tests\TestCase;
use RedKiteLabs\ThemeEngineBundle\Core\Rendering\Compiler\EventListenersRegistrator;

/**
 * EventListenersRegistratorTest
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 */
class EventListenersRegistratorTest extends TestCase
{
    public function testEventsDispatcherDefinitionDoesNotExist()
    {
        $definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $definition->expects($this->never())
            ->method('addMethodCall');
        
        $builder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $builder->expects($this->any())
            ->method('hasDefinition')
            ->will($this->returnValue(false));
        
        EventListenersRegistrator::registerByTaggedServiceId($builder, 'rkcms.event');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Service "my_event_subscriber" must define the "event" attribute on "rkcms.event" tags.
     */
    public function testAnExceptionIsThrownWhenEventOptionIsNotProvided()
    {
        $definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        
        $builder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $builder->expects($this->any())
            ->method('hasDefinition')
            ->will($this->returnValue(true));
        
        $builder->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->returnValue($definition));
        
        $services = array(
            'my_event_subscriber' => array(
                0 => array(
                    'method' => 'event.method', 
                    'priority' => '128',
                ),
            ),
        );
                
        $builder->expects($this->atLeastOnce())
            ->method('findTaggedServiceIds')
            ->will($this->returnValue($services));
        
        EventListenersRegistrator::registerByTaggedServiceId($builder, 'rkcms.event');
    }
    
    /**
     * @dataProvider eventsSubscriberProvider
     */
    public function testSubscribeEvents($services, $servicesIds, $servicesResults)
    {
        $definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $definition->expects($this->at(0))
            ->method('addMethodCall')
            ->with('addListenerId', $servicesIds);
        
        $definition->expects($this->at(1))
            ->method('addMethodCall')
            ->with('addListenerService', $servicesResults);
        
        $builder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $builder->expects($this->any())
            ->method('hasDefinition')
            ->will($this->returnValue(true));
        
        $builder->expects($this->atLeastOnce())
            ->method('getDefinition')
            ->will($this->returnValue($definition));
        
        $builder->expects($this->atLeastOnce())
            ->method('findTaggedServiceIds')
            ->will($this->returnValue($services));
        
        EventListenersRegistrator::registerByTaggedServiceId($builder, 'red_kite_labs_theme_engine.event_listener');
    }
    
    public function eventsSubscriberProvider()
    {
        return array(
            array(
                array(
                    'my_event_subscriber' => array(
                        0 => array(
                            'event' => 'event.name', 
                            'method' => 'event.method', 
                            'priority' => '128',
                        ),
                    ),
                ),
                array(
                    'my_event_subscriber'
                ),
                array(
                    'event.name', 
                    array('my_event_subscriber', 'event.method'), '128',
                ),
            ),
            array(
                array(
                    'my_event_subscriber' => array(
                        1 => array(
                            'event' => 'event.name', 
                            'priority' => '-128',
                        ),
                    ),
                ),
                array(
                    'my_event_subscriber'
                ),
                array(
                    'event.name', 
                    array('my_event_subscriber', 'onEventName'), '-128',
                ),
            ),
        );
    }
}