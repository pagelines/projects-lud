<?php
/*
	Section: Projects Lud
	Author: bestrag
	Version: 1.1
	Author URI: http://bestrag.net
	Demo: http://bestrag.net/projects_lud/
	Description: Custom Post Type Section for displaying Projects/Portfolio
	Class Name: ProjectsLud
	Filter: component
*/

class ProjectsLud extends PageLinesSection {
	var $lud_opts		= array();
	var $multiple_up	= 'Projects';
	var $multiple		= 'projects';
	var $single_up		= 'Project';
	var $single		= 'project';
	var $prefix		= 'proj';
	var $taxID		= 'project-sets';
	var $section_id		= 'projects-lud';
	var $default_template	= 'default';
	var $temp_meta	= array();

	/* section_styles */
	function section_scripts() {
		wp_enqueue_script( 'jquery-masonry', array( 'jquery' ) );
		wp_enqueue_script( 'jquery-fred', $this->base_url.'/min.caroufredsel.js', array( 'jquery' ), true );
		wp_enqueue_script( 'jquery-colorbox', $this->base_url.'/jquery.colorbox-min.js', array( 'jquery' ), true );
		wp_enqueue_script( 'jquery-ludloop', $this->base_url.'/jquery.ludloop.js', array( 'jquery' ), true );
	}

	/* clone specific styles */
	function section_styles(){
		$colors=array(
			'templatebg'		=> array('.'.$this->prefix.'-container','#'.$this->opt('templatebg'), 'background-color'),
			'singlebg'		=> array('.'.$this->prefix.'-item-inner','#'.$this->opt('singlebg'), 'background-color'),
			'groupbg'		=> array('.'.$this->prefix.'-group','#'.$this->opt('groupbg'), 'background-color'),
			'imgbg'			=> array('.'.$this->prefix.'-feature','#'.$this->opt('imgbg'), 'background-color'),
			'title_color'		=> array('.'.$this->prefix.'-post_title','#'.$this->opt('title_color'), 'color'),
			'content_color'		=> array('.'.$this->prefix.'-post_content','#'.$this->opt('content_color'), 'color'),
			'excerpt_color'		=> array('.'.$this->prefix.'-post_excerpt','#'.$this->opt('content_color'), 'color'),
			'client_color'		=> array('.'.$this->prefix.'-client_name','#'.$this->opt('client_color'), 'color'),
			'slogan_color'		=> array('.'.$this->prefix.'-project_slogan','#'.$this->opt('slogan_color'), 'color'),
			'partner_color'		=> array('.'.$this->prefix.'-partner','#'.$this->opt('partner_color'), 'color'),
			'custom1_color'		=> array('.'.$this->prefix.'-custom_text1','#'.$this->opt('custom1_color'), 'color'),
			'custom2_color'		=> array('.'.$this->prefix.'-custom_text2','#'.$this->opt('custom2_color'), 'color'),
			'arrow_colorL'		=> array('.'.$this->prefix.'-prev a','#'.$this->opt('arrow_color'), 'color'),
			'arrow_colorR'		=> array('.'.$this->prefix.'-next a','#'.$this->opt('arrow_color'), 'color'),
			'arrow_size'		=> array('.'.$this->prefix.'-prev, .'.$this->prefix.'-next', $this->opt('arrow_size').'px', 'font-size'),
			'pager_color'		=> array('.'.$this->prefix.'-pager span','#'.$this->opt('pager_color'), 'color'),
			'pageractive_color'	=> array('.'.$this->prefix.'-pager a.selected span','#'.$this->opt('pageractive_color'), 'color'),
		);

		$css_code = '';
		foreach ($colors as $key => $value) {
			if($value[1] && $value[1] !== '#' && $value[1] !== 'px' ){
				$css_code .= sprintf('#%4$s%5$s %1$s{%2$s:%3$s;}', $value[0], $value[2], $value[1], $this->section_id, $this->meta['clone']);
			}
		}
		if($this->opt('cbxbg')) $css_code .= sprintf( 'body #cboxOverlay{background-color:#%1$s} body #cboxLoadedContent{border-color:#%1$s}',$this->opt('cbxbg'));
		if ($css_code) {
			$lud_style = sprintf('<style type="text/css" id="%1$s-custom-%2$s">%3$s</style>', $this->prefix, $this->meta['clone'], $css_code);
			echo $lud_style;
		}
	}

	/* section_head */
	function section_head() {
		$clone_id = $this->meta['clone'];
		if( $this->opt('opt_set_select') ) {
			$this->update_lud_settings($this->opt('opt_set_select'));
			if( array_key_exists($clone_id, $this->temp_meta) )$this->meta['set'] = wp_parse_args( $this->temp_meta[$clone_id], $this->meta['set'] );
		}
		//less template
		$this->lud_opts['template_name']	= ( $this->opt( 'template_name' ) ) ? $this->opt( 'template_name' ) : $this->default_template;
		//text style and weight
		$this->lud_opts['text_italic']	= ( $this->opt( 'text_italic' ) ) ? 'italic' : 'normal' ;
		$this->lud_opts['text_bold']	= ( $this->opt( 'text_bold' ) ) ? 'bold' : 'normal' ;
		//jQuery CarouFredCel variables
		$this->lud_opts['pause']		= ( $this->opt( 'pause' ) ) ? intval($this->opt( 'pause' )) : 4000 ;
		$this->lud_opts['pause_on_hover']	= ( $this->opt( 'pause_on_hover' ) ) ? true : false ;
		$this->lud_opts['auto']		= ( $this->opt( 'auto' ) ) ? false : true ;
		$this->lud_opts['speed']		= ( $this->opt( 'speed' ) ) ? intval($this->opt( 'speed' )) : 500 ;
		$this->lud_opts['mode']		= ( $this->opt( 'mode' ) ) ? $this->opt( 'mode' ) : 'scroll' ;
		//controls/pager
		$this->lud_opts['pager']		= ( $this->opt( 'pager') ) ? true : false ;
		$this->lud_opts['controls']	= ( $this->opt( 'controls') ) ? true : false ;
		//fred vs masonry
		$this->lud_opts['enable_animation']	= ( $this->opt( 'animation' ) )  ? true : false;
		$this->lud_opts['grid_selector']		= ( $this->opt( 'grid_selector' ) ) ? $this->opt( 'grid_selector' ) : 1 ;
		//single post vs lightbox
		$this->lud_opts['use_link']	= ( $this->opt( 'use_link' ) ) ? $this->opt( 'use_link' ) : false;
		//layout
		$this->lud_opts['numslides']	= ( $this->opt( 'col_num' ) )  ? intval($this->opt( 'col_num' )) : 4;
		$this->lud_opts['fluid']	= ( $this->opt( 'fluid') ) ? true : false ;
		$this->lud_opts['slide_gutter']	= ( $this->opt( 'slide_gutter' ) ) ?  intval($this->opt( 'slide_gutter' ) ) : '0' ;
		if(is_numeric($this->lud_opts['slide_gutter'])) $this->lud_opts['slide_gutter'] .= 'px';
		//true by default
		$this->lud_opts['equal_height']		= ( $this->opt( 'equal_height') ) ? false : true ;
		//carousell single item min width
		$this->lud_opts['defFredWidth']		= 200;
		$this->lud_opts['fredWidth']		= 300;

		//all you need is json
		$lud_opts	= json_encode($this->lud_opts);
		?>
		<script type="text/javascript">
			/* <![CDATA[ */
			//lud objects
			var ludOpts 	= {};
			var ludSelectors	= {};
			jQuery(document).ready(function(){
				//selectors
				var cloneID 		= '<?php echo $this->meta['clone']; ?>';
				var sectionPrefix	= '<?php echo $this->prefix; ?>';
				var sectionClone	= jQuery('section#'+'<?php echo $this->section_id; ?>' + cloneID);
				ludSelectors[cloneID] = {
					'sectionPrefix'	: sectionPrefix,
					'sectionClone'	: sectionClone,
					'container'	: jQuery('.'+sectionPrefix+'-container', sectionClone),
					'wraper'		: jQuery('.'+sectionPrefix+'-wraper', sectionClone),
					'ludItem'	: jQuery('.'+sectionPrefix+'-item', sectionClone),
					'inner'		: jQuery('.'+sectionPrefix+'-item-inner', sectionClone),
					'pager' 		: jQuery('.'+sectionPrefix+'-pager', sectionClone),
					'prev'		: jQuery('.'+sectionPrefix+'-prev', sectionClone),
					'next'		: jQuery('.'+sectionPrefix+'-next', sectionClone)
				};
				//get options
				ludOpts[cloneID]	= <?php echo $lud_opts; ?>;
				//style and classes
				ItemStyle();
				responsiveClasses();
				//functions
				function ItemStyle(){
					ludSelectors[cloneID]['ludItem'].css({
						'padding-left'	: ludOpts[cloneID]['slide_gutter'],
						'padding-right'	: ludOpts[cloneID]['slide_gutter'],
						'font-style'	: ludOpts[cloneID]['text_italic'],
						'font-weight'	: ludOpts[cloneID]['text_bold']
					});
				}
				function responsiveClasses(){
					if( ludOpts[cloneID]['numslides'] === 0 ){
						// if numslides = 'custom', Set CarouFredSel default maxslides
						if(ludOpts[cloneID]['enable_animation'] === true){
							ludOpts[cloneID]['numslides'] =4;
							ludOpts[cloneID]['itemWidth'] = '';
						}
						else{
							//calculate masonry column width and add selector class
							jQuery( 'li:nth-child(' + ludOpts[cloneID]['grid_selector'] + ')',ludSelectors[cloneID]['wraper'] ).addClass(sectionPrefix+'-grid-selector');
							ludOpts[cloneID]['itemWidth'] = jQuery('.'+sectionPrefix+'-grid-selector').outerWidth();
						}
					}
					if( ludOpts[cloneID]['numslides'] !== 0 ){
						if(768 > ludSelectors[cloneID]['container'].width()){
							if(ludOpts[cloneID]['numslides'] > 3) ludOpts[cloneID]['numslides'] = 3;
						}
						if(480 > ludSelectors[cloneID]['container'].width()){
							if(ludOpts[cloneID]['numslides'] > 2) ludOpts[cloneID]['numslides'] = 2;
						}
						if(300 > ludSelectors[cloneID]['container'].width()){
							ludOpts[cloneID]['numslides'] = 1;
						}

						//set single item width
						var calcItemWidth = Math.floor((ludSelectors[cloneID]['container'].width()/ludOpts[cloneID]['numslides']) );
						ludSelectors[cloneID]['ludItem'].css({
							'width' :	calcItemWidth
						});
						ludOpts[cloneID]['itemWidth'] = calcItemWidth;
						//add responsive classes to each item
						if (400 < calcItemWidth && 600 > calcItemWidth) ludSelectors[cloneID]['container'].addClass(ludOpts[cloneID]['template_name'] + '-c2');
						if (400 > calcItemWidth) ludSelectors[cloneID]['container'].addClass(ludOpts[cloneID]['template_name'] + '-c3');
					}
				}
			});
			jQuery(window).load(function(){
				cloneID 		= '<?php echo $this->meta['clone']; ?>';
				//engage
				ludSelectors[cloneID]['wraper'].ludLoop(ludSelectors[cloneID], ludOpts[cloneID]);
				//show
				ludSelectors[cloneID]['container'].animate({'height':'100%'},400);
				ludSelectors[cloneID]['wraper'].animate({'opacity':1},400);
			});
			/* ]]> */
		</script>
		<?php
		/* font */
		$font_selector = 'section#'.$this->section_id.$this->meta['clone'].' div.'.$this->prefix.'-container';
		if ( $this->opt( 'text_font' ) ) {
			echo load_custom_font( $this->opt( 'text_font' ), $font_selector );
		}
	}

	//section template
	function section_template(){
		$clone_id = $this->meta['clone'];
		if( array_key_exists($clone_id, $this->temp_meta) )$this->meta['set'] = wp_parse_args( $this->temp_meta[$clone_id], $this->meta['set'] );
		//params
		$template_name	= ( $this->opt( 'template_name' ) ) ? $this->opt( 'template_name' ) : $this->default_template;
		$use_link		= ( $this->opt('use_link') ) ? $this->opt('use_link') : false;
		$link_elems		= array('feature','img','img1','img2','post_title');
		$a_open		= '';
		$a_close		= '';
		$animation		= ( $this->opt( 'animation' ) ) ? 'fredslider' : 'in-grid';
		//template json
		$data_path	= $this->base_dir.'/data/';
		$template_json	= (file_exists($data_path.$template_name.'.json')) ? file_get_contents($data_path.$template_name.'.json') : file_get_contents($data_path.'default.json') ;
		$template_json	= json_decode($template_json);
		//query params
		$slides_num	= ( $this->opt( 'slides_num' ) ) ? $this->opt( 'slides_num' ) : '-1';
		$orderby	= ( $this->opt( 'orderby' ) ) ? $this->opt( 'orderby' ) : 'date';
		$order		= ( $this->opt( 'order' ) ) ? $this->opt( 'order' ) : 'DESC';
		$params	= array( 'post_type' => $this->multiple, 'orderby' => $orderby, 'order' => $order, 'posts_per_page' => $slides_num );
		$taxonomy	= ( $this->opt( 'taxonomy' ) ) ? $this->opt( 'taxonomy' ) : null ;
		if ( $taxonomy ) {
			$query_tax = array(
				array(
					'taxonomy' => $this->taxID,
					'field'    => 'slug',
					'terms'    => array( $taxonomy )
				)
			);
			$params['tax_query'] = $query_tax;
		}
		//query
		$post_data	= array();
		$query		= null;
		$query		= new WP_Query( $params );
		$index 		= 0;
		//collect all posts
		$all_posts	= '';
		if($query->have_posts()){
			while($query->have_posts()){
				$query->the_post();
				//get post data for every post
				$temp_data = get_post_meta( get_the_ID() );
				//update
				$temp_data['post_title'][0]	= get_the_title( );
				$temp_data['post_content'][0]	= get_the_content( );
				$temp_data['post_excerpt'][0]	= get_the_excerpt( );
				$temp_data['post_url'][0]	= get_post_permalink();
				$temp_data['feature'][0]	= get_the_post_thumbnail();

				if(array_key_exists('img1', $temp_data) && $temp_data['img1'][0]) {
					$temp_data['img1'][0]	= (array_key_exists('demo', $temp_data) && $temp_data['demo'][0]) ?  '<img src="'. $temp_data['img1'][0] . '">' :  wp_get_attachment_image($temp_data['img1'][0], 'full');
				}
				if(array_key_exists('img2', $temp_data) && $temp_data['img2'][0]) {
					$temp_data['img2'][0]	= (array_key_exists('demo', $temp_data) && $temp_data['demo'][0]) ?  '<img src="'. $temp_data['img2'][0] . '">' :  wp_get_attachment_image($temp_data['img2'][0], 'full');
				}
				if (array_key_exists('demo', $temp_data) && $temp_data['demo'][0])  $temp_data['feature'][0] =  $temp_data['img1'][0];
				//add link where needed
				foreach ($temp_data as $key => $value) {
					if( array_key_exists($key . '_url', $temp_data)){
						$temp_data[$key][0] = '<a href="' .  $temp_data[$key.'_url'][0] . '">'. $temp_data[$key][0] .'</a>';
					}
				}
				//collects all posts data in one array
				$post_data[] = $temp_data;
				//create link to single post
				if(in_array($use_link, array('link', 'colorbox'))) {
					$link_index = $index + 1;
					$a_open = sprintf('<a href="%1$s" class="%2$s-link %2$s-link-%3$s" data-proj-id="%3$s">', $post_data[$index]['post_url'][0], $this->prefix, $link_index );
					$a_close = '</a>';
				}
				//render elements
				$all_elems = '';
				$group_index = 1;
				foreach ($template_json as $key => $value) {
					$key++;
					//template json - if array in array
					if(is_array($value)){
						$group_elems = '';
						//elements
						foreach ($value as $i => $val) {
							//annoying wp notice fix
							if(!array_key_exists($val, $post_data[$index])) $post_data[$index][$val][0] = '';
							//add link only to imgs and title
							if($a_close && in_array($val, $link_elems)){$a_start = $a_open; $a_end = $a_close;}else{$a_start = ''; $a_end = '';}
							$group_elem = sprintf('%4$s<div class="%1$s-%2$s">%3$s</div>%5$s',$this->prefix, $val, $post_data[$index][$val][0], $a_start, $a_end );
							$group_elems .= $group_elem;
						}
						//wrap elements
						$group = sprintf('<div id ="%1$s-group-%2$s" class="%1$s-group">%3$s</div>', $this->prefix, $group_index, $group_elems);
						$all_elems .= $group;
						$group_index++;
					}else{
						//and again
						if(!array_key_exists($value, $post_data[$index])) $post_data[$index][$value][0] = '';
						//add link only to imgs and title
						if($a_close && in_array($value, $link_elems)){$a_start = $a_open; $a_end = $a_close;}else{$a_start = ''; $a_end = '';}
						$elem = sprintf('%4$s<div class="%1$s-%2$s">%3$s</div>%5$s',$this->prefix, $value, $post_data[$index][$value][0], $a_start, $a_end );
						$all_elems .= $elem;
					}
				}
				//wrap elements in <li>
				$index++;
				$all_posts .= sprintf('<li class="%1$s-item %1$s-item-%2$s"><div id="%1$s-inner-%2$s" class="%1$s-item-inner %1$s-%4$s">%3$s</div></li>', $this->prefix, $index, $all_elems, $template_name);
			}
		}
		wp_reset_postdata();
		//add controls
		$controls = ('fredslider' === $animation) ?
			sprintf(
				'<span class="%1$s-prev">
					<a class="%1$s-prev-link" href="#"><i class="icon-chevron-left"></i></a>
				</span>
				<span class="%1$s-next">
					<a class="%1$s-next-link" href="#"><i class="icon-chevron-right"></i></a>
				</span>
				<div class="%1$s-pager"></div>'
			, $this->prefix)	: null;
		//wrap it up
		$ludloop = sprintf('<div class="%1$s-container post-id-%2$s template-%3$s"><ul class="%1$s-wraper %4$s">%5$s</ul>%6$s</div>',
			$this->prefix,
			$this->multiple,
			$template_name,
			$animation,
			$all_posts,
			$controls
		);
		//print
		echo do_shortcode( $ludloop );
	}

	//section opts
	function section_opts() {
		$opts	= array();
		if($this->opt('opt_set_select')) {
		 	$master_info = '<div class="alert" style="font-weight:400;">Last loaded Master Template: <br><span style="font-weight:bold;">'.$this->opt('opt_set_select').'</span></div>';
 		}else{
 			$master_info = ($this->opt('opt_set_info')) ? '<div class="alert" style="font-weight:400;">Last loaded Master Template: <br><span style="font-weight:bold;">'.$this->opt('opt_set_info').'</span></div>' : '<div class="alert" style="font-weight:400;">No Master Template Loaded</div>';
 		}
		$opts[] = array(
			'key'		=> 'master_template_settings',
			'type'		=>  'multi',
			'col'		=> 1,
			'title'		=> __( 'Master Template Settings', 'pagelines' ),
			'opts' => array(
				array(
					'key'	=>	'opt_set_select',
					'type'	=> 'select',
					'label'	=> __( 'Select Master Template', 'pagelines' ),
					'opts'	=> $this->opt_set_select(),
				),
				array(
					'key'	=>	'master_info',
					'type'	=> 'template',
					'label'	=> __( 'Select Master Template', 'pagelines' ),
					'template'	=> $master_info,
					'help'	=> __( "Master Template is innovative way to switch between section templates. When selected, master template will load chosen css template and set all required options for section's best design and functionality.", 'pagelines' ),
				),
				array(
					'key'	=>	'opt_set_info',
					'type'	=> 'text',
				)
			)
		);
		$opts[] = array(
			'key'		=> 'ccname_set',
			'type'		=>  'multi',
			'col'		=> 2,
			'title'		=> __( 'General settings', 'pagelines' ),
			'opts' => array(
				array(
					'key'	=>	'template_name',
					'type'		=> 'select',
					'label'	=> __( 'Choose Template', 'pagelines' ),
					'opts'		=> $this->get_template_selectvalues(),
					'compile'	=> true
				),
				array(
					'key'	=>	'use_link',
					'type'       => 'select',
					'label' => __( 'Handle click on '.$this->multiple_up.' Single Item', 'pagelines' ),
					'opts' => array(
						'link'		=> array( 'name' => __( 'Link to '.$this->multiple_up.' single', 'pagelines' ) ),
						'colorbox'	=> array( 'name' => __( 'Open in Lightbox', 'pagelines' ) ),
						'none'		=> array( 'name' => __( 'None', 'pagelines' ) ),
					)
				),
				array(
					'key'	=>	'taxonomy',
					'type'			=> 'select_taxonomy',
					'taxonomy_id'	=> $this->taxID,
					'label'	=> __( 'Select '.$this->single_up.' Set (default "all")', 'pagelines' ),
					'default'	=> false
				),
				array(
					'key'	=>	'order',
					'type'		=> 'select',
					'label'	=> __( 'Order of '.$this->multiple, 'pagelines' ),
					'opts'	=> array(
						'ASC'		=> array( 'name' => __( 'Ascending', 'pagelines' ) ),
						'DESC'		=> array( 'name' => __( 'Descending (default)', 'pagelines' ) ),
					)
				),
				array(
					'key'	=>	'orderby',
					'type'		=> 'select',
					'label'	=> __( 'Orderby', 'pagelines' ),
					'opts'	=> array(
						'title'		=> array( 'name' => __( 'Order by title.', 'pagelines' ) ),
						'name'		=> array( 'name' => __( 'Order by post name (post slug).', 'pagelines' ) ),
						'date'		=> array( 'name' => __( 'Order by date.', 'pagelines' ) ),
						'modified'	=> array( 'name' => __( 'Order by last modified date.', 'pagelines' ) ),
						'ID'		=> array( 'name' => __( 'Order by post id.', 'pagelines' ) ),
						'author'		=> array( 'name' => __( 'Order by author.', 'pagelines' ) ),
						'none'		=> array( 'name' => __( 'No order.', 'pagelines' ) ),
					)
				)
			)
		);
		$opts[] = array(
			'key'		=> 'layout_settings',
			'type'		=>  'multi',
			'col'		=> 3,
			'title'		=> __( 'Layout & Query Params', 'pagelines' ),
			'opts' => array(
				array(
					'key'	=>	'animation',
					'type'	=> 'check',
					'label'	=> __( 'Enable animation (carousel)', 'pagelines' ),
				),
				array(
					'key'	=>	'col_num',
					'type'	=> 'select',
					'label'	=> __( 'Number of columns', 'pagelines' ),
					'opts'	=> array(
						'custom'	=> array( 'name' => __( 'Custom', 'pagelines' ) ),
						'1'		=> array( 'name' => __( '1', 'pagelines' ) ),
						'2'		=> array( 'name' => __( '2', 'pagelines' ) ),
						'3'		=> array( 'name' => __( '3', 'pagelines' ) ),
						'4'		=> array( 'name' => __( '4', 'pagelines' ) ),
						'5'		=> array( 'name' => __( '5', 'pagelines' ) ),
						'6'		=> array( 'name' => __( '6', 'pagelines' ) ),
					),
					'help'	=> __( 'Custom can be used in grid layout, if you want variable/fluid items width. More at Project Lud Docs page.', 'pagelines' )
				),
				array(
					'key'	=>	'slides_num',
					'type'			=> 'text',
					'label'	=> __( 'Number of '.$this->multiple.' to use (default all)', 'pagelines' ),
					'default'	=> false
				),
				array(
					'key'	=>	'slide_gutter',
					'type'			=> 'text',
					'label'	=> __( 'Gutter between '.$this->multiple.' (default 0)', 'pagelines' ),
					'default'	=> false
				),
				array(
					'key'	=>	'equal_height',
					'type'			=> 'check',
					'label'	=> __( 'Enable variable items height', 'pagelines' ),
				),
				array(
					'key'	=>	'grid_selector',
					'type'			=> 'select',
					'label'	=> __( 'Grid Item selector(read more info)', 'pagelines' ),
					'opts'	=> array(
						'1'		=> array( 'name' => __( '1', 'pagelines' ) ),
						'2'		=> array( 'name' => __( '2', 'pagelines' ) ),
						'3'		=> array( 'name' => __( '3', 'pagelines' ) ),
						'4'		=> array( 'name' => __( '4', 'pagelines' ) ),
						'5'		=> array( 'name' => __( '5', 'pagelines' ) ),
						'6'		=> array( 'name' => __( '6', 'pagelines' ) ),
					)
				),
				array(
					'key'	=>	'fluid',
					'type'			=> 'check',
					'label'	=> __( 'Enable fluid items (read more info)', 'pagelines' ),
				)
			)
		);
		$opts[] = array(
			'key'		=> 'text_settings',
			'type'		=>  'multi',
			'col'		=> 1,
			'title'		=> __(  $this->single_up.' Content Options', 'pagelines' ),
			'opts' => array(
				array(
					'key'	=>	'text_italic',
					'type'			=> 'check',
					'label'	=> __( 'Italic text style of '.$this->single.' content', 'pagelines' ),
				),
				array(
					'key'	=>	'text_bold',
					'type'			=> 'check',
					'label'	=> __( 'Bold text style of '.$this->single.' content', 'pagelines' ),
				),
				array(
					'key'	=>	'text_font',
					'type' 			=> 'type',
					'label'	=> __( 'Choose '.$this->single_up.' text font', 'pagelines' ),
				)
			)
		);
		$opts[] = array(
			'key'		=> 'control_settings',
			'type'		=>  'multi',
			'col'		=> 2,
			'title'		=> __( 'Controls', 'pagelines' ),
			'opts' => array(
				array(
					'key'	=>	'auto',
					'type'			=> 'check',
					'label'	=> __( 'Enable manual transition mode', 'pagelines' ),
				),
				array(
					'key'	=>	'pause_on_hover',
					'type'			=> 'check',
					'label'	=> __( 'Enable pause on hover', 'pagelines' ),
				),
				array(
					'key'	=>	'controls',
					'type'			=> 'check',
					'label'	=> __( 'Enable controls (arrows)', 'pagelines' ),
				),
				array(
					'key'	=>	'pager',
					'type'			=> 'check',
					'label'	=> __( 'Show pager', 'pagelines' ),
				)
			)
		);
		$opts[] = array(
			'key'		=> 'trans_settings',
			'type'		=>  'multi',
			'col'		=> 3,
			'title'		=> __( 'Transition', 'pagelines' ),
			'opts' => array(
				array(
					'key'	=>	'mode',
					'type'			=> 'select',
					'label'	=> __( 'Choose Transition Effect', 'pagelines' ),
					'opts'	=> array(
						'scroll'		=> array( 'name' => __( 'Scroll', 'pagelines' ) ),
						'fade'			=> array( 'name' => __( 'Fade', 'pagelines' ) ),
						'cover'			=> array( 'name' => __( 'Cover', 'pagelines' ) ),
						'cover-fade'	=> array( 'name' => __( 'Cover - Fade', 'pagelines' ) ),
						'uncover'		=> array( 'name' => __( 'Uncover', 'pagelines' ) ),
						'uncover-fade'	=> array( 'name' => __( 'Uncover - Fade', 'pagelines' ) ),
					)
				),
				array(
					'key'	=>	'pause',
					'type'	=> 'text',
					'label'	=> __( 'Pause timeout in milliseconds (default 5000)', 'pagelines' ),
				),
				array(
					'key'	=>	'speed',
					'type'			=> 'text',
					'label'	=> __( 'Transition Speed in milliseconds (default 500)', 'pagelines' ),
				),
			)
		);
		$opts[] = array(
			'key'	=> 'bg_colors',
			'type' 	=> 	'multi',
			'col'	=> 1,
			'title' => __( 'Background Colors', 'pagelines' ),
			'opts'	=> array(
				array(
					'key'           => 'templatebg',
					'type'       => 'color',
					'label' => __( 'Container Background', 'pagelines' ),
					'default'	=> '',
				),
				array(
					'key'           => 'singlebg',
					'type'       => 'color',
					'label' => __( 'Single '.$this->single_up.' Background', 'pagelines' ),
					'default'	=> '',
				),
				array(
					'key'           => 'groupbg',
					'type'       => 'color',
					'label' => __( 'Group Background', 'pagelines' ),
					'default'	=> '',
					'ref'	=> __('Some meta fields are grouped (depends on template) - <br>
							Usually, title and position.', 'pagelines')
				),
				array(
					'key'           => 'cbxbg',
					'type'       => 'color',
					'label' => __( 'Lightbox Overlay Background', 'pagelines' ),
					'default'	=> ''
				)
			)
		);
		$opts[] = array(
			'key'	=> 'txt-colors',
			'type' 	=> 	'multi',
			'col'	=> 2,
			'title' => __( 'Text Colors', 'pagelines' ),
			'opts'	=> array(
				array(
					'key'           => 'title_color',
					'type'          => 'color',
					'label'    => __( 'Project Title Color', 'pagelines' ),
					 'default'	=> '',
				),
				array(
					'key'           => 'content_color',
					'type'          => 'color',
					'label'    => __( 'Content Color', 'pagelines' ),
					 'default'	=> '',
				),
				array(
					'key'           => 'client_color',
					'type'          => 'color',
					'label'    => __( 'Project Client Color', 'pagelines' ),
					 'default'	=> '',
				),
				array(
					'key'           => 'slogan_color',
					'type'          => 'color',
					'label'    => __( 'Project Slogan Color', 'pagelines' ),
					 'default'	=> '',
				),
				array(
					'key'           => 'partner_color',
					'type'          => 'color',
					'label'    => __( 'Partner Company Color', 'pagelines' ),
					 'default'	=> '',
				),
				array(
					'key'           => 'custom1_color',
					'type'          => 'color',
					'label'    => __( 'Custom Text 1 Color', 'pagelines' ),
					 'default'	=> '',
				),
				array(
					'key'           => 'custom2_color',
					'type'          => 'color',
					'label'    => __( 'Custom Text 2 Color', 'pagelines' ),
					 'default'	=> '',
				)

			)
		);
		$opts[] = array(
			'key'	=> 'controls-colors',
			'type' 	=> 	'multi',
			'col'	=> 3,
			'title' => __( 'Controls Colors and Size', 'pagelines' ),
			'opts'	=> array(
				array(
					'key'           => 'arrow_color',
					'type'          => 'color',
					'label'    => __( 'Arrow Color', 'pagelines' ),
					'default'	=> '',
				),
				array(
					'key'           => 'arrow_size',
					'type'          => 'text',
					'label'    => __( 'Arrow size in pixels', 'pagelines' ),
					'default'	=> '',
				),
				array(
					'key'           => 'pager_color',
					'type'       => 'color',
					'label' => __( 'Pager Color', 'pagelines' ),
					'default'	=> '',
				),
				array(
					'key'           => 'pageractive_color',
					'type'       => 'color',
					'label' => __( 'Pager Active Color', 'pagelines' ),
					'default'	=> '',
				)
			)
		);
		return $opts;
	}

	//template list for section_opts()
	function get_template_selectvalues(){
		$dir 	= $this->base_dir.'/templates/';
		$files = glob($dir.'*.less');
		$array 	= array();
		foreach ($files as $filename) {
			$file 		= basename($dir.$filename, ".less");
			$array[$file] 	= array( 'name' => $file );
		}
		return $array;
	}

	function opt_set_select(){
		$dir 	= $this->base_dir.'/master-template/';
		$files = glob($dir.'*.json');
		$array 	= array();
		foreach ($files as $filename) {
			$file 		= basename($dir.$filename, ".json");
			$array[$file] 	= array( 'name' => $file );
		}
		return $array;
	}

	//section persistent
	function section_persistent(){
		//add_action( 'template_redirect',array(&$this, 'projects_lud_less') );
		add_filter( 'pl_settings_array', array( &$this, 'get_meta_array' ) );
		//set post
		$this->post_type_setup();
		if(!class_exists('RW_Meta_Box')) {
			add_action( 'admin_notices',array(&$this, 'projects_lud_notice') );
		} else  //meta setup
			add_action( 'admin_init',array(&$this, 'post_meta_setup') );
	}

	//notice for metabox
	function projects_lud_notice(){
		echo '<div class="updated">
		   	<p>For the <strong>Projects Lud</strong> you need to install the <strong>Meta Box</strong> plugin by <a href="http://www.deluxeblogtips.com/" >Rilwis</a>. It is well tested, <strong>free</strong>, open source solution that will be seamlessly integrated once you install it. <strong>It does not require your attention.</strong>
		   	You can get it from <a href="http://wordpress.org/plugins/meta-box" target="_blank"><strong>here</strong></a>.</p>
		</div>';
	}

	/* site options metapanel */
	function get_meta_array( $settings ){
		$settings[ $this->id ] = array(
				'name'  => $this->multiple_up.' Lud',
				//'icon'  => $this->icon,
				'opts'  => $this->sec_site_options(),
		);
		return $settings;
	}

	/* site options metapanel */
	function sec_site_options(){
		$options_array = array(
			array(
				'type' 	=> 	'multi',
				'col'	=> 1,
				'title' => __( 'Post Type Settings', 'pagelines' ),
				'opts'	=> array(
					array(
						'key'	=>	'disable_public_pt',
						'type'	=> 'check',
						'label'	=> __( 'Set Projects Post Type to Private.', 'pagelines' ),
						'help'	=> __( 'Disable Single Project Post view.', 'pagelines' ),
					)
				)
			)
		);
		return $options_array;
	}

	//post meta - uses MetaBox plugin
	function post_meta_setup(){
		$type_meta_array = array(
			'settings' => array(
				'type'         =>  'multi_option',
				'title'        => __( 'Single '.$this->single_up.' Options', 'pagelines' ),
				'shortexp'     => __( 'Parameters', 'pagelines' ),
				'exp'          => __( '<strong>Single '.$this->single_up.' Options</strong><br>Add '.$this->single_up.' Metadata that will be used on the page.<br><strong>HEADS UP:<strong> Each template uses different set of metadata. Check out <a href="http://bestrag.net/'.$this->multiple.'-lud" target="_blank">demo page</a> for more information.', 'pagelines' ),
				'selectvalues' => array(
					'client_name' => array(
						'type'       => 'text',
						'inputlabel' =>  __( 'Client Name', 'pagelines' )
					),
					'client_name_url' => array(
						'type'       => 'text',
						'inputlabel' =>  __( 'Client Name URL (eg: http://www.client.co)', 'pagelines' )
					),
					'partner' => array(
						'type'       => 'text',
						'inputlabel' => __( 'Partner Company Name (if any)', 'pagelines' )
					),
					'partner_url' => array(
						'type'       => 'text',
						'inputlabel' =>  __( 'Partner Company URL (if any)', 'pagelines' )
					),
					'project_slogan' => array(
						'type'       => 'text',
						'inputlabel' => __( 'Project Slogan (if any)', 'pagelines' )
					),
					'img1'  => array(
						'inputlabel' => __( 'Associate an image with this '.$this->single, 'pagelines' ),
						'type'       => 'thickbox_image'
					),
					'img2'  => array(
						'inputlabel' => __( 'Associate an image with this '.$this->single, 'pagelines' ),
						'type'       => 'thickbox_image'
					),
					'custom_text1' => array(
						'type'       => 'text',
						'inputlabel' => __( 'Custom Text/HTML/Shortcode 1', 'pagelines' )
					),
					'custom_text2' => array(
						'type'       => 'text',
						'inputlabel' => __( 'Custom Text/HTML/Shortcode 2', 'pagelines' )
					)
				)
			)
		 );
		$fields = $type_meta_array['settings']['selectvalues'];
		$figo = array(); $findex = 0;

		foreach ($fields as $key => $value) {
			$figo[$findex] = array(
				'name'  => $value['inputlabel'],
				'id'    => $key,
				'type'  => $value['type'],
				'std'   => '',
				'class' => 'custom-class',
				'clone' => false
			);
			$findex++;
		}
		$metabox = array(
			'id'       => 'projectal',
			'title'    => 'Projectal Information',
			'pages'    => array( $this->multiple ),
			'context'  => 'normal',
			'priority' => 'high',
			'fields' => $figo
		);
		 new RW_Meta_Box($metabox);
	}

	//post type
	function post_type_setup() {
		$public_pt = (pl_setting('disable_public_pt')) ? false : true;
		//if($this->meta['set']['use_colorbox']) $public_pt = false;
		$args = array(
			'label'			=> __( $this->multiple_up, 'pagelines' ),
			'singular_label'		=> __( $this->single_up, 'pagelines' ),
			'description'		=> __( 'For creating '.$this->multiple.' items.', 'taxonomies' ),
			'taxonomies'		=> array( $this->taxID ),
			'menu_icon'		=> $this->icon,
			'public'			=> $public_pt,
			'show_ui'		=> true,
			'hierarchical'		=> true,
			'featured_image'	=> true,
			'has_archive'		=> true,
			'show_in_menu'		=> true,
			'show_in_nav_menus'	=> true,
			'show_in_admin_bar'	=> true,
			'menu_position'		=> 20,
			'can_export'		=> true,
		);
		$taxonomies = array(
			$this->taxID => array(
				'label'		=> __( $this->single_up.' Sets', 'pagelines' ),
				'singular_label'	=> __( $this->single_up.' Set', 'pagelines' ),
			)
		);
		$columns = array(
			'cb'		=> "<input type=\"checkbox\" />",
			'title'		=> 'Title',
			'media'		=> 'Media',
			'client'		=> 'Client Name',
			'description'	=> 'Description',
			$this->taxID	=> $this->single_up.' Sets',
		);
		$this->post_type = new PageLinesPostType( $this->multiple, $args, $taxonomies, $columns, array( &$this, 'column_display' ) );
		 // Defaults
		$this->post_type->set_default_posts( 'bestrag_default_posts', $this );
	}

	//default autogenerated posts
	function bestrag_default_posts($post_type){
		$def_posts  = array(
			array(
				'title'		=>   'Full Business Branding',
				'content'   =>'Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor inadip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea. Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Give us a try!',
				'client_name' =>   'bestrag',
				'partner' =>   'Cooperation Company',
				'client_name_url'	=>   'http://www.pagelines.com',
				'partner_url'=>   'http://www.pagelines.com',
				'project_slogan'=>   'Random Project Motivation',
				'custom_text1'	=>   '[pl_button type="primary" size="default" link="mailto:#"]Email Me[/pl_button]',
				'custom_text2'	=>   'Great Leader',
				'img1'		=>   $this->base_url.'/images/placeholder-320x320.png',
				'img2'      =>   $this->base_url.'/images/placeholder-320x320.png',
				'demo'		=> true,
			),
			array(
				'title'		=>   'Web Identity',
				'content'   =>'Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor inadip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea. Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Give us a try!',
				'client_name' =>   'bestrag',
				'partner' =>   'Cooperation Company',
				'client_name_url'	=>   'http://www.pagelines.com',
				'partner_url'=>   'http://www.pagelines.com',
				'project_slogan'=>   'Random Project Motivation',
				'custom_text1'	=>   '[pl_button type="primary" size="default" link="mailto:#"]Email Me[/pl_button]',
				'custom_text2'	=>   'Great Leader',
				'img1'		=>   $this->base_url.'/images/placeholder-320x320.png',
				'img2'      =>   $this->base_url.'/images/placeholder-320x320.png',
				'demo'		=> true,
			),
			array(
				'title'		=>   'Events Identity',
				'content'   =>'Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor inadip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea. Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Give us a try!',
				'client_name' =>   'bestrag',
				'partner' =>   'Cooperation Company',
				'client_name_url'	=>   'http://www.pagelines.com',
				'partner_url'=>   'http://www.pagelines.com',
				'project_slogan'=>   'Random Project Motivation',
				'custom_text1'	=>   '[pl_button type="primary" size="default" link="mailto:#"]Email Me[/pl_button]',
				'custom_text2'	=>   'Great Leader',
				'img1'		=>   $this->base_url.'/images/placeholder-320x320.png',
				'img2'      =>   $this->base_url.'/images/placeholder-320x320.png',
				'demo'		=> true,
			),
			array(
				'title'		=>   'Digital Identity',
				'content'   =>'Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor inadip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea. Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Give us a try!',
				'client_name' =>   'bestrag',
				'partner' =>   'Cooperation Company',
				'client_name_url'	=>   'http://www.pagelines.com',
				'partner_url'=>   'http://www.pagelines.com',
				'project_slogan'=>   'Random Project Motivation',
				'custom_text1'	=>   '[pl_button type="primary" size="default" link="mailto:#"]Email Me[/pl_button]',
				'custom_text2'	=>   'Great Leader',
				'img1'		=>   $this->base_url.'/images/placeholder-320x320.png',
				'img2'      =>   $this->base_url.'/images/placeholder-320x320.png',
				'demo'		=> true,
			),
			array(
				'title'		=>   'Business Startups',
				'content'   =>'Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor inadip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea. Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Give us a try!',
				'client_name' =>   'bestrag',
				'partner' =>   'Cooperation Company',
				'client_name_url'	=>   'http://www.pagelines.com',
				'partner_url'=>   'http://www.pagelines.com',
				'project_slogan'=>   'Random Project Motivation',
				'custom_text1'	=>   '[pl_button type="primary" size="default" link="mailto:#"]Email Me[/pl_button]',
				'custom_text2'	=>   'Great Leader',
				'img1'		=>   $this->base_url.'/images/placeholder-320x320.png',
				'img2'      =>   $this->base_url.'/images/placeholder-320x320.png',
				'demo'		=> true,
			),
			array(
				'title'		=>   'Mobile Identity',
				'content'   =>'Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor inadip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea. Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Give us a try!',
				'client_name' =>   'bestrag',
				'partner' =>   'Cooperation Company',
				'client_name_url'	=>   'http://www.pagelines.com',
				'partner_url'=>   'http://www.pagelines.com',
				'project_slogan'=>   'Random Project Motivation',
				'custom_text1'	=>   '[pl_button type="primary" size="default" link="mailto:#"]Email Me[/pl_button]',
				'custom_text2'	=>   'Great Leader',
				'img1'		=>   $this->base_url.'/images/placeholder-320x320.png',
				'img2'      =>   $this->base_url.'/images/placeholder-320x320.png',
				'demo'		=> true,
			),
			array(
				'title'		=>   'Digital Grafic',
				'content'   =>'Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor inadip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea. Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Give us a try!',
				'client_name' =>   'bestrag',
				'partner' =>   'Cooperation Company',
				'client_name_url'	=>   'http://www.pagelines.com',
				'partner_url'=>   'http://www.pagelines.com',
				'project_slogan'=>   'Random Project Motivation',
				'custom_text1'	=>   '[pl_button type="primary" size="default" link="mailto:#"]Email Me[/pl_button]',
				'custom_text2'	=>   'Great Leader',
				'img1'		=>   $this->base_url.'/images/placeholder-320x320.png',
				'img2'      =>   $this->base_url.'/images/placeholder-320x320.png',
				'demo'		=> true,
			),
			array(
				'title'		=>   'Story Telling',
				'content'   =>'Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor inadip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Since we are using Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea. Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, Lorem ipsum dolor sit amet, con se ctetur adip is cing elit, sed do eiusmod tempor sed do eiusmod tempor in cididunt ut labore et do lore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris Give us a try!',
				'client_name' =>   'jumpmouse',
				'partner' =>   'Cooperation Company',
				'client_name_url'	=>   'http://www.pagelines.com',
				'partner_url'=>   'http://www.pagelines.com',
				'project_moto'	=>   'Random Project Motivation',
				'project_slogan'=>   'Random Project Motivation',
				'custom_text1'	=>   '[pl_button type="primary" size="default" link="mailto:#"]Email Me[/pl_button]',
				'custom_text2'	=>   'Great Leader',
				'img1'		=>   $this->base_url.'/images/placeholder-320x320.png',
				'img2'      =>   $this->base_url.'/images/placeholder-320x320.png',
				'demo'		=> true,
			)

		);
		foreach( $def_posts as $p ){
			$defaults			= array();
			$defaults['post_title']		= $p['title'];
			$defaults['post_content']	= $p['content'];
			$defaults['post_type']		= $post_type;
			$defaults['post_status']		= 'publish';
			$id				= wp_insert_post( $defaults );
			//$aid = wp_insert_attachment($defaults, 'placeholder-320x320.png');
			//if (!set_post_thumbnail($id,$aid)) die('x');
			//$attach_data = wp_generate_attachment_metadata( $aid, $this->base_dir.'/images/placeholder-320x320.png' );
			//wp_update_attachment_metadata( $aid, $attach_data );
			//update_post_meta($id, '_thumbnail_id',$aid);
			update_post_meta( $id, 'client_name', $p['client_name'] );
			update_post_meta( $id, 'client_name_url', $p['client_name_url'] );
			update_post_meta( $id, 'partner', $p['partner'] );
			update_post_meta( $id, 'partner_url', $p['partner_url'] );
			update_post_meta( $id, 'custom_text1', $p['custom_text1'] );
			update_post_meta( $id, 'custom_text2', $p['custom_text2'] );
			update_post_meta( $id, 'demo', $p['demo'] );
			update_post_meta( $id, 'project_slogan', $p['project_slogan'] );
			update_post_meta( $id, 'img1', $p['img1'] );
			update_post_meta( $id, 'img2', $p['img2'] );
			wp_set_object_terms( $id, 'default-'.$this->multiple, $this->taxID );
		}
	}

	//post type admin side columns
	function column_display( $column ) {
		global $post;
		switch ( $column ) {
		case 'client_name':
			if ( get_post_meta( $post->ID, 'client_name', true ) )
				echo get_post_meta( $post->ID, 'client_name', true );
			break;
		case 'description':
			echo the_excerpt();
			break;
		case 'media':
		if ( has_post_thumbnail( $post->ID ) ){ echo get_the_post_thumbnail( $post->ID, array(80,80) ); }
			break;
		case $this->taxID:
			echo get_the_term_list( $post->ID, $this->taxID, '', ', ', '' );
			break;
		}
	}

	function update_lud_settings($template){
		//$this->opt_update('opt_set_info', $template, 'local');
		$this->opt_update('opt_set_select', null, 'local');
		$default = array(
			'template_name' => null,
			'use_link' => null,
			'taxonomy' => null,
			'order' => null,
			'orderby' => null,
			'animation' => null,
			'col_num' => null,
			'slides_num' => null,
			'slide_gutter' => null,
			'equal_height' => null,
			'grid_selector' => null,
			'text_italic' => null,
			'text_bold' => null,
			'text_font' => null,
			'auto' => null,
			'pause_on_hover' => null,
			'controls' => null,
			'pager' => null,
			'mode' => null,
			'pause' => null,
			'speed' => null,
			'fluid' => null,
			'opt_set_info' => $template
		);
		$data_path = $this->base_dir.'/master-template/';
		$opts_json	= (file_exists($data_path.$template.'.json')) ? file_get_contents($data_path.$template.'.json')  : array() ;
		$opts_json = json_decode($opts_json, true);
		$opts = wp_parse_args( $opts_json, $default );
		foreach ($opts as $key => $value) {
			$this->opt_update($key, $value, 'local');
			$this->temp_meta[$key] = $value;
 		}
		if( !PL_LESS_DEV ) pl_flush_draft_caches();
	}

	//handle less template
/*	function projects_lud_less(){
		$template	= (isset( $this->meta['set']['template_name'])) ? $this->meta['set']['template_name'] : $this->default_template;
		$template_file 	= sprintf('%s/templates/%s.less', $this->base_dir, $template);
		pagelines_insert_core_less( $template_file );
	}
*/
}//EOC
