<?php declare(strict_types=1);
/*
** Zabbix
** Copyright (C) 2001-2020 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


/**
 * Class to normalize incoming data.
 */
class CImportDataNormalizer {

	protected $rules;

	const EOL_LF = 0x01;

	public function __construct(array $schema) {
		$this->rules = $schema;
	}

	public function normalize($data) {
		$data['zabbix_export'] = $this->normalizeArrayKeys($data['zabbix_export']);
		$data['zabbix_export'] = $this->normalizeStrings($data['zabbix_export']);

		return $data;
	}

	/**
	 * Convert array keys to numeric.
	 *
	 * @param mixed $data   Import data.
	 *
	 * @return array
	 */
	protected function normalizeArrayKeys($data) {
		if (!is_array($data)) {
			return $data;
		}

		if ($this->rules['type'] & XML_ARRAY) {
			foreach ($this->rules['rules'] as $tag => $tag_rules) {
				if (array_key_exists('ex_rules', $tag_rules)) {
					$tag_rules = call_user_func($tag_rules['ex_rules'], $data);
				}

				if (array_key_exists($tag, $data)) {
					$data[$tag] = $this->normalizeArrayKeys($data[$tag], $tag_rules);
				}
			}
		}
		elseif ($this->rules['type'] & XML_INDEXED_ARRAY) {
			$prefix = $this->rules['prefix'];

			foreach ($data as $tag => $value) {
				$data[$tag] = $this->normalizeArrayKeys($value, $this->rules['rules'][$prefix]);
			}

			$data = array_values($data);
		}

		return $data;
	}

	/**
	 * Add CR to string type fields.
	 *
	 * @param mixed $data   Import data.
	 *
	 * @return mixed
	 */
	protected function normalizeStrings($data) {
		if ($this->rules['type'] & XML_STRING) {
			$data = str_replace("\r\n", "\n", $data);
			$data = (array_key_exists('flags', $this->rules) && $this->rules['flags'] & self::EOL_LF)
				? str_replace("\n", "\r\n", $data)
				: $data;
		}

		return $data;
	}
}
