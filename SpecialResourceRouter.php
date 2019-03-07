<?php
/**
 * "Special" page to route traffic to correct files.
 *
 * @file
 * @ingroup Extensions
 * @author Lewis Cawte <lewis@lewiscawte.me>
 * @copyright © 2019, Lewis Cawte, ShoutWiki Limited.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 3.0 or later
 *
 */

class SpecialResourceRouter extends UnlistedSpecialPage {
	private $mType, $mVariable;

	private $mCacheTime = 86400 * 14;

	public function __construct() {
		parent::__construct( 'ResourceRouter' );
	}

	/**
	 * Main entry point
	 *
	 * @param string $subpage Subpage of SpecialPage
	 */
	public function execute( $subpage ) {
		/**
		 * Subpage works as type param, default is "index"
		 */
		$this->mType = 'index';
		if ( !empty( $subpage ) ) {
			$this->mType = $subpage;
		}

		$t = $this->getRequest()->getText( 'type', '' );
		if ( $t != '' ) {
			$this->print404();
		}

		$this->parseType();
		$this->getFile();

	}

	/**
	 * Set mType
	 */
	private function parseType() {
		/**
		 * Work out what the requested file is.
		 */
		if ( $this->mType == ( "favicon.ico" || "favicon.ico" ) ) {
			$this->mType = 'favicon';
			$this->mVariable = 'wgFavicon';
		} elseif ( preg_match( '/apple-touch-icon(.*)\.png/', $this->mType ) ) {
			$this->mType = 'apple';
			$this->mVariable = 'wgAppleTouchIcon';
		} else {
			$this->print404();
		}
	}

	private function getFile() {
		global $wgUploadPath;

		$this->getOutput()->disable();

		header( 'Cache-Control: max-age=' . $this->mCacheTime );
		header( 'Location: ' . $GLOBALS[$this->mVariable] );
	}

	private function print404() {
		$this->getOutput()->disable();

		header( 'Cache-Control: no-cache' );
		header( 'HTTP/1.0 404 Not Found' );
		echo "404: File doesn't exist";
	}
}
