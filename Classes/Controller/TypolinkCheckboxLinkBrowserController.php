<?php

namespace R3H6\FormTypolinkCheckbox\Controller;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Recordlist\Controller\AbstractLinkBrowserController;

class TypolinkCheckboxLinkBrowserController extends AbstractLinkBrowserController
{
    // @phpstan-ignore-next-line
    protected function initDocumentTemplate()
    {
        parent::initDocumentTemplate();
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadRequireJsModule(
            'TYPO3/CMS/FormTypolinkCheckbox/LinkBrowserAdapter',
            'function(LinkBrowserAdapter) {
                LinkBrowserAdapter.target = ' . json_encode($this->parameters['target'], JSON_HEX_APOS | JSON_HEX_QUOT) . ';
            }'
        );
    }

    protected function getAllowedLinkAttributes()
    {
        $this->parameters['params']['blindLinkFields'] = 'target';
        return parent::getAllowedLinkAttributes();
    }

    /**
     * @return int
     */
    protected function getCurrentPageId()
    {
        return 0;
    }
}
