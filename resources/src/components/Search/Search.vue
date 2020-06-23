<template>
  <div>
    <div
      v-if="withForm"
      class="box box-solid"
    >
      <div class="box-body">
        <form
          class="search-form"
          novalidate="novalidate"
          @submit.prevent="search"
        >
          <div class="row">
            <div class="col-lg-3 col-lg-push-9">
              <div class="row">
                <FilterSelector />
                <GroupBySelector />
                <div class="col-xs-12">
                  <AggregateSelector />
                </div>
                <div class="col-xs-12">
                  <div class="form-group">
                    <ConjunctionSelector />
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-9 col-lg-pull-3">
              <fieldset>
                <FiltersForm />
              </fieldset>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 col-lg-9">
              <hr class="visible-xs visible-sm visible-md">
              <FieldsSelector />
            </div>
            <div class="col-md-12 col-lg-3">
              <hr class="visible-xs visible-sm visible-md">
              <div class="row">
                <div class="col-md-6 col-lg-12">
                  <SavedSearchSelector @saved-search-fetched="search" />
                </div>
                <div class="col-md-6 col-lg-12">
                  <SaveSearch />
                </div>
              </div>
            </div>
            <div class="col-xs-12">
              <hr class="visible-xs visible-sm visible-md">
              <span class="visible-lg">&nbsp;</span>
              <button
                class="btn btn-primary btn-sm"
                type="submit"
              >
                <i class="fa fa-search" /> Search
              </button>
              <button
                type="button"
                class="btn btn-default btn-sm"
                @click="searchReset()"
              >
                <i class="fa fa-undo" /> Reset
              </button>
              <button
                v-if="withExport && searchId"
                type="button"
                class="btn btn-default btn-sm"
                @click="searchExport()"
              >
                <i class="fa fa-download" /> Export
              </button>
              <div
                v-if="withSets && searchId && !disableBatch"
                class="btn-group btn-group-sm"
              >
                <button
                  type="button"
                  class="btn btn-default dropdown-toggle"
                  data-toggle="dropdown"
                >
                  <i class="fa fa-plus" /> Add to set <span class="caret" />
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <a
                      href="#"
                      data-toggle="modal"
                      :data-target="'#' + sets.modal"
                    >
                      Create new set
                    </a>
                  </li>
                  <li
                    v-if="0 < sets.list.length"
                    role="separator"
                    class="divider"
                  />
                  <li
                    v-for="(set, setIndex) in sets.list"
                    :key="setIndex"
                  >
                    <a
                      href="#"
                      @click.prevent="setsAddTo(set.id)"
                    >
                      {{ set.name }}
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="box box-solid">
      <div class="box-body">
        <table-ajax
          v-if="loadResult"
          :url="'/api/' + modelUrl + '/search'"
          request-type="POST"
          :data="tableData"
          :order-field="sortByField"
          :order-direction="sortByOrder"
          :model="modelUrl"
          :primary-key="primaryKey"
          :headers="tableHeaders"
          :with-actions="!disableBatch"
          :with-batch-delete="!disableBatch && withBatchDelete"
          :with-batch-edit="!disableBatch && withBatchEdit"
          :with-export="!disableBatch && withExport"
          @sort-field-updated="sortFieldUpdated"
          @sort-order-updated="sortOrderUpdated"
        />
      </div>
    </div>
    <div
      :id="sets.modal"
      class="modal fade"
      tabindex="-1"
      role="dialog"
      aria-labelledby="mySetsLabel"
    >
      <div
        class="modal-dialog"
        role="document"
      >
        <div class="modal-content">
          <div class="modal-header">
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
              Create new set
            </h4>
          </div>
          <div class="modal-body">
            <div class="form-group input text required">
              <label
                class="control-label"
                for="sets-name"
              >
                Name
              </label>
              <input
                v-model="sets.name"
                type="text"
                class="form-control"
                required="required"
              >
            </div>
            <div class="form-group input ">
              <label
                class="control-label"
                for="sets-active"
              >
                Active
              </label>
              <div class="clearfix" />
              <input
                v-model="sets.active"
                type="checkbox"
              >
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-primary"
              :disabled="'' === sets.name"
              @click="setsCreate()"
            >
              Submit
            </button>
            <button
              type="button"
              class="btn btn-link"
              data-dismiss="modal"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios'
import Aggregate from '../../utils/aggregate'
import AggregateSelector from './AggregateSelector.vue'
import ConjunctionSelector from './ConjunctionSelector.vue'
import FieldsSelector from './FieldsSelector.vue'
import FilterSelector from './FilterSelector.vue'
import FiltersForm from './FiltersForm.vue'
import GroupBySelector from './GroupBySelector.vue'
import SavedSearchSelector from './SavedSearchSelector.vue'
import SaveSearch from './SaveSearch.vue'
import tableAjax from '../ui/TableAjax.vue'
import UuidMixin from '../../mixins/uuid.js'
import { dasherize, underscore } from 'inflected'
import { FIELDS_BASIC_TYPES } from '../../utils/search'
import { mapGetters, mapState, mapMutations } from 'vuex'

export default {
  name: 'Search',
  components: {
    AggregateSelector,
    ConjunctionSelector,
    FieldsSelector,
    FilterSelector,
    FiltersForm,
    GroupBySelector,
    SavedSearchSelector,
    SaveSearch,
    tableAjax
  },
  mixins: [UuidMixin],
  props: {
    displayFields: {
      type: Array,
      required: true
    },
    filters: {
      type: Array,
      required: true
    },
    model: {
      type: String,
      required: true
    },
    id: {
      type: String,
      default: ''
    },
    primaryKey: {
      type: String,
      required: true
    },
    searchQuery: {
      type: String,
      default: ''
    },
    userId: {
      type: String,
      required: true
    },
    withForm: {
      type: Boolean,
      default: true
    },
    withBatchDelete: {
      type: Boolean,
      default: true
    },
    withBatchEdit: {
      type: Boolean,
      default: true
    },
    withExport: {
      type: Boolean,
      default: true
    },
    withSets: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      basic_types: FIELDS_BASIC_TYPES,
      disableBatch: false,
      filter: '',
      loadResult: false,
      sets: {
        active: true,
        list: [],
        modal: 'sets-modal',
        name: ''
      },
      tableHeaders: [],
      tableData: {},
      // See setter code in this file for info.
      unwatchCriteria: null
    }
  },
  computed: {
    ...mapGetters({
      filtersFlat: 'search/filtersFlat',
    }),
    ...mapState({
      conjunction: state => state.search.conjunction,
      criteria: state => state.search.criteria,
      fields: state => state.search.fields,
      filtersList: state => state.search.filters,
      modelName: state => state.search.model,
      searchId: state => state.search.id,
      searchName: state => state.search.name,
      sortByField: state => state.search.order_by_field,
      sortByOrder: state => state.search.order_by_direction
    }),
    groupBy: {
      get () {
        return this.$store.state.search.group_by
      },
      set (value) {
        this.$store.commit('search/groupBy', value)
      }
    },
    modelUrl () {
      return dasherize(underscore(this.modelName))
    }
  },
  created() {
    /**
     * This watcher is responsible for initiating the search execution after all related type
     * filters are done fetching the records IDs and display values using the lookup API endpoint.
     *
     * This logic handles the case where a basic search is executed and related type field(s) are
     * part of the basic search criteria.
     *
     * This watcher is destroyed once the search is initiated for the first time.
     */
    this.unwatchCriteria = this.$watch('criteria', function () {
      const self = this

      const hasOnlyUuids = function (filter) {
        const values = Array.isArray(filter.value) ? filter.value : [filter.value]

        return values.every(item => self.isUuid(item))
      }

      const haveOnlyUuids = function (filters) {
        return Object.values(filters).every(item => {
          if ('related' !== item.type) {
            return true
          }

          return hasOnlyUuids(item)
        })
      }

      const canSearch = function () {
        return Object.values(self.criteria).every(item => {
          return haveOnlyUuids(item)
        })
      }

      if (canSearch()) {
        this.search()
      }
    },  { deep: true })

    this.$store.commit('search/filters', this.filters)
    this.$store.commit('search/defaultFields', this.displayFields)
    this.$store.commit('search/savedSearchModel', this.model)
    this.$store.commit('search/savedSearchUserId', this.userId)
    if ('' !== this.id) {
      this.$store.commit('search/savedSearchId', this.id)
      this.$store.dispatch('search/savedSearchGet', this.id).then(() => {
        this.search()
      })
    }

    if ('' === this.id) {
      this.$store.commit('search/fields', this.displayFields)
      if ('' !== this.searchQuery) {
        this.basicSearch(this.searchQuery)
      }

      // If there are no criteria, execute search right away, otherwise we
      // need to wait. See unwatchCriteria setter code in this file for info.
      if (0 === Object.keys(this.criteria).length) {
        this.search()
      }
    }

    this.setsFetch()
  },

  methods: {
    ...mapMutations({
      sortFieldUpdated: 'search/orderByField',
      sortOrderUpdated: 'search/orderByDirection'
    }),
    basicSearch(query) {
      const self = this
      // switch to OR conjunction on basic search
      this.$store.commit('search/conjunction', 'OR')
      this.displayFields.map(function(field) {
        const filter = self.filtersList.find(filter => filter.field === field)
        if (-1 !== self.basic_types.indexOf(filter.type)) {
          self.criteriaCreate(filter.field, query)
        }
      })
    },
    criteriaCreate(filter, value = '') {
      if ('' !== filter) {
        this.$store.commit('search/criteriaCreate', { field: filter, value: value })
      }

      this.filter = ''
    },
    search() {
      const self = this

      // Remove criteria watcher, see setter code in this file for more info.
      this.unwatchCriteria()

      this.loadResult = false
      this.disableBatch = '' !== this.groupBy || Aggregate.hasAggregate(this.fields)

      this.tableData = JSON.parse(JSON.stringify({ criteria: this.criteria, group_by: this.groupBy, conjunction: this.conjunction }))

      this.tableHeaders = []
      this.fields.forEach(function (field) {
        const value = field
        field = Aggregate.isAggregate(value) ? Aggregate.extractAggregateField(field) : field
        const filter = self.filtersList.find(item => item.field === field)
        if (filter === undefined) {
          return
        }

        let extras = ''
        if (Aggregate.isAggregate(value)) {
          extras = ' (' + Aggregate.extractAggregateType(value) + ')'
        }

        if (filter.group !== self.model) {
          extras = ' - ' + filter.group
        }

        self.tableHeaders.push({
          value: value,
          text: filter.label + extras
        })
      })

      // https://github.com/vuejs/Discussion/issues/356#issuecomment-312529480
      this.$nextTick(() => {
        this.loadResult = true
      })
    },
    setsCreate() {
      if (! this.withSets) {
        return
      }

      if ('' === this.sets.name) {
        return
      }

      return axios({
        method: 'post',
        url: '/sets/add',
        data: { name: this.sets.name, active: this.sets.active, module: this.modelUrl },
      }).then(response => {
        $('#' + this.sets.modal).modal('hide')
        this.setsFetch()
      }).catch(error => console.log(error))
    },
    setsFetch() {
      if (! this.withSets) {
        return
      }

      return axios({
        method: 'get',
        url: '/sets/index'
      }).then(response => {
        this.sets.list = []
        for (const key of Object.keys(response.data.sets)) {
          if (! response.data.sets[key].active) {
            continue
          }

          if (response.data.sets[key].module !== this.modelUrl) {
            continue
          }

          this.sets.list.push(response.data.sets[key])
        }
      }).catch(error => console.log(error))
    },
    setsAddTo(setId) {
      if (!this.withSets || !this.searchId || this.disableBatch) {
        return
      }

      axios({
        method: 'put',
        url: '/sets/assign-search',
        data: { id: setId, record_id: this.searchId },
      }).then(response => {
        this.$notify({
          'group': 'SearchNotification',
          'type': response.data.success ? 'success' : 'error',
          'text': response.data.success ?
            'The records have been successfully added to the set' :
            response.data.error
        })
      }).catch(error => console.log(error))
    },
    searchExport() {
      if (!this.withExport || !this.searchId) {
        return
      }

      const date = new Date()
      const addZero = function (value) {
        return value < 10 ? '0' + value : value
      }
      const datetime = [
        date.getFullYear(),
        addZero(date.getMonth() + 1),
        addZero(date.getDate()),
        addZero(date.getHours()),
        addZero(date.getMinutes()),
        addZero(date.getSeconds())
      ]

      window.location.href = encodeURI('/' + this.modelUrl + '/export-search/' + this.searchId + '/' + this.searchName + ' - ' + datetime.join(''))
    },
    searchReset() {
      this.$store.dispatch('search/reset')

      this.search()
    }
  }
}
</script>
<style lang="css">
  @import '~@anvyst/vue-fieldhandlers/dist/vue-fieldhandlers.css';

  .search-form .form-group {
      margin-bottom: 10px;
  }

  .vs__dropdown-toggle {
    border-radius: 0;
    padding: 0;
  }

  .vs__selected {
    margin: 4px;
  }

  .v-select input[type=search] {
    padding: 4px 10px !important;
    margin: 1px 5px !important;

  }

  .search-label {
    padding: 6px 0;
  }
</style>
