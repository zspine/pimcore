/**
 * Pimcore
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS("pimcore.object.tags.textarea");
pimcore.object.tags.textarea = Class.create(pimcore.object.tags.abstract, {

    type: "textarea",

    initialize: function (data, fieldConfig) {
        this.data = data;
        this.fieldConfig = fieldConfig;

    },

    getGridColumnEditor: function(field) {
        var editorConfig = {};

        if (field.config) {
            if (field.config.width) {
                if (intval(field.config.width) > 10) {
                    editorConfig.width = field.config.width;
                }
            }
        }

        if(field.layout.noteditable) {
            return null;
        }
        // TEXTAREA
        if (field.type == "textarea") {
           return new Ext.form.TextArea(editorConfig);
        }
    },

    getGridColumnFilter: function(field) {
        return {type: 'string', dataIndex: field.key};
    },

    getLayoutEdit: function () {


        if (intval(this.fieldConfig.width) < 1) {
            this.fieldConfig.width = 250;
        }
        if (intval(this.fieldConfig.height) < 1) {
            this.fieldConfig.height = 250;
        }

        var labelWidth = this.fieldConfig.labelWidth ? this.fieldConfig.labelWidth : 100;

        var conf = {
            width: this.fieldConfig.width,
            height: this.fieldConfig.height,
            fieldLabel: this.fieldConfig.title,
            componentCls: "object_field",
            labelWidth: labelWidth
        };

        conf.width += conf.labelWidth;

        if (this.data) {
            conf.value = this.data;
        }

        this.component = new Ext.form.TextArea(conf);

        return this.component;
    },


    getLayoutShow: function () {

        this.component = this.getLayoutEdit();
        this.component.disable();

        return this.component;
    },

    getValue: function () {
        return this.component.getValue();
    },

    getName: function () {
        return this.fieldConfig.name;
    }
});