import './bootstrap';
import '../css/app.css';
import Swal from "sweetalert2";
import { initInertiaApp, initAuthInterceptor, duplicateInitialPageProps } from "./shared/setup";
import axios from 'axios'
window.Swal = Swal

window.axios = axios
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.headers.common["Accept"] =
    "application/json; charset=UTF-8";
window.axios.defaults.withCredentials = true;
initInertiaApp({
    pages: import.meta.glob('./pages/**/*.vue', { eager: true }),
    onSetup(appInstance, store, props) {
        initAuthInterceptor(store)
        duplicateInitialPageProps(store, props)
    },
    mixin: {
        methods: {
            devicePlatform() {
                const userAgent =
                    navigator.userAgent || navigator.vendor || window.opera

                if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                    return 'iOS'
                }

                if (/android/i.test(userAgent)) {
                    return 'Android'
                }

                return 'Desktop'
            },
            isNativeMapAppAvailable() {
                const platform = this.devicePlatform()

                return platform === 'iOS' || platform === 'Android'
            },
        },
    },
}).then((r) => {
    console.log('Inertia app created')
})

