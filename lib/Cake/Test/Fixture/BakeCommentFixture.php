<?php
/**
 * BakeCommentFixture
 *
 * PHP 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.Test.Fixture
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * BakeCommentFixture fixture for testing bake
 *
 * @package       Cake.Test.Fixture
 */
class BakeCommentFixture extends CakeTestFixture {

/**
 * name property
 *
 * @var string 'Comment'
 */
	public $name = 'BakeComment';

/**
 * fields property
 *
 * @var array
 */
	public $fields = array(
		'otherid' => array('type' => 'integer', 'key' => 'primary'),
		'bake_article_id' => array('type' => 'integer', 'null' => false),
		'bake_user_id' => array('type' => 'integer', 'null' => false),
		'comment' => 'text',
		'published' => array('type' => 'string', 'length' => 1, 'default' => 'N'),
		'created' => 'datetime',
		'updated' => 'datetime'
	);

/**
 * records property
 *
 * @var array
 */
	public $records = array();
}
