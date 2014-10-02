
/**
 * Permalink input control
 * Permalink control is for an site panel
 *
 * @author www.pcsg.de (Henning Leutz)
 */

define([

    'qui/QUI',
    'qui/controls/Control',
    'qui/controls/buttons/Button',
    'qui/controls/windows/Confirm',
    'Ajax',
    'Locale',

    'css!URL_OPT_DIR/quiqqer/meta/bin/permalink/Input.css'

], function(QUI, QUIControl, QUIButton, QUIConfirm, Ajax, Locale)
{
    "use strict";

    var lg = 'quiqqer/meta';

    return new Class({

        Type    : 'URL_OPT_DIR/quiqqer/meta/bin/permalink/Input',
        Extends : QUIControl,

        Binds : [
            'deletePermalink',
            '$onImport'
        ],

        initialize : function(options)
        {
            this.parent( options );

            this.$Input        = null;
            this.$DeleteButton = null;

            this.addEvents({
                onImport : this.$onImport
            });
        },

        /**
         * event : on import
         */
        $onImport : function()
        {
            var Container = new Element('div', {
                'data-quiid' : this.getId(),
                styles : {
                    'float' : 'left'
                }
            });

            this.$Input = this.$Elm.clone();
            this.$Input.inject( Container );

            this.$Input.setStyles({
                'float' : 'left'
            });

            this.$Input.addClass( 'meta-permalink-input' );

            // delete button
            this.$DeleteButton = new QUIButton({
                text     : Locale.get( lg, 'meta.permalink.button.delete.text' ),
                disabled : true,
                events   : {
                    onClick : this.deletePermalink
                }
            }).inject( Container );

            if ( this.$Input.value !== '' )
            {
                this.$Input.disabled = true;
                this.$DeleteButton.enable();
            }

            Container.replaces( this.$Elm );

            this.$Elm = Container;


            // id 1 cant have a permalink
            var PanelElm = this.$Elm.getParent( '.qui-panel' ),
                Panel    = QUI.Controls.getById( PanelElm.get( 'data-quiid' ) ),
                Site     = Panel.getSite();

            if ( Site.getId() == 1 )
            {
                var self = this;

                this.$Input.disabled = true;

                QUI.getMessageHandler(function(MH)
                {
                    MH.addInformation(
                        Locale.get( lg, 'exception.permalink.firstChild.cant.have.permalink' ),
                        self.$Input
                    );
                });
            }


        },

        /**
         * Delete the permalink
         */
        deletePermalink : function()
        {
            if ( this.$Input.value === '' ) {
                return;
            }

            var self     = this,
                PanelElm = this.$Elm.getParent( '.qui-panel' ),
                Panel    = QUI.Controls.getById( PanelElm.get( 'data-quiid' ) ),

                Site     = Panel.getSite(),
                Project  = Site.getProject();

            new QUIConfirm({
                title     : Locale.get( lg, 'meta.permalink.window.delete.title' ),
                maxHeight : 300,
                maxWidth  : 500,
                autoclose : false,
                text      : Locale.get( lg, 'meta.permalink.window.delete.text', {
                    id : Site.getId()
                }),
                events :
                {
                    onOpen : function() {
                        Panel.Loader.show();
                    },

                    onSubmit : function(Win)
                    {
                        Win.Loader.show();

                        Ajax.post('package_quiqqer_meta_ajax_permalink_delete', function(result)
                        {
                            self.$Input.value = result;

                            if ( self.$Input.value === '' )
                            {
                                self.$Input.disabled = false;
                                self.$DeleteButton.disable();
                            }

                            Win.close();
                            Panel.Loader.hide();

                        }, {
                            project   : Project.getName(),
                            lang      : Project.getLang(),
                            id        : Site.getId(),
                            'package' : 'quiqqer/meta',
                            onError   : function() {
                                Panel.Loader.hide();
                            }
                        });
                    },

                    onCancel : function() {
                        Panel.Loader.hide();
                    }
                }
            }).open();
        }
    });
});
