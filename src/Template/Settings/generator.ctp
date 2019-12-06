<?php
/**
 * The structure of the HTML is the some of /settings, but rendered with jQuery.
 * To review maybe in VUE.js
 *
 * to fix/add :
 * - button to delete fields
 * - button to edit fields (maybe)
 *
 */
use Cake\Core\Configure;

echo $this->Html->css('Qobo/Utils./plugins/datatables/css/dataTables.bootstrap.min', ['block' => 'css']);
echo $this->Html->script(
    [
        'Qobo/Utils./plugins/datatables/datatables.min',
        'Qobo/Utils./plugins/datatables/js/dataTables.bootstrap.min'
    ],
    ['block' => 'scriptBottom']
);

$this->Html->scriptStart(array('block' => 'scriptBottom', 'inline' => false)); ?>

$(document).ready(function(){
    // settings.php array in json
    let data = (<?= json_encode($data) ?>)
    // Tabs
    $.each(data,function(tab, cols){
        genTab(tab)
        // Cols
        $.each(cols,function(col,sections){
            genColumn(tab,col)
            // Sections
            $.each(sections,function(section,field){
                genSection(tab,col,section)
                // Fields
                $.each(field,function(key,value){
                    genField(tab,col,section,key,value)
                })
                addFieldButton(tab,col,section)
            })
            addSectionButton(tab,col)
        })
        addColButtun(tab)
    })

    // Active the first tab and column
    $('#list_tabs li:nth-child(2)').addClass('active');
    $('.tab-pane:nth-child(1)').addClass('active');

    // Add a new Tab : it is composed by the link on the top, and the panel in the bottom
    // tab : new tab name
    function genTab(tab){
        let idTab = tab.replace(/ |_/g,"_")
        let new_tab = `<li><a href="#tab`+ idTab  +`" data-tab="`+ idTab  +`" data-toggle="tab" aria-expanded="true">`+ tab +`</a></li>`
        let new_tab_panel =`<div class="tab-pane" id="tab`+ idTab  +`">
                                <div class="row">
                                    <div class="col-md-3">
                                        <ul id="col_`+ idTab  +`" class="nav nav-pills nav-stacked">
                                        </ul>
                                    </div>
                                    <div class="tab-content col-md-9" id="section_`+ idTab  +`">
                                    </div>
                                </div>
                            </div>`

        $('#list_tabs').append(new_tab)
        $('#list_tabs_pane').append(new_tab_panel)
    }

    // Add a new column in the select tab : it is composed by a link in the left and a panel in the right
    // tab : select the tab where to add the new column
    // col : name of the new column
    function genColumn(tab,col){
        let idCol = tab.replace(/ |_/g,"_") +"-"+ col.replace(/ |_/g,"_")
        let new_col_link = `<li><a href="#`+ idCol +`" data-toggle="tab">`+ col +`</a></li>`
        $('#col_'+ tab.replace(/ |_/g,"_")).append(new_col_link)

        let new_col_tab = `<div class="tab-pane" id="`+ idCol +`"></div>`
        $('#section_'+ tab.replace(/ |_/g,"_")).append(new_col_tab)
    }

    // Add new section on selected tab -> column
    // tab,col : select the tab and column where to add the section
    // section : name of the new section
    function genSection(tab,col,section){
        let idSection = tab.replace(/ |_/g,"_") +"-"+ col.replace(/ |_/g,"_") +"-"+ section.replace(/ |_/g,"_")
        let new_section = `<div class="box box-primary" id="`+ idSection +`">
                                   <div class="box-header">
                                       <h3 class="box-title">`+ section +`</h3>
                                   </div>
                           </div>`
        $('#'+ tab.replace(/ |_/g,"_") +"-"+ col.replace(/ |_/g,"_") ).append(new_section)
    }

    // Add new field on selected tab -> column -> section
    // tab,col,section : select the tab,column and section where to add the field
    // field : name of the new field
    // value : array with the alias,type,scope,help
    function genField(tab,col,section,name,value){
            let idField = tab.replace(/ |_/g,"_") +"-"+ col.replace(/ |_/g,"_") +"-"+ section.replace(/ |_/g,"_")
            let new_field = `<div class="box-body">
                                <div class="form-group input text">
                                    <label for="settings-theme-title">`+ name +`</label>
                                    Alias :
                                    <label for="settings-theme-title" alias='`+ value.alias +`'>`+ value.alias +`</label>
                                </div>
                            </div>`
            $('#'+ idField ).append(new_field)
    }

    // +++++++++++++++++++++++++++++++++++++++++++++++++++++
    // Adding the input for new columns, sections and fields
    // Input for the columns
    function addColButtun(tab){
        let addCol = `<li>
                        <form class="addCol">
                            <input type="text" placeholder="Add Column">
                            <button type="submit" class="btn btn-primary">
                                <i class="menu-icon fa fa-plus-circle"></i>
                            </button>
                        </form>
                      </li>`
        $('#col_'+ tab.replace(/ |_/g,"_")).append(addCol)
    }

    // Input for the section
    function addSectionButton(tab,col){
        let addSection = `<div class="box-body">
                            <form class="addSection">
                                <input type="text" placeholder="Add Section">
                                <button type="submit" class="btn btn-primary">
                                    <i class="menu-icon fa fa-plus-circle"></i>
                                </button>
                            </form>
                          </div>`
        $('#'+ tab.replace(/ |_/g,"_") +"-"+ col.replace(/ |_/g,"_")).append(addSection)
    }

    // Input for the field
    function addFieldButton(tab,col,section){
        let addField = `<div class="box-body">
                            <form class="addField">
                                <button type="submit" class="btn btn-primary">
                                    Add fields
                                </button>
                            </form>
                          </div>`
        $('#'+ tab.replace(/ |_/g,"_") +"-"+ col.replace(/ |_/g,"_") +"-"+ section.replace(/ |_/g,"_")).append(addField)
    }

    function genNewColumn(tab,col){
        genColumn(tab,col)
        addSectionButton(tab,col)
    }

    function genNewSection(tab,col,section){
        genSection(tab,col,section)
        addFieldButton(tab,col,section)
    }
    // +++++++++++++++++++++++++++++++++++++++++++++++++++++

    // Tab button submit
    $("#addTab").on('submit',function(event){
        event.preventDefault()
        genTab($("#addTab input:text").val())
        addColButtun($("#addTab input:text").val())
        $("#addTab input:text").val('')
    })

    // Column button submit
    $(document).on('submit','.addCol',function(event){
        event.preventDefault()
        let tab = $(this).parents().parents().attr('id')
        let col = $(this).find("input:text").val()
        genNewColumn(tab.replace('col_',''),col)
        $(this).find("input:text").val('')
    })

    // Section button submit
    $(document).on('submit','.addSection',function(event){
        event.preventDefault()
        let id = $(this).parents().parents().attr('id').split('-')
        let section = $(this).find("input:text").val()
        genNewSection(id[0],id[1],section)
        $(this).find("input:text").val('')
    })

    // Field button submit
    $(document).on('submit','.addField',function(event){
        event.preventDefault()
        // it closes all the other "search for the new field"
        closeSearch()
        // Change the name and the action of the some button
        $(this).find('button').text('Add the selected field')
        $(this).removeClass('addField').addClass('AddSelectedFields')
        let id = $(this).parents().parents().attr('id')
        // Add the "search for the new fields"
        let search = `<div class="box box-primary">
                          <div class="box-body">
                              <table id='dataSettings' class="table table-hover table-condensed table-vertical-align table-datatable">
                                  <thead>
                                  <tr>
                                      <th><?= __d('Qobo/ProjectTemplateCakephp', 'alias') ?></th>
                                      <th><?= __d('Qobo/ProjectTemplateCakephp', 'name') ?></th>
                                      <th><?= __d('Qobo/ProjectTemplateCakephp', 'type') ?></th>
                                      <th><?= __d('Qobo/ProjectTemplateCakephp', 'help') ?></th>
                                      <th><?= __d('Qobo/ProjectTemplateCakephp', 'scope') ?></th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <?php foreach ($alldata as $key => $value) :
                                      if( is_array($value) || is_object($value)):
                                          continue;
                                      endif;
                                  ?>
                                      <tr>
                                          <td class="alias"><?= $key ?></td>
                                          <td class="name"><input type="text" placeholder="write the name" value="<?= str_replace('.',' ',$key) ?>" required ></td>
                                          <td class="type"><?= gettype($value) ?></td>
                                          <td class="help"><input type="text" placeholder="write the help here"></td>
                                          <td class="scope">
                                             <?php
                                                $s = '';
                                                foreach($scope as $key){
                                                    $s = $s . $key. ',';
                                                }
                                             ?>
                                           <input type="text" value="<?= substr($s, 0, -1) ?>">
                                          </td>
                                      </tr>
                                  <?php endforeach; ?>
                                  </tbody>
                              </table>
                          </div>
                      </div>`
        $('#' + id).append(search)
        $("#dataSettings").DataTable({
            stateSave:true,
            paging:true,
            searching:true,
            select: {
                        selector:'td:first-child',
                        style: 'multi',
                    }
            })
        $.each($('.tab-content').find("[alias]"),function(index,value){
            removeTdData($(value).text())
        })
    })

    // Remove from dataSettings table, the already chosen records
    function removeTdData(alias){
         $("#dataSettings").DataTable().columns('alias').search(alias).row().remove().draw();
    }

    // Get all the selected fields and send them to the array(data)
    $(document).on('submit','.AddSelectedFields',function(event){
            event.preventDefault()
            let data = $('#dataSettings .selected')
            let path = $(this).parents().parents().attr('id').replace(/_/g," ").split("-")
            $.each(data,function(index,value){
                value = $(value)[0]

                let mydata = {
                    'alias' : $(value).find('.alias').text(),
                    'type' :  $(value).find('.type').text(),
                    'links' : '',
                    'help' :  $(value).find('.help input').val(),
                    'scope' : $(value).find('.scope input').val().split(","),
                }
                let fieldName = $(value).find('.name input').val()
                addToArray(path,mydata,fieldName)
                genField(path[0],path[1],path[2],fieldName,mydata)
            })
            closeSearch()
        })

    // Merge the new data with the main one
    // path   : tab -> col -> section
    // field  : field name
    // mydaya : object with alias, type, help, scope
    function addToArray(path,mydata,field){
        let newdata = JSON.parse('{"'+path[0]+'":{"'+path[1]+'":{"'+path[2]+'":{"'+field+'": ' + JSON.stringify(mydata) + ' }}}}')
        data = $.extend(true,data,newdata)
    }

    // find and remove any table for the "search for the new field"
    function closeSearch(){
        $("#dataSettings").closest('.box-primary').remove()
        $('.AddSelectedFields').removeClass('AddSelectedFields').addClass('addField')
        $('.addField').find('button').text('Add fields')
    }

    // return the php value for Setting[] in settings.php
    $("#render").click(function(event){
        event.preventDefault()
        let token = JSON.parse('{ "_csrfToken" : "' + $('input[name="_csrfToken"]').attr('value') + '"}')
        data = $.extend(true,data,token)
        $.ajax({
            url: "/settings/generator",
            type : 'post',
            contentType: 'application/json',
            dataType : 'text',
            data : JSON.stringify(data),
            success: function(result){
                result = 'return [\n\'Settings\' => \n ' + result.replace(/array\ \(/gi, '[').replace(/\),\n/gi, '],\n').slice(0, -1) + ' ],\n];';

                $("#preArray").html('<pre>'+ result +'<pre>');
            }
        });
    });
});

<?php $this->Html->scriptEnd(); ?>
<style type="text/css">
   table.dataTable tbody tr.selected {
        color: blue;
        background-color: #c0c0c0;
    }
</style>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul id='list_tabs' class="nav nav-tabs">
                 <li class="box-header pull-right">
                    <form id="addTab">
                        <input type="text"  placeholder="Add Tab">
                        <button type="submit" class="btn btn-primary">
                            <i class="menu-icon fa fa-plus-circle"></i>
                        </button>
                    </form>
                 </li>
                </ul>
                <div id='list_tabs_pane' class="tab-content"></div>
            </div>
            <?= $this->Form->create(); ?>
            <?= $this->Form->button(__d('Qobo/ProjectTemplateCakephp', 'Submit'), ['class' => 'btn btn-primary','value' => 'submit', 'id' => 'render']);?>
            <?= $this->Form->end(); ?>
            <div id='preArray'></div>
        </div>
    </div>
</section>
