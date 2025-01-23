<?php

namespace R3H6\FormTypolinkCheckbox\ViewHelpers;

use Closure;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\VariableProviderInterface;
use TYPO3\CMS\Form\ViewHelpers\TranslateElementPropertyViewHelper;

class MyTranslateElementPropertyViewHelper implements ViewHelperInterface
{
    public function __construct(
        private TranslateElementPropertyViewHelper $decoratedViewhelper,
    ) {}

    public static function postParseEvent(ViewHelperNode $node, array $arguments, VariableProviderInterface $variableContainer) {}

    public function prepareArguments() {
        return $this->decoratedViewhelper->prepareArguments();
    }

    public function setArguments(array $arguments) {
        return $this->decoratedViewhelper->setArguments($arguments);
    }

    public function getContentArgumentName(): ?string {
        return $this->decoratedViewhelper->getContentArgumentName();
    }

    public function setChildNodes(array $nodes) {
        return $this->decoratedViewhelper->setChildNodes($nodes);
    }

    public function setViewHelperNode(ViewHelperNode $node) {
        DebuggerUtility::var_dump($node);exit;
        return $this->decoratedViewhelper->setViewHelperNode($node);
    }

    public function setRenderingContext(RenderingContextInterface $renderingContext) {
        return $this->decoratedViewhelper->setRenderingContext($renderingContext);
    }

    public function initializeArgumentsAndRender() {
        return $this->decoratedViewhelper->initializeArgumentsAndRender();
    }

    public function handleAdditionalArguments(array $arguments) {
        return $this->decoratedViewhelper->handleAdditionalArguments($arguments);
    }

    public function validateAdditionalArguments(array $arguments) {
        return $this->decoratedViewhelper->validateAdditionalArguments($arguments);
    }

    public function setRenderChildrenClosure(Closure $renderChildrenClosure) {
        return $this->decoratedViewhelper->setRenderChildrenClosure($renderChildrenClosure);
    }

    public function convert(TemplateCompiler $templateCompiler): array {
        return $this->decoratedViewhelper->convert($templateCompiler);
    }

    public function isChildrenEscapingEnabled() {
        // return false;
        return $this->decoratedViewhelper->isChildrenEscapingEnabled();
    }

    public function isOutputEscapingEnabled() {
        // return false;
        return $this->decoratedViewhelper->isOutputEscapingEnabled();
    }
}

