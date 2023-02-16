<template>
  <div class="app-container documentation-container">
    <el-row>
      <el-card class="box-card">
        <div slot="header" class="clearfix">
          <span>Works Summary</span>
        </div>
        <div style="margin-bottom:50px;">

          <el-form ref="form" label-width="200px">

            <el-row>
              <el-col :span="12">
                <el-form-item label="Start date">
                  <el-date-picker v-model="temp.starttimestamp" format="yyyy-MM-dd" type="date" placeholder="Pick a date" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="End date">
                  <el-date-picker v-model="temp.endtimestamp" format="yyyy-MM-dd" type="date" placeholder="Pick a date" />
                </el-form-item>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="12">
                <el-form-item label="Select spreadsheet">
                  <el-select v-model="form.spreadsheet" placeholder="please select a spreadsheet" @change="onChangeSpreadsheet(form.spreadsheet)">
                    <el-option
                      v-for="(object, index) in spreadsheetlist"
                      :key="index"
                      :value="{ id: object.id, text: object.title }"
                      :label="object.title"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="Element">
                  <el-select v-model="form.element" placeholder="please select an element" @change="onChangeElement(form.element)">
                    <el-option
                      v-for="(object, index) in spreadsheetelement"
                      :key="index"
                      :value="{ id: object.id, text: object.title }"
                      :label="object.title"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
            </el-row>

            <el-row>
              <el-col :span="2"><span>&nbsp;</span></el-col>
              <el-col :span="3">
                <el-form-item>
                  <el-button type="primary" @click="onGenerateSpreadsheet(active_spreadsheet)">
                    Fetch Excel
                  </el-button>
                </el-form-item></el-col>
              <el-col :span="4">
                <el-form-item>
                  <el-button type="primary" @click="onDownloadSpreadsheet(active_spreadsheet)">
                    Download excel worksheet
                  </el-button>
                </el-form-item>
              </el-col>
              <el-col :span="4">
                <el-form-item>
                  <el-button type="primary" @click="onPDFSummary(active_spreadsheet)">
                    PDF Summary
                  </el-button>
                </el-form-item>
              </el-col>
            </el-row>

          </el-form>
          <div id="spreadsheet" class="dhx_sample-container__widget" style="max-width: 100%;height:70vh;" />
        </div>
      </el-card>
    </el-row>
  </div>
</template>
<script type="application/javascript" src="/static/codebase/spreadsheet.js?v=4.3.0"></script>
<link rel="stylesheet" href="/static/codebase/spreadsheet.css?v=4.3.0" />
<link rel="stylesheet" href="/static/common/index.css?v=4.3.0" />
<script src="/static/common/data.js?v=4.3.0"></script>
<script>
// import PanThumb from '@/components/PanThumb';
// import MdInput from '@/components/MDinput';
// import Mallki from '@/components/TextHoverEffect/Mallki';
// import DropdownMenu from '@/components/Share/DropdownMenu';
import waves from '@/directive/waves/index.js'; // v-wave directive
import { fetchSpreadSheetList, generateWorkExcel , fetchSpreadSheetLevel , generatePDFSummary} from '@/api/bill-of-quantities';
var excel_path,spreadsheet,active_spreadsheet,active_element;
export default {
  name: 'ComponentMixinDemo',
  components: {
    // PanThumb,
    // MdInput,
    // Mallki,
    // DropdownMenu,
  },
  directives: {
    waves,
  },
  data() {
    const validate = (rule, value, callback) => {
      if (value.length !== 6) {
        callback(new Error('Please enter 6 chars'));
      } else {
        callback();
      }
    };
    return {
      excel_path,
      spreadsheet,
      spreadsheetlist: null,
      spreadsheetelement: null,
      active_spreadsheet,
      active_element,
      form: {
        spreadsheet: '',
        element:''
      },
      temp: {
        starttimestamp: new Date(),
        endtimestamp: new Date(),
      },
      demo: {
        title: '',
      },
      demoRules: {
        title: [{ required: true, trigger: 'change', validator: validate }],
      },
      articleList: [
        { title: 'Basic article', href: 'https://dev.to/tuandm/laravel--vuejs--laravue---a-beautiful-dashboard-for-laravel-3h11' },
        { title: 'Login permission', href: 'https://doc.laravue.dev/guide/essentials/permission.html' },
        { title: 'laravue-core', href: 'https://dev.to/tuandm/laravue-core---a-laravel-package-to-build-a-beautiful-dashboard-5aia' },
        { title: 'Github', href: 'https://github.com/tuandm/laravue' },
      ],
    };
  },computed: {
    dateFormat: function() {
     let sdate = new Date(this.temp.starttimestamp);
     var dates = new Array();
     dates[0] = sdate.getFullYear() + '-' +	(sdate.getMonth() +1) + '-' + sdate.getDate();
     let edate = new Date(this.temp.endtimestamp);
     dates[1] = edate.getFullYear() + '-' +	(edate.getMonth() +1) + '-' + edate.getDate();
      return dates;
    }
  },created() {
    this.getSpreadsheet();
  },mounted() {
    const recaptchaScript = document.createElement('script');
    recaptchaScript.setAttribute(
      'src',
      '/static/codebase/spreadsheet.js?v=4.3.0'
    );
    document.head.appendChild(recaptchaScript);
    this.spreadsheet = new dhx.Spreadsheet('spreadsheet', {
      //topSplit: 1, // the number of row to "freeze"
      importModulePath: "https://cdn.dhtmlx.com/libs/excel2json/1.0/worker.js",//https://cdn.dhtmlx.com/libs/excel2json/1.0/worker.js
      exportModulePath :"https://cdn.dhtmlx.com/libs/json2excel/1.0/worker.js",
      menu: false, // the menu is switched on, false - to switch it off
    });
  },
  methods: {
    async onPDFSummary(active_spreadsheet){
      if(active_spreadsheet){
      const res = await generatePDFSummary({selected:active_spreadsheet}).then((obj)=>{
        window.open("http://workstation.construction/"+obj.file ,'_blank');
      }).catch((err)=>{});
    }else{
           alert("Please select a spreadsheet from the drop down!");
      }
    },
    async onDownloadSpreadsheet(){
      if(this.active_spreadsheet){
          const  { message, file }  = await generateWorkExcel({id:this.active_spreadsheet,startdate:this.dateFormat[0],enddate:this.dateFormat[1], active_element : this.active_element });
          this.excel_path = file;
          window.open("http://workstation.construction/"+this.excel_path ,'_blank');
      }else{
           alert("Please select a spreadsheet from the drop down!");
      }
    },
    async onGenerateSpreadsheet(){
      if(this.active_spreadsheet){
        const  { message, file }  = await generateWorkExcel({id:this.active_spreadsheet,startdate:this.dateFormat[0],enddate:this.dateFormat[1], active_element : this.active_element });
        this.excel_path = file;
        var vm = this;
        this.spreadsheet.clear();
        this.spreadsheet.load(this.excel_path, "xlsx").then(function(){

          vm.spreadsheet.setStyle("A1", {'background':"green",'color':"white",'font-style':"normal",'font-weight':"900",'border':"solid 1px yellow",'text-decoration':"none"});
          vm.spreadsheet.setStyle("A2:I2", {'height':'20px','background':"green",'color':"white",'font-style':"normal",'font-weight':"900",'border':"solid 1px yellow",'text-decoration':"none"});
          vm.spreadsheet.setStyle("A3:I50", {'font-style':"normal",'font-weight':"normal",'border':"solid 1px yellow",'text-decoration':"none"});

          vm.spreadsheet.setStyle("Materials Used!A1", {'background':"green",'color':"white",'font-style':"normal",'font-weight':"900",'border':"solid 1px yellow",'text-decoration':"none"});
          vm.spreadsheet.setStyle("Materials Used!A2:J2", {'background':"green",'color':"white",'font-style':"normal",'font-weight':"900",'border':"solid 1px yellow",'text-decoration':"none"});
          vm.spreadsheet.setStyle("Materials Used!A3:J50", {'font-style':"normal",'font-weight':"normal",'border':"solid 1px yellow",'text-decoration':"none"});

          vm.spreadsheet.setStyle("Labour!A1", {'background':"green",'color':"white",'font-style':"normal",'font-weight':"900",'border':"solid 1px yellow",'text-decoration':"none"});
          vm.spreadsheet.setStyle("Labour!A2:I2", {'background':"green",'color':"white",'font-style':"normal",'font-weight':"900",'border':"solid 1px yellow",'text-decoration':"none"});
          vm.spreadsheet.setStyle("Labour!A3:I50", {'font-style':"normal",'font-weight':"normal",'border':"solid 1px yellow",'text-decoration':"none"});

          vm.spreadsheet.setStyle("Equipment!A1", {'background':"green",'color':"white",'font-style':"normal",'font-weight':"900",'border':"solid 1px yellow",'text-decoration':"none"});
          vm.spreadsheet.setStyle("Equipment!A2:I2", {'background':"green",'color':"white",'font-style':"normal",'font-weight':"900",'border':"solid 1px yellow",'text-decoration':"none"});
          vm.spreadsheet.setStyle("Equipment!A3:I50", {'font-style':"normal",'font-weight':"normal",'border':"solid 1px yellow",'text-decoration':"none"});

          //vm.spreadsheet.setFormat("I8","number");
          //vm.spreadsheet.setStyle("I8", {'background':"yellow",'float':"right"});

          //for (let index = 0; index < 100; index++) {
           // vm.spreadsheet.deleteRow("I"+index);
          //}
          //vm.spreadsheet.deleteRow("I4");
          //vm.spreadsheet.deleteRow("I5");

        });
      }else{
           alert("Please select a spreadsheet from the drop down!");
      }
    },
    onChangeElement(selected_object){
      this.active_element = selected_object.id;
      this.form.element = selected_object.text;
    },
    async onChangeSpreadsheet(selected_object){
      this.active_spreadsheet = selected_object.id;
      const { data } = await fetchSpreadSheetLevel({selected:this.active_spreadsheet , level : 1});
      this.spreadsheetelement = data.items;
    },
    async getSpreadsheet() {
      const { data } = await fetchSpreadSheetList();
      this.spreadsheetlist = data.items;
    },
  },
};
</script>

<style scoped>
.mixin-components-container {
  background-color: #f0f2f5;
  padding: 30px;
  min-height: calc(100vh - 84px);
}
.component-item{
  min-height: 100px;
}
</style>
