<?php

namespace R3H6\FormTypolinkCheckbox\Controller;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Controller\AbstractLinkBrowserController;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TypolinkCheckboxLinkBrowserController extends AbstractLinkBrowserController
{
    protected function initDocumentTemplate(): void
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadJavaScriptModule('@r3h6/formtypolinkcheckbox/LinkBrowserAdapter');
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
