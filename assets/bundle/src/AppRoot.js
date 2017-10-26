import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Route, withRouter, Switch } from 'react-router-dom';
import debounce from 'lodash/debounce';
import Notifications from 'react-notification-system-redux';
import { connect } from 'react-redux';
import { onWindowResize } from './store/modules/app';

import Header from './component/Header';
import Footer from './component/Footer';
import Settings from './route/Settings';

class AppRoot extends Component {
  static propTypes = {
    notifications: PropTypes.arrayOf(PropTypes.object).isRequired,
    onWindowResize: PropTypes.func.isRequired
  };
  static defaultProps = {
  };

  componentWillMount() {
    window.onresize = debounce(this.props.onWindowResize, 10);
  }

  renderContent() {
    return (
      <div>
        <Settings />
      </div>
    );
  }

  render = () => (
    <div>
      <Header />
      { this.renderContent() }
      <Notifications notifications={this.props.notifications} />
      <Footer />
    </div>
  )
}

const ConnectedAppRoot = withRouter(connect(state => ({
  notifications: state.notifications,
}), {
  onWindowResize
})(AppRoot));

export default ConnectedAppRoot;
