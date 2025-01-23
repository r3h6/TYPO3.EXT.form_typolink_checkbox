import $ from 'jquery';
import * as Helper from '@typo3/form/backend/form-editor/helper.js';
import Modal from '@typo3/backend/modal.js';

let _formEditorApp = null;

function getFormEditorApp() {
    return _formEditorApp;
};

function getPublisherSubscriber() {
    return getFormEditorApp().getPublisherSubscriber();
};

function getUtility() {
    return getFormEditorApp().getUtility();
};

function getHelper() {
    return Helper;
};

function getCurrentlySelectedFormElement() {
    return getFormEditorApp().getCurrentlySelectedFormElement();
};

function assert(test, message, messageCode) {
    return getFormEditorApp().assert(test, message, messageCode);
};

function _helperSetup() {
    assert('function' === $.type(Helper.bootstrap),
        'The view model helper does not implement the method "bootstrap"',
        1491643380
    );
    Helper.bootstrap(getFormEditorApp());
};

function _subscribeEvents() {
  getPublisherSubscriber().subscribe('view/stage/abstract/render/template/perform', function (topic, args) {
    if (args[0].get('type') === 'TypolinkCheckbox') {
      getFormEditorApp().getViewModel().getStage().renderCheckboxTemplate(args[0], args[1]);
    }
  });
  getPublisherSubscriber().subscribe('view/inspector/editor/insert/perform', function (topic, args) {
    if (args[0]['templateName'] === 'Inspector-TypolinkEditor') {
      renderTypolinkEditor(args[0], args[1], args[2], args[3]);
    }
  });
};

function renderTypolinkEditor(editorConfiguration, editorHtml, collectionElementIdentifier, collectionName) {
  getHelper().getTemplatePropertyDomElement('label', editorHtml).append(editorConfiguration.label);

  if (getUtility().isNonEmptyString(editorConfiguration['fieldExplanationText'])) {
    getHelper()
      .getTemplatePropertyDomElement('fieldExplanationText', editorHtml)
      .text(editorConfiguration['fieldExplanationText']);
  } else {
    getHelper()
      .getTemplatePropertyDomElement('fieldExplanationText', editorHtml)
      .remove();
  }

  var propertyPath = getFormEditorApp().buildPropertyPath(editorConfiguration['propertyPath'], collectionElementIdentifier, collectionName);
  var propertyData = getCurrentlySelectedFormElement().get(propertyPath);

  $('input', $(editorHtml)).val(propertyData);

  $('input', $(editorHtml)).on('keyup paste change', function () {
    getCurrentlySelectedFormElement().set(propertyPath, $(this).val());
  });

  $(editorHtml).on('click', 'a', function (e) {
    e.preventDefault();
    var input = $(editorHtml).find('input');
    var id = input.attr('id');
    var val = input.val();
    var url = $(this).attr('href') + '&P[target]=' + id + '&P[currentValue]=' + val;
    Modal.advanced({
      type: Modal.types.iframe,
      content: url,
      size: Modal.sizes.large,
    });
  });
}

export function bootstrap(formEditorApp) {
  _formEditorApp = formEditorApp;
  _helperSetup();
  _subscribeEvents();
}
