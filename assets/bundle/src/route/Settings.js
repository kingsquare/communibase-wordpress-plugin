import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Loader from '../component/Loader';

class Settings extends Component {
  render () {
    return (
      <div>
        Settings
        <Loader />

        <div className="form-group">
          <label htmlFor="communibase-apiKey">
            API Key
          </label>
          <input id="communibase-apiKey" name="communibase.apiKey" placeholder="API Key" className="form-control" />
        </div>

        <button type="button" name="validateConnection" id="comunibase-validateConnection" className="button button-primary">
          Validate connection
        </button>

        <button type="button" name="submit" id="submit" className="button button-primary">
          Submit
        </button>

      </div>
    );
  }
}

export default Settings;
