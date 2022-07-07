/**
 * Do some fancy UI stuff in a rather unfancy way.
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @license MIT
 */

import Main from './classes/Main';
import version from '../../../.jump-version';

console.info(`%c
----------------------------------

     ██ ██    ██ ███    ███ ██████
     ██ ██    ██ ████  ████ ██   ██
     ██ ██    ██ ██ ████ ██ ██████
██   ██ ██    ██ ██  ██  ██ ██
 █████   ██████  ██      ██ ██

https://github.com/daledavies/jump

----------------------------------

     Jump ${version}

----------------------------------

`, "font-family:monospace");

let jumpapp = new Main();
jumpapp.init();
