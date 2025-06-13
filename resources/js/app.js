import './bootstrap';
import '../../public/assets/js/scripts.js';
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

// Jika ingin pakai bahasa Indonesia:
import { Indonesian } from "flatpickr/dist/l10n/id.js";
flatpickr.localize(Indonesian);

// Buat fungsi reusable
window.initFlatpickr = (selector, options = {}) => {
    flatpickr(selector, {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        ...options,
    });
};
