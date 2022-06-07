/**
 * Do some fancy UI stuff in a rather unfancy way.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */

import Main from './classes/Main';

console.info(`%c
     ██ ██    ██ ███    ███ ██████
     ██ ██    ██ ████  ████ ██   ██
     ██ ██    ██ ██ ████ ██ ██████
██   ██ ██    ██ ██  ██  ██ ██
 █████   ██████  ██      ██ ██

https://github.com/daledavies/jump

`, "font-family:monospace");

let jumpapp = new Main();
jumpapp.init();
