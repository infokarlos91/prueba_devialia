<?php
/*
*Plugin Name: Prueba Entrevista
*Plugin URI: http://laurldelplugin.aqui
*Description: Plugin desarrollado por Carlos, al activar este plugin se creará un custom post type Puntos de venta , donde se podrán añadir post de dicho tipo. Por otro lado, en la sección de Ajustes > Aparecerá otra opción de ajustes Shortcode Carlos donde podréis configurar y ver el shortcode. El shortcode es [carrito_shortcode] , pero si le añadis atributos donde lo publiquéis se saltará la configuración por defecto. 
*Author: Carlos García-Blanco Sardinero 677895751 carargan91@hotmail.com
*Version: 1.0
*Author URI: http://laurldeldesarrolladordelplugin.aqui
 */
function prueba_style() {
	wp_enqueue_style( 'pruebastyle', plugin_dir_url( __FILE__ ) . 'css/adminstyle.css');
	wp_register_script('more_script', plugins_url('/js/admin.js', __FILE__),array('jquery'));
	wp_localize_script('more_script', 'admin_url', array('ajax_url' => admin_url('admin-ajax.php')));
	wp_enqueue_script('more_script');
}
add_action( 'admin_enqueue_scripts', 'prueba_style' );
	
function prueba_src() {
	wp_enqueue_style( 'pruebastyle', plugin_dir_url( __FILE__ ) . 'css/style.css');
	wp_localize_script('codigo_js', 'admin_url', array('ajax_url' => admin_url('admin-ajax.php')));
	wp_enqueue_script('codigo_js');
}
add_action( 'wp_enqueue_scripts', 'prueba_src' );

function function_carrito_prueba( $atts ) {
	$defaults = array("hover" => "");
	$atts = shortcode_atts( $defaults, $atts );
	$icon_html = "";
	$icon_html = '<i class="fa fa-shopping-cart"></i> ';
	$cart_count = "";
	$html="";
	if ( class_exists( "WooCommerce" ) ) {
		$numero_productos = " (" . WC()->cart->get_cart_contents_count() . ")";
		if($atts['hover'] == ""){
			$atts['hover'] = get_option( 'producto_on_hover' );
		}
		
		if("true" == $atts['hover']){
			$items = WC()->cart->get_cart();
			$tooltip="";
			foreach($items as $item => $values) { 
				$producto =  wc_get_product( $values['data']->get_id() );
				$tooltip.="<b>".$producto->get_title() .'</b>  <br> Cantidad: '.$values['quantity'].'<br>'; 
				$precio = get_post_meta($values['product_id'] , '_price', true);
				$tooltip.="  Precio: ".$precio."€<br>";
			}
			$show_tooltip = '<span class="tooltiptext">'.$tooltip.'</span>';
		}else{
			$show_tooltip = "";
		}
		$html  = '<div class="tooltip">
		<i class="fa fa-shopping-cart"></i>' . $numero_productos.$show_tooltip.'
		</div>';		
	}
	return $html;
}
add_shortcode( "carrito_shortcode", "function_carrito_prueba" );

function register_cpt_venta() {
    $args = array(
        'label'                 => 'Puntos de Venta',
        'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'revisions' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'has_archive'           => false,       
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'puntodeventa', $args );
}
add_action( 'init', 'register_cpt_venta', 0 );

function register_cpt_venta_sub(){
 add_submenu_page( 'edit.php?post_type=puntodeventa', 'Opciones', 'Opciones','manage_options', 'edit.php?post_type=puntodeventa');}

add_action( 'admin_menu', 'register_cpt_venta_sub' );



class MisAjustes{
    private $en_hover;
    public function __construct(){
        add_action( 'admin_menu', array( $this, 'add_opciones' ) );
    }
    public function add_opciones(){
        add_options_page('Ajustes Shortcode Carlos','AJUSTES SHORTCODE CARLOS','manage_options','ajustes_shortcode_carlos',array( $this, 'pagina_opciones' ));
    }
    public function pagina_opciones(){
        $this->en_hover = get_option( 'producto_on_hover' );
        ?>
        <div class="wrap">
            <h1>Opciones Shortcode Carlos</h1>
			<label>
				<input type="checkbox" id="cboxhover" value="" <?php if($this->en_hover == "true"){echo 'checked';}else{echo'';}?>>El shortcode muestra productos en hover.
			</label><br>
			[<span class="shortcode_final">carrito_shortcode<?php if($this->en_hover == "true"){echo ' hover="true"';}else{echo '';}?></span>]
			<br>
			<span class="save_default_shortcode">Guardar acciones por defecto</span>
        </div>
        <?php
    }
   
}

if( is_admin()){  $mis_ajustes = new MisAjustes();}


add_action('wp_ajax_actualizar_opciones_shortcode', 'actualizar_opciones_shortcode');
add_action('wp_ajax_nopriv_actualizar_opciones_shortcode', 'actualizar_opciones_shortcode');
function actualizar_opciones_shortcode(){
	$en_hover = esc_attr($_POST['en_hover']);
	update_option( 'producto_on_hover', $en_hover );
	wp_die();
}

?>
