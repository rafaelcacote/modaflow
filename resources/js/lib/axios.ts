import axios from 'axios';

// Configurar o CSRF token automaticamente
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Configurar headers padrão
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default axios;

