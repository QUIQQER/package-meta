/**
 * Permalink input control
 * Permalink control is for an site panel
 *
 * @module package/quiqqer/meta/bin/permalink/Input
 * @author www.pcsg.de (Henning Leutz)
 */
define('package/quiqqer/meta/bin/permalink/Input', [

    'qui/QUI',
    'qui/controls/Control',
    'qui/controls/buttons/Button',
    'qui/controls/windows/Confirm',
    'qui/controls/messages/Information',
    'Ajax',
    'Locale',

    'css!package/quiqqer/meta/bin/permalink/Input.css'

], function (QUI, QUIControl, QUIButton, QUIConfirm, QUIInformation, Ajax, Locale) {
    "use strict";

    var lg = 'quiqqer/meta';

    return new Class({

        Type   : 'package/quiqqer/meta/bin/permalink/Input',
        Extends: QUIControl,

        Binds: [
            'deletePermalink',
            '$onImport'
        ],

        initialize: function (options) {
            this.parent(options);

            this.$Input        = null;
            this.$DeleteButton = null;

            this.addEvents({
                onImport: this.$onImport
            });
        },

        /**
         * event : on import
         */
        $onImport: function () {
            var Container = new Element('div', {
                'data-quiid': this.getId(),
                'class'     : 'field-container-field field-container-field-no-padding',
                styles      : {
                    display: 'flex'
                }
            });

            this.$Input = this.$Elm.clone();
            this.$Input.inject(Container);

            this.$Input.addClass('meta-permalink-input');

            // delete button
            this.$DeleteButton = new QUIButton({
                text    : Locale.get(lg, 'meta.permalink.button.delete.text'),
                disabled: true,
                styles  : {
                    borderRadius: 0,
                    'float'     : 'none'
                },
                events  : {
                    onClick: this.deletePermalink
                }
            }).inject(Container);

            if (this.$Input.value !== '') {
                this.$Input.disabled = true;
                this.$DeleteButton.enable();
            }

            Container.replaces(this.$Elm);

            this.$Elm = Container;


            // id 1 cant have a permalink
            var PanelElm = this.$Elm.getParent('.qui-panel'),
                Panel    = QUI.Controls.getById(PanelElm.get('data-quiid')),
                Site     = Panel.getSite();

            if (Site.getId() === 1) {
                this.$Input.disabled = true;

                new QUIInformation({
                    message: Locale.get(lg, 'exception.permalink.firstChild.cant.have.permalink'),
                    styles : {
                        marginBottom: 10
                    }
                }).inject(this.$Elm);
            }
        },

        /**
         * Delete the permalink
         */
        deletePermalink: function () {
            if (this.$Input.value === '') {
                return;
            }

            var self     = this,
                PanelElm = this.$Elm.getParent('.qui-panel'),
                Panel    = QUI.Controls.getById(PanelElm.get('data-quiid')),

                Site     = Panel.getSite(),
                Project  = Site.getProject();

            new QUIConfirm({
                title    : Locale.get(lg, 'meta.permalink.window.delete.title'),
                maxHeight: 300,
                maxWidth : 500,
                autoclose: false,
                text     : Locale.get(lg, 'meta.permalink.window.delete.text', {
                    id: Site.getId()
                }),
                events   : {
                    onOpen  : function () {
                        Panel.Loader.show();
                    },
                    onSubmit: function (Win) {
                        Win.Loader.show();

                        Ajax.post('package_quiqqer_meta_ajax_permalink_delete', function (result) {
                            self.$Input.value = result;

                            if (self.$Input.value === '') {
                                self.$Input.disabled = false;
                                self.$DeleteButton.disable();
                            }

                            Win.close();
                            Panel.Loader.hide();
                        }, {
                            project  : Project.getName(),
                            lang     : Project.getLang(),
                            id       : Site.getId(),
                            'package': 'quiqqer/meta',
                            onError  : function () {
                                Panel.Loader.hide();
                            }
                        });
                    },
                    onCancel: function () {
                        Panel.Loader.hide();
                    }
                }
            }).open();
        }
    });
});
