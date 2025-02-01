import LinkBrowser from '@typo3/backend/link-browser.js';
import Modal from '@typo3/backend/modal.js';

class LinkBrowserAdapter {

  constructor(){
    this.inputId = null;
  }

  initialize(inputId) {
    this.inputId = inputId;
  }

  finalizeFunction(link) {
    let input = this._getParent().document.getElementById(this.inputId);
    input.value = link;
    input.dispatchEvent(new Event('change'));
    Modal.dismiss();
  }

  _getParent() {
    let opener;
    if (
      typeof window.parent !== 'undefined' &&
      typeof window.parent.document.list_frame !== 'undefined' &&
      window.parent.document.list_frame.parent.document.querySelector('.t3js-modal-iframe') !== null
    ) {
      opener = window.parent.document.list_frame;
    } else if (
      typeof window.parent !== 'undefined' &&
      typeof window.parent.frames.list_frame !== 'undefined' &&
      window.parent.frames.list_frame.parent.document.querySelector('.t3js-modal-iframe') !== null
    ) {
      opener = window.parent.frames.list_frame;
    } else if (
      typeof window.frames !== 'undefined' &&
      typeof window.frames.frameElement !== 'undefined' &&
      window.frames.frameElement !== null &&
      window.frames.frameElement.classList.contains('t3js-modal-iframe')
    ) {
      opener = (window.frames.frameElement).contentWindow.parent;
    } else if (window.opener) {
      opener = window.opener;
    }

    return opener;
  }
};

const linkBrowserAdapter = new LinkBrowserAdapter();
export default linkBrowserAdapter;
LinkBrowser.finalizeFunction = (link) => { linkBrowserAdapter.finalizeFunction(link); };
