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
use RedKiteLabs\ThemeEngineBundle\Core\Asset\Asset;
use org\bovigo\vfs\vfsStream;

/**
 * AssetTest
 *
 * @author RedKite Labs <webmaster@redkite-labs.com>
 */
class AssetTest extends TestCase
{
    private $kernel;

    protected function setUp()
    {
        $this->kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
    }

    public function testANullOrBlankAssetDidNothing()
    {
        $alAsset = new Asset($this->kernel, null);
        $this->assertNull($alAsset->getRealPath());
        $this->assertNull($alAsset->getAbsolutePath());

        $alAsset = new Asset($this->kernel, "");
        $this->assertNull($alAsset->getRealPath());
        $this->assertNull($alAsset->getAbsolutePath());
    }

    public function testANullAbsolutePathIsCalculateWhenAssetPointsToANonStandardSymfony2Path()
    {
        $asset = '/path/to/asset/asset.js';
        $alAsset = new Asset($this->kernel, $asset);
        $this->assertEquals($asset, $alAsset->getAsset());
        $this->assertEquals($asset, $alAsset->getRealPath());
        $this->assertNull($alAsset->getAbsolutePath());
    }
    
    public function testTheResourceCannotBeLocated()
    {
        $asset = '@BusinessWebsiteThemeBundle/Resources/public/css/reset.css';        
        $this->kernel
             ->expects($this->once())
             ->method('locateResource')
             ->will($this->throwException(new \RuntimeException()))
        ;

        $alAsset = new Asset($this->kernel, $asset);
        $this->assertEquals($asset, $alAsset->getAsset());
        $this->assertEquals('@BusinessWebsiteThemeBundle/Resources/public/css/reset.css', $alAsset->getRealPath());
    }

    public function testAssetPathAreCalculatedFromARelativePath()
    {
        $asset = '@BusinessWebsiteThemeBundle/Resources/public/css/reset.css';
        $bundleAssetPath = '/path/to/bundle/folder';
        $this->setUpKernel($bundleAssetPath);
        
        $this->kernel
             ->expects($this->exactly(2))
             ->method('getRootDir')
             ->will($this->returnValue('/path/to/kernel/root/dir'))
        ;

        $alAsset = new Asset($this->kernel, $asset);
        $this->assertEquals($asset, $alAsset->getAsset());
        $this->assertEquals($bundleAssetPath . '/Resources/public/css/reset.css', $alAsset->getRealPath());
        $this->assertEquals('bundles/businesswebsitetheme/css/reset.css', $alAsset->getAbsolutePath());
        $this->assertEquals('/path/to/kernel/root/dir/../web/bundles/businesswebsitetheme/css/reset.css', $alAsset->getWebFolderRealPath());        
    }

    public function testAssetPathAreCalculatedFromARealPath()
    {
        $asset = '/path/to/web/folder/bundles/businesswebsitetheme/css/style.css';
        $this->setUpKernel($asset, 0);

        $alAsset = new Asset($this->kernel, $asset);
        $this->assertEquals($asset, $alAsset->getAsset());
        $this->assertEquals($asset, $alAsset->getRealPath());
        $this->assertEquals('bundles/businesswebsitetheme/css/style.css', $alAsset->getAbsolutePath());
    }

    public function testAssetPathsAreAlwaysNormalized()
    {
        $asset = '\\path\\to\\web\\folder\\bundles\\businesswebsitetheme\\css\\style.css';
        $normalizedAsset = '/path/to/web/folder/bundles/businesswebsitetheme/css/style.css';
        $this->setUpKernel($normalizedAsset, 0);

        $alAsset = new Asset($this->kernel, $asset);
        $this->assertEquals($normalizedAsset, $alAsset->getAsset());
        $this->assertEquals($normalizedAsset, $alAsset->getRealPath());
        $this->assertEquals('bundles/businesswebsitetheme/css/style.css', $alAsset->getAbsolutePath());
    }
    
    public function testAssetIsRetrievedUsingAutolodingNamespacesFile()
    {
        
        $namespacesFile = "<?php\n";
        $namespacesFile .= '$vendorDir = "vendors";' . "\n";
        $namespacesFile .= '$baseDir = dirname($vendorDir);' . "\n";
        $namespacesFile .= 'return array(' . "\n";
        $namespacesFile .= '    \'RedKiteCmsBundle\' => array($vendorDir . \'/path/to/bundle\'),' . "\n";
        $namespacesFile .= ');' . "\n";
        
        $this->root = vfsStream::setup('root', null, array(
            'app' => array(),
            'vendor' => array(
                'composer' => array(
                    'autoload_namespaces.php' => $namespacesFile,
                ),
            ),
        ));
        
        $asset = '/vendors/path/to/bundle/RedKiteCmsBundle/Resources/public/js/asset.js';
        
        $this->kernel
             ->expects($this->once())
             ->method('getRootDir')
             ->will($this->returnValue(vfsStream::url('root/app')))
        ;

        $alAsset = new Asset($this->kernel, $asset);
        $this->assertEquals($asset, $alAsset->getRealPath());
        $this->assertEquals('bundles/redkitecms/js/asset.js', $alAsset->getAbsolutePath());
    }
    
    public function testAssetIsRecognizedAsBundle()
    {
        $asset = 'FakeBundle';
        $this->setUpKernel($asset);

        $alAsset = new Asset($this->kernel, $asset);
        $this->assertEquals($asset, $alAsset->getRealPath());
        $this->assertEquals('bundles/fake', $alAsset->getAbsolutePath());
    }
    
    public function testWebPath()
    {
        $asset = 'FakeBundle';
        $this->setUpKernel($asset);

        $alAsset = new Asset($this->kernel, $asset, 'foo');
        $this->assertEquals($asset, $alAsset->getRealPath());
        $this->assertEquals('foo/bundles/fake', $alAsset->getAbsolutePath());
    }

    private function setUpKernel($asset, $numberOfCalls = 1)
    {
        $this->kernel->expects($this->exactly($numberOfCalls))
            ->method('locateResource')
            ->will($this->returnValue($asset));
    }
}