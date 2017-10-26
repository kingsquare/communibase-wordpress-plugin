import { createStore, applyMiddleware, compose } from 'redux';
import thunkMiddleware from 'redux-thunk';
import createHistory from 'history/createBrowserHistory';
import { routerMiddleware } from 'react-router-redux';

import rootReducer from './reducers';
import promiseMiddleware from './middleware/promise';

// Create a history of your choosing (we're using a browser history in this case)
const history = createHistory();

let store;

// ======================================================
// Store Enhancers
// ======================================================
const enhancers = [];
if (process.env.NODE_ENV === 'development') {
  const devToolsExtension = window.devToolsExtension;
  if (typeof devToolsExtension === 'function') {
    enhancers.push(devToolsExtension());
  }
}

export const initStore = (initialState = {}) => {
  store = createStore(
    rootReducer(),
    initialState,
    compose(applyMiddleware(promiseMiddleware, thunkMiddleware, routerMiddleware(history)), ...enhancers)
  );
  store.asyncReducers = {};
  store.reduxFormReducerPlugins = {};

  store.history = history;

  if (process.env.NODE_ENV !== 'production' && module.hot) {
    module.hot.accept('./reducers', () => {
      store.replaceReducer(rootReducer(store.asyncReducers, store.reduxFormReducerPlugins));
    });
  }

  return store;
};

export const getStore = () => store;

export default initStore;
