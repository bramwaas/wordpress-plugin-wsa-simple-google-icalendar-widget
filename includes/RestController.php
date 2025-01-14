<?php
/**
 * a simple wordpress REST Controller for the simple-ical-block.
 * @copyright Copyright (C) 2024 - 2025 Bram Waasdorp. All rights reserved.
 * @license GNU General Public License version 3 or later
 *
 * 2.6.0 
 * 2.4.1 adressed Notice: register_rest_route was called <strong>incorrectly</strong>. Namespace must not start or end with a slash.
 *  and added 'permission_callback' => '__return_true', for public routes.
 * 2.4.4 add all (non default) attributes to returned params 'get_content_by_ids';
 *  add attribute tag_title (default h3); remove calendar_id from returned params.
 *  when saved attributes are not found and calendar_id is present in params use params as attributes
 *  2.6.0 SimpleicalBlock => SimpleicalHelper 
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
     * Prefix for name option with widget attributes/instance 
     *
     * @var    static
     * @since  2.3.0
     */
    protected static $sib_attr_pf;
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
     * initial values for $namespace string, $rest_base string defined in extended class WP_REST_Controller
     *    their is also a variable $schema array defined to cache results of the schema. 
     *    2.4.1 removed end slash
     *
     * @return  void  ($this RestController object)
     *
     * @since       2.3.0
     */
    public function __construct()
    {
        $this->namespace = 'simple-google-icalendar-widget';
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
        register_rest_route( $this->namespace, '/v1/content-by-ids', array(
            array(
                'methods'             => 'GET, POST',
                'callback'            => array( $this, 'get_content_by_ids' ),
                'permission_callback' => '__return_true',
                'args'                => array(
                   'sibid' => array(
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
            'permission_callback' => '__return_true'
        ) );
        register_rest_route( $this->namespace, '/v1/content-by-ids/schema', array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => array( $this, 'get_content_by_ids_schema' ),
            'permission_callback' => '__return_true'
        ) );
//        
        register_rest_route( $this->namespace, '/v1/set-sib-attrs', array(
            array(
                'methods'             => 'GET, POST',
                'callback'            => array( $this, 'set_sib_attrs' ),
                'permission_callback' => array( $this,'set_sib_attrs_permissions_check'),
                'args'                => array(
                    'sibid' => [],
                    'prev_sib'   => []
                )
            ),
            'schema' => array(
                $this,
                'set_sib_attrs_schema'
            ),
            'permission_callback' => '__return_true'
        ));
        register_rest_route($this->namespace, '/v1/set-sib-attrs/schema', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array(
                $this,
                'set_sib_attrs_schema'
            ),
            'permission_callback' => '__return_true' 
            
        ));
    }

    /**
     * Get block content with sibid or complete set of attributes including calendar_id,  and client timezone from request
     *
     * @param WP_REST_Request $request
     *            Full data about the request.
     * @return WP_Error|WP_REST_Response $since 2.3.0
     */
    public function get_content_by_ids($request)
    {
        // get parameters from request
        $params = $request->get_params();
        if (empty($params['sibid'])) {return new WP_Error('404', __('Empty sibid. Not possible to get block content', 'simple-google-icalendar-widget'));}
        else {$baa = get_option(SimpleicalHelper::SIB_ATTR);
            $block_attributes = isset($baa[$params['sibid']]) ? $baa[$params['sibid']] : [];}
        if (empty($block_attributes) && empty($params['calendar_id'])) {
                $content = '<p>' . __('Settings not found in saved option', 'simple-google-icalendar-widget') . '<br>' .
                __('Not possible to get block content', 'simple-google-icalendar-widget') . '</p>';
        } else {
        $block_attributes = array_merge($block_attributes, $params);
        $content = SimpleicalHelper::render_block($block_attributes, []);
        unset($block_attributes['calendar_id']);
        }
        $data = $this->prepare_item_for_response([
                'content' => $content,
                'params' => $block_attributes
            ], $request);
        if (isset($data)) {
            return new WP_REST_Response($data, 200);
        } else {
            $data = $this->prepare_item_for_response([
                'content' => '<p>' . __('Not possible to get block content', 'simple-google-icalendar-widget') .'</p>',
                'params' => $params
            ], $request);
            return new WP_REST_Response($data, 404);
        }
    }
    /**
     * Set attributes in option.
     *
     * @param WP_REST_Request $request attributes to save with $params['sibid'] as key.
     * @return WP_Error|WP_REST_Response (when a change is made response.content = $params['sibid'] else false or 'FALSE')
     * $since 2.3.0
     * example .../wp-json/simple-google-icalendar-widget/v1/set-sib-attrs?sibid=b123&test=xyz&prev_sibid=w234
     */
    public function set_sib_attrs( $request ) {
        //get parameters from request
        $params = $request->get_params();
        $content = SimpleicalHelper::update_rest_attrs($params);
        $data = $this->prepare_item_for_response( ['content' => $content, 'params' => $params], $request );
        //return a response or error based on some conditional
        if (isset($data)) {
            return new WP_REST_Response($data, 200);
        } else {
            $data = $this->prepare_item_for_response([
                'content' => 'FALSE',
                'params' => $params
            ], $request);
            return new WP_REST_Response($data, 404);
        }
    }
    /**
     * Get schema for block_content.
     *
     * @return array The schema
     * 
     */
    public function get_content_by_ids_schema() {
        if ( !empty($this->get_content_by_ids_schema) ) {
            // Since WordPress 5.3, the schema can be cached in the $schema property.
            return $this->get_content_by_ids_schema;
        }
        
        $this->get_content_by_ids_schema = array(
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
        
        return $this->get_content_by_ids_schema;
    }
    /**
     * Get schema for set_sib_attrs_schema.
     *
     * @return array The schema
     *
     */
    public function set_sib_attrs_schema() {
        if ( $this->set_sib_attrs_schema ) {
            // Since WordPress 5.3, the schema can be cached in the $schema property.
            return $this->set_sib_attrs_schema;
        }
        
        $this->set_sib_attrs_schema = array(
            // This tells the spec of JSON Schema we are using which is draft 4.
            '$schema'              => 'http://json-schema.org/draft-04/schema#',
            // The title property marks the identity of the resource.
            'title'                => 'set_sib_attrs',
            'type'                 => 'object',
            // In JSON Schema you can specify object properties in the properties attribute.
            'properties'           => array(
                'content' => array(
                    'description'  => esc_html__( 'The result of the action.', 'simple-google-icalendar-widget' ),
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
        
        return $this->set_sib_attrs_schema;
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
    public function set_sib_attrs_permissions_check( $request ) {
        //return true; <--use to make readable by all
        return current_user_can( 'edit_others_posts' );
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
    /**
     * Method to recursively attributes from multidimensional array of blocks.
     *
     * @param array $blocks blocks haystack
     * @param string $bid needle 1 attr['sibid']
     * @param string $bname  needle 2 name of block
     * $params int $depth depth of  recursion.
     * @return  array $attributes or false
     *
     * @since       2.3.0
     *
     */
    public static function find_block_attributes($blocks, $bid, $bname, $depth = 10 )
    {
        $depth = $depth - 1;
        foreach ($blocks as $block){
            if (!empty($block['blockName']) && $block['blockName'] == $bname 
                && !empty($block['attrs']['sibid']) && $block['attrs']['sibid'] == $bid ) {
            //found
                  return $block['attrs'];
            }
             if (0 < $depth && !empty($block['innerBlocks'])) {
                //maybe in innerblock.
                 $result = self::find_block_attributes($block['innerBlocks'], $bid, $bname, $depth );
                if (false !== $result) return $result;
            } 
        }
        return false;
    }
}