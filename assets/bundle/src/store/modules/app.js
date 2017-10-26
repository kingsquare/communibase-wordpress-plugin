import Immutable from 'immutable';

// ------------------------------------
// Actions
// ------------------------------------

export const WINDOW_RESIZE = '@app/WINDOW_RESIZE';
export const TOGGLE_MODAL = '@app/TOGGLE_MODAL';

// ------------------------------------
// Action generators
// ------------------------------------

export const onWindowResize = () => ({
  type: WINDOW_RESIZE,
  payload: {
    windowWidth: window.innerWidth,
    windowHeight: window.innerHeight
  }
});

export const toggleModal = name => ({ type: TOGGLE_MODAL, payload: name });

// ------------------------------------
// Default State
// ------------------------------------

const State = Immutable.Record({
  windowWidth: window.innerWidth,
  windowHeight: window.innerHeight,
  modal: Immutable.Map()
});

// ------------------------------------
// Action Handlers
// ------------------------------------

const ACTION_HANDLERS = {
  [WINDOW_RESIZE]: (state, { payload }) => state
    .set('windowWidth', payload.windowWidth)
    .set('windowHeight', payload.windowHeight),
  [TOGGLE_MODAL]: (state, { payload }) => state.setIn(['modal', payload], !state.getIn(['modal', payload], false))
};

// ------------------------------------
// Reducer
// ------------------------------------

export default function reducer(state = new State(), action) {
  const handler = ACTION_HANDLERS[action.type];
  return handler ? handler(state, action) : state;
}
