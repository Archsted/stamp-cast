require('./bootstrap');
require('./echo');

import stampDisplay from './components/stamp_display';

const app = new Vue({
    el: '#app',
    data: {

    },
    components: {
        'stamp-display': stampDisplay
    },
});
