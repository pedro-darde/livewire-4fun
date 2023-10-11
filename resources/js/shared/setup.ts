import {createInertiaApp} from "@inertiajs/vue3";
import axios from "axios";
import {createApp, h, reactive} from "vue";
import 'vuetify/styles'
import {createVuetify} from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css'
import DateFnsAdapter from '@date-io/date-fns';
import ptBR from 'date-fns/locale/pt-BR'
import en from 'date-fns/locale/en-US'
import {vMaska} from "maska"


interface InertiaAppProps {
    pages: Record<string, any>;
    id?: string
    mixin?: object;
    store?: object;
    onSetup?: (appInstance: any, store: any, props: any) => void;
}

export function initInertiaApp({pages, id, mixin, store, onSetup}: InertiaAppProps) {
    return createInertiaApp({
        id: id ?? 'app',
        resolve: name => {
            console.log('page name', name);
            return pages[`./pages/${name}.vue`]
        },
        setup({el, App, props, plugin}) {
            const appInstance = createApp({render: () => h(App, props)})
                .use(plugin);

            appInstance.directive('maska', vMaska)

            const vuetify = createVuetify({
                components,
                directives: {
                    ...directives,
                    maska: vMaska
                },
                date: {
                    adapter: DateFnsAdapter,
                    locale: {
                        en,
                        'pt-BR': ptBR
                    },
                },

            })
            appInstance.use(vuetify)

            const provide = reactive({
                currentUser: null,
                flash: {},
                errors: {},
                logout() {
                    this.currentUser = null;
                },
                ...store
            });

            appInstance.provide('store', provide);

            if (mixin)
                appInstance.mixin(mixin);

            if (onSetup)
                onSetup(appInstance, provide, props);

            appInstance.mount(el);
        }
    })
}

export function initAuthInterceptor(store) {
    axios.interceptors.response.use(
        function (response) {
            try {
                if (response.data.props && response.data.props.auth && response.data.props.auth.user)
                    store.currentUser = response.data.props.auth.user
            } catch (e) {
                console.log(e);
            }
            return response;
        }
    )
}

export function duplicateInitialPageProps(store, props) {
    for (const key in props.initialPage.props) {
        if (key in store) {
            store[key] = props.initialPage.props[key];
        }
    }

    // store.currentUser = props.initialPage.props.auth.user;
}
