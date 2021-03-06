<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */
namespace LaminasTest\Feed\Reader;

use Laminas\Feed\Reader\Extension\Syndication\Feed;
use Laminas\Feed\Reader\Extension\WellFormedWeb\Entry;
use Laminas\Feed\Reader\ExtensionManagerInterface;
use Laminas\Feed\Reader\StandaloneExtensionManager;
use PHPUnit\Framework\TestCase;

class StandaloneExtensionManagerTest extends TestCase
{
    /**
     * @var StandaloneExtensionManager
     */
    private $extensions;

    public function setUp()
    {
        $this->extensions = new StandaloneExtensionManager();
    }

    public function testIsAnExtensionManagerImplementation()
    {
        $this->assertInstanceOf(ExtensionManagerInterface::class, $this->extensions);
    }

    public function defaultPlugins()
    {
        return [
            'Atom\Entry'            => ['Atom\Entry', \Laminas\Feed\Reader\Extension\Atom\Entry::class],
            'Atom\Feed'             => ['Atom\Feed', \Laminas\Feed\Reader\Extension\Atom\Feed::class],
            'Content\Entry'         => ['Content\Entry', \Laminas\Feed\Reader\Extension\Content\Entry::class],
            'CreativeCommons\Entry' => [
                'CreativeCommons\Entry',
                \Laminas\Feed\Reader\Extension\CreativeCommons\Entry::class
            ],
            'CreativeCommons\Feed'  => [
                'CreativeCommons\Feed',
                \Laminas\Feed\Reader\Extension\CreativeCommons\Feed::class
            ],
            'DublinCore\Entry'      => ['DublinCore\Entry', \Laminas\Feed\Reader\Extension\DublinCore\Entry::class],
            'DublinCore\Feed'       => ['DublinCore\Feed', \Laminas\Feed\Reader\Extension\DublinCore\Feed::class],
            'Podcast\Entry'         => ['Podcast\Entry', \Laminas\Feed\Reader\Extension\Podcast\Entry::class],
            'Podcast\Feed'          => ['Podcast\Feed', \Laminas\Feed\Reader\Extension\Podcast\Feed::class],
            'Slash\Entry'           => ['Slash\Entry', \Laminas\Feed\Reader\Extension\Slash\Entry::class],
            'Syndication\Feed'      => ['Syndication\Feed', Feed::class],
            'Thread\Entry'          => ['Thread\Entry', \Laminas\Feed\Reader\Extension\Thread\Entry::class],
            'WellFormedWeb\Entry'   => ['WellFormedWeb\Entry', Entry::class],
        ];
    }

    /**
     * @dataProvider defaultPlugins
     */
    public function testHasAllDefaultPlugins($pluginName, $pluginClass)
    {
        $this->assertTrue($this->extensions->has($pluginName));
    }

    /**
     * @dataProvider defaultPlugins
     */
    public function testCanRetrieveDefaultPluginInstances($pluginName, $pluginClass)
    {
        $extension = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $extension);
    }

    /**
     * @dataProvider defaultPlugins
     */
    public function testEachPluginRetrievalReturnsNewInstance($pluginName, $pluginClass)
    {
        $extension = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $extension);

        $test = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $test);
        $this->assertNotSame($extension, $test);
    }

    public function testAddAcceptsValidExtensionClasses()
    {
        $ext = $this->createMock(\Laminas\Feed\Reader\Extension\AbstractEntry::class);
        $this->extensions->add('Test/Entry', get_class($ext));
        $this->assertTrue($this->extensions->has('Test/Entry'));
        $ext = $this->createMock(\Laminas\Feed\Reader\Extension\AbstractFeed::class);
        $this->extensions->add('Test/Feed', get_class($ext));
        $this->assertTrue($this->extensions->has('Test/Feed'));
    }

    public function testAddRejectsInvalidExtensions()
    {
        $this->expectException(\Laminas\Feed\Reader\Exception\InvalidArgumentException::class);
        $this->extensions->add('Test/Entry', 'blah');
    }

    public function testExtensionRemoval()
    {
        $ext = $this->createMock(\Laminas\Feed\Reader\Extension\AbstractEntry::class);
        $this->extensions->add('Test/Entry', get_class($ext));
        $this->assertTrue($this->extensions->has('Test/Entry'));
        $this->extensions->remove('Test/Entry');
        $this->assertFalse($this->extensions->has('Test/Entry'));
    }
}
