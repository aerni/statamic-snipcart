!function(t){var e={};function n(i){if(e[i])return e[i].exports;var r=e[i]={i:i,l:!1,exports:{}};return t[i].call(r.exports,r,r.exports,n),r.l=!0,r.exports}n.m=t,n.c=e,n.d=function(t,e,i){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:i})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var i=Object.create(null);if(n.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var r in t)n.d(i,r,function(e){return t[e]}.bind(null,r));return i},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="/",n(n.s=0)}([function(t,e,n){t.exports=n(1)},function(t,e,n){"use strict";n.r(e);function i(t,e,n,i,r,o,s,u){var a,l="function"==typeof t?t.options:t;if(e&&(l.render=e,l.staticRenderFns=n,l._compiled=!0),i&&(l.functional=!0),o&&(l._scopeId="data-v-"+o),s?(a=function(t){(t=t||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext)||"undefined"==typeof __VUE_SSR_CONTEXT__||(t=__VUE_SSR_CONTEXT__),r&&r.call(this,t),t&&t._registeredComponents&&t._registeredComponents.add(s)},l._ssrRegister=a):r&&(a=u?function(){r.call(this,(l.functional?this.parent:this).$root.$options.shadowRoot)}:r),a)if(l.functional){l._injectStyles=a;var c=l.render;l.render=function(t,e){return a.call(e),c(t,e)}}else{var p=l.beforeCreate;l.beforeCreate=p?[].concat(p,a):[a]}return{exports:t,options:l}}var r=i({name:"dimension-fieldtype",mixins:[Fieldtype],data:function(){return{short:this.meta.short}}},(function(){var t=this.$createElement,e=this._self._c||t;return e("div",[e("text-input",{attrs:{type:"number",append:this.short,isReadOnly:this.isReadOnly,min:"0",step:"0.01",value:this.value},on:{input:this.update}})],1)}),[],!1,null,null,null).exports,o=i({name:"money-fieldtype",mixins:[Fieldtype],computed:{originalPrice:function(){var t=this.$store.state.publish.base.values.price;if(t)return"Base Price: ".concat(t)},symbol:function(){return this.meta[this.site].symbol},site:function(){return this.$store.state.publish.base.site}}},(function(){var t=this.$createElement,e=this._self._c||t;return e("div",[e("text-input",{attrs:{type:"number",prepend:this.symbol,placeholder:this.originalPrice,isReadOnly:this.isReadOnly,min:"0",step:"0.01",value:this.value},on:{input:this.update}})],1)}),[],!1,null,null,null).exports,s=i({name:"stock-fieldtype",mixins:[Fieldtype],computed:{originalStock:function(){return this.$store.state.publish.base.values.stock}}},(function(){var t=this.$createElement,e=this._self._c||t;return e("div",[e("text-input",{attrs:{type:"number",placeholder:this.originalStock,isReadOnly:this.isReadOnly,min:"0",step:"1",value:this.value},on:{input:this.update}})],1)}),[],!1,null,null,null).exports;Statamic.booting((function(){Statamic.$components.register("dimension-fieldtype",r),Statamic.$components.register("money-fieldtype",o),Statamic.$components.register("stock-fieldtype",s)}))}]);