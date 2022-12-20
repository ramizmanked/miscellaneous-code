<?php

namespace NWMLS;

class Import_Posts {

    protected $category;
    protected $post_count;
    protected $year;
    protected $month;
    protected $date;
    protected $hours;
    protected $minutes;
    protected $seconds;

    protected $month_by_name = array(
        'january'   => '01',
        'february'  => '02',
        'march'     => '03',
        'april'     => '04',
        'may'       => '05',
        'june'      => '06',
        'july'      => '07',
        'august'    => '08',
        'september' => '09',
        'october'   => '10',
        'november'  => '11',
        'december'  => '12',
    );

    public function __construct() {
        $this->category     = get_option('default_category');
        $this->post_count   = 0;
        $this->date         = date('d', strtotime('-1 day', date('now') ) );
        $this->hours        = rand( 0, 23 );
        $this->minutes      = rand( 0, 59 );
        $this->seconds      = rand( 0, 59 );
    }

	/**
	 * @param $args
	 * @param $assoc_args
	 *
	 * @throws \Exception
	 */
    public function __invoke( $args, $assoc_args ) {

		/*if ( ! isset( $assoc_args['site'] ) || empty( $assoc_args['site'] ) ) {
			WP_CLI::error( 'Site missing! Please provide site name.' );
		}*/

        $startTime  = microtime( true );

        $this->getDirContents( NWMLS_CUSTOM_MIGRATIONS_CONTENT_PATH );

        WP_CLI::log("Alright! $this->post_count new posts added in last run.");

        // Calculate Time
        $endTime     = microtime( true );
        $diff        = round( $endTime - $startTime );
        $hours       = (int) ( $diff / 60 / 60 );
        $minutes     = (int) ( $diff / 60 ) - $hours * 60;
        $seconds     = (int) $diff - $hours * 60 * 60 - $minutes * 60;
        $time_taken  = PHP_EOL . 'Migration completed in: ';
        $time_taken .= ( 0 === $hours ) ? '' : $hours . ' hours ';
        $time_taken .= ( 0 === $minutes ) ? '' : $minutes . ' minutes ';
        $time_taken .= ( 0 === $seconds ) ? '' : $seconds . ' seconds';

        WP_CLI::success( $time_taken );
	}

    public function getDirContents( $dir, &$results = array() ) {
        $files      = scandir( $dir );
        $doc        = new DOMDocument();

        foreach ( $files as $key => $value ) {

            $path = realpath($dir . DIRECTORY_SEPARATOR . $value );

            if ( ! is_dir( $path ) && '.DS_Store' != $value ) {
                if ( 'html' === pathinfo( $value, PATHINFO_EXTENSION ) ) {

                    $doc->loadHTML( file_get_contents( $path ) );
                    $finder = new DomXPath($doc);
                    $nodes = $finder->query("//*[contains(@class, 'content-story')]");

                    try {
                        if ( count( $nodes ) < 1 ) {
                            $post_content = '';
                            throw new Exception('No element found with class `content-story` in "' . $value . '" file, post content is set to null.');
                        }
                        $post_content = $this->DOMInnerHTML( $nodes->item(0 ) );

                    }
                    catch ( Exception $e ) {
                        WP_CLI::warning( $e->getMessage() );
                    }

                    $nodes = $doc->getElementsByTagName('h4');

                    try {
                        if ( count( $nodes ) < 1 ) {
                            $post_title = str_replace('.html', '', str_replace( '-', ' ', $value ) );
                            throw new Exception('No element found with H4 in "' . $value . '" file, post title is set to Filename.' );
                        }
                        $post_title = $this->DOMInnerHTML( $nodes->item(0 ) );
                    }
                    catch ( Exception $e ) {
                        WP_CLI::warning( $e->getMessage() );
                    }

                    if ( empty( $this->year ) && empty( $this->month ) ) {
                        $post_date = date("Y-m-d H:i:s");
                    } else {
                        if ( empty( $this->year ) ) {
                            $this->year = date('Y');
                        }

                        if ( empty( $this->month ) ) {
                            $this->month = date('m');
                        }

                        $post_date = "$this->year-$this->month-$this->date $this->hours:$this->minutes:$this->seconds";
                    }

                    //if( !post_exists( $post_title ) ) {
                        $post_args = array(
                            'post_author'   => 1,
                            'post_content'  => $post_content,
                            'post_title'    => $post_title,
                            'post_status'   => 'publish',
                            'post_category' => array( $this->category ),
                            'post_date'     => $post_date,
                        );

                        $post_id = wp_insert_post( $post_args, true );
                        if ( ! is_wp_error( $post_id ) ) {
                            $this->post_count++;
                            WP_CLI::success('Post having title "' . $post_title . '" inserted successfully with ID: ' . $post_id );
                        } else {
                            WP_CLI::error('There was an error ' . $post_id->get_error_message() . ' while inserting post having title ' . $post_title . '.'  );
                        }
                    // } else {
                    //     WP_CLI::warning( 'Post: ' . $post_title . ' already exists ' );
                    // }
                }

            } else if ( $value != "." && $value != ".." && $value != ".DS_Store" ) {
                $category = get_category_by_slug( strtolower( $value ) );

                if ( $category instanceof WP_Term ) {

                    $this->category = $category->term_id;

                } else if ( preg_match('/^[0-9]{4}$/', $value ) ) {

                    $this->year = $value;

                } else if ( array_key_exists( strtolower( $value ), $this->month_by_name ) ) {

                    $this->month = $this->month_by_name[ strtolower( $value ) ];

                }
                $this->getDirContents( $path, $results );
                //$results[] = $path;
            }
        }
    }

    public function DOMInnerHTML( DOMNode $element ) {
        $innerHTML = "";
        $children  = $element->childNodes;

        foreach ($children as $child) {
            $innerHTML .= $element->ownerDocument->saveHTML($child);
        }

        return $innerHTML;
    }

}
