import React, { Component } from 'react';
import PropTypes from 'prop-types';

class Header extends Component {
  render () {
    return (
      <div>
        <div className="communibase-unknown-info">
          <a href="https://www.communibase.nl" target="_blank" rel="noopener">Communibase</a> is a paid service for community/association/club/society membership administration.
          <ul>
            <li><a href="https://www.communibase.nl/#openLogin" target="_blank" rel="noopener">Request a demo account</a></li>
            <li><a href="https://www.communibase.nl" target="_blank" rel="noopener">More information</a></li>
          </ul>
        </div>
      </div>
    );
  }
}

export default Header;
