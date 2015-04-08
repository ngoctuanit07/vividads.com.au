/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
FormElementDependenceController = Class.create();
FormElementDependenceController.prototype = {
    /**
     * Structure of elements: {
     *     'id_of_dependent_element' : {
     *         'id_of_master_element_1' : 'reference_value',
     *         'id_of_master_element_2' : 'reference_value'
     *         'id_of_master_element_3' : ['reference_value1', 'reference_value2']
     *         ...
     *     }
     * }
     * @param object elementsMap
     * @param object config
     */
    initialize : function (elementsMap, config)
    {
        if (config) {
            this._config = config;
        }
        for (var idTo in elementsMap) {
            for (var idFrom in elementsMap[idTo]) {
                if ($(idFrom)) {
                    Event.observe($(idFrom), 'change', this.trackChange.bindAsEventListener(this, idTo, elementsMap[idTo]));
                    this.trackChange(null, idTo, elementsMap[idTo]);
                } else {
                    this.trackChange(null, idTo, elementsMap[idTo]);
                }
            }
        }
    },

    /**
     * Misc. config options
     * Keys are underscored intentionally
     */
    _config : {
        levels_up : 1 // how many levels up to travel when toggling element
    },

    /**
     * Define whether target element should be toggled and show/hide its row
     *
     * @param object e - event
     * @param string idTo - id of target element
     * @param valuesFrom - ids of master elements and reference values
     * @return
     */
    trackChange : function(e, idTo, valuesFrom)
    {
        // define whether the target should show up
        var shouldShowUp = true;
        for (var idFrom in valuesFrom) {
            var from = $(idFrom);
            if (valuesFrom[idFrom] instanceof Array) {
                if (!from || valuesFrom[idFrom].indexOf(from.value) == -1) {
                    shouldShowUp = false;
                }
            } else {
                if (!from || from.value != valuesFrom[idFrom]) {
                    shouldShowUp = false;
                }
            }
        }

        // toggle target row
        if (shouldShowUp) {
            var currentConfig = this._config;
            $(idTo).up(this._config.levels_up).select('input', 'select', 'td').each(function (item) {
                // don't touch hidden inputs (and Use Default inputs too), bc they may have custom logic
                if ((!item.type || item.type != 'hidden') && !($(item.id+'_inherit') && $(item.id+'_inherit').checked)
                    && !(currentConfig.can_edit_price != undefined && !currentConfig.can_edit_price)) {
                    item.disabled = false;
                }
            });
            $(idTo).up(this._config.levels_up).show();
        } else {
            $(idTo).up(this._config.levels_up).select('input', 'select', 'td').each(function (item){
                // don't touch hidden inputs (and Use Default inputs too), bc they may have custom logic
                if ((!item.type || item.type != 'hidden') && !($(item.id+'_inherit') && $(item.id+'_inherit').checked)) {
                    item.disabled = true;
                }
            });
            $(idTo).up(this._config.levels_up).hide();
        }
    }
}