<template>
  <div class="app-container documentation-container">
    <p>Project Management</p>
    <div id="gantt_here" style="height: calc(100vh - 60px);" />
  </div>
</template>
<script src=/static/gantt/codebase/dhtmlxgantt.js></script>
<script>
import { fetchProjectGantt, createNewTask } from '@/api/project-management';
var ganttDetails = null;
export default {
  name: 'Default',
  props: {
    project: {
      type: Object,
      default: () => [],
      required: true,
    },
  },
  data() {
    return {
	ganttDetails:null,
     listQuery: {
		id: 0,
        page: 1,
        limit: 5,
        sort: '+id',
      },
	temp: {
        id: 0,
        parent: 1,
        planned_end: new Date(),
        planned_start: new Date(),
        progress: 0,
        start_date: new Date(),
        text: '',
        type: '',
        users: ''
      },
    };
  },
  created(){
	var vm = this;
    gantt.attachEvent("onLightboxSave", function(id, task, is_new){
		var dateToStr = gantt.date.date_to_str("%d-%m-%Y");
			this.temp = {
				id: task.id,
				parent: task.parent,
				planned_end: task.planned_end,
				planned_start: task.planned_start,
				progress: task.progress,
				start_date: task.start_date,
				text: task.text,
				type: task.type,
				users: task.users,
				is_new:is_new
				};
			createNewTask(this.temp).then(() => {
				vm.$emit('updated');
				vm.$notify({
				title: 'Success',
				message: 'Created successfully',
				type: 'success',
				duration: 2000,
				});
			});
		return true;
	});
  },
  mounted(){
    var vm = this;
    vm.getProjectGantt().then((response) => {

	gantt.config.scale_height = 50;

	gantt.locale.labels["users"] = "Assigned to";

	gantt.config.types["customType"] = "type_id";
	gantt.locale.labels['type_' + "customType"] = "New Type";
	gantt.config.lightbox["customType" + "_sections"] = [
		{name: "description", height: 70, map_to: "text", type: "textarea", focus: true},
		{name: "type", type: "typeselect", map_to: "type"}
	];

	gantt.config.scales = [
		{unit: "month", step: 1, format: "%F, %Y"},
		{unit: "day", step: 1, format: "%j, %D"}
	];

	gantt.templates.rightside_text = function (start, end, task) {
		if (task.type == gantt.config.types.milestone) {
			return task.text;
		}
		return "";
	};

	gantt.form_blocks["employee"] = {
		render: function (sns) {
			return "<div class='dhx_cal_ltext' style='height:20px;width:100%'><span style=''></span><select  style='margin-left: 2%; width: 97%;' class='employee_dropdown' type='text'><option value='martin'>Martin</option></select></div><br><br>";
		},
		set_value: function (node, value, task) {
			node.querySelector(".employee_dropdown").value = value || "";
		},
		get_value: function (node, task) {
			task.users = node.querySelector(".employee_dropdown").value;
			return node.querySelector(".employee_dropdown").value;
		},
		focus: function (node) {
			var a = node.querySelector(".employee_dropdown");
			//a.select();
			//a.focus();
		}
	};

	gantt.config.lightbox.sections = [
        {name:"employee", map_to:"text", type:"employee", focus:true},
		{name: "description", height: 70, map_to: "text", type: "textarea", focus: true},
		{name: "type", type: "typeselect", map_to: "type"},
		{name: "time", type: "duration", map_to: "auto"},
		{name: "baseline",  map_to: {start_date: "planned_start", end_date: "planned_end"}, type: "duration"}
	];
	gantt.locale.labels.section_baseline = "End date";
	gantt.locale.labels.section_employee= "Assigned to";
	gantt.locale.labels.section_time= "Start date";
	//gantt.locale.labels.section_end_date= "End date";
	gantt.init("gantt_here");
	gantt.clearAll();
	var projects_with_milestones = {
		"data": response.items
		/*"data":[
			{"id":11, "text":"Project #1", type:gantt.config.types.project, "progress": 0.6, "open": true},

			{"id":12, "text":"Task #1", "start_date":"03-04-2018", "duration":"5", "parent":"11", "progress": 1, "open": true},
			{"id":13, "text":"Task #2", "start_date":"03-04-2018", type:gantt.config.types.project, "parent":"11", "progress": 0.5, "open": true},
			{"id":14, "text":"Task #3", "start_date":"02-04-2018", "duration":"6", "parent":"11", "progress": 0.8, "open": true},
			{"id":15, "text":"Task #4", type:gantt.config.types.project, "parent":"11", "progress": 0.2, "open": true},
			{"id":16, "text":"Final milestone", "start_date":"15-04-2018", type:gantt.config.types.milestone, "parent":"11", "progress": 0, "open": true},

			{"id":17, "text":"Task #2.1", "start_date":"03-04-2018", "duration":"2", "parent":"13", "progress": 1, "open": true},
			{"id":18, "text":"Task #2.2", "start_date":"06-04-2018", "duration":"3", "parent":"13", "progress": 0.8, "open": true},
			{"id":19, "text":"Task #2.3", "start_date":"10-04-2018", "duration":"4", "parent":"13", "progress": 0.2, "open": true},
			{"id":20, "text":"Task #2.4", "start_date":"10-04-2018", "duration":"4", "parent":"13", "progress": 0, "open": true},
			{"id":21, "text":"Task #4.1", "start_date":"03-04-2018", "duration":"4", "parent":"15", "progress": 0.5, "open": true},
			{"id":22, "text":"Task #4.2", "start_date":"03-04-2018", "duration":"4", "parent":"15", "progress": 0.1, "open": true},
			{"id":23, "text":"Mediate milestone", "start_date":"14-04-2018", type:gantt.config.types.milestone, "parent":"15", "progress": 0, "open": true}
		],
		"links":[
			{"id":"10","source":"11","target":"12","type":"1"},
			{"id":"11","source":"11","target":"13","type":"1"},
			{"id":"12","source":"11","target":"14","type":"1"},
			{"id":"13","source":"11","target":"15","type":"1"},
			{"id":"14","source":"23","target":"16","type":"0"},
			{"id":"15","source":"13","target":"17","type":"1"},
			{"id":"16","source":"17","target":"18","type":"0"},
			{"id":"17","source":"18","target":"19","type":"0"},
			{"id":"18","source":"19","target":"20","type":"0"},
			{"id":"19","source":"15","target":"21","type":"2"},
			{"id":"20","source":"15","target":"22","type":"2"},
			{"id":"21","source":"15","target":"23","type":"0"}
		]*/
	};

	gantt.parse(projects_with_milestones);
   });

  },methods: {

    async getProjectGantt() {
      //this.loading = true;
      this.$emit('create'); // for test
      this.listQuery.id = this.project.id;
      const { data } = await fetchProjectGantt(this.listQuery);
      this.ganttDetails = data;
      return this.ganttDetails;

      //this.list = data.items;
      //this.loading = false;
    },
  },
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
</style>
