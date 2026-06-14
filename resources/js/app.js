import './bootstrap';

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";

import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';

import Swal from 'sweetalert2';

import { Indonesian } from "flatpickr/dist/l10n/id.js";

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

Swiper.use([Navigation, Pagination]);

window.flatpickr = flatpickr;
window.Swal = Swal;
window.Indonesian = Indonesian;

document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.mySwiper', {
        loop: true,
        spaceBetween: 10,

        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },

        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
});