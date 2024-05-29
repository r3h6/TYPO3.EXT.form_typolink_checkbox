<?php

namespace R3H6\FormTypolinkCheckbox\ViewHelpers;

use DOMNodeList;
use R3H6\FormTypolinkCheckbox\Domain\Model\FormElements\TypolinkCheckbox;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class SubstituteLinkViewHelper extends AbstractViewHelper
{
    protected $escapeChildren = false;
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('element', TypolinkCheckbox::class, 'The checkbox label property', true);
        $this->registerArgument('absolute', 'bool', 'Ensure the resulting URL is an absolute URL', false);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var TypolinkCheckbox $element */
        $element = $arguments['element'];
        /** @var string $content */
        $content = htmlentities($renderChildrenClosure());

        $typolink = [
            'parameter' => $element->getProperties()['link'],
            'forceAbsoluteUrl' => $arguments['absolute'] ?? false,
            'target' => '_blank',
            'fileTarget' => '_blank',
            'extTarget' => '_blank',
        ];

        /** @var ContentObjectRenderer $contentObject */
        $contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $linkedLabel = preg_replace_callback('/(\[\[([^\]]+)\]\])/si', function (array $match) use ($contentObject, $typolink): string {
            return $contentObject->stdWrap($match[2], ['typolink.' => $typolink]);
        }, $element->getOriginalLabel());

        if ($linkedLabel === $element->getOriginalLabel()) {
            $linkedLabel .= ' ' . $contentObject->stdWrap('', ['typolink.' => $typolink]);
        }

        $dom = new \DOMDocument();
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//text()');
        assert($nodes instanceof DOMNodeList);
        $replacements = [];
        foreach ($nodes as $node) {
            $replaced = str_replace($element->getLabel(), $linkedLabel, $node->nodeValue);
            if ($replaced !== $node->nodeValue) {
                $node->nodeValue = uniqid(':');
                $replacements[$node->nodeValue] = $replaced;
            }
        }

        return str_replace(array_keys($replacements), array_values($replacements), trim((string)$dom->saveHTML()));
    }
}
