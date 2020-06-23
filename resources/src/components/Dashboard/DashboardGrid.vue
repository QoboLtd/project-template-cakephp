<template>
  <div>
    <input
      id="dashboard-options"
      type="hidden"
      name="options"
      class="form-control"
    >
    <div class="box box-primary">
      <div class="box-body">
        <div class="box-header">
          <h3 class="box-title">
            Widgets
          </h3>
        </div>
        <div
          class="box-body"
          style="border:1px dashed #d3d3d3;"
        >
          <grid-layout
            :layout="layout"
            :row-height="50"
            :vertical-compact="false"
            :margin="[5, 5]"
            :use-css-transforms="true"
          >
            <grid-item
              v-for="item in layout"
              :key="item.i"
              :x="item.x"
              :y="item.y"
              :w="item.w"
              :h="item.h"
              :min-w="2"
              :min-h="2"
              :i="item.i"
              class="box box-solid"
              :class="getElementBackground(item)"
            >
              <div class="box-header with-border">
                <h3 class="box-title">
                  <i
                    class="fa"
                    :class="getElementIcon(item)"
                  />
                  {{ item.title }}
                </h3>
                <div class="box-tools">
                  <div class="btn btn-box-tool">
                    <a
                      href="#"
                      @click="removeItem(item)"
                    >
                      <i class="fa fa-minus-circle" />
                    </a>
                  </div>
                </div>
              </div>

              <div class="box-body">
                <p>
                  {{ item.data.name }}
                </p>
              </div>
            </grid-item>
          </grid-layout>
        </div>
      </div>
    </div>

    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">
          Available Widgets
        </h3>
      </div>
      <div class="row">
        <div class="col-md-2">
          <ul class="nav nav-tabs nav-stacked">
            <li
              v-for="(type, wIndex) in widgetTypes"
              :key="wIndex"
              :class="getActiveTab(type, widgetTypes[0], '')"
              class="widget-tab"
            >
              <a
                :href="'#' + type + '_tab'"
                data-toggle="tab"
              >
                {{ camelize(type) }}
              </a>
            </li>
          </ul>
        </div>
        <div class="col-md-10">
          <div class="tab-content">
            <div
              v-for="(type, wIndex) in widgetTypes"
              :id="type + '_tab'"
              :key="wIndex"
              :class="getActiveTab(type, widgetTypes[0], 'tab-pane')"
              role="tabpanel"
            >
              <div class="box-body">
                <ul
                  v-if="type == 'saved_search'"
                  class="nav nav-tabs"
                >
                  <li
                    v-for="(model, moduleIndex) in searchModules"
                    :key="moduleIndex"
                    :class="getActiveTab(model, searchModules[0], '')"
                  >
                    <a
                      :href="'#' + model"
                      data-toggle="tab"
                    >
                      {{ camelize(model) }}
                    </a>
                  </li>
                </ul>
                <div
                  v-if="type == 'saved_search'"
                  class="tab-content"
                >
                  <div
                    v-for="(model, moduleIndex) in searchModules"
                    :id="model"
                    :key="moduleIndex"
                    :class="getActiveTab(model, searchModules[0], 'tab-pane')"
                    role="tabpanel"
                  >
                    <ul class="droppable-area">
                      <template v-for="(item, elementIndex) in elements">
                        <li
                          v-if="item.type == type && item.data.model == model"
                          :key="elementIndex"
                          class="col-lg-3 col-xs-6"
                        >
                          <dashboard-widget
                            :item="item"
                            @gridItemAdd="addItem"
                          />
                        </li>
                      </template>
                    </ul>
                  </div>
                </div>
                <ul
                  v-if="type != 'saved_search'"
                  class="droppable-area"
                >
                  <template v-for="(item, itemIndex) in elements">
                    <li
                      v-if="item.type == type"
                      :key="itemIndex"
                      class="col-lg-3 col-xs-6"
                    >
                      <dashboard-widget
                        :item="item"
                        @gridItemAdd="addItem"
                      />
                    </li>
                  </template>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- .row -->
    </div> <!-- /.box -->
  </div>
</template>

<script>
import DashboardWidget from './DashboardWidget.vue'
import DashboardMixin from './DashboardMixin.js'
import VueGridLayout from 'vue-grid-layout'
import * as $ from 'jquery'

export default {
  components: {
    GridLayout: VueGridLayout.GridLayout,
    GridItem: VueGridLayout.GridItem,
    DashboardWidget: DashboardWidget
  },
  mixins: [DashboardMixin],
  data () {
    return {
      targetElement: '#dashboard-options',
      dashboard:[],
      elements: [],
      widgetTypes: [],
      searchModules: [],
      layout: [],
      index:0,
      token: null
    }
  },
  watch: {
    // save all the visible options into dashboard var
    layout: {
      handler: function () {
        var that = this;
        this.dashboard = [];

        if (this.layout.length > 0) {
          this.layout.forEach(function (element) {
            that.dashboard.push({
              i: element.i,
              h: element.h,
              w: element.w,
              x: element.x,
              y: element.y,
              id: element.data.id,
              type: element.type,
            });
          });
        }

        $(this.targetElement).val(JSON.stringify(this.dashboard));
      },
      deep: true
    }
  },
  mounted () {
    let gridLayout = this.$el.attributes['grid-layout'].value
    if (typeof gridLayout !== undefined ) {
      this.layout = JSON.parse(gridLayout)
    }

    this.index = this.layout.length
    this.getGridElements()
  },
  beforeUpdate () {
    this.$nextTick(function () {
      this.adjustBoxesHeight()
    });
  },
  methods: {
    getGridElements () {
      var that = this
      let types = []
      let models = []
      $.ajax({
        type: 'get',
        dataType: 'json',
        url: '/search/widgets/index',
      }).then(function (response) {
        that.elements = response

        that.elements.forEach(function (element) {
            if (!types.includes(element.type)) {
                types.push(element.type)
            }

            if (element.type == 'saved_search' && !models.includes(element.data.model)) {
                models.push(element.data.model)
            }
        });

        that.widgetTypes = types.sort();
        that.searchModules = models.sort();
      });
    },
    addItem (item) {
      let element = {
        x: 0,
        y: this.getLastRow(),
        w: 6,
        h: 2,
        i: this.getUniqueId(),
        draggable: true,
      };

      let layoutElement = Object.assign({}, element, item);
      this.layout.push(layoutElement);
      this.index = this.layout.length;
    },
    removeItem (item) {
      this.layout.splice(this.layout.indexOf(item), 1);
      this.index = this.layout.index;
    },
    getUniqueId () {
      return '_' + Math.random().toString(36).substr(2, 9);
    },
    getLastRow () {
      let last = 0;

      if (!this.layout.length) {
        return last;
      }

      this.layout.forEach(function (element) {
        if (element.y >= last) {
          last = element.y;
        }
      });
      last++;

      return last;
    },
    camelize (str) {
      str = str.replace(/(?:\_|\W)(.)/g, function (match, chr) {
        return ' ' + chr.toUpperCase();
      });

      return str.charAt(0).toUpperCase() + str.slice(1);
    },
    getActiveTab (type, defaultValue, cssClass) {
      return cssClass + ' ' + (type == defaultValue ? 'active' : '');
    },
    adjustBoxesHeight () {
      var maxHeight = Math.max.apply(null, $("div.available-widget").map(function () {
        return $(this).height();
      }).get());

      $("div.available-widget").height(maxHeight + 5);
    }
  }
}
</script>
