require('./bootstrap');
require('./echo');

import stampList from './components/stamp_list';

const app = new Vue({
    el: '#app',
    data: {

    },
    components: {
        'stamp-list': stampList
    },
});