define([
    'jquery',
    'TYPO3/CMS/Form/Backend/FormEditor/Helper',
    'TYPO3/CMS/Backend/Modal',
    'TYPO3/CMS/FormTypolinkCheckbox/LinkBrowserAdapter',
    'TYPO3/CMS/Backend/Icons',
    'TYPO3/CMS/Form/Backend/Contrib/jquery.mjs.nestedSortable'
  ], function($, Helper, Modal, LinkBrowserAdapter) {
    'use strict';
    LinkBrowserAdapter.foo = 'bar';
    return (function($, Helper, Modal) {

      /**
       * @private
       *
       * @var object
       */
      var _formEditorApp = null;

      /**
       * @private
       *
       * @return object
       */
      function getFormEditorApp() {
        return _formEditorApp;
      };

      /**
       * @private
       *
       * @return object
       */
      function getPublisherSubscriber() {
        return getFormEditorApp().getPublisherSubscriber();
      };

      /**
       * @private
       *
       * @return object
       */
      function getUtility() {
        return getFormEditorApp().getUtility();
      };

      /**
       * @private
       *
       * @param object
       * @return object
       */
      function getHelper() {
        return Helper;
      };

      /**
       * @private
       *
       * @return object
       */
      function getCurrentlySelectedFormElement() {
        return getFormEditorApp().getCurrentlySelectedFormElement();
      };

      /**
       * @private
       *
       * @param mixed test
       * @param string message
       * @param int messageCode
       * @return void
       */
      function assert(test, message, messageCode) {
        return getFormEditorApp().assert(test, message, messageCode);
      };

      /**
       * @private
       *
       * @return void
       * @throws 1491643380
       */
      function _helperSetup() {
        assert('function' === $.type(Helper.bootstrap),
          'The view model helper does not implement the method "bootstrap"',
          1491643380
        );
        Helper.bootstrap(getFormEditorApp());
      };

      /**
       * @private
       *
       * @return void
       */
      function _subscribeEvents() {

        getPublisherSubscriber().subscribe('view/stage/abstract/render/template/perform', function(topic, args) {
          if (args[0].get('type') === 'TypolinkCheckbox') {
            getFormEditorApp().getViewModel().getStage().renderSimpleTemplateWithValidators(args[0], args[1]);
          }
        });

        getPublisherSubscriber().subscribe('view/inspector/editor/insert/perform', function(topic, args){
            if (args[0]['templateName'] === 'Inspector-TypolinkEditor') {
              _initializeTypolinkEditor(args[0], args[1], args[2], args[3]);
            }
        });
      };

      function _initializeTypolinkEditor(editorConfiguration, editorHtml, collectionElementIdentifier, collectionName)
      {
        getHelper()
        .getTemplatePropertyDomElement('label', editorHtml)
        .append(editorConfiguration['label']);
        if (getUtility().isNonEmptyString(editorConfiguration['fieldExplanationText'])) {
          getHelper()
            .getTemplatePropertyDomElement('fieldExplanationText', editorHtml)
            .text(editorConfiguration['fieldExplanationText']);
        } else {
          getHelper()
            .getTemplatePropertyDomElement('fieldExplanationText', editorHtml)
            .remove();
        }

        var propertyPath = getFormEditorApp()
          .buildPropertyPath(editorConfiguration['propertyPath'], collectionElementIdentifier, collectionName);
        var propertyData = getCurrentlySelectedFormElement().get(propertyPath);
        $('input', $(editorHtml)).val(propertyData);

        $('input', $(editorHtml)).on('keyup paste change', function() {
          getCurrentlySelectedFormElement().set(propertyPath, $(this).val());
        });

        $(editorHtml).on('click', 'a', function(e){
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

      function finalizeFunction(link)
      {
        console.log('finalizeFunction '+link);
        Modal.dismiss();
      }

      /**
       * @public
       *
       * @param object formEditorApp
       * @return void
       */
      function bootstrap(formEditorApp) {
        _formEditorApp = formEditorApp;
        _helperSetup();
        _subscribeEvents();
      };



      /**
       * Publish the public methods.
       * Implements the "Revealing Module Pattern".
       */
      return {
        bootstrap: bootstrap
      };
    })($, Helper, Modal);
  });
