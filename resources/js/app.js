import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import mask from '@alpinejs/mask';
document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(mask);
});