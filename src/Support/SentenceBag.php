<?php namespace PragmaRX\Glottos\Support;
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

use Countable;
use PragmaRX\Support\Config;
use PragmaRX\Glottos\Support\Sentence;
use PragmaRX\Glottos\Support\Mode;

class SentenceBag implements Countable {

	/**
	 * All of the registered sentences.
	 *
	 * @var array
	 */
	private $sentences = array();

	private $prefix;

	private $suffix;

	private $config;

	/**
	 * Create a new sentence bag instance.
	 *
	 * @param  array  $sentences
	 * @return void
	 */
	public function __construct(Config $config, $paragraph = null, $domain = null)
	{
		$this->config = $config;

		$this->parseParagraph($paragraph, $domain);
	}

	/**
	 * Parse a string or array to get our sentences
	 * 
	 * @param  string/array $sentences
	 * @return void
	 */
	public function parseParagraph($paragraph, $domain = null)
	{
		$this->clear();

		if (trim($paragraph) !== '')
		{
			if (is_string($paragraph))
			{
				$paragraph = $this->explodeParagraph($paragraph);
			}

			foreach ($paragraph as $key => $sentence)
			{
				$this->add( $this->parseSentence($sentence, $domain) );
			}
		}
	}

	/**
	 * Remove prefix, suffix and explode a paragraph in to sentences
	 * 
	 * @param  string $paragraph 
	 * @return array
	 */
	private function explodeParagraph($paragraph)
	{
		$paragraph = $this->parseRemovePrefixAndSuffix($paragraph);

		$sentences = preg_split("/((?<=[.?!]))(\s+(?=[a-z]))/i", $paragraph, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		// Had to tweak this, I need a Regex Guru!
		$keep = '';

		$return = array();

		foreach ($sentences as $sentence)
		{
			if (trim($sentence) == '')
			{
				$keep = $sentence;
			}
			else
			{
				$return[] = $keep . $sentence;
				$keep = '';
			}
		}

		return $return;
	}

	/**
	 * Parse paragraph to remove and keep prefix and suffix
	 * 
	 * @param  string $paragraph 
	 * @return string
	 */
	private function parseRemovePrefixAndSuffix($paragraph)
	{
		SentenceParser::parse($paragraph, $this->prefix, $this->suffix, $this->config);

		return $paragraph;
	}

	/**
	 * Transform a message into a sentence
	 * 
	 * @param  string $sentence
	 * @return array
	 */
	private function parseSentence($sentence, $domain = null)
	{
		return new Sentence($sentence, $domain, new Mode($this->config->get('mode')), $this->config);
	}

	/**
	 * Clear the sentences bag
	 * 
	 * @return void
	 */
	public function clear()
	{
		$this->prefix = '';

		$this->suffix = '';

		$this->sentences = array();
	}

	/**
	 * Add a sentence to the bag
	 *
	 * @param  string  $sentence
	 * @return string
	 */
	public function add($sentence)
	{
		return $this->sentences[] = $sentence;
	}

	/**
	 * Get a particular sentence from the bag
	 *
	 * @param  string  $key
	 * @return string
	 */
	public function get($key)
	{
		if (array_key_exists($key, $this->sentences))
		{
			return $this->sentences[$key];
		}

		return '';
	}

	/**
	 * Replace a particular sentence
	 *
	 * @param  string  $key
	 * @return string
	 */
	public function put($key, $sentence)
	{
		if (array_key_exists($key, $this->sentences))
		{
			$sentence = $this->parseSentence($sentence);

			$this->sentences[$key] = $sentence;
		}
	}

	/**
	 * Get all of the sentences from the bag.
	 *
	 * @param  string  $format
	 * @return array
	 */
	public function all()
	{
		return $this->sentences;
	}

	/**
	 * Get the sentences for the instance.
	 *
	 * @return \PragmaRX\Support\SentenceBag
	 */
	public function getSentenceBag()
	{
		return $this;
	}

	/**
	 * Determine if the sentence bag has any sentences.
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		return ! $this->any();
	}

	/**
	 * Determine if the sentence bag has any sentences.
	 *
	 * @return bool
	 */
	public function any()
	{
		return $this->count() > 0;
	}

	/**
	 * Get the number of sentences in the container.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->sentences);
	}

	/**
	 * Join the sentences back in its paragraph form
	 * 
	 * @return string
	 */
	public function getParagraph()	
	{
		return $this->joinSentences('sentence');
	}

	/**
	 * Join the sentences back in its paragraph form
	 * 
	 * @return string
	 */
	public function getTranslatedParagraph()	
	{
		return $this->joinSentences('translation');
	}

	/**
	 * Join the array of sentences into a string of sentences
	 * 
	 * @return string
	 */
	public function joinSentences($property = 'sentence')
	{
		return $this->prefix . $this->implodeSentences($property) . $this->suffix;
	}

	/**
	 * Join all sentences to recreate the paragraph
	 * 
	 * @param  string $property 
	 * @return string           
	 */
	public function implodeSentences($property)
	{
		$paragraph = '';

		foreach ($this->sentences as $key => $sentence)
		{
			$paragraph .= $sentence->getProperty($property, true);
		}

		return $paragraph;
	}
}
