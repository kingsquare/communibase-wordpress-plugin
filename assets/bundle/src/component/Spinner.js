// @see https://codepen.io/mrrocks/pen/EiplA
import React, { Component } from 'react';
import PropTypes from 'prop-types';
import stylePropType from 'react-style-proptype';
import './Spinner.scss';

export default class Spinner extends Component {
  static propTypes = {
    color: PropTypes.string,
    style: stylePropType
  };
  static defaultProps = {
    color: null,
    style: null
  };

  render = () => {
    const svgProps = {
      className: 'react-spinner',
      width: '100%',
      height: '100%',
      viewBox: '0 0 66 66',
      xmlns: 'http://www.w3.org/2000/svg'
    };
    const pathProps = {
      className: 'path',
      fill: 'none',
      strokeWidth: 6,
      strokeLinecap: 'round',
      cx: 33,
      cy: 33,
      r: 30
    };

    if (this.props.color) {
      pathProps.stroke = this.props.color;
    } else {
      svgProps.className += ' rainbow';
    }

    return (
      <div style={this.props.style}>
        <svg {...svgProps} >
          <circle {...pathProps} />
        </svg>
      </div>
    );
  }
}
