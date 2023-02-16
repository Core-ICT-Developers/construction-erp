import request from '@/utils/request';

export function fetchEquipment(query) {
  return request({
    url: '/equipment',
    method: 'get',
    params: query,
  });
}

export function createEquipment(data) {
  return request({
    url: '/equipment-create',
    method: 'post',
    data,
  });
}

export function updateEquipment(data) {
  return request({
    url: 'equipment-update',
    method: 'post',
    data,
  });
}

export function deleteEquipment(data) {
  return request({
    url: 'equipment-delete',
    method: 'post',
    data,
  });
}

