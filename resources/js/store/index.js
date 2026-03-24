// resources/js/store/index.js

import { createStore } from 'vuex';

// Intenta restaurar el usuario desde localStorage al iniciar la tienda
const savedUser = JSON.parse(localStorage.getItem('user'));

const store = createStore({
    state: {
        user: savedUser || null,
    },
    mutations: {
        setUser(state, user) {
            state.user = user;
            // Guarda el usuario en localStorage
            localStorage.setItem('user', JSON.stringify(user));
        },
        clearUser(state) {
            state.user = null;
            // Elimina el usuario de localStorage
            localStorage.removeItem('user');
        }
    },
    actions: {
        login({ commit }, user) {
            commit('setUser', user);
        },
        logout({ commit }) {
            commit('clearUser');
        }
    },
    getters: {
        isAuthenticated(state) {
            return !!state.user;
        },
        getUser(state) {
            return state.user;
        }
    }
});

export default store;
