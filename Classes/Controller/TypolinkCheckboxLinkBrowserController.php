<?php

namespace R3H6\FormTypolinkCheckbox\Controller;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Recordlist\Controller\AbstractLinkBrowserController;

class TypolinkCheckboxLinkBrowserController extends AbstractLinkBrowserController
{
    protected function initDocumentTemplate(): void
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule(
            'TYPO3/CMS/FormTypolinkCheckbox/LinkBrowserAdapter',
        );
    }

    protected function initVariables(ServerRequestInterface $request): void
    {
        parent::initVariables($request);
        $this->parameters['params']['allowedOptions'] = 'target';
        $this->parameters['params']['allowedTypes'] = 'page,url,record,file,email,telephone';
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
