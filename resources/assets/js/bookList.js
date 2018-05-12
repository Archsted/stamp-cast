require('./bootstrap');

import bookList from './components/book_list';
import bookShow from './components/book_show';

const app = new Vue({
    el: '#app',
    data: {

    },
    components: {
        'book-list': bookList,
        'book-show': bookShow,
    },
});
