<template>
  <div id="app">
    <div class="row">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">
            General Field Handler Components
          </h3>
        </div>
        <div class="box-body">
          <!-- fh/phone.vue -->
          <div class="row">
            <div class="col-xs-12 col-md-6">
              <label>Phone Handler:</label> <br>
              <Phone
                guid="123-123-123"
                field="phone"
                value="8008081"
                @input-value-updated="showPhoneValue"
              />
              <br>
              <pre>
                {{ phoneValue }}
              </pre>
            </div>
          </div>
          <!-- fh/url.vue -->

          <div class="row">
            <div class="col-xs-12 col-md-6">
              <label>Url Handler:</label> <br>
              <Url
                guid="uuid-url"
                field="url_field"
                value="http://localhost"
                @input-value-updated="showUrlValue"
              />
              <br>
              <pre>
                {{ urlValue }}
              </pre>
            </div>
          </div>
          <!-- fh/Text.vue -->
          <div class="row">
            <div class="col-xs-12 col-md-6">
              <label> Input field handler</label><br>
              <TextInput
                guid="text-uuid"
                field="text_inputfield"
                value="foobar"
                label="Real Label"
                :is-required="true"
                size="input-sm"
                @input-value-updated="showTextValue"
              />
              <br>
              <pre>
                {{ textValue }}
              </pre>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Phone from '../src/components/fh/Phone.vue'
import Url from '../src/components/fh/Url.vue'
import TextInput from '../src/components/fh/Text.vue'

export default {
  components: {
    Phone,
    Url,
    TextInput
  },
  data () {
    return {
      phoneValue: {},
      urlValue: {},
      textValue: {}
    }
  },
  methods: {
    showPhoneValue (field, guid, val) {
      this.phoneValue = {
        'field': field,
        'guid': guid,
        'val': val
      }
    },
    showUrlValue (field, guid, val) {
      this.urlValue = {
        'field': field,
        'guid': guid,
        'val': val
      }
    },
    showTextValue (field, guid, val) {
      this.textValue = { 'field': field, 'guid': guid, 'val': val }
    }
  }
}
</script>
<style>
@import "~admin-lte/dist/css/AdminLTE.min.css";
@import "~admin-lte/dist/css/skins/_all-skins.min.css";

pre {
  border:1px solid #aaa;
  border-radius: 6px;
  background-color: #eee;
}
</style>
