import Notifications from 'react-notification-system-redux';

const messages = {
  Forbidden: 'Controleer uw inloggegevens',
  'Password Expired': 'Wachtwoord verlopen, nieuwe wachtwoord invoeren'
};

export default function promiseMiddleware(_ref) {
  const dispatch = _ref.dispatch;

  return next => (action) => {
    if (!action.payload || !action.payload.then) {
      next(action);
      return null;
    }

    dispatch(Object.assign({}, action, { status: 'pending', promise: action.payload, payload: null }));
    return action.payload.then((result) => {
      dispatch(Object.assign({}, action, { status: 'success', payload: result }));
      return result;
    }, (error) => {
      dispatch(Object.assign({}, action, { status: 'error', payload: error }));
      // eslint-disable-next-line no-console
      console.error(error.stack);
      dispatch(Notifications.error({
        title: 'Er gaat iets mis!',
        message: messages[error.message] || error.message,
        position: 'tc',
        autoDismiss: 2
      }));
      throw error;
    });
  };
}
