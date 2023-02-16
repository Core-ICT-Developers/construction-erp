import request from '@/utils/request';

export function generatePDFSummary(query) {
  return request({
    url: '/work-summary-pdf',
    method: 'get',
    params: query,
  });
}

export function generateWorkExcel(query) {
  return request({
    url: '/generate-work-excel',
    method: 'get',
    params: query,
  });
}
//
export function fetchCellsFiltered(query) {
  return request({
    url: '/spreadsheet-cells',
    method: 'get',
    params: query,
  });
}

export function fetchSpreadSheetList(query) {
  return request({
    url: '/spreadsheet-list',
    method: 'get',
    params: query,
  });
}
export function fetchSpreadSheetLevel(query) {
  return request({
    url: '/spreadsheet-level',
    method: 'get',
    params: query,
  });
}

export function fetchSpreadSheetLevelOne(query) {
  return request({
    url: '/spreadsheet-level-one',
    method: 'get',
    params: query,
  });
}

export function fetchMaterialsList(query) {
  return request({
    url: '/materials-used',
    method: 'get',
    params: query,
  });
}

export function createMaterial(data) {
  return request({
    url: '/materials-used-create',
    method: 'post',
    data,
  });
}

export function updateMaterial(data) {
  return request({
    url: 'materials-used-update',
    method: 'post',
    data,
  });
}

export function fetchQuantityDone(query) {
  return request({
    url: '/fetch-quantity-work-done',
    method: 'get',
    params: query,
  });
}

export function updateBQTotals(data) {
  return request({
    url: 'update-bq-totals',
    method: 'post',
    data,
  });
}

export function deleteMaterialsUsed(data) {
  return request({
    url: 'materials-used-delete',
    method: 'post',
    data,
  });
}
/* export function fetchArticle(id) {
  return request({
    url: '/articles/' + id,
    method: 'get',
  });
}

export function fetchPv(id) {
  return request({
    url: '/articles/' + id + '/pageviews',
    method: 'get',
  });
}

export function createArticle(data) {
  return request({
    url: '/article/create',
    method: 'post',
    data,
  });
}

export function updateArticle(data) {
  return request({
    url: '/article/update',
    method: 'post',
    data,
  });
}*/
