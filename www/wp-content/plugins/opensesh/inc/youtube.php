<?php

class OpenSesh_Youtube {

	public function __construct() {

	}

	//format=5 = embed only and format=1,6 is mobile only
	public static function search( $search, $max_results = 24, $start_index = 1 ) {
		$search = sanitize_text_field( $search );
		$search = urlencode( $search );
		$url    = 'https://gdata.youtube.com/feeds/api/videos?q=' . $search . '&max-results=' . $max_results . '&start-index=' . $start_index . '&format=1,5,6&v=2&alt=jsonc';

		$response = wp_remote_get( $url );
		$data     = json_decode( wp_remote_retrieve_body( $response ) );

		return self::normalize( $data );
	}


	private static function normalize( $data ) {
		$response = array( 'total' => 0, 'items' => array() );

		if( ! empty( $data ) && ! isset( $data->error ) && isset( $data->data->items ) ) {
			$response['total']       = $data->data->totalItems;
			$response['start_index'] = $data->data->startIndex;
			$items                   = $data->data->items;

			foreach( $items as $item ) {
				array_push( $response['items'], array(
					'id'        => $item->id,
					'title'     => $item->title,
					'thumbnail' => array( 'normal' => $item->thumbnail->sqDefault, 'high' => $item->thumbnail->hqDefault ),
					'duration'  => $item->duration,
					'mobile'    => isset( $item->player->mobile )
				) );
			}
		}

		return $response;
	}
}