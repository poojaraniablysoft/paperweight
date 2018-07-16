<?php 
class Utilities { 
		
	public static function getLabel( $key, $namespace = '' ) { 
		if ( $key == '' ) return;
		
		global $lang_array;
		
		$key_original 	= $key;
		$key 			= strtoupper( $key );
		if ( isset( $lang_array[ $key ] ) ) return $lang_array[ $key ];
	
		$db		= &Syspage::getdb();
		$val	= '';
		/* $rs		= $db->query( "SELECT * FROM tbl_language_labels WHERE label_key = " . $db->quoteVariable( $key ) . " AND label_lang_id = " . $db->quoteVariable( CONF_DEFAULT_LANG_ID ) ); */
		$rs		= $db->query( "SELECT * FROM tbl_language_labels WHERE label_key = " . $db->quoteVariable( $key ) );
		
		if ( $db->total_records( $rs ) > 0 != false ) { 
			$row = $db->fetch( $rs );
			$val = $row[ 'label_caption' ];
		} else {
			
			$arr = explode( '_', $key_original );
			array_shift( $arr );
			$val = implode(' ', $arr);
				
			/* foreach( CONF_ALL_LANGUAGES as $lang_id => $lang_name ) {  */
				$db->insert_from_array( 'tbl_language_labels', array( 
					'label_key'		=> $key,
					/* 'label_lang_id' => $lang_id, */
					'label_caption' => $val,
					) );
			/* } */
			
		}

		return $lang_array[ $key ] = self::strip_javascript( $val );

	}
	
	public static function strip_javascript( $content = '' ) { 
		$javascript	= '/<script[^>]*?>.*?<\/script>/si';
		$noscript	= '';
		return preg_replace( $javascript, $noscript, $content );
	}
		
}