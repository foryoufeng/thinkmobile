/*
 * handlebars 扩展插件 v0.0.1
 * Copyright (c) 2015 rico
 * require jquery 1.7+
 * Licensed same as jquery - MIT License
 * http://www.opensource.org/licenses/mit-license.php
 * email: sunflower_rico@163.com
 * Date: 2015-12-27
 */
;
(function($, window, document, undefined) {
    var pluginName = "handlebarsUtils",
        defaults = {
    		template: $('#template'), 
            data: {}
        };
    function HandlebarsUtils(element, options) {
        this.element = element;
        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        this.version = 'v0.0.1';
        this.init();
    }
    HandlebarsUtils.prototype = {
        init: function() {
        	var compiled = {},that = this,template = that.settings.template,data = that.settings.data;
        	 if (template instanceof jQuery) {
                 template = $(template).html();
             }
         compiled[template] = Handlebars.compile(template);
         that.html(compiled[template](data));
        },
        debug: function($element) {
        	Handlebars.registerHelper("debug", function(optionalValue) {  
        		  console.log("Current Context");
        		  console.log("====================");
        		  console.log(this);
        		  if (optionalValue) {
        		    console.log("Value");
        		    console.log("====================");
        		    console.log(optionalValue);
        		  }
        		});
        }
    };
    $.fn[pluginName] = function(options) {
        this.each(function() {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new HandlebarsUtils(this, options));
            }
        });
        return this;
    };
})(jQuery, window, document);