/**
 * @module package/quiqqer/meta/bin/permalink/SeoDescription
 * @author www.pcsg.de (Henning Leutz)
 *
 * @require qui/QUI
 * @require qui/controls/Control
 * @require Locale
 */
define('package/quiqqer/meta/bin/seo/Description', [

    'qui/QUI',
    'qui/controls/Control',
    'Locale'

], function (QUI, QUIControl, QUILocale) {
    "use strict";

    var lg = 'quiqqer/meta';

    return new Class({

        Type   : 'package/quiqqer/meta/bin/seo/Description',
        Extends: QUIControl,

        Binds: [
            '$onImport',
            '$onKeyUp'
        ],

        initialize: function (options) {
            this.parent(options);

            this.$Display = null;

            this.addEvents({
                onImport: this.$onImport
            });
        },

        /**
         * event : on import
         */
        $onImport: function () {
            this.getElm().addEvent('keyup', this.$onKeyUp);
            this.$onKeyUp();
        },

        /**
         * event: on key up
         */
        $onKeyUp: function () {
            var value = this.getElm().value;

            // Get description field beneath/for input
            var Description = this.getElm().getParent().getParent().getElementsByClassName('field-container-item-desc')[0];

            Description.innerHTML = QUILocale.get(
                lg,
                'message.seo.description.length',
                {length: value.length}
            );
        }
    });
});
