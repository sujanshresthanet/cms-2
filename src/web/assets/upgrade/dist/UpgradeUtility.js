!function(){var e={956:function(){},695:function(e,t,n){var r=n(956);r.__esModule&&(r=r.default),"string"==typeof r&&(r=[[e.id,r,""]]),r.locals&&(e.exports=r.locals),(0,n(673).Z)("00d5c8f2",r,!0,{})},673:function(e,t,n){"use strict";function r(e,t){for(var n=[],r={},a=0;a<t.length;a++){var o=t[a],i=o[0],s={id:e+":"+a,css:o[1],media:o[2],sourceMap:o[3]};r[i]?r[i].parts.push(s):n.push(r[i]={id:i,parts:[s]})}return n}n.d(t,{Z:function(){return h}});var a="undefined"!=typeof document;if("undefined"!=typeof DEBUG&&DEBUG&&!a)throw new Error("vue-style-loader cannot be used in a non-browser environment. Use { target: 'node' } in your Webpack config to indicate a server-rendering environment.");var o={},i=a&&(document.head||document.getElementsByTagName("head")[0]),s=null,p=0,d=!1,l=function(){},u=null,c="data-vue-ssr-id",f="undefined"!=typeof navigator&&/msie [6-9]\b/.test(navigator.userAgent.toLowerCase());function h(e,t,n,a){d=n,u=a||{};var i=r(e,t);return v(i),function(t){for(var n=[],a=0;a<i.length;a++){var s=i[a];(p=o[s.id]).refs--,n.push(p)}for(t?v(i=r(e,t)):i=[],a=0;a<n.length;a++){var p;if(0===(p=n[a]).refs){for(var d=0;d<p.parts.length;d++)p.parts[d]();delete o[p.id]}}}}function v(e){for(var t=0;t<e.length;t++){var n=e[t],r=o[n.id];if(r){r.refs++;for(var a=0;a<r.parts.length;a++)r.parts[a](n.parts[a]);for(;a<n.parts.length;a++)r.parts.push(m(n.parts[a]));r.parts.length>n.parts.length&&(r.parts.length=n.parts.length)}else{var i=[];for(a=0;a<n.parts.length;a++)i.push(m(n.parts[a]));o[n.id]={id:n.id,refs:1,parts:i}}}}function g(){var e=document.createElement("style");return e.type="text/css",i.appendChild(e),e}function m(e){var t,n,r=document.querySelector("style["+c+'~="'+e.id+'"]');if(r){if(d)return l;r.parentNode.removeChild(r)}if(f){var a=p++;r=s||(s=g()),t=C.bind(null,r,a,!1),n=C.bind(null,r,a,!0)}else r=g(),t=x.bind(null,r),n=function(){r.parentNode.removeChild(r)};return t(e),function(r){if(r){if(r.css===e.css&&r.media===e.media&&r.sourceMap===e.sourceMap)return;t(e=r)}else n()}}var y,b=(y=[],function(e,t){return y[e]=t,y.filter(Boolean).join("\n")});function C(e,t,n,r){var a=n?"":r.css;if(e.styleSheet)e.styleSheet.cssText=b(t,a);else{var o=document.createTextNode(a),i=e.childNodes;i[t]&&e.removeChild(i[t]),i.length?e.insertBefore(o,i[t]):e.appendChild(o)}}function x(e,t){var n=t.css,r=t.media,a=t.sourceMap;if(r&&e.setAttribute("media",r),u.ssrId&&e.setAttribute(c,t.id),a&&(n+="\n/*# sourceURL="+a.sources[0]+" */",n+="\n/*# sourceMappingURL=data:application/json;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(a))))+" */"),e.styleSheet)e.styleSheet.cssText=n;else{for(;e.firstChild;)e.removeChild(e.firstChild);e.appendChild(document.createTextNode(n))}}}},t={};function n(r){var a=t[r];if(void 0!==a)return a.exports;var o=t[r]={id:r,exports:{}};return e[r](o,o.exports,n),o.exports}n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,{a:t}),t},n.d=function(e,t){for(var r in t)n.o(t,r)&&!n.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){"use strict";var e;function t(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=new Array(t);n<t;n++)r[n]=e[n];return r}n(695),e=jQuery,Craft.UpgradeUtility=Garnish.Base.extend({version:null,$body:null,$graphic:null,$status:null,init:function(t){var n=this;this.version=t,this.$body=e("#content"),this.$graphic=e("#graphic"),this.$status=e("#status"),Craft.sendApiRequest("GET","upgrade-info?cmsConstraint=^".concat(t,".0")).then((function(e){e.cms.latestVersion?n.showUpgradeInfo(e):n.displayError()})).catch((function(){n.displayError()}))},showUpgradeInfo:function(n){console.log(n),this.$graphic.remove(),this.$status.remove();var r=e('<div class="readable"/>').append(e("<h2/>",{text:Craft.t("app","Plugins")})).appendTo(this.$body);if(n.plugins.length){r.append(e("<p/>",{text:Craft.t("app","All plugins must be compatible with Craft {version} before you can upgrade.",{version:this.version})}));var a=e('<table id="plugins" class="data fullwidth"/>').appendTo(this.$body);e("\n          <thead>\n            <tr>\n              <th>".concat(Craft.t("app","Plugin"),"</th>\n              <th>").concat(Craft.t("app","Status"),"</th>\n              <th>").concat(Craft.t("app","Notes"),"</th>\n            </tr>\n          </thead>\n        ")).appendTo(a);var o,i=e("<tbody/>").appendTo(a),s=function(e,n){var r="undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(!r){if(Array.isArray(e)||(r=function(e,n){if(e){if("string"==typeof e)return t(e,n);var r=Object.prototype.toString.call(e).slice(8,-1);return"Object"===r&&e.constructor&&(r=e.constructor.name),"Map"===r||"Set"===r?Array.from(e):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?t(e,n):void 0}}(e))||n&&e&&"number"==typeof e.length){r&&(e=r);var a=0,o=function(){};return{s:o,n:function(){return a>=e.length?{done:!0}:{done:!1,value:e[a++]}},e:function(e){throw e},f:o}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var i,s=!0,p=!1;return{s:function(){r=r.call(e)},n:function(){var e=r.next();return s=e.done,e},e:function(e){p=!0,i=e},f:function(){try{s||null==r.return||r.return()}finally{if(p)throw i}}}}(n.plugins);try{for(s.s();!(o=s.n()).done;){var p=o.value,d=e("<tr/>").appendTo(i),l=e("<th/>").appendTo(d);p.icon&&e('<div class="plugin-icon">'.concat(p.icon,"</div>")).appendTo(l),e("<div/>",{class:"plugin-name",text:p.name}).appendTo(l);var u=e('<div class="plugin-developer"/>').appendTo(l);p.developerUrl?e("<a/>",{href:p.developerUrl,text:p.developerName}).appendTo(u):u.text(p.developerName);var c=e("<td/>").appendTo(d),f="";p.abandoned?(e("<div/>",{class:"plugin-status plugin-abandoned",text:Craft.t("app","Abandoned")}).appendTo(c),p.replacement&&(f=Craft.t("app",'The developer recommends using <a href="{url}">{name}</a> instead.',{url:"https://plugins.craftcms.com/".concat(p.replacement.handle),name:p.replacement.name}))):p.latestVersion?(e("<div/>",{class:"plugin-status plugin-ready",text:Craft.t("app","Ready")}).appendTo(c),e("<div/>",{class:"plugin-status",text:p.latestVersion}).appendTo(c),p.phpConstraint&&p.phpConstraint!=n.cms.phpConstraint&&(f=Craft.t("app","Requires PHP {version}",{version:p.phpConstraint}))):e("<div/>",{class:"plugin-status plugin-not-ready",text:Craft.t("app","Not ready")}).appendTo(c),p.note&&(f=Craft.escapeHtml(p.note)),e("<td/>",{class:"plugin-note",html:f}).appendTo(d)}}catch(e){s.e(e)}finally{s.f()}}else r.append(e("<p/>",{text:Craft.t("app","No plugins are installed.")}));e('<div class="readable centeralign pane"/>').append(e("<h2/>",{text:Craft.t("app","Ready to upgrade?")})).append(e("<p/>",{html:Craft.t("app",'Read the <a href="{url}">upgrade guide</a> for instructions on upgrading your site.',{url:"https://craftcms.com/docs/".concat(this.version,".x/upgrade.html")})})).appendTo(this.$body)},displayError:function(){this.$graphic.addClass("error"),this.$status.text(Craft.t("app","Unable to fetch upgrade info at this time."))}})}()}();
//# sourceMappingURL=UpgradeUtility.js.map