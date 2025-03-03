import './bootstrap';

import './helpers.js';

import flatpickr from 'flatpickr';
window.flatpickr = flatpickr;

import Alpine from 'alpinejs';

import pricelog from './pricelog.js';
Alpine.data('pricelog', pricelog);

import Autosize from '@marcreichel/alpine-autosize';
Alpine.plugin(Autosize);

window.Alpine = Alpine;

Alpine.start();
