<?php

declare(strict_types=1);

namespace R3H6\FormTypolinkCheckbox\Domain\Model\FormElements;

use TYPO3\CMS\Form\Domain\Model\FormElements\AbstractFormElement;

class TypolinkCheckbox extends AbstractFormElement
{
    // public function getLabel(): string
    // {
    //     // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this); exit;
    //     return str_replace(['[[', ']]'], '', $this->label);
    // }

    public function getOriginalLabel(): string
    {
        return $this->label;
    }
}
