<?php

declare(strict_types=1);

namespace R3H6\FormTypolinkCheckbox\Tests\ViewHelpers;

use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function renderReplacesLabelWithLink(): void
    {
        $template = '<label title="Grüße 运气!"><input type="checkbox"><i class="icon"/> Grüße 运气!</label>';
        $expected = '<label title="Gr&uuml;&szlig;e &#36816;&#27668;!"><input type="checkbox"><i class="icon"></i> Grüße <a href="/test">运气</a>!</label>';

        $cobj = $this->prophesize(ContentObjectRenderer::class);
        $cobj->stdWrap('运气', Argument::type('array'))->willReturn('<a href="/test">运气</a>');
        GeneralUtility::addInstance(ContentObjectRenderer::class, $cobj->reveal());

        $element = new TypolinkCheckbox('test-1', 'TypolinkCheckbox');
        $element->setLabel('Grüße [[运气]]!');
        $element->setProperty('link', 't3://page?uid=1');

        $arguments = ['element' => $element];
        $renderChildrenClosure = function () use ($template) { return $template; };
        $renderingContext = $this->createMock(RenderingContextInterface::class);
        $result = SubstituteLinkViewHelper::renderStatic($arguments, $renderChildrenClosure, $renderingContext);

        self::assertSame($expected, $result);
    }

    #[Test]
    public function renderAppendsLabelWithLink(): void
    {
        $template = '<label title="Grüße 运气!"><input type="checkbox"><i class="icon"/> Grüße 运气!</label>';
        $expected = '<label title="Gr&uuml;&szlig;e &#36816;&#27668;!"><input type="checkbox"><i class="icon"></i> Grüße 运气! <a href="/test">/test</a></label>';

        $cobj = $this->prophesize(ContentObjectRenderer::class);
        $cobj->stdWrap('', Argument::type('array'))->willReturn('<a href="/test">/test</a>');
        GeneralUtility::addInstance(ContentObjectRenderer::class, $cobj->reveal());

        $element = new TypolinkCheckbox('test-1', 'TypolinkCheckbox');
        $element->setLabel('Grüße 运气!');
        $element->setProperty('link', 't3://page?uid=1');

        $arguments = ['element' => $element];
        $renderChildrenClosure = function () use ($template) { return $template; };
        $renderingContext = $this->createMock(RenderingContextInterface::class);
        $result = SubstituteLinkViewHelper::renderStatic($arguments, $renderChildrenClosure, $renderingContext);

        self::assertSame($expected, $result);
    }
}
