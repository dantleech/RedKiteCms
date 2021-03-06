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

namespace RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Tests\Unit\Core\Listener\Page;

use RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Listener\Page\AddSeoListener;
use RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Tests\Unit\Core\Listener\Base\BaseTemplateManagerListenerTest;

/**
 * AddSeoListenerTest
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 */
class AddSeoListenerTest extends BaseTemplateManagerListenerTest
{
    protected $event;
    protected $testListener;
    protected $pageManager;
    protected $seoManager;
    protected $pageRepository;
    protected $languageRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->seoManager = $this->getMockBuilder('RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Content\Seo\SeoManager')
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->event = $this->getMockBuilder('RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Event\Content\Page\BeforeAddPageCommitEvent')
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->pageManager = $this->getMockBuilder('RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Content\Page\PageManager')
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->pageRepository = $this->getMockBuilder('RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Repository\Propel\PageRepositoryPropel')
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->languageRepository = $this->getMockBuilder('RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Repository\Propel\LanguageRepositoryPropel')
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->factoryRepository = $this->getMock('RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Repository\Factory\FactoryRepositoryInterface');
        $this->factoryRepository->expects($this->once())
            ->method('createRepository')
            ->will($this->returnValue($this->languageRepository));

        $this->testListener = new AddSeoListener($this->seoManager, $this->factoryRepository);
    }

    public function testAnythingIsExecutedWhenTheEventHadBeenAborted()
    {
        $this->event->expects($this->once())
            ->method('isAborted')
            ->will($this->returnValue(true));

        $this->testListener->onBeforeAddPageCommit($this->event);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testValuesParamIsNotAnArray()
    {
        $this->event->expects($this->once())
            ->method('getContentManager')
            ->will($this->returnValue($this->pageManager));

        $this->event->expects($this->once())
            ->method('getValues')
            ->will($this->returnValue('fake'));

        $this->pageRepository->expects($this->never())
            ->method('startTransaction');

        $this->testListener->onBeforeAddPageCommit($this->event);
    }

    public function testNothingIsAddedWhenAnyLanguageExists()
    {
        $this->pageRepository->expects($this->never())
            ->method('startTransaction');

        $this->pageRepository->expects($this->never())
            ->method('commit');

        $this->pageRepository->expects($this->never())
            ->method('rollBack');

        $this->event->expects($this->once())
            ->method('getValues')
            ->will($this->returnValue(array()));

        $this->event->expects($this->once())
            ->method('getContentManager')
            ->will($this->returnValue($this->pageManager));

        $this->event->expects($this->never())
            ->method('abort');

        $this->languageRepository->expects($this->once())
            ->method('activeLanguages')
            ->will($this->returnValue(array()));

        $this->pageManager->expects($this->once())
            ->method('getPageRepository')
            ->will($this->returnValue($this->pageRepository));

        $this->testListener->onBeforeAddPageCommit($this->event);
    }

    public function testSaveFailsWhenAttributesAreNotSaved()
    {
        $this->setUpBlockRepository();
        $page = $this->setUpPage(2);
        $language = $this->setUpLanguage(2);

        $this->pageRepository->expects($this->once())
            ->method('startTransaction');

        $this->pageRepository->expects($this->once())
            ->method('rollBack');

        $this->event->expects($this->once())
            ->method('getContentManager')
            ->will($this->returnValue($this->pageManager));

        $this->event->expects($this->once())
            ->method('getValues')
            ->will($this->returnValue(array()));

        $this->event->expects($this->once())
            ->method('abort');

        $this->languageRepository->expects($this->once())
            ->method('activeLanguages')
            ->will($this->returnValue(array($language)));

        $this->pageManager->expects($this->once())
            ->method('get')
            ->will($this->returnValue($page));

        $this->pageManager->expects($this->once())
            ->method('getPageRepository')
            ->will($this->returnValue($this->pageRepository));

        $this->seoManager->expects($this->once())
            ->method('save')
            ->will($this->returnValue(false));

        $this->testListener->onBeforeAddPageCommit($this->event);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSaveFailsBecauseAndUnespectedExceptionIsThrown()
    {
        $this->setUpBlockRepository();
        $page = $this->setUpPage(2);
        $language = $this->setUpLanguage(2);

        $this->pageRepository->expects($this->once())
            ->method('startTransaction');

        $this->pageRepository->expects($this->once())
            ->method('rollback');

        $this->event->expects($this->once())
            ->method('getContentManager')
            ->will($this->returnValue($this->pageManager));

        $this->event->expects($this->once())
            ->method('abort');

        $this->event->expects($this->once())
            ->method('getValues')
            ->will($this->returnValue(array()));

        $this->languageRepository->expects($this->once())
            ->method('activeLanguages')
            ->will($this->returnValue(array($language)));

        $this->pageManager->expects($this->once())
            ->method('get')
            ->will($this->returnValue($page));

        $this->pageManager->expects($this->once())
            ->method('getPageRepository')
            ->will($this->returnValue($this->pageRepository));

        $this->seoManager->expects($this->once())
            ->method('save')
            ->will($this->throwException(new \RuntimeException()));

        $this->testListener->onBeforeAddPageCommit($this->event);
    }

    public function testSave()
    {
        $this->setUpBlockRepository();
        $page = $this->setUpPage(2);
        $language = $this->setUpLanguage(2);

        $this->pageRepository->expects($this->once())
            ->method('startTransaction');

        $this->pageRepository->expects($this->once())
            ->method('commit');

        $this->pageRepository->expects($this->never())
            ->method('rollback');

        $this->event->expects($this->once())
            ->method('getContentManager')
            ->will($this->returnValue($this->pageManager));

        $this->event->expects($this->once())
            ->method('getValues')
            ->will($this->returnValue(array()));

        $this->languageRepository->expects($this->once())
            ->method('activeLanguages')
            ->will($this->returnValue(array($language)));

        $this->pageManager->expects($this->once())
            ->method('get')
            ->will($this->returnValue($page));

        $this->pageManager->expects($this->once())
            ->method('getPageRepository')
            ->will($this->returnValue($this->pageRepository));

        $this->seoManager->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));

        $this->testListener->onBeforeAddPageCommit($this->event);
    }

    public function testSaveFailsWhenAtLeastAtributeIsNotSaved()
    {
        $this->setUpBlockRepository();
        $page = $this->setUpPage(2);
        $language1 = $this->setUpLanguage(2);
        $language2 = $this->setUpLanguage(3);

        $this->pageRepository->expects($this->once())
            ->method('startTransaction');

        $this->pageRepository->expects($this->once())
            ->method('rollBack');

        $this->event->expects($this->once())
            ->method('getContentManager')
            ->will($this->returnValue($this->pageManager));

        $this->event->expects($this->once())
            ->method('getValues')
            ->will($this->returnValue(array()));

        $this->event->expects($this->once())
            ->method('abort');

        $this->languageRepository->expects($this->once())
            ->method('activeLanguages')
            ->will($this->returnValue(array($language1, $language2)));

        $this->pageManager->expects($this->once())
            ->method('get')
            ->will($this->returnValue($page));

        $this->pageManager->expects($this->once())
            ->method('getPageRepository')
            ->will($this->returnValue($this->pageRepository));

        $this->seoManager->expects($this->exactly(2))
            ->method('save')
            ->will($this->onConsecutiveCalls(true, false));

        $this->testListener->onBeforeAddPageCommit($this->event);
    }

    public function testSaveWhenSiteHasMoreLanguages()
    {
        $this->setUpBlockRepository();
        $page = $this->setUpPage(2);
        $language1 = $this->setUpLanguage(2);
        $language2 = $this->setUpLanguage(3);

        $this->pageRepository->expects($this->once())
            ->method('startTransaction');

        $this->pageRepository->expects($this->once())
            ->method('commit');

        $this->pageRepository->expects($this->never())
            ->method('rollback');

        $this->event->expects($this->once())
            ->method('getContentManager')
            ->will($this->returnValue($this->pageManager));

        $this->event->expects($this->once())
            ->method('getValues')
            ->will($this->returnValue(array()));

        $this->languageRepository->expects($this->once())
            ->method('activeLanguages')
            ->will($this->returnValue(array($language1, $language2)));

        $this->pageManager->expects($this->once())
            ->method('get')
            ->will($this->returnValue($page));

        $this->pageManager->expects($this->once())
            ->method('getPageRepository')
            ->will($this->returnValue($this->pageRepository));

        $this->seoManager->expects($this->exactly(2))
            ->method('save')
            ->will($this->returnValue(true));

        $this->testListener->onBeforeAddPageCommit($this->event);
    }

    private function setUpBlockRepository()
    {
        $blockRepository = $this->getMockBuilder('RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Repository\Propel\BlockRepositoryPropel')
                                ->disableOriginalConstructor()
                                ->getMock();

        $this->seoManager->expects($this->once())
            ->method('getSeoRepository')
            ->will($this->returnValue($blockRepository));
    }
}
