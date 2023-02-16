<template>
  <div class="tab-container">
    <el-tabs v-model="activeName" style="margin-top:15px;" type="border-card">
      <el-tab-pane label="New Project" name="New Project">
        <keep-alive>
          <new-project ref="newproject" />
        </keep-alive>
      </el-tab-pane>
      <el-tab-pane label="Project List" name="Project List">
        <keep-alive>
          <project-list ref="projectlist" />
        </keep-alive>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import NewProject from './components/NewProject';
import ProjectList from './components/ProjectList';

export default {
  name: 'Tab',
  components: { NewProject, ProjectList },
  data() {
    return {
      activeName: 'New Project',
    };
  },
  methods: {
    showCreatedTimes() {
      this.createdTimes = this.createdTimes + 1;
    },
  },
};
</script>

  <style scoped>
    .tab-container {
      margin: 30px;
    }
  </style>

