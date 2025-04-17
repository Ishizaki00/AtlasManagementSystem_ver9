import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


require('./bootstrap');
require('./modal'); // ←ここで読み込む
