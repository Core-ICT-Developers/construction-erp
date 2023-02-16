<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="listQuery.title" :placeholder="$t('materialUsedTable.description')" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />

      <el-select v-model="listQuery.sort" style="width: 140px" class="filter-item" @change="handleFilter">
        <el-option v-for="item in sortOptions" :key="item.key" :label="item.label" :value="item.key" />
      </el-select>
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
        {{ $t('table.search') }}
      </el-button>
      <el-button class="filter-item" style="margin-left: 10px;" type="primary" icon="el-icon-edit" @click="handleCreate">
        {{ $t('table.add') }}
      </el-button>
      <el-button v-waves :loading="downloadLoading" class="filter-item" type="primary" icon="el-icon-download" @click="handleDownload">
        {{ $t('table.export') }}
      </el-button>
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
      @sort-change="sortChange"
    >
      <!-- <el-table-column :label="$t('materialUsedTable.id')" prop="id" sortable="custom" align="center" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column> -->
      <!--<el-table-column :label="$t('materialUsedTable.date')" width="150px" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.timestamp | parseTime('{y}-{m}-{d} {h}:{i}') }}</span>
        </template>
      </el-table-column>-->
      <!-- <el-table-column :label="$t('workDoneTable.id')" sortable="custom" align="center" width="80">
        <template slot-scope="{row}">
          <span>{{ row.id }}</span>
        </template>
      </el-table-column> -->

      <el-table-column :label="$t('workDoneTable.quantity')" min-width="150px">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleUpdate(row)">{{ row.quantity }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('materialUsedTable.unit')" min-width="150px">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleUpdate(row)">{{ row.unit }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('materialUsedTable.rate')" min-width="150px">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleUpdate(row)">{{ row.rate }}</span>
        </template>
      </el-table-column>

      <el-table-column :label="$t('materialUsedTable.amount')" min-width="150px">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleUpdate(row)">{{ row.amount }}</span>
        </template>
      </el-table-column>

      <!-- <el-table-column :label="$t('materialUsedTable.done')" min-width="150px">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleUpdate(row)"><el-checkbox name="done" prop="done" /></span>
        </template>
      </el-table-column> -->

      <el-table-column :label="$t('table.actions')" align="center" width="230" class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button type="primary" size="mini" @click="handleUpdate(row)">
            {{ $t('table.edit') }}
          </el-button>
          <el-button v-if="row.status!='deleted'" size="mini" type="danger" @click="handleModifyStatus(row,'deleted')">
            {{ $t('table.delete') }}
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="170px" style="width: auto; margin-left:50px;">
        <!--<el-form-item :label="$t('materialUsedTable.date')" prop="timestamp">
          <el-date-picker v-model="temp.timestamp" type="datetime" placeholder="Please pick a date" />
        </el-form-item>-->

        <el-form-item :label="$t('workDoneTable.quantity')" prop="quantity">
          <el-input v-model="temp.quantity" />
        </el-form-item>

        <el-form-item :label="$t('materialUsedTable.unit')" prop="unit">
          <el-input id="unit" ref="unit" v-model="temp.unit" :value="selected_unit" />
        </el-form-item>

        <el-form-item :label="$t('materialUsedTable.rate')" prop="rate">
          <el-input id="rate" ref="rate" v-model="temp.rate" :value="selected_rate" />
        </el-form-item>

        <!-- <el-form-item :label="$t('materialUsedTable.done')" prop="done">
          <el-checkbox v-model="temp.done" />
        </el-form-item> -->

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">
          {{ $t('table.cancel') }}
        </el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData()">
          {{ $t('table.confirm') }}
        </el-button>
      </div>
    </el-dialog>

    <el-dialog :visible.sync="dialogPvVisible" title="Reading statistics">
      <el-table :data="pvData" border fit highlight-current-row style="width: 100%">
        <el-table-column prop="key" label="Channel" />
        <el-table-column prop="pv" label="Pv" />
      </el-table>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="dialogPvVisible = false">{{ $t('table.confirm') }}</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { fetchPv } from '@/api/article';
import { fetchWorkDoneList, createWorkDone, updateWorkDone, deleteWorkDone } from '@/api/work-done';

import waves from '@/directive/waves'; // Waves directive
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination

const calendarTypeOptions = [
  { key: 'CN', display_name: 'China' },
  { key: 'US', display_name: 'USA' },
  { key: 'JA', display_name: 'Japan' },
  { key: 'VI', display_name: 'Vietnam' },
];

// arr to obj ,such as { CN : "China", US : "USA" }
const calendarTypeKeyValue = calendarTypeOptions.reduce((acc, cur) => {
  acc[cur.key] = cur.display_name;
  return acc;
}, {});
var selected_cell, selected_spreadsheet, selected_date, selected_rate, selected_unit;
export default {
  name: 'ComplexTable',
  components: { Pagination },
  directives: { waves },
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger',
      };
      return statusMap[status];
    },
    typeFilter(type) {
      return calendarTypeKeyValue[type];
    },
  },
  data() {
    return {
      selected_cell,
      selected_spreadsheet,
      selected_date,
      selected_rate,
      selected_unit,
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
        importance: undefined,
        title: undefined,
        type: undefined,
        sort: '+id',
      },
      importanceOptions: [1, 2, 3],
      calendarTypeOptions,
      sortOptions: [{ label: 'ID Ascending', key: '+id' }, { label: 'ID Descending', key: '-id' }],
      statusOptions: ['published', 'draft', 'deleted'],
      showReviewer: false,
      temp: {
        id: undefined,
        importance: 1,
        remark: '',
        timestamp: new Date(),
        title: '',
        type: '',
        status: 'published',
        description: '',
        unit: '',
        quantity: '',
        selected_cell: '',
        selected_spreadsheet: '',
        selected_date: '',
        selected_rate: '',
        selected_unit: '',
        rate: '',
        done: '',
        amount: '',
      },
      dialogFormVisible: false,
      dialogStatus: '',
      textMap: {
        update: 'Edit',
        create: 'Create',
      },
      dialogPvVisible: false,
      pvData: [],
      rules: {
        type: [{ required: true, message: 'type is required', trigger: 'change' }],
        timestamp: [{ type: 'date', required: true, message: 'timestamp is required', trigger: 'change' }],
        title: [{ required: true, message: 'title is required', trigger: 'blur' }],
      },
      downloadLoading: false,
    };
  },
  created() {
    // this.getList();
  },
  methods: {
    async getList(selected_cell, selected_spreadsheet, selected_date, selected_rate, selected_unit) {
      try {
        this.selected_rate = selected_rate;
        this.selected_unit = selected_unit;
        this.selected_date = selected_date;
        this.selected_spreadsheet = selected_spreadsheet;
        this.listLoading = true;
        this.listQuery.selected_cell = selected_cell;
        this.listQuery.selected_spreadsheet = selected_spreadsheet;
        this.listQuery.selected_date = this.selected_date;
        this.selected_cell = selected_cell;
        const { data } = await fetchWorkDoneList(this.listQuery);
        this.list = data.items;
        this.total = data.total;

        // Just to simulate the time of the request
        this.listLoading = false;
      } catch (err) {
        console.log(err);
      }
    },
    handleFilter() {
      // this.listQuery.page = 1;
      // this.getList(this.selected_cell, this.selected_spreadsheet, this.selected_date);
    },
    handleModifyStatus(row, status) {
      const vm = this;
      deleteWorkDone(row).then((response) => {
        vm.$emit('refresh');
        this.$notify({
          title: 'Deleting',
          message: response.message,
          type: response.bool,
          duration: 2000,
        });
        const index = this.list.indexOf(row);
        vm.list.splice(index, 1);
      });
    },
    sortChange(data) {
      const { prop, order } = data;
      if (prop === 'id') {
        this.sortByID(order);
      }
    },
    sortByID(order) {
      if (order === 'ascending') {
        this.listQuery.sort = '+id';
      } else {
        this.listQuery.sort = '-id';
      }
      this.handleFilter();
    },
    resetTemp() {
      this.temp = {
        id: undefined,
        importance: 1,
        remark: '',
        timestamp: new Date(),
        title: '',
        status: 'published',
        type: '',
      };
    },
    handleCreate() {
      if (this.selected_spreadsheet){
        if (this.selected_cell){
          this.resetTemp();
          this.temp = {
            unit: this.selected_unit,
            rate: this.selected_rate,
          };
          this.temp.unit = this.selected_unit;

          this.dialogStatus = 'create';
          this.dialogFormVisible = true;
          this.$nextTick(() => {
            this.$refs['dataForm'].clearValidate();
          });
        } else {
          alert('Please select a spreadsheet cell!');
        }
      } else {
        alert('Please import or select a spreadsheet!');
      }
    },
    createData() {
      const vm = this;
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          // this.temp.id = parseInt(Math.random() * 100) + 1024; // mock a id
          this.temp.selected_cell = this.selected_cell;
          this.temp.selected_spreadsheet = this.selected_spreadsheet;
          this.temp.amount = (this.temp.quantity * this.temp.rate);
          this.temp.timestamp = this.selected_date;
          createWorkDone(this.temp).then((response) => {
            vm.temp.id = response.id;
            this.list.unshift(this.temp);
            this.dialogFormVisible = false;
            vm.$emit('refresh');
            this.$notify({
              title: 'Success',
              message: 'Created successfully',
              type: 'success',
              duration: 2000,
            });
          });
        }
      });
    },
    handleUpdate(row) {
      this.temp = Object.assign({}, row); // copy obj
      // this.temp.timestamp = new Date(this.temp.timestamp);
      this.temp.timestamp = this.selected_date;
      this.dialogStatus = 'update';
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate();
      });
    },
    updateData() {
      const vm = this;
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp);
          // tempData.timestamp = +new Date(tempData.timestamp); // change Thu Nov 30 2017 16:41:05 GMT+0800 (CST) to 1512031311464
          tempData.timestamp = this.selected_date;
          this.temp.amount = (this.temp.quantity * this.temp.rate);
          updateWorkDone(tempData).then(() => {
            vm.$emit('refresh');
            for (const v of this.list) {
              if (v.id === this.temp.id) {
                const index = this.list.indexOf(v);
                this.list.splice(index, 1, this.temp);
                break;
              }
            }
            this.dialogFormVisible = false;
            this.$notify({
              title: 'Success',
              message: 'Updated successfully',
              type: 'success',
              duration: 2000,
            });
          });
        }
      });
    },
    handleDelete(row) {
      this.$notify({
        title: 'Success',
        message: 'Deleted successfully',
        type: 'success',
        duration: 2000,
      });
      const index = this.list.indexOf(row);
      this.list.splice(index, 1);
    },
    handleFetchPv(pv) {
      fetchPv(pv).then(response => {
        this.pvData = response.data.pvData;
        this.dialogPvVisible = true;
      });
    },
    handleDownload() {
      this.downloadLoading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = ['timestamp', 'title', 'type', 'importance', 'status'];
        const filterVal = ['timestamp', 'title', 'type', 'importance', 'status'];
        const data = this.formatJson(filterVal, this.list);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: 'table-list',
        });
        this.downloadLoading = false;
      });
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => {
        if (j === 'timestamp') {
          return parseTime(v[j]);
        } else {
          return v[j];
        }
      }));
    },
  },
};
</script>

