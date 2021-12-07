<?php

namespace R3H6\FormTypolinkCheckbox\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Service\TypoLinkCodecService;
use TYPO3\CMS\Recordlist\Controller\AbstractLinkBrowserController;

class TypolinkCheckboxLinkBrowserController extends AbstractLinkBrowserController
{

    protected function initCurrentUrl()
    {
        $currentLink = isset($this->parameters['currentValue']) ? trim($this->parameters['currentValue']) : '';
        /** @var array<string, string> $currentLinkParts */
        $currentLinkParts = GeneralUtility::makeInstance(TypoLinkCodecService::class)->decode($currentLink);
        $currentLinkParts['params'] = $currentLinkParts['additionalParams'];
        unset($currentLinkParts['additionalParams']);

        if (!empty($currentLinkParts['url'])) {
            $data = $this->linkService->resolve($currentLinkParts['url']);
            $currentLinkParts['type'] = $data['type'];
            unset($data['type']);
            $currentLinkParts['url'] = $data;
        }

        $this->currentLinkParts = $currentLinkParts;

        parent::initCurrentUrl();
    }

    protected function initDocumentTemplate()
    {
        parent::initDocumentTemplate();
        $this->pageRenderer->loadRequireJsModule(
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
