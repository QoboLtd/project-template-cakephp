<template>
  <div
    :class="{'required': isRequired}"
    class="form-group"
  >
    <label
      v-if="label !== ''"
      :for="guid"
      class="control-label"
    >
      {{ label }}
    </label>
    <v-select
      v-model="val"
      placeholder="-- Please choose --"
      :options="options"
      label="label"
      :multiple="multiple"
      :filterable="false"
      @search="onSearch"
    >
      <template slot="no-options">
        type to search..
      </template>
      <template
        slot="option"
        slot-scope="option"
      >
        <div class="d-center">
          {{ option.label }}
        </div>
      </template>
      <template
        slot="selected-option"
        scope="option"
      >
        <div class="selected d-center">
          {{ option.label }}
        </div>
      </template>
    </v-select>
  </div>
</template>

<script>
import axios from 'axios'
import debounce from 'lodash.debounce'
import vSelect from 'vue-select'
import { MAGIC_VALUE_WRAPPER } from '../../utils/constants.js'
import UuidMixin from '../../mixins/uuid.js'

export default {
  components: {
    vSelect
  },
  mixins: [UuidMixin],
  props: {
    displayField: {
      type: String,
      required: true
    },
    field: {
      type: String,
      required: true
    },
    guid: {
      type: String,
      required: true
    },
    multiple: {
      type: Boolean,
      default: false
    },
    source: {
      type: String,
      required: true
    },
    value: {
      type: [String, Array],
      default: ''
    },
    label: {
      type: String,
      default: '',
      required: false
    },
    size: {
      type: String,
      default: '',
      required: false
    },
    isRequired: {
      type: Boolean,
      default: false,
      required: false
    }
  },
  data () {
    return {
      magicValue: { value: MAGIC_VALUE_WRAPPER + 'me' + MAGIC_VALUE_WRAPPER, label: '<< me >>' },
      options: [],
      val: []
    }
  },
  watch: {
    val () {
      let value = []
      for (const key of Object.keys(this.val)) {
        value.push(this.val[key].value)
      }

      this.$emit('input-value-updated', this.field, this.guid, value)
    }
  },
  created () {
    if ('' === this.value || [] === this.value) {
      return
    }

    const self = this
    const value = 'string' === typeof this.value ? [this.value] : this.value

    let hasMagicValue = false
    let promises = []

    value.forEach(function (id) {
      if (MAGIC_VALUE_WRAPPER + 'me' + MAGIC_VALUE_WRAPPER === id) {
        hasMagicValue = true
        return
      }

      promises.push(self.isUuid(id) ? self.getDisplayValueById(id) : self.getDisplayValueByLookup(id))
    })

    Promise.all(promises).then(function(values) {
      function flatten(array) {
        var result = []
        array.forEach(item => {
          if (Array.isArray(item)) {
            result = result.concat(flatten(item))
          } else {
            result.push(item)
          }
        })
        return result
      }

      values = flatten(values)

      if (hasMagicValue) {
        values.push(self.magicValue)
      }
      self.options = values
      self.val = values
    })
  },
  methods: {
    onSearch(search, loading) {
      loading(true)
      this.options = []
      this.search(search, loading, this)
    },
    search: debounce((search, loading, vm, page = 1) => {
      axios({
        method: 'get',
        url: encodeURI('/api/' + vm.source + '/lookup?query=' + search + '&limit=100&page=' + page),
      }).then(response => {
        const pagination = response.data.pagination

        if ('users' === vm.source && 1 === pagination.current_page) {
          vm.options.push(vm.magicValue)
        }

        for (const key of Object.keys(response.data.data)) {
          vm.options.push({ value: key, label: response.data.data[key] })
        }

        if (pagination.current_page < pagination.page_count) {
          vm.search(search, loading, vm, pagination.current_page + 1)
        } else {
          loading(false)
        }
      }).catch(error => console.log(error))
    }, 1000),
    getDisplayValueById(id) {
      return axios({
        method: 'get',
        url: encodeURI('/api/' + this.source + '/view/' + id),
      }).then(response => {
        let label = true === response.data.success && response.data.data.hasOwnProperty(this.displayField) ?
            label = response.data.data[this.displayField] :
            id

        return { value: id, label: label }
      }).catch(error => console.log(error))
    },
    getDisplayValueByLookup(query) {
      return axios({
        method: 'get',
        url: encodeURI('/api/' + this.source + '/lookup?query=' + query),
      }).then(response => {
        if (true !== response.data.success) {
          return
        }

        let values = []
        Object.keys(response.data.data).forEach(key => {
          values.push({ value: key, label: response.data.data[key] })
        })

        return values
      }).catch(error => console.log(error))
    }
  }
}
</script>
<style>
.v-select .dropdown-toggle {
     border-radius: 0 !important;
     padding: 0 !important;
}

.v-select .selected-tag {
    font-size: 12px !important;
    margin: 3px 2px !important;
}

.v-select input[type=search] {
    font-size: 12px !important;
    margin: 0 !important;
    padding: 5px 10px !important;
    height: 28px !important;
}
</style>
