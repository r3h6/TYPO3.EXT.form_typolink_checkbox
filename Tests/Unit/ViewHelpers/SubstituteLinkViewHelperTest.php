<?php

namespace R3H6\FormTypolinkCheckbox\Tests\ViewHelpers;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use R3H6\FormTypolinkCheckbox\Domain\Model\FormElements\TypolinkCheckbox;
use R3H6\FormTypolinkCheckbox\ViewHelpers\SubstituteLinkViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class SubstituteLinkViewHelperTest extends UnitTestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function renderReplacesLabelWithLink(): void
    {
        $template = '<label title="Hello wörld"><input type="checkbox"><i class="icon"/> Hello wörld</label>';
        $expected = '<label title="Hello w&ouml;rld"><input type="checkbox"><i class="icon"></i> Hello <a href="/test">wörld</a></label>';

        $cobj = $this->prophesize(ContentObjectRenderer::class);
        $cobj->stdWrap('wörld', Argument::type('array'))->willReturn('<a href="/test">wörld</a>');
        GeneralUtility::addInstance(ContentObjectRenderer::class, $cobj->reveal());

        $element = new TypolinkCheckbox('test', 'TypolinkCheckbox');
        $element->setLabel('Hello [[wörld]]');
        $element->setProperty('link', 't3://page?uid=1');

        $arguments = ['element' => $element];
        $renderChildrenClosure = function () use ($template) { return $template; };
        $renderingContext = $this->createMock(RenderingContextInterface::class);
        $result = SubstituteLinkViewHelper::renderStatic($arguments, $renderChildrenClosure, $renderingContext);

        self::assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function renderAppendsLabelWithLink(): void
    {
        $template = '<label title="Hello wörld"><input type="checkbox"><i class="icon"/> Hello wörld</label>';
        $expected = '<label title="Hello w&ouml;rld"><input type="checkbox"><i class="icon"></i> Hello wörld <a href="/test">/test</a></label>';

        $cobj = $this->prophesize(ContentObjectRenderer::class);
        $cobj->stdWrap('', Argument::type('array'))->willReturn('<a href="/test">/test</a>');
        GeneralUtility::addInstance(ContentObjectRenderer::class, $cobj->reveal());

        $element = new TypolinkCheckbox('test', 'TypolinkCheckbox');
        $element->setLabel('Hello wörld');
        $element->setProperty('link', 't3://page?uid=1');

        $arguments = ['element' => $element];
        $renderChildrenClosure = function () use ($template) { return $template; };
        $renderingContext = $this->createMock(RenderingContextInterface::class);
        $result = SubstituteLinkViewHelper::renderStatic($arguments, $renderChildrenClosure, $renderingContext);

        self::assertSame($expected, $result);
    }
}
