const urlWithPagination = (urlOld, options, addon = '') => {
  let url = urlOld;
  const {
    page,
    rowsPerPage,
    sortBy,
    sortType,
  } = options;
  if (rowsPerPage) url += `?limit=${rowsPerPage}`;
  else url += '&limit=10';
  if (sortBy) {
    if (!sortType) url += `&sort[${sortBy}]=ASC`;
    else url += `&sort[${sortBy}]=${sortType}`;
  }
  if (page) url += `&page=${page}`;
  if (addon) url += `&${addon}`;
  return url;
};

export default urlWithPagination;
