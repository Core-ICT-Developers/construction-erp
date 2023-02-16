<template>
  <el-table :data="list" border fit highlight-current-row style="width: 100%">
    <el-table-column
      v-loading="loading"
      align="center"
      label="ID"
      width="65"
      element-loading-text="Pleas be patient！"
    >
      <template slot-scope="scope">
        <span>{{ scope.row.id }}</span>
      </template>
    </el-table-column>

    <el-table-column width="180px" align="center" label="Date">
      <template slot-scope="scope">
        <span>{{ scope.row.startdate | parseTime('{y}-{m}-{d}') }}</span>
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Project">
      <template slot-scope="scope">
        <span class="link-type" @click="handleOpenGantt(scope.row)">{{ scope.row.projectname }}</span>
      </template>
    </el-table-column>

    <el-table-column width="110px" align="center" label="Start time">
      <template slot-scope="scope">
        <span>{{ scope.row.starttime }}</span>
      </template>
    </el-table-column>

    <el-table-column width="120px" label="End time">
      <template slot-scope="scope">
        <span>{{ scope.row.endtime }}</span>
      </template>
    </el-table-column>

    <!--<el-table-column align="center" label="Working days" width="95">
      <template slot-scope="scope">
        <span>{{ scope.row.workingdays }}</span>
      </template>
    </el-table-column>-->
  </el-table>
</template>

<script>
import { fetchProjectlist } from '@/api/project-management';

export default {
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger',
      };
      return statusMap[status];
    },
  },
  props: {
    type: {
      type: String,
      default: 'VI',
    },
  },
  data() {
    return {
      list: null,
      listQuery: {
        page: 1,
        limit: 5,
        type: this.type,
        sort: '+id',
      },
      loading: false,
    };
  },
  created() {
    this.getList();
  },
  methods: {
    handleOpenGantt(object){
      this.$router.push({ name: 'Gantt', params: { // project: Object.assign({}, ...[object]), //path: '/project-management',
        project: { ...object },
      }});
    },
    async getList() {
      this.loading = true;
      this.$emit('create'); // for test
      const { data } = await fetchProjectlist(this.listQuery);
      this.list = data.items;
      this.loading = false;
    },
  },
};
</script>

