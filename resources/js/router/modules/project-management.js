/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';
// import request from '@/utils/request';

// const returnedRoutes = async function projectManagementRoutes(){
//   const getRoutes = request({
//     url: '/projects',
//     method: 'get',
//     params: [],
//   }).then(result => result.data).catch(error => { // (**)
//     alert(`The unknown error has occurred: ${error}`);
//     // don't return anything => execution goes the normal way
//   });

//   const { data } = await getRoutes();

//   console.log(data);

//   const routesRoutes = {
//     path: '/Project Management',
//     component: Layout,
//     redirect: '/project-management/index',
//     name: 'Project Management',
//     meta: {
//       title: 'Project Management',
//       icon: 'documentation',
//       permissions: ['view menu nested routes'],
//     },
//     children: [
//       {
//         path: 'New Project',
//         name: 'New Project',
//         component: () => import('@/views/project-management/new'),
//         meta: { title: 'New Project', icon: 'clipboard' },
//       },
//       {
//         path: 'All projects',
//         component: () => import('@/views/nested/menu1/index'), // Parent router-view
//         name: 'All projects',
//         meta: { title: 'All projects', icon: 'international' },
//         children: [
//           {
//             path: 'Project 1',
//             component: () => import('@/views/nested/menu1/menu1-1'),
//             name: 'Project 1',
//             meta: { title: 'Project 1', icon: 'documentation' },
//           },
//           {
//             path: 'Project 2',
//             component: () => import('@/views/nested/menu1/menu1-1'),
//             name: 'Project 2',
//             meta: { title: 'Project 2', icon: 'documentation' },
//           },

//         ],
//       },
//       {
//         path: 'My tasks',
//         component: () => import('@/views/nested/menu2/index'),
//         meta: { title: 'My tasks', icon: 'guide' },
//       },
//       {
//         path: 'My time log',
//         component: () => import('@/views/nested/menu2/index'),
//         meta: { title: 'My time log', icon: 'guide' },
//       },
//       {
//         path: 'Reports',
//         component: () => import('@/views/nested/menu2/index'),
//         meta: { title: 'Reports', icon: 'guide' },
//       },
//       {
//         path: 'Workload',
//         component: () => import('@/views/nested/menu2/index'),
//         meta: { title: 'Workload', icon: 'guide' },
//       },
//     ],
//   };

//   return routesRoutes;
// };

// console.log(returnedRoutes);
// export default returnedRoutes;

/* import request from '@/utils/request';

const getRoutes = request({
  url: '/projects',
  method: 'get',
  params: [],
}).then(result => result.data).catch(error => { // (**)
  alert(`The unknown error has occurred: ${error}`);
  // don't return anything => execution goes the normal way
});

// const data = getRoutes;
// var data = async(_data) => {
//   const { data } = await getRoutes();
//   return data;
// };

const data = Promise.resolve(getRoutes);
data.then(value => {
  console.log(value); // 👉️ "hello"
}).catch(err => {
  console.log(err);
});

console.log(data);

// routesfetched.then((response) => {
//   console.log(response);
// });
*/

const projectManagementRoutes = {
  path: '/project-management/index',
  component: Layout,
  redirect: '/project-management/index',
  name: 'Projects',
  meta: {
    title: 'Projects',
    icon: 'documentation',
    permissions: ['view menu nested routes'],
  },
  children: [
    {
      path: '/views/project-management/new',
      name: 'New Project',
      component: () => import('@/views/project-management/new'),
      meta: { title: 'New Project', icon: 'clipboard' },
    },
    {
      path: 'All projects',
      component: () => import('@/views/project-management/index'), // Parent router-view
      name: 'All projects',
      meta: { title: 'All projects', icon: 'international' },
      children: [
        /* {
          path: 'Project 1',
          component: () => import('@/views/nested/menu1/menu1-1'),
          name: 'Project 1',
          meta: { title: 'Project 1', icon: 'documentation' },
        },
        {
          path: 'Project 2',
          component: () => import('@/views/nested/menu1/menu1-1'),
          name: 'Project 2',
          meta: { title: 'Project 2', icon: 'documentation' },
        },*/

      ],
    },
    {
      path: '/views/project-management/index',
      component: () => import('@/views/project-management/index'),
      meta: { title: 'My tasks', icon: 'guide' },
    },
    {
      path: '/views/project-management/index',
      component: () => import('@/views/project-management/index'),
      meta: { title: 'My time log', icon: 'guide' },
    },
    {
      path: '/views/project-management/index',
      component: () => import('@/views/project-management/index'),
      meta: { title: 'Reports', icon: 'guide' },
    },
    {
      path: '/views/project-management/index',
      component: () => import('@/views/project-management/index'),
      meta: { title: 'Workload', icon: 'guide' },
    },
  ],
};

// getRoutes.then(function(result) {
// console.log(result);
// });

export default projectManagementRoutes;

