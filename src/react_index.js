import React from 'react';
import ReactDOM from 'react-dom/client';

import App from '../components/App';

const root = ReactDOM.createRoot(document.querySelector('#react_app'));

root.render(
    <React.StrictMode>
        <App />
    </React.StrictMode>
);