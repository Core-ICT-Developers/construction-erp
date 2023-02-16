<template>
  <div class="app-container">
    <el-form ref="dataForm" :rules="rules" :model="form" label-width="120px">
      <el-form-item label="Project name">
        <el-input v-model="form.projectname" />
      </el-form-item>

      <el-form-item label="Start date">
        <el-col :span="11">
          <el-date-picker v-model="form.startdate" type="date" placeholder="Pick a date" style="width: 100%;" />
        </el-col>
        <el-col :span="2" class="line">
          Duration step
        </el-col>
        <el-col :span="11">
          <el-select v-model="form.durationstep" placeholder="please select your duration step">
            <el-option label="Hours" value="Hours" />
            <el-option label="Days" value="Days" />
            <el-option label="Weeks" value="Weeks" />
            <el-option label="Months" value="Months" />
          </el-select>
        </el-col>
      </el-form-item>

      <el-form-item label="Templates">
        <el-select v-model="form.templates" placeholder="please select a template">
          <el-option label="Empty project" value="Empty project" />
        </el-select>
      </el-form-item>

      <el-form-item label="Working days">
        <el-col :span="2" class="line">
          Mon. <el-switch v-model="form.workingdaysmon" />
        </el-col>
        <el-col :span="2" class="line">
          Tue.<el-switch v-model="form.workingdaystue" />
        </el-col>
        <el-col :span="2" class="line">
          Wed.<el-switch v-model="form.workingdayswed" />
        </el-col>
        <el-col :span="2" class="line">
          Thur.<el-switch v-model="form.workingdaysthur" />
        </el-col>
        <el-col :span="2" class="line">
          Fri.<el-switch v-model="form.workingdaysfri" />
        </el-col>
        <el-col :span="2" class="line">
          Sat.<el-switch v-model="form.workingdayssat" />
        </el-col>
        <el-col :span="2" class="line">
          Sun.<el-switch v-model="form.workingdayssun" />
        </el-col>
      </el-form-item>

      <el-form-item label="Start time">
        <el-col :span="11">
          <el-time-picker v-model="form.starttime" type="fixed-time" placeholder="Pick a time" style="width: 100%;" format="hh:mm:ss A" value-format="hh:mm:ss A" />
        </el-col>
        <el-col :span="2" class="line">
          End time
        </el-col>
        <el-col :span="11">
          <el-time-picker v-model="form.endtime" type="fixed-time" placeholder="Pick a time" style="width: 100%;" />
        </el-col>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="onSubmit">
          Create new project
        </el-button>
        <el-button @click="onCancel">
          Import project
        </el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script>
import { createNewProject } from '@/api/project-management';
export default {
  data() {
    return {
      form: {
        projectname: '',
        startdate: '',
        durationstep: '',
        templates: '',
        workingdaysmon: false,
        workingdaystue: false,
        workingdayswed: false,
        workingdaysthur: false,
        workingdaysfri: false,
        workingdayssat: false,
        workingdayssun: false,
        starttime: '',
      },
      rules: {
        type: [{ required: true, message: 'type is required', trigger: 'change' }],
        timestamp: [{ type: 'date', required: true, message: 'timestamp is required', trigger: 'change' }],
        title: [{ required: true, message: 'title is required', trigger: 'blur' }],
      },
    };
  },
  methods: {
    onSubmit() {
      const vm = this;
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          /* this.temp.selected_cell = this.selected_cell;
          this.temp.selected_spreadsheet = this.selected_spreadsheet;
          this.temp.amount = (this.temp.quantity * this.temp.rate);
          this.temp.timestamp = this.selected_date;*/
          createNewProject(this.form).then(() => {
            // this.list.unshift(this.temp);
            // this.dialogFormVisible = false;
            vm.$emit('updated');
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
    // onSubmit() {
    // this.$message('Successfully created a new Project!');
    //  },
    onCancel() {
      this.$message({
        message: 'cancel!',
        type: 'warning',
      });
    },
  },
};
</script>

  <style scoped>
  .line{
    text-align: center;
  }
  </style>

