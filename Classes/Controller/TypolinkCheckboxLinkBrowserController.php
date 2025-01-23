<?php

declare(strict_types=1);

namespace R3H6\FormTypolinkCheckbox\Controller;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Controller\AbstractLinkBrowserController;
use TYPO3\CMS\Core\LinkHandling\Exception\UnknownLinkHandlerException;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\LinkHandling\TypoLinkCodecService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

#[AsController]
class TypolinkCheckboxLinkBrowserController extends AbstractLinkBrowserController
{
    public function __construct(
        protected readonly LinkService $linkService,
        protected readonly TypoLinkCodecService $typoLinkCodecService,
        protected readonly FlashMessageService $flashMessageService,
    ) {
    }

    protected function initDocumentTemplate(): void
    {
        $this->pageRenderer->getJavaScriptRenderer()->addJavaScriptModuleInstruction(
            JavaScriptModuleInstruction::create('@r3h6/form-typolink-checkbox/backend/link-browser-adapter.js')
                ->invoke('initialize', $this->parameters['target'])
        );
    }

    protected function initVariables(ServerRequestInterface $request): void
    {
        parent::initVariables($request);
        $this->parameters['params']['allowedOptions'] = 'target';
    }

    protected function initCurrentUrl(): void
    {
        $currentLink = isset($this->parameters['currentValue']) ? trim($this->parameters['currentValue']) : '';
        /** @var array<string, string> $currentLinkParts */
        $currentLinkParts = $this->typoLinkCodecService->decode($currentLink);
        $currentLinkParts['params'] = $currentLinkParts['additionalParams'];
        unset($currentLinkParts['additionalParams']);

        if (!empty($currentLinkParts['url'])) {
            try {
                $data = $this->linkService->resolve($currentLinkParts['url']);
                $currentLinkParts['type'] = $data['type'];
                unset($data['type']);
                $currentLinkParts['url'] = $data;
            } catch (UnknownLinkHandlerException $e) {
                $this->flashMessageService->getMessageQueueByIdentifier()->enqueue(
                    new FlashMessage(message: $e->getMessage(), severity: ContextualFeedbackSeverity::ERROR)
                );
            }
        }

        $this->currentLinkParts = $currentLinkParts;

        parent::initCurrentUrl();
    }

    public function getConfiguration(): array
    {
        return [];
    }

    protected function getCurrentPageId(): int
    {
        return 0;
    }
}
