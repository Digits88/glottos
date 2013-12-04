<?php namespace PragmaRX\Glottos\Vendor\Laravel\Models;
/**
 * Part of the Glottos package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Glottos
 * @version    1.0.0
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

use Illuminate\Database\Eloquent\Model as Eloquent;

class Translation extends Eloquent {

	protected $table = 'glottos_translations';

	protected $guarded = array();
	
	/**
	 * Message relationship
	 * 
	 * @return BelongsTo
	 */
	public function message()
	{
		return $this->belongsTo('PragmaRX\Glottos\Vendor\Laravel\Models\Message');
	}

}