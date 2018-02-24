require('./bootstrap');

import stampImprint from './components/stamp_imprint';

const app = new Vue({
    el: '#app',
    data: {
    },
    components: {
        'stamp-imprint': stampImprint
    },
});
