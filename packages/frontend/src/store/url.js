const initState = {
  url: null,
  page: null,
  module: null,
  subModule: null,
  section: null,
  id: null,
  type: null,
};

const getters = {
  url: (state) => state.url,
  page: (state) => state.page || 'management',
  module: (state) => state.module || 'student',
  subModule: (state) => state.subModule || 'student',
  section: (state) => state.section || 'student-list',
  id: (state) => state.id,
  type: (state) => state.type,
};

const actions = {
  async updateUrl ({ commit }, url) {
    commit('setUrl', url);
  },
  async updatePage ({ commit }, page) {
    commit('setPage', page);
  },
  async updateModule ({ commit }, module) {
    commit('setModule', module);
  },
  async updateSubModule ({ commit }, subModule) {
    commit('setSubModule', subModule);
  },
  async updateSection ({ commit }, section) {
    commit('setSection', section);
  },
  async updateId ({ commit }, id) {
    commit('setId', id);
  },
  async updateType ({ commit }, type) {
    commit('setType', type);
  },
  clearUrls ({ commit }) {
    commit('setModule', null);
    commit('setSubModule', null);
    commit('setSection', null);
    commit('setType', null);
  },
};

const mutations = {
  setUrl: (state, url) => {
    state.url = url;
    const subUrl = url.substring(1).split('/').slice(1);
    [state.page, state.module, state.subModule, state.section, state.id] = subUrl;
  },
  setPage: (state, page) => {
    state.page = page;
  },
  setModule: (state, module) => {
    state.module = module;
  },
  setSubModule: (state, subModule) => {
    state.subModule = subModule;
  },
  setSection: (state, section) => {
    state.section = section;
  },
  setId: (state, id) => {
    state.id = id;
  },
  setType: (state, type) => {
    state.type = type;
  },
};

export default {
  namespaced: true,
  state: initState,
  getters,
  actions,
  mutations,
};
