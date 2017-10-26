import 'babel-polyfill';
import 'es6-shim';
import 'es5-shim';
import React from 'react';
import ReactDOM from 'react-dom';
import injectTapEventPlugin from 'react-tap-event-plugin';
import { Provider } from 'react-redux';
import { ConnectedRouter } from 'react-router-redux';
import { I18nextProvider } from 'react-i18next';

import i18n from './i18n';
import './index.scss';
import AppRoot from './AppRoot';
import { initStore } from './store';

injectTapEventPlugin();

const store = initStore();

const render = (cmp) => {
  ReactDOM.render(
    <Provider store={store}>
      <ConnectedRouter history={store.history}>
        <I18nextProvider i18n={i18n}>
          {cmp}
        </I18nextProvider>
      </ConnectedRouter>
    </Provider>,
    document.getElementById('communibase-plugin-app')
  );
};

render(<AppRoot />);

if (module.hot) {
  module.hot.accept('./AppRoot', () => {
    render(<AppRoot />);
  });
}
