import request from '@/utils/request';

export function createNewBuilding(data) {
  return request({
    url: '/create-building',
    method: 'post',
    data,
  });
}

export function fetchBuildingList(query) {
  return request({
    url: '/buildings',
    method: 'get',
    params: query,
  });
}
