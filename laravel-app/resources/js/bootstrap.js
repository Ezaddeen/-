import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// يجهز Axios والمكتبات العامة التي تشترك بها واجهات JavaScript.
