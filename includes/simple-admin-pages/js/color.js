/**
 * Javascript functions for Color component
 *
 * @package Simple Admin Pages
 */

jQuery(document).ready(function ($) {

	"use strict";
    // Add Color Picker to all inputs that have 'color-field' class
    $(function() {
        $('.sap-color-field').wpColorPicker();
    });

});
