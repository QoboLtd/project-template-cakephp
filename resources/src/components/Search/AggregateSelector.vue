<template>
  <div>
    <select v-model="aggregate" class="form-control input-sm form-group">
      <option value="">-- Aggregate --</option>
      <option v-for="item in aggregates" :value="item.value">{{ item.text }}</option>
    </select>
    <div class="row form-group" v-if="aggregate">
      <div class="col-xs-4">
        <select v-model="model" class="form-control input-sm">
          <option v-for="item in models">{{ item }}</option>
        </select>
      </div>
      <div class="col-xs-8">
        <select v-model="field" class="form-control input-sm">
          <option value="">-- Aggregate field --</option>
          <option v-for="item in fieldsList" v-if="item.group === model" :value="item.field">{{ item.label }}</option>
        </select>
      </div>
    </div>
    <div class="row" v-if="aggregate && field">
      <div class="col-xs-4 form-group">
        <select v-model="filter" class="form-control input-sm">
          <option value=""></option>
          <option v-for="item in filters" :value="item.value">{{ item.text }}</option>
        </select>
      </div>
      <div class="col-xs-8">
        <div class="form-group">
            <input type="number" v-model="value" step="any" max="99999999999" class="form-control input-sm" />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Aggregate from '@/utils/aggregate'
import { AGGREGATES, FIELD_OPERATOR_TYPES } from '@/utils/search'
import { mapGetters, mapState } from 'vuex'
import { uuid } from 'vue-uuid'

export default {
  name: 'AggregateSelector',
  computed: {
    ...mapGetters({
      models: 'search/displayableModels'
    }),
    ...mapState({
      criteria: state => state.search.criteria,
      fields: state => state.search.fields,
      fieldsList: state => state.search.filters,
      groupBy: state => state.search.group_by
    }),
  },
  data() {
    return {
      aggregate: '',
      aggregates: AGGREGATES,
      field: '',
      filter: '',
      filters: FIELD_OPERATOR_TYPES['number'],
      guid: uuid.v4(),
      model: this.$store.state.search.model,
      value: ''
    }
  },
  watch: {
    criteria(value) {
      const aggregate = Object.keys(value).find(item => Aggregate.isAggregate(item))
      // if criteria include an aggregate, populate guid, filter and value inputs from it
      if (aggregate !== undefined) {
        const guid = Object.keys(value[aggregate])[0]
        this.guid = guid
        this.filter = value[aggregate][guid].operator
        this.value = value[aggregate][guid].value
      } else {
        this.filter = ''
      }
    },
    fields(value) {
      // if fields include an aggregate, populate aggregate and field inputs from it
      if (Aggregate.hasAggregate(value)) {
        this.aggregate = Aggregate.extractAggregateType(Aggregate.getAggregate(value))
        this.field = Aggregate.extractAggregateField(Aggregate.getAggregate(value))
      } else {
        this.aggregate = ''
        this.field = ''
      }
    }
  },
  created() {
    this.$watch(vm => [vm.aggregate, vm.field, vm.filter, vm.model, vm.value], (newValue, oldValue) => {
      this.$store.commit('search/criteriaRemove', this.guid)
      if (this.shouldCreateCriteria()) {
        this.$store.commit('search/criteriaCreate', {
          field:  Aggregate.getExpression(this.aggregate, this.field),
          guid: this.guid,
          operator: this.filter,
          type: 'decimal',
          value: this.value
        })
      }

      // if no aggregate selected reset the field and model inputs
      if (!this.aggregate) {
        this.field = ''
        this.model = this.$store.state.search.model
      }

      // if no model selected reset the field input
      if (!this.model) {
        this.field = ''
      }

      // if model had a value and it does not match the new value reset the field input
      if (oldValue[3] && newValue[3] !== oldValue[3]) {
        this.field = ''
      }

      // if field selected set model input to field's group/model
      if (this.field) {
        this.model = this.fieldsList.find(item => item.field === this.field).group
      }

      // if no field selected reset filter and value inputs
      if (!this.field) {
        this.filter = ''
        this.value = ''
      }

      // if no filter selected reset value input
      if (!this.filter) {
        this.value = ''
      }

      // re-calculate fields only if aggregate input had a value
      if (oldValue[0]) {
        let fields = []
        if (this.groupBy) {
          fields.push(this.groupBy)
        }
        if (this.aggregate && this.field) {
          fields.push(Aggregate.getExpression(this.aggregate, this.field))
        }

        this.$store.commit('search/fields', fields)
      }
    })
  },
  methods: {
    shouldCreateCriteria() {
      return '' !== this.aggregate && '' !== this.field && '' !== this.filter && '' !== this.value
    }
  }
}
</script>