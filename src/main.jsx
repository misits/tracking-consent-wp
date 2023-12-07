import React from 'react'
import ReactDOM from 'react-dom/client'
import Front from '@/Front.jsx'
import Admin from '@/Admin.jsx'
import '@scss/app.scss'

let appToRender = document.querySelector('.wp-admin') ? 'admin' : 'front';

// Create root element at the bottom of the body
let rootElement;
if (appToRender !== 'admin') {
  rootElement = document.createElement('div')
  rootElement.id = 'wp-tracking-consent-' + appToRender;
  document.body.appendChild(rootElement);
}

// Render the app
const root = document.getElementById('wp-tracking-consent-' + appToRender);

if (root) {
  ReactDOM.createRoot(root).render(
    <React.StrictMode>
      {appToRender === 'front' ? <Front /> : <Admin /> }
    </React.StrictMode>,
  )
};