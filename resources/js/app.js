import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import Alpine from 'alpinejs';
import { Chart } from 'chart.js/auto';
import '../css/app.css';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();
