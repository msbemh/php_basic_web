import { createRoot } from 'react-dom/client';

function NavigationBar() {
  // TODO: Actually implement a navigation bar
  return React.createElement(
    'h1',
    null,
    'Hello from React!'
  );
}

var domNode = document.getElementById('navigation');
var root = createRoot(domNode);
root.render(React.createElement(NavigationBar, null));