var affiliatexExports;(()=>{"use strict";var e={n:t=>{var i=t&&t.__esModule?()=>t.default:()=>t;return e.d(i,{a:i}),i},d:(t,i)=>{for(var a in i)e.o(i,a)&&!e.o(t,a)&&Object.defineProperty(t,a,{enumerable:!0,get:i[a]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t)};const t=window.jQuery;var i=e.n(t);i()(".notice-affiliatex-plugin").on("click",".affx-notice--dismiss, .notice-dismiss",(function(e){e.preventDefault(),e.stopPropagation();const t=i()(e.target).parents(".notice-affiliatex-plugin"),a=t.data("notice");i().ajax({type:"POST",url:ajaxurl,data:{action:"affiliatex_notice_dismissed",notice:a,security:AffiliateXNotice.ajax_nonce},dataType:"JSON",success:function(e){!0===e.success&&t.slideUp(200,(()=>{t.remove()}))}})})),(affiliatexExports=void 0===affiliatexExports?{}:affiliatexExports).noticesJS={}})();