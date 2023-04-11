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
			$content = '<pre>';
			foreach ((new \Jump\Config())->get_all() as $param => $value) {
				$content .= '<b>'.$param.'</b> : '.$value.'<br>';
			}
			$content .= '</pre>';
			return [
				'tab' => 'Jump Config',
				'panel' => $content,
				'bottom' => true
			];
		}
		return null;
	}
}
