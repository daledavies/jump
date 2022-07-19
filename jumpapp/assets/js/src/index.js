/**
 *      ██ ██    ██ ███    ███ ██████
 *      ██ ██    ██ ████  ████ ██   ██
 *      ██ ██    ██ ██ ████ ██ ██████
 * ██   ██ ██    ██ ██  ██  ██ ██
 *  █████   ██████  ██      ██ ██
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @copyright Copyright (c) 2022, Dale Davies
 * @license MIT
 */

/**
 * Do some fancy UI stuff in a rather unfancy way.
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
