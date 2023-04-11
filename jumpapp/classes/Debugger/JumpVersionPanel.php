<?php
/**
 *      ██ ██    ██ ███    ███ ██████
 *      ██ ██    ██ ████  ████ ██   ██
 *      ██ ██    ██ ██ ████ ██ ██████
 * ██   ██ ██    ██ ██  ██  ██ ██
 *  █████   ██████  ██      ██ ██
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @copyright Copyright (c) 2023, Dale Davies
 * @license MIT
 */

namespace Jump\Debugger;

class JumpVersionPanel {
	public static function panel(?\Throwable $e) {
		if ($e === null) {
			$version = file_get_contents(__DIR__ . '/../../.jump-version');
			return [
				'tab' => 'Jump Version',
				'panel' => '<pre>'.$version.'</pre>',
				'bottom' => true
			];
		}
		return null;
	}
}
