import '@/styles/index.scss';
import 'core-js';
import Vue from 'vue';
import Cookies from 'js-cookie';
import ElementUI from 'element-ui';
import App from './views/App';
import store from './store';
import router from '@/router';
import i18n from './lang'; // Internationalization
import '@/icons'; // icon
import '@/permission'; // permission control

import * as filters from './filters'; // global filters

import '@progress/kendo-ui';
import '@progress/kendo-ui/js/kendo.spreadsheet';
import '@progress/kendo-theme-default/dist/all.css';

import { Spreadsheet,
  SpreadsheetSheet,
  SpreadsheetInstaller } from '@progress/kendo-spreadsheet-vue-wrapper';

Vue.use(SpreadsheetInstaller);

import JsZip from 'jszip';
Vue.use(JsZip);

Vue.use(ElementUI, {
  size: Cookies.get('size') || 'medium', // set element-ui default size
  i18n: (key, value) => i18n.t(key, value),
});

// register global utility filters.
Object.keys(filters).forEach(key => {
  Vue.filter(key, filters[key]);
});

Vue.config.productionTip = false;

new Vue({
  el: '#app',
  router,
  store,
  i18n,
  components: {
    Spreadsheet,
    SpreadsheetSheet,
  },
  render: h => h(App),
});
