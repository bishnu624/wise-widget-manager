import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import { Provider } from './context';
import Notifications from './notify';
import './style.css';
ReactDOM.createRoot(document.getElementById('wisema-widget-manager')).render(
  <Provider>
    <Notifications />
    <App />
  </Provider>
);
