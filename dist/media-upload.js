!function(e){var t={};function n(o){if(t[o])return t[o].exports;var r=t[o]={i:o,l:!1,exports:{}};return e[o].call(r.exports,r,r.exports,n),r.l=!0,r.exports}n.m=e,n.c=t,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)n.d(o,r,function(t){return e[t]}.bind(null,r));return o},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="/dist/",n(n.s=16)}({16:function(e,t){jQuery(document).ready(function(e){function t(t){var n=e("#imageseo-alt-"+t).val();e.post(ajaxurl,{action:"imageseo_media_alt_update",post_id:t,alt:n,success:function(){setTimeout(function(){e("#wrapper-imageseo-"+t+" .imageseo-loading").hide(),e("#wrapper-imageseo-"+t+" button span").show()},500)}})}e(this).on("keydown","input.imageseo-alt-ajax",function(t){if(13===t.keyCode)return e(this).blur(),!1}).on("blur","input.imageseo-alt-ajax",function(){var n=e(this).data("id");return e("#wrapper-imageseo-"+n+" button span").hide(),e("#wrapper-imageseo-"+n+" .imageseo-loading").show(),t(n),!1}),e(".wrapper-imageseo-input-alt button").on("click",function(n){n.preventDefault();var o=e(this).data("id");e("#wrapper-imageseo-"+o+" button span").hide(),e("#wrapper-imageseo-"+o+" .imageseo-loading").show(),t(o)})})}});