import React from 'react';
import PropTypes from 'prop-types';

import Spinner from './Spinner';

const Loader = ({ message, children, messagePosition, color, backgroundColor, spinnerSize }) => {
  const containerClasses = [
    'd-flex'
  ];
  const messageClasses = [
  ];
  switch (messagePosition) {
    case 'top':
      containerClasses.push('flex-column');
      containerClasses.push('flex-column-reverse');
      containerClasses.push('align-items-center');
      messageClasses.push('mb-3');
      break;
    case 'right':
      containerClasses.push('flex-row');
      containerClasses.push('align-items-center');
      messageClasses.push('ml-3');
      break;
    case 'bottom':
      containerClasses.push('flex-column');
      containerClasses.push('align-items-center');
      messageClasses.push('mt-3');
      break;
    case 'left':
      containerClasses.push('flex-row');
      containerClasses.push('flex-row-reverse');
      containerClasses.push('align-items-center');
      messageClasses.push('mr-3');
      break;
    default:
      break;
  }
  return (
    <div className="d-flex flex-1 flex-column justify-content-center align-items-center" style={{ backgroundColor }}>
      <div className={containerClasses.join(' ')} style={{ color }}>
        <Spinner color={color} style={{ maxWidth: spinnerSize }} />
        <div className={messageClasses.join(' ')}>
          { children || message }
        </div>
      </div>
    </div>
  );
};

Loader.propTypes = {
  message: PropTypes.string,
  // eslint-disable-next-line react/forbid-prop-types
  children: PropTypes.any,
  messagePosition: PropTypes.oneOf(['top', 'right', 'bottom', 'right']),
  color: PropTypes.string,
  backgroundColor: PropTypes.string,
  spinnerSize: PropTypes.number,
};

Loader.defaultProps = {
  message: null,
  children: null,
  messagePosition: 'right',
  color: 'gray',
  backgroundColor: 'transparent',
  spinnerSize: 30
};

export default Loader;
