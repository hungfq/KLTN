const urlWithPagination = (urlOld, options, addon = '') => {
  let url = urlOld;
  if (options) {
    const {
      page,
      rowsPerPage,
      sortBy,
      sortType,
      search,
      addOn,
    } = options;
    if (rowsPerPage) url += `?limit=${rowsPerPage}`;
    else url += '&limit=10';
    if (sortBy) {
      if (!sortType) url += `&sort[${sortBy}]=ASC`;
      else url += `&sort[${sortBy}]=${sortType}`;
    }
    if (page) url += `&page=${page}`;
    if (search) url += `&search=${search}`;
    if (addOn) url += `${addOn}`;
  }
  if (addon) url += `&${addon}`;
  // replace all ? to &
  const result = url.replaceAll('?', '&');
  // change the first & to ?
  const finalUrl = result.replace('&', '?');
  return finalUrl;
};

export default urlWithPagination;
