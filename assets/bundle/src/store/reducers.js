import { combineReducers } from 'redux';
import { reducer as reduxForm } from 'redux-form';
import { reducer as notifications } from 'react-notification-system-redux';

import app from './modules/app';

export const makeRootReducer = (asyncReducers, reduxFormReducerPlugins) => combineReducers({
  ...asyncReducers,
  app,
  form: reduxForm.plugin({ ...reduxFormReducerPlugins }),
  notifications
});

export const injectReducer = (store, { key, reducer }) => {
  store.asyncReducers[key] = reducer;
  store.replaceReducer(makeRootReducer(store.asyncReducers, store.reduxFormReducerPlugins));
};

const reduxFormReducerPlugins = {};
export const injectReduxFormReducerPlugin = (store, { form, reducer }) => {
  if (!reduxFormReducerPlugins[form]) {
    reduxFormReducerPlugins[form] = [reducer];
  } else {
    reduxFormReducerPlugins[form].push(reducer);
  }
  store.reduxFormReducerPlugins[form] = (state, action) => {
    reduxFormReducerPlugins[form].forEach((r) => {
      state = r(state, action);
    });
    return state;
  };
  store.replaceReducer(makeRootReducer(store.asyncReducers, store.reduxFormReducerPlugins));
};

export default makeRootReducer;
