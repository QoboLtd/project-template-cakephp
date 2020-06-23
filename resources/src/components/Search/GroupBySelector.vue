<template>
  <div>
    <div class="col-xs-4 col-md-5">
      <div class="form-group">
        <select
          v-model="model"
          class="form-control input-sm"
        >
          <option
            v-for="(item, modelIndex) in models"
            :key="modelIndex"
            :value="item"
          >
            {{ item }}
          </option>
        </select>
      </div>
    </div>
    <div class="col-xs-8 col-md-7">
      <div class="form-group">
        <select
          v-model="groupBy"
          class="form-control input-sm"
        >
          <option value="">
            -- Group by --
          </option>
          <template v-for="(item, fieldsListIndex) in searchableFieldsList">
            <option
              v-if="item.group === model"
              :key="fieldsListIndex"
              :value="item.field"
            >
              {{ item.label }}
            </option>
          </template>
        </select>
      </div>
    </div>
  </div>
</template>
<script>
import Aggregate from '../../utils/aggregate'
import { mapGetters, mapState } from 'vuex'

export default {
  name: 'GroupBySelector',
  data () {
    return {
      model: this.$store.state.search.model
    }
  },
  computed: {
    ...mapGetters({
      models: 'search/displayableModels'
    }),
    ...mapState({
      fields: state => state.search.fields,
      fieldsList: state => state.search.filters
    }),
    groupBy: {
      get () {
        return this.$store.state.search.group_by
      },
      set (value) {
        this.$store.commit('search/groupBy', value)

        let fields = []
        if (value) {
          fields.push(value)
        }

        const aggregate = this.fields.find(item => Aggregate.isAggregate(item))
        if (aggregate !== undefined) {
          fields.push(aggregate)
        }

        this.$store.commit('search/fields', fields)
      }
    },
    searchableFieldsList () {
      let result = []

      if (this.fieldsList.length) {
        result = this.fieldsList.filter( item => item.searchable === true)
      }

      return result
    }
  },
  watch: {
    groupBy (value) {
      this.model = value ?
        this.fieldsList.find(item => item.field === this.groupBy).group :
        this.$store.state.search.model
    }
  }
}
</script>
