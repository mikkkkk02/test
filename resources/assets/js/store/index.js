import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {

        fields: {
            namespaced: true,
            
            state: {
                errors: [],
            },

            mutations: {
                setErrors(state, value) {
                    state.errors.push(value);
                },

                clearErrors(state) {
                    state.errors = [];
                }
            },
        },
    }
});
