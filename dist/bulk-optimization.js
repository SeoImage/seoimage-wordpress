!function(e){var t={};function o(s){if(t[s])return t[s].exports;var r=t[s]={i:s,l:!1,exports:{}};return e[s].call(r.exports,r,r.exports,o),r.l=!0,r.exports}o.m=e,o.c=t,o.d=function(e,t,s){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:s})},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(e,t){if(1&t&&(e=o(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var s=Object.create(null);if(o.r(s),Object.defineProperty(s,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)o.d(s,r,function(t){return e[t]}.bind(null,r));return s},o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="/dist/",o(o.s=2)}({2:function(e,t){document.addEventListener("DOMContentLoaded",function(){const e=jQuery;let t=!1;if(0===IMAGESEO_ATTACHMENTS.length||0===IMAGESEO_ATTACHMENTS_WITH_TAG_EMPTY.length)return;const o=()=>{return e("#option-update-alt").is(":checked")?IMAGESEO_ATTACHMENTS_WITH_TAG_EMPTY:IMAGESEO_ATTACHMENTS};document.querySelector("#imageseo-bulk-reports--stop").addEventListener("click",function(o){o.preventDefault(),e(this).html("Current shutdown ..."),t=!1}),document.querySelector("#imageseo-bulk-reports--start").addEventListener("click",function(s){let r,i,n;s.preventDefault(),document.querySelector("#imageseo-percent-bulk").style.display="block",e(this).prop("disabled",!0),e("#imageseo-bulk-reports--preview").prop("disabled",!0),e("#option-update-alt").prop("disabled",!0),e("#option-update-alt-not-empty").prop("disabled",!0),e("#option-rename-file").prop("disabled",!0),e("span",e(this)).hide(),e(".imageseo-loading",e(this)).show(),e("#imageseo-reports-js .imageseo-reports-body").html(""),e("#imageseo-bulk-reports--stop").prop("disabled",!1),"new"===e("input[name='method']:checked").val()?(r=o().length,i=0,n=0):(r=o().length-(IMAGESEO_CURRENT_PROCESS+1),i=IMAGESEO_CURRENT_PROCESS,n=1),t=!0,a(i,0,r,n)}),document.querySelector("#imageseo-bulk-reports--preview").addEventListener("click",function(s){let r,i,n;s.preventDefault(),document.querySelector("#imageseo-percent-bulk").style.display="block",e(this).prop("disabled",!0),e("#option-update-alt").prop("disabled",!0).prop("checked",!1),e("#option-update-alt-not-empty").prop("disabled",!0).prop("checked",!1),e("#option-rename-file").prop("disabled",!0).prop("checked",!1),e("span",e(this)).hide(),e("#imageseo-bulk-reports--start").prop("disabled",!0),e(".imageseo-loading",e(this)).show(),e("#imageseo-reports-js .imageseo-reports-body").html(""),e("#imageseo-bulk-reports--stop").prop("disabled",!1),"new"===e("input[name='method']:checked").val()?(r=o().length,i=0,n=0):(r=o().length-(IMAGESEO_CURRENT_PROCESS+1),i=IMAGESEO_CURRENT_PROCESS,n=1),t=!0,a(i,0,r,n)});const s=(e,t)=>{const o=((e,t)=>e>t?100:Math.round(100*e/t))(e,t),s=document.querySelector("#imageseo-percent-bulk .imageseo-percent--item");s.style.width=`${o}%`,s.textContent=`${o}% (${e}/${t})`},r=({dashicons:e,current_name_file:t,file_generate:o,current_alt:s,alt_generate:r,file:i=""})=>`<div class="imageseo-reports-body-item">\n\t\t<div class="imageseo-reports--status"><span class="dashicons dashicons-${e}"></span></div>\n\t\t<div class="imageseo-reports--image"><div class="imageseo-reports--image-itm" style="background-image:url('${i}')"></div></div>\n\t\t<div class="imageseo-reports--src"><div>Current name file : ${t}<hr /> <strong>ImageSEO AI suggestion</strong> : ${o}</div></div>\n\t\t<div class="imageseo-reports--alt"><div>Current alt : ${s} <hr />  <strong>ImageSEO AI suggestion</strong> : ${r}</div></div>\n\t</div>`,i=({dashicons:e,src:t})=>`<div class="imageseo-reports-body-item imageseo-reports-body-item--error">\n\t\t<div class="imageseo-reports--status"><span class="dashicons dashicons-${e}"></span></div>\n\t\t<div class="imageseo-reports--src">${t}</div>\n\t\t<div class="imageseo-reports--error">Impossible to generate the report of this image. Remember to check your remaining credits</div>\n\t</div>`;function a(d,p,l,c=0){const u=d+p+c;if(p>IMAGESEO_LIMIT_IMAGES)return e("#imageseo-reports-js .imageseo-reports-body").prepend('<div class="imageseo-reports-body-item imageseo-reports-body-item--error">\n\t\t<div class="imageseo-reports--status"><span class="dashicons dashicons-no"></span></div>\n\t\t<div class="imageseo-reports--src"></div>\n\t\t<div class="imageseo-reports--error">You have exceeded your image limit</div>\n\t</div>'),void n();if(p>l||!t)return void n();if(void 0===o()[u])return void a(d,++p,l,c);const m=e("#option-update-alt").is(":checked"),g=e("#option-update-alt-not-empty").is(":checked"),b=e("#option-rename-file").is(":checked");e.post({url:ajaxurl,success:t=>{IMAGESEO_CURRENT_PROCESS=p+1;let n=`Attachment ID : ${o()[u]}`;t.data&&t.data.src&&(n=t.data.src),s(++p,l),t.success?e("#imageseo-reports-js .imageseo-reports-body").prepend(r({...t.data,src:n,dashicons:"yes"})):e("#imageseo-reports-js .imageseo-reports-body").prepend(i({...t.data,src:n,dashicons:"no"})),a(d,p,l,c)},error:t=>{e("#imageseo-reports-js .imageseo-reports-body").prepend(r({src:`Attachment ID: ${o()[u]}`,name_file:"",alt_generate:"",dashicons:"no"})),s(++p,l),a(d,p,l,c)}},{action:"imageseo_report_attachment",update_alt:m,update_alt_not_empty:g,rename_file:b,total:o().length,current:u,attachment_id:o()[u]})}function n(){t=!1,e("#imageseo-bulk-reports--start").prop("disabled",!1),e("#imageseo-bulk-reports--start .imageseo-loading").hide(),e("#imageseo-bulk-reports--start span").show(),e("#imageseo-bulk-reports--preview").prop("disabled",!1),e("#imageseo-bulk-reports--preview .imageseo-loading").hide(),e("#imageseo-bulk-reports--preview span").show(),e("#option-update-alt").prop("disabled",!1),e("#option-update-alt-not-empty").prop("disabled",!1),e("#option-rename-file").prop("disabled",!1),e("#imageseo-bulk-reports--stop").prop("disabled",!0).html("Stop")}})}});