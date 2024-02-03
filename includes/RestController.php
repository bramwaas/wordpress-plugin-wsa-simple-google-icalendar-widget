<?php
/**
 * a simple wordpress REST Controller for the simple-ical-block.
 * @copyright Copyright (C) 2024 - 2024 Bram Waasdorp. All rights reserved.
 * @license GNU General Public License version 3 or later
 *
 * 2.3.0 
 */
namespace WaasdorpSoekhan\WP\Plugin\SimpleGoogleIcalendarWidget;

use \WP_REST_Controller as WP_REST_Controller;
use \WP_Error as WP_Error;
use \WP_REST_Response as WP_REST_Response;
use \WP_REST_Server as WP_REST_Server;

class RestController extends WP_REST_Controller {
    /**
     * Register the routes for the objects of the controller.
     */
    /**
     * Instance container.
     *
     * @var    static
     * @since  2.3.0
     */
    protected static $instance;
    /**
     * Constructor.
     *
     * initial values ​​for $namespace string, $rest_base string defined in extended class WP_REST_Controller
     *    their is also a variable $schema array defined to cache results of the schema.
     *
     * @return  void  ($this RestController object)
     *
     * @since       2.3.0
     */
    public function __construct()
    {
        $this->namespace = 'simple-google-icalendar-widget/';
        $this->rest_base = 'content-by-';
    }
    
    /**
     * Instantiate class and register routes.
     *
     * @return  void
     *
     * @since       2.3.0
     *
     */
    public static function init_and_register_routes() {
        self::getInstance();
        self::$instance->register_routes();
    }
    
    /**
     * Register routes.
     *  the 'schema' in a rest route equates to an OPTIONS request.

     * @return  void
     *
     * @since       2.3.0
     *
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/v1/' . $this->rest_base .'ids', array(
            array(
                'methods'             => 'GET, POST',
                'callback'            => array( $this, 'get_content_by_ids' ),
                'permission_callback' => array( $this,'get_block_content_permissions_check'),
                'args'                => array(
                   'blockid' => array(
                       'default' => '',
                   ),
                    'postid' => array(
                        'default' => '',
                    ),
                    'tzid_ui' => array(
                        'default' => null,
                    ),
                ),
            ),     
            'schema' => array ($this, 'get_content_by_ids_schema'),
        ) );
        register_rest_route( $this->namespace, '/v1/' . $this->rest_base . 'ids/schema', array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => array( $this, 'get_content_by_ids_schema' ),
        ) );
        register_rest_route( $this->namespace, '/v1/' . $this->rest_base .'attributes', array(
            array(
                'methods'             => 'GET, POST',
                'callback'            => array( $this, 'get_content_by_attributes' ),
                'permission_callback' => array( $this,'get_block_content_permissions_check'),
                'args'                => array(
                    'calendar_id' => array(
                        'default' => '#example',
                    ),
                ),
            ),
            'schema' => array ($this, 'get_content_by_attributes_schema'),
        ) );
        register_rest_route( $this->namespace, '/v1/' . $this->rest_base . 'attributes/schema', array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => array( $this, 'get_content_by_attributes_schema' ),
        ) );
    }

    /**
     * Get block content wth blockid, postid, and client timezone from request
     *
     * @param WP_REST_Request $request
     *            Full data about the request.
     * @return WP_Error|WP_REST_Response $since 2.3.0
     */
    public function get_content_by_ids($request)
    {
        // get parameters from request
        $params = $request->get_params();
        $transientId = 'sib-r-' . $params['blockid'];
         if (false === ($block_attributes = get_transient($transientId))) {
//            return new WP_Error( '404', __( "Attributes not found in transient", 'simple-google-icalendar-widget' ) );
            $content = "<p>Attributes not found in transient</p>";
            
            $data = $this->prepare_item_for_response([
                'content' => $content,
                'params' => $params
            ], $request);
            /*
             * $parser = new IcsParser($instance['calendar_id'], $instance['cache_time'], $instance['event_period'], $instance['tzid_ui'] );
             * $data = $parser->fetch( );
             * // do not cache data if fetching failed
             * if ($data) {
             * set_transient($transientId, $data, $instance['cache_time']*60);
             * }
             */
        } else {
            $block_attributes = wp_parse_args((array) $params, $block_attributes);
//            $block_attributes['period_limits'] = $block_attributes['period_limits'] % 4;
            $block_attributes['rest_end'] = true;
            $content = SimpleicalBlock::render_block($block_attributes, []);
            $data = $this->prepare_item_for_response([
                'content' => $content,
                'params' => $params
            ], $request);
        }
        // return a response or error based on some conditional
        if (isset($data)) {
            return new WP_REST_Response($data, 200);
        } else {
            return new WP_Error( '404', __( 'Not possible to get block content', 'simple-google-icalendar-widget' ) );
        }
    }
    /**
     * Get block content with attributes from request.
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     * $since 2.3.0
     */
    public function get_content_by_attributes( $request ) {
        //get parameters from request
        $params = $request->get_params();
        $content = SimpleicalBlock::render_block($params,[]);
        $data = $this->prepare_item_for_response( ['content' => $content, 'params' => $params], $request );
        
        //return a response or error based on some conditional
        if (isset($data)) {
            return new WP_REST_Response($data, 200);
        } else {
            return new WP_Error( '404', __( 'Not possible to get block content', 'simple-google-icalendar-widget' ) );
        }
    }
    /**
     * Get schema for block_content.
     *
     * @return array The schema
     * 
     */
    public function get_content_by_ids_schema() {
        if ( $this->schema ) {
            // Since WordPress 5.3, the schema can be cached in the $schema property.
            return $this->schema;
        }
        
        $this->schema = array(
            // This tells the spec of JSON Schema we are using which is draft 4.
            '$schema'              => 'http://json-schema.org/draft-04/schema#',
            // The title property marks the identity of the resource.
            'title'                => 'block-content',
            'type'                 => 'object',
            // In JSON Schema you can specify object properties in the properties attribute.
            'properties'           => array(
                'content' => array(
                    'description'  => esc_html__( 'The content for the block.', 'simple-google-icalendar-widget' ),
                    'type'         => 'string',
                    'context'      => array( 'view', 'edit', 'embed' ),
                    'readonly'     => true,
                ),
                'params' => array(
                    'description'  => esc_html__( 'The parameters used.', 'simple-google-icalendar-widget' ),
                    'type'         => 'array',
                    'context'      => array( 'view' ),
                    'readonly'     => true,
                ),
            ),
        );
        
        return $this->schema;
    }
    /**
     * Get schema for block_content.
     *
     * @return array The schema
     *
     */
    public function get_content_by_attributes_schema() {
        if ( $this->schema ) {
            // Since WordPress 5.3, the schema can be cached in the $schema property.
            return $this->schema;
        }
        
        $this->schema = array(
            // This tells the spec of JSON Schema we are using which is draft 4.
            '$schema'              => 'http://json-schema.org/draft-04/schema#',
            // The title property marks the identity of the resource.
            'title'                => 'block-content',
            'type'                 => 'object',
            // In JSON Schema you can specify object properties in the properties attribute.
            'properties'           => array(
                'content' => array(
                    'description'  => esc_html__( 'The content for the block.', 'simple-google-icalendar-widget' ),
                    'type'         => 'string',
                    'context'      => array( 'view', 'edit', 'embed' ),
                    'readonly'     => true,
                ),
                'params' => array(
                    'description'  => esc_html__( 'The parameters used.', 'simple-google-icalendar-widget' ),
                    'type'         => 'array',
                    'context'      => array( 'view' ),
                    'readonly'     => true,
                ),
            ),
        );
        
        return $this->schema;
    }
    
    
    /**
     * Check if a given request has access to block_content
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     * @since 2.3.0
     */
    public function get_block_content_permissions_check( $request ) {
        //return true; <--use to make readable by all
        return true;
        return current_user_can( 'edit_something' );
    }
    
    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check( $request ) {
        //return true; <--use to make readable by all
        return true;
        return current_user_can( 'edit_something' );
    }
    
    /**
     * Check if a given request has access to get a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_item_permissions_check( $request ) {
        return $this->get_items_permissions_check( $request );
    }
    
    /**
     * Check if a given request has access to create items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function create_item_permissions_check( $request ) {
        return current_user_can( 'edit_something' );
    }
    
    /**
     * Check if a given request has access to update a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function update_item_permissions_check( $request ) {
        return $this->create_item_permissions_check( $request );
    }
    
    /**
     * Check if a given request has access to delete a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function delete_item_permissions_check( $request ) {
        return $this->create_item_permissions_check( $request );
    }
    
    /**
     * Prepare the item for create or update operation
     *
     * @param WP_REST_Request $request Request object
     * @return WP_Error|object $prepared_item
     */
    protected function prepare_item_for_database( $request ) {
        return array();
    }
    
    /**
     * Prepare the item for the REST response
     *
     * @param mixed $item WordPress representation of the item.
     * @param WP_REST_Request $request Request object.
     * @return mixed
     */
    public function prepare_item_for_response( $item, $request ) {
        return $item;
        return array();
    }
    
    /**
     * Get the query params for collections
     *
     * @return array
     */
    public function get_collection_params() {
        return array(
            'page'     => array(
                'description'       => 'Current page of the collection.',
                'type'              => 'integer',
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ),
            'per_page' => array(
                'description'       => 'Maximum number of items to be returned in result set.',
                'type'              => 'integer',
                'default'           => 10,
                'sanitize_callback' => 'absint',
            ),
            'search'   => array(
                'description'       => 'Limit results to those matching a string.',
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
    }


    /**
     * Method to get a singleton controller instance.
     *
     * @return  static
     *
     * @since       2.3.0
     *
     */
    public static function getInstance()
    {
        if (\is_object(self::$instance)) {
            return self::$instance;
        }
            self::$instance = new RestController;
        return self::$instance;
    }
    
}