<template>
  <div class="app-container documentation-container">
    <el-form>
      <el-form-item>
        <el-button type="primary" @click="opendropZone()">
          Create a new Bill of Quantity
        </el-button>
        <!-- <el-button @click="spreadsheetExport()">
          Export
        </el-button> -->
      </el-form-item>
    </el-form>

    <el-form ref="form" :model="form" label-width="100px">

      <el-form-item>
        <el-col :span="5">
          <el-form-item label="Spreadsheet" />
          <el-select v-model="form.region" placeholder="Spreadsheet" @change="onChangeLevel1(form.region,1)">
            <el-option
              v-for="(object, index) in spreadsheetlist"
              :key="index"
              :value="{ id: object.id, text: object.title }"
              :label="object.title"
            />
          </el-select>
        </el-col>

        <el-col :span="5">
          <el-form-item label="Level 1" />
          <el-select v-model="form.level1" placeholder="Level 1" @change="onChangeLevel2(form.level1,2)">
            <el-option
              v-for="(object, index) in spreadsheetlevel1"
              :key="index"
              :value="object.id"
              :label="object.title"
            />
          </el-select>
        </el-col>

        <!-- <el-col :span="3">
          <el-form-item label="Level 2" />
          <el-select v-model="form.level2" placeholder="Level 2" @change="onChangeLevel3(form.level2,3)">
            <el-option
              v-for="(object, index) in spreadsheetlevel2"
              :key="index"
              :value="object.id"
              :label="object.title"
            />
          </el-select>
        </el-col>-->

        <!--  <el-col :span="3">
          <el-form-item label="Level 3" />
          <el-select v-model="form.level3" placeholder="Level 3">
            <el-option
              v-for="(object, index) in spreadsheetlevel3"
              :key="index"
              :value="object.id"
              :label="object.title"
            />
          </el-select>
        </el-col>-->

      </el-form-item>

    </el-form>

    <div id="spreadsheet" class="dhx_sample-container__widget" style="max-width: 100%;height:70vh;" />

    <div class="filter-container" style="padding-bottom: 4%;">
      <el-form ref="dataForm" :model="temp" label-position="left" label-width="150px" style="width: auto; margin-left:0px;"> <!--:rules="rules" -->
        <el-col :span="4" style="position: relative;">
          <el-form-item :label="$t('filterform.date')">
            <el-date-picker v-model="temp.timestamp" format="yyyy-MM-dd" type="date" placeholder="Please pick a date" @change="getDate" />
          </el-form-item>
        </el-col>
        <el-col :span="18" style="padding-left: 14%;position: relative;">
          <div style="border: none; font-weight: bold; margin-top: 1%; color: #46A6FF;">{{ selected_item_name }}</div>
          <!-- <el-form-item prop="quantity" style="border: none; font-weight: bold; margin-left: 5px; color: #46A6FF;">
            <el-input v-model="selected_item_name" style="border: none; font-weight: bold; margin-left: 5px; color: #46A6FF;" class="input_label" />
          </el-form-item> -->
        </el-col>
        <span><div>&nbsp;</div></span>
      </el-form>
    </div>

    <div class="tab-container">
      <el-tabs v-model="activeName" @tab-click="handleClick">
        <el-tab-pane label="Work Done" name="WorkDone">
          <keep-alive>
            <work-done ref="WorkDoneComponent" :selected_cell="selected_cell" @refresh="getSummation(spreadsheet)" />
          </keep-alive>
        </el-tab-pane>
        <el-tab-pane label="Material Used" name="Material Used">
          <keep-alive>
            <material-used ref="MaterialsUsedComponent" :selected_cell="selected_cell" @refresh="getSummation(spreadsheet)" />
          </keep-alive>
        </el-tab-pane>
        <el-tab-pane label="Labour" name="Labour">
          <keep-alive>
            <labour ref="LabourComponent" :selected_cell="selected_cell" @refresh="getSummation(spreadsheet)" />
          </keep-alive>
        </el-tab-pane>
        <el-tab-pane label="Equipment" name="Equipment">
          <keep-alive>
            <equipment ref="EquipmentComponent" :selected_cell="selected_cell" @refresh="getSummation(spreadsheet)" />
          </keep-alive>
        </el-tab-pane>
      </el-tabs>

      <!--<el-tabs v-model="activeName" style="margin-top:15px;" type="border-card">
        <el-tab-pane v-for="item in tabMapOptions" :key="item.key" :label="item.label" :name="item.key">
          <keep-alive>
            <tab-pane v-if="activeName==item.key" :type="item.key" @create="showCreatedTimes" />
          </keep-alive>
        </el-tab-pane>
      </el-tabs>-->
    </div>

    <div v-show="show_section">
      <Transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-container">
              <div class="editor-container">

                <el-form ref="projectForm"> <!--:rules="rules" -->

                  <el-row>
                    <el-col :span="12">
                      <el-form-item>
                        <el-select v-model="form.project" placeholder="Project" @change="onChangProject">
                          <el-option
                            v-for="(object, index) in project_list"
                            :key="index"
                            :value="{ id: object.id, text: object.projectname }"
                            :label="object.projectname"
                          />
                        </el-select>
                      </el-form-item>
                    </el-col>
                    <el-col :span="12">
                      <span class="link-type" @click="openProjects()">
                        <div class="icon-item" style="margin-top: 10px;margin-left: 2px;">
                          <i class="el-icon-circle-plus" />
                          <span>New Project</span>
                        </div>
                      </span>
                    </el-col>
                  </el-row>
                  <el-row>
                    <el-col :span="24">&nbsp;</el-col>
                  </el-row>
                  <el-row>
                    <el-col :span="12">
                      <el-form-item>
                        <el-select v-model="form.building" placeholder="Building">
                          <el-option
                            v-for="(object, index) in building_list"
                            :key="index"
                            :value="{ id: object.id, text: object.building_name }"
                            :label="object.building_name"
                          />
                        </el-select>
                      </el-form-item>
                    </el-col>
                    <el-col :span="12">
                      <span class="link-type" @click="openBuilding()">
                        <div class="icon-item" style="margin-top: 10px;margin-left: 2px;">
                          <i class="el-icon-circle-plus" />
                          <span>New Building</span>
                        </div>
                      </span>
                    </el-col>
                  </el-row>
                  <el-row>
                    <el-col :span="24">&nbsp;</el-col>
                  </el-row>

                </el-form>
                <dropzone id="myVueDropzone" ref="myVueDropzone" url="api/upload-bill-of-quantity" @dropzone-removedFile="dropzoneR" @dropzone-success="dropzoneS">
                  <div class="dz-message" data-dz-message><span>Upload or Drop your Excel file</span></div>
                </dropzone>
              </div>
              <div class="modal-footer">
                <slot name="footer">
                  <el-button
                    type="primary"
                    class="modal-default-button"
                    @click="closeUpload()"
                  >Close pop up</el-button>
                </slot>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </div>

    <div v-show="show_building_section">
      <Transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-container">
              <div class="editor-container">

                <el-form ref="buildingForm"> <!--:rules="rules" -->
                  <el-input v-model="form.buildingid" type="hidden" />

                  <el-row>
                    <el-col :span="24">
                      <el-form-item>
                        <el-input v-model="form.projectbuilding" readonly />
                      </el-form-item>
                    </el-col>
                  </el-row>
                  <el-row>
                    <el-col :span="24">&nbsp;</el-col>
                  </el-row>
                  <el-row>
                    <el-col :span="12">
                      <span class="link-type" @click="openBuilding()">
                        <div class="icon-item" style="margin-top: 10px;margin-left: 2px;">
                          <span>Building name</span>
                        </div>
                      </span>
                    </el-col>
                    <el-col :span="12">
                      <el-input v-model="form.buildingname" />
                    </el-col>
                  </el-row>
                  <el-row>
                    <el-col :span="24">&nbsp;</el-col>
                  </el-row>

                </el-form>
              </div>
              <div class="modal-footer">
                <slot name="footer">
                  <el-row>
                    <el-col :span="10">
                      <el-button
                        type="primary"
                        class="modal-default-button" style="width: 100px;"
                        @click="createBuilding()"
                      >Save</el-button>
                    </el-col>
                    <el-col :span="2">&nbsp;</el-col>
                    <el-col :span="10">
                      <el-button
                        type="primary"
                        class="modal-default-button" style="width: 100px;"
                        @click="closeBuildingPopup()"
                      >Close</el-button></el-col>
                  </el-row>
                </slot>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </div>

</template>
<script type="text/javascript" src="/static/codebase/spreadsheet_pro.js"></script>
<link rel="stylesheet" href="/static/codebase/spreadsheet.css?v=4.3.0" />
<link rel="stylesheet" href="/static/common/index.css?v=4.3.0" />
<script>
import Dropzone from '@/components/Dropzone';
import Equipment from './components/Equipment';
import MaterialUsed from './components/MaterialUsed';
import WorkDone from './components/WorkDone';
import Labour from './components/Labour';
import { fetchQuantityDone ,fetchSpreadSheetList ,fetchSpreadSheetLevel,fetchSpreadSheetLevelOne,fetchCellsFiltered,updateBQTotals} from '@/api/bill-of-quantities';
import { fetchProjectlist } from '@/api/project-management';
import { createNewBuilding, fetchBuildingList } from '@/api/buildings';
import $ from 'jquery';
var token,excel_path,spreadsheet,selected_cell,active_spreadsheet, selected_rate, select_unit,boq_cell_id,active_cell_number,selected_item_name,is_loaded_xhttp,serialized_data;
var vm = null;
var currdate = new Date();
var current_date = currdate.getFullYear() + '-' +	(currdate.getMonth() +1) + '-' + currdate.getDate();

export default {
  name: 'BillofQuantity',
  components: { Dropzone , Equipment , MaterialUsed , WorkDone , Labour },
  filters: {
    props: {
       show: Boolean
    },
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger',
      };
      return statusMap[status];
    },
  },
  data() {
    return {
      serialized_data,
      is_loaded_xhttp : 1,
      selected_item_name,
      active_cell_number,
      boq_cell_id,
      selected_cell,
      active_spreadsheet,
      selected_rate,
      selected_date : current_date,
      select_unit,
      rules: {
        type: [{ required: true, message: 'type is required', trigger: 'change' }],
        timestamp: [{ type: 'date', required: true, message: 'timestamp is required', trigger: 'change' }],
        title: [{ required: true, message: 'title is required', trigger: 'blur' }],
      },
      tabMapOptions: [
        { label: 'Work Done', key: 'WorkDone' },
        { label: 'Material Used', key: 'MaterialUsed' },
        { label: 'Labour', key: 'Labour' },
        { label: 'Equipment', key: 'Equipment' }
      ],
      activeName: 'WorkDone',
      createdTimes: 0,
      show_section: false,
      show_building_section: false,
      articleList: [
      ],
      list: null,
      project_list: null,
      building_list: null,
      spreadsheetlist: null,
      spreadsheetlevel1:null,
      spreadsheetlevel2:null,
      spreadsheetlevel3:null,
      token,
      excel_path,
      spreadsheet,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 10,
      },
      listQueryLevel1: {
        selected: 1,
        level:0
      },
      listQueryLevel2: {
        selected_text: "",
        selected: 1,
        level:0
      },
      listQueryLevel3: {
        selected: 1,
        level:0
      },
      postQuery: {
        cell: 1
      },
      temp: {
        timestamp: new Date()
      },
      rules: {
        type: [{ required: true, message: 'type is required', trigger: 'change' }],
        timestamp: [{ type: 'date', required: true, message: 'timestamp is required', trigger: 'change' }],
        title: [{ required: true, message: 'title is required', trigger: 'blur' }],
      },
      form: {
        projectid:'',
        buildingid:'',
        project:'',
        projectbuilding:'',
        building:'',
        buildingname:'',
        name: '',
        region: '',
        date1: '',
        date2: '',
        delivery: false,
        type: [],
        resource: '',
        desc: '',
        level1: '',
        level2: '',
        level3: '',
      },
    };
  },
  created() {
    this.getSpreadsheet();
    this.getProjectList();
    this.show_section = false;
    this.show_building_section = false;
  },
  mounted(){
    const vm = this;
    this.token = document.head.querySelector('meta[name="csrf-token"]').content;
    const ctoken = this.token ;
    this.$refs.myVueDropzone.dropzone.on("addedfile", function(file, xhr, data) {
        return false;
      });
    this.$refs.myVueDropzone.dropzone.on("sending", function(file, xhr, data) {
        data.append("_token", ctoken);
      });
    vm.spreadsheet = new dhx.Spreadsheet('spreadsheet', {
      topSplit: 1,
      importModulePath: "https://cdn.dhtmlx.com/libs/excel2json/1.0/worker.js",
      exportModulePath :"https://cdn.dhtmlx.com/libs/json2excel/1.0/worker.js",
      menu: false,
    });
    //// alert(vm.is_loaded_xhttp);

    const workdonecomponent = vm.$refs.WorkDoneComponent;
    const materialsusedcomponent = vm.$refs.MaterialsUsedComponent;
    const labourcomponent = vm.$refs.LabourComponent;
    const equipmentcomponent = vm.$refs.EquipmentComponent;

    workdonecomponent.getList(vm.boq_cell_id,vm.active_spreadsheet,vm.selected_date,vm.selected_rate, vm.select_unit);
    materialsusedcomponent.getList(vm.boq_cell_id,vm.active_spreadsheet,vm.selected_date);
    labourcomponent.getList(vm.boq_cell_id,vm.active_spreadsheet,vm.selected_date);
    equipmentcomponent.getList(vm.boq_cell_id,vm.active_spreadsheet,vm.selected_date); //dhx_grid-row   dhx_selected_row
    $(".dhx_grid-content").click(function(){
            workdonecomponent.getList(vm.boq_cell_id,vm.active_spreadsheet,vm.selected_date,vm.selected_rate, vm.select_unit);
            materialsusedcomponent.getList(vm.boq_cell_id,vm.active_spreadsheet,vm.selected_date);
            labourcomponent.getList(vm.boq_cell_id,vm.active_spreadsheet,vm.selected_date);
            equipmentcomponent.getList(vm.boq_cell_id,vm.active_spreadsheet,vm.selected_date);

            const arraySize =vm.serialized_data.sheets[0].rows.length;
            vm.spreadsheet.setStyle("A2:J"+arraySize, {background:"#FFFF"});
            vm.spreadsheet.setStyle("F2:J"+arraySize, {background:"#FFFF",color: "#46A6FF" , "font-weight": "bold"});

            vm.spreadsheet.setStyle("A"+vm.active_cell_number+":E"+vm.active_cell_number, {background:"#99ff99"});
            vm.spreadsheet.setStyle("F"+vm.active_cell_number+":J"+vm.active_cell_number, {background:"#99ff99",color: "#46A6FF" , "font-weight": "bold"});

    });
    var clickEvent = vm.spreadsheet.events.on("afterSelectionSet", function (obj,state) {
        var cellnumber = obj.match(/\d+/);
        vm.active_cell_number = cellnumber;
        vm.selected_cell = "F"+cellnumber;
        vm.selected_rate = vm.spreadsheet.getValue("E"+cellnumber);
        vm.select_unit = vm.spreadsheet.getValue("C"+cellnumber);
        vm.boq_cell_id = Number(vm.spreadsheet.getValue("Z"+cellnumber));
        vm.selected_item_name = vm.spreadsheet.getValue("B"+cellnumber);
        vm.serialized_data = vm.spreadsheet.serialize();
      });
  },
  methods: {
    dropzoneS(file,data,obj) {
      this.show_section = false;
      this.$message({ message: 'Upload success', type: 'success' });
      this.excel_path = "/uploads/bill-of-quantities/" + file.name;
      const excelspreadsheet =   this.spreadsheet;
      const workdonecomponent = this.$refs.WorkDoneComponent;
      const materialsusedcomponent = this.$refs.MaterialsUsedComponent;
      const labourcomponent = this.$refs.LabourComponent;
      const equipmentcomponent = this.$refs.EquipmentComponent;
      const vm = this;
      this.getSpreadsheet();
    },
    handleClick(tab, event) {
        //console.log(tab, event);
      },
    async getBuildingList(project_id) {
      const { data } = await fetchBuildingList({project_id:project_id});
      this.building_list = data.items;
    },
    onChangProject(project_object){
      this.form.projectid = project_object.id;
      this.getBuildingList(project_object.id);
    },
    createBuilding(){
      createNewBuilding(this.form).then((response) => {
            this.$notify({
              title: 'Success',
              message: 'Created successfully',
              type: 'success',
              duration: 2000,
            });
            this.show_building_section = false;
            this.form.buildingid = response.id;
            this.getBuildingList(this.form.project.id);
          });
    },
    openBuilding(){
      if(this.form.project.id){
        this.form.projectbuilding = this.form.project.text;
        this.form.projectid = this.form.project.id; //projectid //buildingid
        this.show_building_section = true;
      }else{
        this.$notify({
              title: 'Error!',
              message: 'Please select a project from the drop down!',
              type: 'error',
              duration: 2000,
            });
      }

    },
    openProjects(){
      this.$router.push({ name: 'New Project', params: {
        parameters: { },
      }});
    },
    getDate(date_selected){
     let sdate = new Date(date_selected);
     var dates = new Array();
      const vm = this;
      vm.selected_date = sdate.getFullYear() + '-' +	(sdate.getMonth() +1) + '-' + sdate.getDate();
      const workdonecomponent = this.$refs.WorkDoneComponent;
      const materialsusedcomponent = this.$refs.MaterialsUsedComponent;
      const labourcomponent = this.$refs.LabourComponent;
      const equipmentcomponent = this.$refs.EquipmentComponent;
      workdonecomponent.getList(vm.boq_cell_id,vm.active_spreadsheet,vm.selected_date,vm.selected_rate, vm.select_unit);
      materialsusedcomponent.getList(vm.boq_cell_id,vm.active_spreadsheet,vm.selected_date);
      labourcomponent.getList(vm.boq_cell_id,vm.active_spreadsheet,vm.selected_date);
      equipmentcomponent.getList(vm.boq_cell_id,vm.active_spreadsheet,vm.selected_date);
    },
    async getProjectList() {
      const { data } = await fetchProjectlist(this.listQuery);
      this.project_list = data.items;
    },
    opendropZone(){
      this.show_section = true;
    },
    dropzoneR(file) {
      this.$message({ message: 'Delete success', type: 'success' });
    },
    closeUpload(){
      this.show_section = false;
    },
    closeBuildingPopup(){
      this.show_building_section = false;
    },
    async getSummation(excelspreadsheet) {
      try {
       var  quantity_done  = 0;
       var  cost_of_work = 0;
       var  value_of_work = 0;
       var  profit_loss = 0

       const vm = this;

      this.postQuery.cell = vm.boq_cell_id;//vm.selected_cell;
      this.postQuery.spreadsheet_id = this.active_spreadsheet;
      const  data  = await fetchQuantityDone(this.postQuery);
      const resquantitydone = Object.entries(data.quantitydone);
      var activecell = vm.selected_cell;
      var excelcell = null;
      var index = vm.active_cell_number;

      var cellValueRate = Number(excelspreadsheet.getValue("E"+index));

      resquantitydone.forEach(([key, value]) => {
        var cellvalue = value.cell;
        var cellnumber = cellvalue.match(/\d+/);
        if(value.rate) cellValueRate = value.rate;
        quantity_done = value.qty;
      });
      const resworkdone = Object.entries(data.costofwork);
      resworkdone.forEach(([key, value]) => {
        var cellvalue = value.cell;
        var cellnumber = cellvalue.match(/\d+/);
        cost_of_work = value.qty;
      });
      var cellValurQuantityDone = quantity_done;
      if(typeof Number(cellValueRate) && typeof Number(cellValurQuantityDone)){
        var valueofwork = cellValueRate * cellValurQuantityDone;
        if(valueofwork){
          value_of_work = valueofwork;
            var costOfWorkValue =  cost_of_work;
            if(typeof Number(costOfWorkValue)){
              var profitLoss = valueofwork - costOfWorkValue;
              profit_loss = profitLoss;
            }
            }
          }
          return this.getSummationPost(excelspreadsheet,vm.active_spreadsheet,activecell,quantity_done,cost_of_work,value_of_work,profit_loss);
        }catch(err) {
        console.log(err);
      }
    },
    getSummationPost(excelspreadsheet,active_spreadsheet,activecell,quantity_done,cost_of_work,value_of_work,profit_loss){
          const tempData = Object.assign({},null);
          const vm = this;
          tempData.spreadsheet_id = active_spreadsheet;
          tempData.cell = this.boq_cell_id;
          tempData.quantity_done = quantity_done;
          tempData.cost_of_work = cost_of_work;
          tempData.value_of_work = value_of_work;
          tempData.profit_loss = profit_loss;
          var responseData = [];
          updateBQTotals(tempData).then((response) => {
            this.$notify({
              title: 'Success',
              message: 'Updated bq cell totals',
              type: 'success',
              duration: 1000,
            });
            responseData = response;
            var cellnumber = activecell.match(/\d+/);
            vm.is_loaded_xhttp = 0;

            excelspreadsheet.setValue("G"+ cellnumber,responseData.quantity_done);
            excelspreadsheet.setValue("H"+ cellnumber,responseData.cost_of_work);
            excelspreadsheet.setValue("I"+ cellnumber,responseData.value_of_work);
            excelspreadsheet.setValue("J"+ cellnumber,responseData.profit_loss);
            if (responseData.profit_loss < 0) {
                excelspreadsheet.setStyle("J"+cellnumber, {background:"#f70202", color: "#46A6FF"});
              }else{
                excelspreadsheet.setStyle("J"+cellnumber, {background:"#46A6FF", color: "#FFFFFF"});
              }
              excelspreadsheet.setStyle("I"+cellnumber, {background:"#dae8db", color: "#46A6FF"});
              excelspreadsheet.setStyle("H"+cellnumber, {background:"#dae8db", color: "#f70202"});
              excelspreadsheet.setStyle("G"+cellnumber, {background:"#dae8db", color: "#f70202"});
          });
          return responseData;
    },
    async getSpreadsheet() {
      const { data } = await fetchSpreadSheetList();
      this.spreadsheetlist = data.items;
    },
    async getProjectList() {
      const { data } = await fetchProjectlist(this.listQuery);
      this.project_list = data.items;
    },
    async onChangeLevel1(selected_spreadsheet,level){
      this.listQueryLevel1.selected = selected_spreadsheet.id;
      this.listQueryLevel1.level = level;

      this.active_spreadsheet = selected_spreadsheet.id;
      this.form.region = selected_spreadsheet.text;

      const { data } = await fetchSpreadSheetLevel(this.listQueryLevel1);
      this.spreadsheetlevel1 = data.items;
      const { data : level_one } = await fetchSpreadSheetLevelOne(this.listQueryLevel1);
      this.filterSpreadsheet(level_one.items);

    },
    filterSpreadsheet(recordSet){
      const vm = this;
      const spreadsheet_data = [
              { cell: "A1", value: "#", css: "headerclass" },
              { cell: "B1", value: "Description", css: "headerclass titleclass" },
              { cell: "C1", value: "Unit" , css: "headerclass" },
              { cell: "D1", value: "Quantity" , css: "headerclass" },
              { cell: "E1", value: "Rate" , css: "headerclass" },
              { cell: "F1", value: "Amount" , css: "headerclass" },
              { cell: "G1", value: "Quantity done" , css: "headerclass"},
              { cell: "H1", value: "Cost of work done" , css: "headerclass" },
              { cell: "I1", value: "Value of work done" , css: "headerclass" },
              { cell: "J1", value: "Profit/loss" , css: "headerclass" },
              { cell: "Z1", value: "" }];
      var count = 2;
      for (let resultSet in recordSet) {
        var spreadsheetrow = recordSet[resultSet];
        var bcss = "";
        if(spreadsheetrow.level == 1){
          bcss = "boldtitleclass";
        }
        var profitloss  = ""+bcss;
        if (spreadsheetrow.profit_loss < 0) {
          profitloss  = "lossClass "+bcss;
        }else{
          profitloss  = "profitClass "+bcss;
        }
        var data_sr = [
          { cell: "A"+count, value: spreadsheetrow.index, css: "aligncenterclass "+ bcss },
          { cell: "B"+count, value: spreadsheetrow.title, css: "titleclass "+bcss },
          { cell: "C"+count, value: spreadsheetrow.unit , css: "aligncenterclass "+ bcss},
          { cell: "D"+count, value: spreadsheetrow.quantity, css: "aligncenterclass "+ bcss },
          { cell: "E"+count, value: spreadsheetrow.rate, css: "alignrightclass "+ bcss },
          { cell: "F"+count, value: spreadsheetrow.amount, css: "linkclass "+ bcss},
          { cell: "G"+count, value: spreadsheetrow.quantity_done, css: "colIclass "+bcss},
          { cell: "H"+count, value: spreadsheetrow.cost_of_work, css: "colIclass "+bcss},
          { cell: "I"+count, value: spreadsheetrow.value_of_work, css: "colIclass "+bcss},
          { cell: "J"+count, value: spreadsheetrow.profit_loss, css: profitloss},
          { cell: "Z"+count, value: spreadsheetrow.bc_id, css: "readOnlyClass" }
        ];
       spreadsheet_data.push(...data_sr);
       count++;
      }
      const resultData = {
        styles: {
          titleclass:{
            width:"900px"
          },
          readOnlyClass: {
            color: "#FFFFFF"
              },
            lossClass: {
                  background: "#f70202",
                  color: "#46A6FF",
                  "text-align": "right",
                  "font-weight": "bold"
              },
             profitClass: {
                  color: "#46A6FF",
                  "text-align": "right",
                  "font-weight": "bold"
              },
              colIclass: {
                  background: "#FFFFFF",
                  color: "#46A6FF",
                  "text-align": "right",
                  "font-weight": "bold"
              },
              headerclass: {
                  background: "#42B983",
                  color: "#FFFFFF"
              },
              linkclass: {
                  background: "#DAE8DB",
                  color: "#46A6FF",
                  "font-weight": "bold",
                  "text-align": "right"
              },
              aligncenterclass: {
                  "text-align": "center"
              },
              alignrightclass: {
                  "text-align": "right"
              },
              boldtitleclass: {
                  "font-weight": "bold",
                  background: "#DCDCDC",
                  color: "#46A6FF"
              }
          },
      data:spreadsheet_data,
      }
      this.spreadsheet.parse(resultData);
      this.spreadsheet.fitColumn("B2");
      vm.is_loaded_xhttp = 1;
    },
    async onChangeLevel2(selected_spreadsheet,level){
      this.listQueryLevel2.selected = selected_spreadsheet;
      this.listQueryLevel2.level = level;
      const { data } = await fetchSpreadSheetLevel(this.listQueryLevel2);
      this.spreadsheetlevel2 = data.items;
      var vm = this;
      this.filterSpreadsheet(data.items);
    },
    async onChangeLevel3(selected_spreadsheet,level){
      this.listQueryLevel3.selected = selected_spreadsheet;
      this.listQueryLevel3.level = level;
      const { data } = await fetchSpreadSheetLevel(this.listQueryLevel3);
      this.spreadsheetlevel3 = data.items;
      this.filterSpreadsheet(data.items);
    }

  }
};
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
.documentation-container {
  margin: 50px;
  .document-btn {
    float: left;
    margin-left: 50px;
    display: block;
    cursor: pointer;
    background: black;
    color: white;
    height: 60px;
    width: 200px;
    line-height: 60px;
    font-size: 20px;
    text-align: center;
  }
}
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: table;
  transition: opacity 0.3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.modal-container {
  width: 300px;
  margin: 0px auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.3s ease;
}

.modal-header h3 {
  margin-top: 0;
  color: #42b983;
}

.modal-body {
  margin: 20px 0;
}

.modal-default-button {
  float: right;
}

/*
 * The following styles are auto-applied to elements with
 * transition="modal" when their visibility is toggled
 * by Vue.js.
 *
 * You can easily play with the modal transition by editing
 * these styles.
 */

.modal-enter-from {
  opacity: 0;
}

.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal-container,
.modal-leave-to .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>
