<?php

class Listify_Customizer_CSS extends Listify_Customizer {

	private static $data = array();

	public function add( $args ) {
		if ( ! isset( $args[ 'selectors' ] ) || ! isset( $args[ 'declarations' ] ) ) {
			return;
		}

		$entry = array();

		$entry['selectors'] = array_map( 'trim', (array) $args['selectors'] );
		$entry['selectors'] = array_unique( $entry['selectors'] );

		$entry['declarations'] = array_map( 'trim', (array) $args['declarations'] );

		if ( isset( $args['media'] ) ) {
			$media = $args['media'];
		} else {
			$media = 'all';
		}

		if ( ! isset( self::$data[ $media ] ) || ! is_array( self::$data[ $media ] ) ) {
			self::$data[ $media ] = array();
		}

		$match = false;

		foreach ( self::$data[ $media ] as $key => $rule ) {
			$diff1 = array_diff( $rule['selectors'], $entry['selectors'] );
			$diff2 = array_diff( $entry['selectors'], $rule['selectors'] );
			if ( empty( $diff1 ) && empty( $diff2 ) ) {
				$match = $key;
				break;
			}
		}

		if ( false === $match ) {
			self::$data[ $media ][] = $entry;
		} else {
			self::$data[ $media ][ $match ]['declarations'] = array_merge( self::$data[ $media ][ $match ]['declarations'], $entry['declarations'] );
		}
	}

	public function output() {
		if ( empty( self::$data ) ) {
			return '';
		}

		// Make sure the 'all' array is first
		if ( isset( self::$data['all'] ) && count( self::$data ) > 1 ) {
			$all = array ( 'all' => self::$data['all'] );
			unset( self::$data['all'] );
			self::$data = array_merge( $all, self::$data);
		}

		$output = '';

		foreach ( self::$data as $query => $ruleset ) {
			if ( 'all' !== $query ) {
				$output .= "\n@media " . $query . '{';
			}

			// Build each rule
			foreach ( $ruleset as $rule ) {
				$output .= $this->parse_selectors( $rule['selectors'] ) . '{';
				$output .= $this->parse_declarations( $rule['declarations'] );
				$output .= '}';
			}

			if ( 'all' !== $query ) {
				$output .= '}';
			}
		}

		wp_add_inline_style( 'listify', $output );
	}

	private function parse_selectors( $selectors ) {
		$selectors = array_map( 'trim', $selectors );
		$output = implode( ',', $selectors );

		return $output;
	}

	private function parse_declarations( $declarations ) {
		$output = '';

		foreach ( $declarations as $propery => $value ) {
			$output .= sprintf( '%1$s:%2$s;', $propery, $value );
		}

		return $output;
	}

	public function darken( $hex, $steps ) {
		$steps = max(-255, min(255, $steps));

		$hex = str_replace('#', '', $hex);

		if (strlen($hex) == 3) {
			$hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
		}

		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));

		$r = max(0,min(255,$r + $steps));
		$g = max(0,min(255,$g + $steps));
		$b = max(0,min(255,$b + $steps));

		$r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
		$g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
		$b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

		return '#'.$r_hex.$g_hex.$b_hex;
	}

}

