<?php

declare(strict_types=1);

namespace R3H6\FormTypolinkCheckbox\ViewHelpers;

use DOMNodeList;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Service\TranslationService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Form\ViewHelpers\RenderRenderableViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use R3H6\FormTypolinkCheckbox\Domain\Model\FormElements\TypolinkCheckbox;

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
        $convmap = [0x80, 0xFFFF, 0, 0xFFFF];
        $content = mb_encode_numericentity($renderChildrenClosure(), $convmap, 'UTF-8');


        $translateionService = GeneralUtility::makeInstance(TranslationService::class);
        /** @var FormRuntime $formRuntime */
        $formRuntime = $renderingContext
            ->getViewHelperVariableContainer()
            ->get(RenderRenderableViewHelper::class, 'formRuntime');

        $label = $translateionService->translateFormElementValue($element, ['label'], $formRuntime);


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

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//text()');
        assert($nodes instanceof DOMNodeList);
        $replacements = [];
        foreach ($nodes as $node) {
            $replaced = str_replace($label, $linkedLabel, $node->nodeValue);
            if ($replaced !== $node->nodeValue) {
                $node->nodeValue = uniqid(':');
                $replacements[$node->nodeValue] = $replaced;
            }
        }

        return str_replace(array_keys($replacements), array_values($replacements), trim((string)$dom->saveHTML()));
    }
}
