import request from '@/utils/request';

export function fetchWorkDoneList(query) {
  return request({
    url: '/work-done',
    method: 'get',
    params: query,
  });
}

export function createWorkDone(data) {
  return request({
    url: '/work-done-create',
    method: 'post',
    data,
  });
}

export function updateWorkDone(data) {
  return request({
    url: 'work-done-update',
    method: 'post',
    data,
  });
}

export function deleteWorkDone(data) {
  return request({
    url: 'work-done-delete',
    method: 'post',
    data,
  });
}
