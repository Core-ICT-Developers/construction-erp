import request from '@/utils/request';

export function fetchLabourList(query) {
  return request({
    url: '/labour',
    method: 'get',
    params: query,
  });
}

export function createLabour(data) {
  return request({
    url: '/labour-create',
    method: 'post',
    data,
  });
}

export function updateLabour(data) {
  return request({
    url: 'labour-update',
    method: 'post',
    data,
  });
}
export function deleteLabour(data) {
  return request({
    url: 'labour-delete',
    method: 'post',
    data,
  });
}

