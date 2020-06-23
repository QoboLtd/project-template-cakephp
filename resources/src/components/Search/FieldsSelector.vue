<template>
  <div class="row">
    <div class="col-md-5">
      <label for="available-columns">Available Columns</label>
      <select
        v-model="selected.available"
        class="form-control input-sm"
        multiple
        size="7"
        :disabled="disableSelection"
      >
        <option
          v-for="(item, listIndex) in availableList"
          :key="listIndex"
          :value="item.field"
        >
          {{ item.label }}
        </option>
      </select>
      <select
        v-model="selected.model"
        class="form-control input-sm"
        :disabled="disableSelection"
      >
        <option
          v-for="(item, itemIndex) in models"
          :key="itemIndex"
          :value="item"
        >
          {{ item }}
        </option>
      </select>
    </div>
    <div class="col-md-2">
      <label>&nbsp;</label>
      <div class="row">
        <div class="col-xs-6 col-md-12">
          <button
            type="button"
            class="btn btn-block btn-sm"
            :disabled="disableAdd"
            @click="add"
          >
            <span class="visible-md visible-lg"><i class="fa fa-angle-right" /></span>
            <span class="visible-xs visible-sm"><i class="fa fa-angle-down" /></span>
          </button>
        </div>
        <span class="visible-md visible-lg">&nbsp;</span>
        <div class="col-xs-6 col-md-12">
          <button
            type="button"
            class="btn btn-block btn-sm"
            :disabled="disableRemove"
            @click="remove"
          >
            <span class="visible-md visible-lg"><i class="fa fa-angle-left" /></span>
            <span class="visible-xs visible-sm"><i class="fa fa-angle-up" /></span>
          </button>
        </div>
      </div>
    </div>
    <div class="col-md-5">
      <span class="visible-xs visible-sm">&nbsp;</span>
      <label for="display-columns">Display Columns</label>
      <select
        v-model="selected.display"
        class="form-control input-sm"
        multiple
        size="7"
        :disabled="disableSelection"
      >
        <option
          v-for="(item, displayIndex) in displayList"
          :key="displayIndex"
          :value="item.field"
        >
          {{ item.label }} <template v-if="item.group !== model">
            - {{ item.group }}
          </template>
        </option>
      </select>
      <div class="row">
        <div class="col-xs-6">
          <button
            type="button"
            class="btn btn-block btn-sm"
            :disabled="disableSorting"
            @click="moveUp"
          >
            <i class="fa fa-angle-up" />
          </button>
        </div>
        <div class="col-xs-6">
          <button
            type="button"
            class="btn btn-block btn-sm"
            :disabled="disableSorting"
            @click="moveDown"
          >
            <i class="fa fa-angle-down" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapGetters, mapState } from 'vuex'
import Aggregate from '../../utils/aggregate'

export default {
  name: 'FieldsSelector',
  data () {
    return {
      selected: {
        available: [],
        display: [],
        model: this.$store.state.search.model
      }
    }
  },
  computed: {
    ...mapGetters({
      models: 'search/displayableModels'
    }),
    ...mapState({
      fields: state => state.search.fields,
      filters: state => state.search.filters,
      groupBy: state => state.search.group_by,
      model: state => state.search.model
    }),
    availableList () {
      return this.filters.filter(item => -1 === this.fields.indexOf(item.field) && item.group === this.selected.model)
    },
    disableAdd () {
      return Aggregate.hasAggregate(this.fields) || '' !== this.groupBy || !this.selected.available.length
    },
    disableRemove () {
      return Aggregate.hasAggregate(this.fields) || '' !== this.groupBy || !this.selected.display.length || !this.fields.length
    },
    disableSelection () {
      return Aggregate.hasAggregate(this.fields) || '' !== this.groupBy
    },
    disableSorting () {
      return Aggregate.hasAggregate(this.fields) || '' !== this.groupBy || !this.selected.display.length || !this.fields.length
    },
    displayList () {
      const result = this.fields.map((field) => {
        if (Aggregate.isAggregate(field)) {
          const aggregateField = this.filters.find(filter => filter.field === Aggregate.extractAggregateField(field))
          return {
            field: field,
            label: aggregateField.label + ' (' + Aggregate.extractAggregateType(field) + ')',
            group: aggregateField.group
          }
        }

        const filter = this.filters.find(item => item.field === field)
        return {
          field: filter.field,
          label: filter.label,
          group: filter.group
        }
      })

      return result
    }
  },
  methods: {
    moveDown() {
      let fields = JSON.parse(JSON.stringify(this.fields))
      let previous = -1
      this.selected.display.reverse().forEach(function (item) {
        const length = fields.length - 1
        const index = fields.indexOf(item)
        const start = index + 1
        if (length === index || start === previous) {
          previous = index

          return
        }

        fields.splice(start, 0, fields.splice(index, 1)[0])
      })

      this.$store.commit('search/fields', fields)
    },
    moveUp() {
      let fields = JSON.parse(JSON.stringify(this.fields))
      let previous = -1
      this.selected.display.forEach(function (item) {
        const length = 0
        const index = fields.indexOf(item)
        const start = index - 1
        if (length === index || start === previous) {
          previous = index

          return
        }

        fields.splice(start, 0, fields.splice(index, 1)[0])
      })

      this.$store.commit('search/fields', fields)
    },
    add() {
      const self = this
      let fields = JSON.parse(JSON.stringify(this.fields))

      this.selected.available.map(function (field) {
        if (fields.indexOf(field) !== -1) {
          return
        }
        const found = self.filters.find(item => item.field === field)
        if (found !== undefined) {
          fields.push(field)
        }
      })

      this.$store.commit('search/fields', fields)
    },
    remove() {
      let fields = JSON.parse(JSON.stringify(this.fields))

      this.selected.display.map(function (item) {
        const index = fields.indexOf(item)
        if (index > -1) {
          fields.splice(index, 1)
        }
      })

      this.$store.commit('search/fields', fields)
    },
    getFieldOptions(field) {
      return this.filters.find(filter => filter.field === field)
    }
  }
}
</script>
