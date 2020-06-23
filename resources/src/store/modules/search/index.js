import ApiSearch from '../../../ApiService/ApiSearch'
import Vue from 'vue'
import { dasherize, underscore } from 'inflected'
import { uuid } from 'vue-uuid'
import {
  API_STORE_SEARCH,
  API_VIEW_SEARCH,
  API_LIST_SEARCHES,
  API_EDIT_SEARCH,
  API_DELETE_SEARCH,
  FIELD_TYPE_MAP,
  FIELD_OPERATOR_TYPES
} from '../../../utils/search'

export default {
  namespaced: true,
  state: {
    conjunction: 'AND',
    criteria: {},
    default_fields: [],
    id: '',
    fields: [],
    filters: [],
    group_by: '',
    model: '',
    name: '',
    order_by_field: '',
    order_by_direction: 'DESC',
    savedSearches: [],
    user_id: ''
  },

  getters: {
    filtersGroup (state) {
      const result = {}
      for (var index in state.filters) {
        const filter = state.filters[index]
        if (!result.hasOwnProperty(filter.group)) {
          result[filter.group] = []
        }
        result[filter.group].push(filter)
      }

      return result
    },
    filtersFlat (state) {
      const result = {}
      for (var index in state.filters) {
        const filter = state.filters[index]
        result[filter.field] = filter
      }

      return result
    },
    filterModels (state, getters) {
      const models = Object.keys(getters.filtersGroup)

      return models.sort()
    },
    displayableModels (state, getters) {
      const filters = getters.filtersGroup
      const models = Object.keys(getters.filtersGroup)

      let result = models.filter((model) => {
        return -1 === ['oneToMany', 'manyToMany'].indexOf(filters[model][0].association)
      })

      return result.sort()
    }
  },

  mutations: {
    conjunction (state, value) {
      state.conjunction = value
    },
    criteriaCreate (state, payload) {
      if (!payload.field) {
        return
      }

      const type = payload.type ? payload.type : state.filters.find(item => item.field === payload.field).type
      const guid = payload.guid ? payload.guid : uuid.v4()
      const criteria = state.criteria
      if (!criteria.hasOwnProperty(payload.field)) {
        Vue.set(criteria, payload.field, {})
      }

      Vue.set(criteria[payload.field], guid, {
        type: type,
        operator: payload.operator !== undefined ? payload.operator : FIELD_OPERATOR_TYPES[FIELD_TYPE_MAP[type]][0].text,
        value: payload.value !== undefined ? payload.value : (type === 'boolean' ? 0 : '')
      })
    },
    criteriaCopy (state, value) {
      const newGuid = uuid.v4()
      const criteria = state.criteria

      for (const field in criteria) {
        for (const guid in criteria[field]) {
          if (value !== guid) {
            continue
          }

          const data = Object.assign({}, criteria[field][guid])

          Vue.set(criteria[field], newGuid, data)
        }
      }
    },
    criteriaRemove (state, value) {
      const criteria = state.criteria

      for (const field in criteria) {
        for (const guid in criteria[field]) {
          if (value !== guid) {
            continue
          }

          Vue.delete(criteria[field], guid)

          if (Object.keys(criteria[field]).length === 0) {
            Vue.delete(criteria, field)
          }
        }
      }
    },
    criteriaOperator (state, payload) {
      Vue.set(state.criteria[payload.field][payload.guid], 'operator', payload.operator)
    },
    criteriaValue (state, { field, guid, value }) {
      Vue.set(state.criteria[field][guid], 'value', value)
    },
    defaultFields (state, value) {
      state.default_fields = value
    },
    fields (state, value) {
      state.fields = value.length ? value : state.default_fields
    },
    filters (state, value) {
      state.filters = value.sort(function (a, b) {
        const aLabel = a.label.toLowerCase()
        const bLabel = b.label.toLowerCase()

        if (aLabel < bLabel) {
          return -1
        }

        if (aLabel > bLabel) {
          return 1
        }

        return 0
      })
    },
    groupBy (state, value) {
      state.group_by = value
    },
    name(state, value) {
      state.name = value
    },
    orderByDirection (state, value) {
      state.order_by_direction = value
    },
    orderByField (state, value) {
      state.order_by_field = value
    },
    result (state, value) {
      state.result = value
    },
    savedSearches (state, value) {
      value.sort((a, b) => a.name.toLowerCase() < b.name.toLowerCase() ? -1 : ((a.name.toLowerCase() > b.name.toLowerCase()) ? 1 : 0))
      state.savedSearches = value
    },
    savedSearchId (state, value) {
      state.id = value
      history.pushState({}, document.title, '/' + dasherize(underscore(state.model)) + '/search/' + value)
    },
    savedSearchModel (state, value) {
      state.model = value
    },
    savedSearchUserId (state, value) {
      state.user_id = value
    }
  },

  actions: {
    reset ({ commit, state }) {
      commit('conjunction', 'AND')
      commit('fields', state.default_fields)
      commit('groupBy', '')
      commit('name', '')
      commit('savedSearchId', '')
      commit('orderByField', '')
      commit('orderByDirection', 'DESC')

      Object.keys(state.criteria).map(
        (key) => Object.keys(state.criteria[key]).map(
          (guid) => commit('criteriaRemove', guid)
        )
      )
    },
    savedSearchCopy ({ commit, state, dispatch }, payload) {
      return ApiSearch
        .getSearch(API_VIEW_SEARCH, payload.id)
        .then(response => {
          const data = response.data.data
          delete data.id
          data.user_id = payload.user_id

          ApiSearch
            .addSearch(API_STORE_SEARCH, data)
            .then(resp => {
              dispatch('savedSearchesGet')
              dispatch('setNotification', {
                type: 'success',
                text: 'Search successfully copied.'
              })
            })
        })
    },
    savedSearchDelete ({ commit, state, dispatch }, id) {
      return ApiSearch
        .deleteSearch(`${API_DELETE_SEARCH}/${id}`)
        .then(response => {
          dispatch('savedSearchesGet')
          dispatch('setNotification', {
            type: 'success',
            text: 'Search successfully deleted.'
          })
        })
    },
    savedSearchGet ({ commit, state, dispatch }, id) {
      return ApiSearch
        .getSearch(API_VIEW_SEARCH, id)
        .then(response => {
          const data = response.data.data

          dispatch('reset')
          commit('conjunction', data.conjunction)
          commit('groupBy', data.group_by)
          commit('fields', data.fields)
          commit('name', data.name)
          commit('savedSearchId', data.id)
          commit('savedSearchModel', data.model)
          commit('savedSearchUserId', data.user_id)
          commit('orderByField', data.order_by_field)
          commit('orderByDirection', data.order_by_direction)
          if (data.hasOwnProperty('criteria') && typeof data.criteria === 'object' && data.criteria !== null) {
            Object.keys(data.criteria).forEach((item) => {
              Object.keys(data.criteria[item]).forEach((guid) => {
                const filter = data.criteria[item][guid]
                commit('criteriaCreate', {
                  field: item,
                  value: filter.value,
                  operator: filter.operator,
                  guid: guid,
                  type: filter.type
                })
              })
            })
          }
        })
    },
    savedSearchSave ({ commit, state, dispatch }) {
      const create = state.id === ''
      const url = create ? API_STORE_SEARCH : `${API_EDIT_SEARCH}/${state.id}`

      const payload = {
        id: state.id,
        name: state.name,
        user_id: state.user_id,
        model: state.model,
        conjunction: state.conjunction,
        criteria: JSON.parse(JSON.stringify(state.criteria)),
        fields: state.fields,
        group_by: state.group_by,
        order_by_field: state.order_by_field,
        order_by_direction: state.order_by_direction
      }

      if (create) {
        return ApiSearch
          .addSearch(url, payload)
          .then(response => {
            commit('savedSearchId', response.data.data.id)
            dispatch('savedSearchesGet')
            dispatch('setNotification', {
              type: 'success',
              text: 'Search successfully created.'
            })
          })
      } else {
        return ApiSearch
          .editSearch(url, payload)
          .then(response => {
            dispatch('savedSearchesGet')
            dispatch('setNotification', {
              type: 'success',
              text: 'Search successfully updated.'
            })
          })
      }
    },
    savedSearchesGet ({ commit, state }) {
      return ApiSearch
        .getSearches(API_LIST_SEARCHES, {
          'model': state.model,
          'system': 0,
          'user_id': state.user_id
        })
        .then(response => {
          commit('savedSearches', response.data.data)
        })
    },
    setNotification ({ commit, state }, data) {
      Vue.notify({
        type: data.type,
        text: data.text
      })
    }
  }
}
