import Vue from 'vue'
import Vuex from 'vuex'
import search from '../store/modules/search'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    search
  }
})
