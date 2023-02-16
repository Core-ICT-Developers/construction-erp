import request from '@/utils/request';

export function createNewProject(data) {
  return request({
    url: '/create-project',
    method: 'post',
    data,
  });
}

export function fetchProjectlist(query) {
  return request({
    url: '/projects',
    method: 'get',
    params: query,
  });
}

export function fetchProjectGantt(query) {
  return request({
    url: '/fetch-projects-gantt',
    method: 'get',
    params: query,
  });
}

export function createNewTask(data) {
  return request({
    url: '/create-task',
    method: 'post',
    data,
  });
}

