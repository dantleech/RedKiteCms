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

namespace RedKiteLabs\ThemeEngineBundle\Tests\Unit\Core\Asset;

use RedKiteLabs\ThemeEngineBundle\Tests\TestCase;
use RedKiteLabs\ThemeEngineBundle\Core\Theme\Theme;

/**
 * ThemeTest
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 */
class ThemeTest extends TestCase
{
    protected function setUp()
    {
        $this->themeSlots = $this->getMock('RedKiteLabs\ThemeEngineBundle\Core\ThemeSlots\ThemeSlotsInterface');
    }

    /**
     * @expectedException \RedKiteLabs\ThemeEngineBundle\Core\Exception\InvalidArgumentException
     */
    public function testAnExceptionIsThrownsWhenTheThemeNotReceiveAString()
    {
        $theme = new Theme(array('fake'), $this->themeSlots);
    }

    public function testTheThemeNameIsAlwaysSuffixedWithBundle()
    {
        $theme = $this->setUpTheme('fake');
        $this->assertEquals('FakeBundle', $theme->getThemeName());

        $theme = $this->setUpTheme('FakeBundle');
        $this->assertEquals('FakeBundle', $theme->getThemeName());
    }

    public function testSetATemplate()
    {
        $template = $this->setUpTemplate();
        $theme = $this->setUpTheme('FakeBundle', $template);
        $otherTemplate = $this->getMockBuilder('RedKiteLabs\ThemeEngineBundle\Core\Template\Template')
                                    ->disableOriginalConstructor()
                                    ->getMock();
        $this->assertSame($template, $theme->getTemplate('home'));
        $theme->setTemplate('home', $otherTemplate);
        $this->assertNotSame($template, $theme->getTemplate('home'));
        $this->assertSame($otherTemplate, $theme->getTemplate('home'));
        $this->assertSame($this->themeSlots, $theme->getThemeSlots());
    }

    public function testAddATemplate()
    {
        $template = $this->setUpTemplate();
        $theme = $this->setUpTheme('FakeBundle', $template);

        $this->assertEquals(1, count($theme));
        $this->assertEquals($template, $theme->current());
        $this->assertEquals('home', $theme->key());
        $this->assertTrue($theme->valid());
        $this->assertCount(1, $theme->getTemplates());
    }

    public function testRetrivingATemplateFromAnInvalidKey()
    {
        $template = $this->setUpTemplate();
        $theme = $this->setUpTheme('FakeBundle', $template);

        $this->assertNull($theme->getTemplate('Internal'));
    }

    public function testRetrivingATemplateFromAValidKey()
    {
        $template = $this->setUpTemplate();
        $theme = $this->setUpTheme('FakeBundle', $template);

        $this->assertEquals($template, $theme->getTemplate('home'));
    }

    public function testKeyIsNormalized()
    {
        $template = $this->setUpTemplate();
        $theme = $this->setUpTheme('FakeBundle', $template);

        $this->assertEquals($template, $theme->getTemplate('Home'));
    }
    
    public function testGetHomeTemplateReturnsATemplateNamedHome()
    {
        $homeTemplate = $this->setUpTemplate();
        $template = $this->setUpTemplate('Internal');
        $theme = $this->setUpTheme('FakeBundle', $template);
        $theme->addTemplate($homeTemplate);

        $this->assertEquals($homeTemplate, $theme->getHomeTemplate());
    }
    
    public function testGetHomeTemplateReturnsTheFirstTemplateWhenTheHomeOneDoesNotExist()
    {
        $template = $this->setUpTemplate('Fake');
        $theme = $this->setUpTheme('FakeBundle', $template);

        $this->assertEquals($template, $theme->getHomeTemplate());
    }

    private function setUpTheme($themeName = 'FakeBundle', $template = null)
    {
        $theme = new Theme($themeName, $this->themeSlots);
        if(null !== $template) $theme->addTemplate($template);

        return $theme;
    }

    private function setUpTemplate($templateName = 'Home')
    {
        $template = $this->getMockBuilder('RedKiteLabs\ThemeEngineBundle\Core\Template\Template')
                                    ->disableOriginalConstructor()
                                    ->getMock();
        $template->expects($this->once())
            ->method('getTemplateName')
            ->will($this->returnValue($templateName));

        return $template;
    }
}