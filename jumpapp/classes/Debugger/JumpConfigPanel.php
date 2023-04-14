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

class JumpConfigPanel {
	public static function panel(?\Throwable $e) {
		if ($e === null) {
			// Get all config params as an array and sort them by param name.
			$configparams = (new \Jump\Config())->get_all();
			ksort($configparams);
			// Prepare the panel HTML, listing the config param key and value.
			$content = '<pre>';
			foreach ($configparams as $param => $value) {
				$content .= '<b>'.$param.'</b> : '.$value.'<br>';
			}
			$content .= '</pre>';
			// Return the panel items.
			return [
				'tab' => 'Jump Config',
				'panel' => $content,
				'bottom' => true
			];
		}
		return null;
	}
}
