<?php
/*
	Section: Projects Single Lud
	Author: bestrag
	Version: 0.0.5
	Author URI: http://bestrag.net
	Demo: http://bestrag.net/projects-lud/
	Description: Projects Single Lud
	Class Name: ProjectsSingleLud
	Filter: component
*/

class ProjectsSingleLud extends PageLinesSection {
	var $multiple_up	= 'Projects';
	var $multiple		= 'projects';
	var $single_up		= 'Project';
	var $prefix		= 'sproj';
	var $section_id		= 'projects-single-lud';
	var $default_template	= 'default';

	function section_template(){
		//template name
		$post_data	= array();
		$template	= ( $this->opt( 'template_name' ) ) ? $this->opt( 'template_name' ) : $this->default_template;
		//collect post data
		$single_post_id	= ( $this->opt( 'single_post_id' ) ) ? $this->opt( 'single_post_id' ) : get_the_ID();
		$single_post 	= get_post($single_post_id);
		//collect post meta
		$temp_data = get_post_meta( $single_post_id );
		//update
		$temp_data['post_title'][0]	= $single_post->post_title;
		$temp_data['post_content'][0]	= $single_post->post_content;
		$temp_data['post_excerpt'][0]	= $single_post->post_excerpt;
		$temp_data['feature'][0]	= get_the_post_thumbnail($single_post_id);
		if(array_key_exists('img1', $temp_data) && $temp_data['img1'][0]) {
			$temp_data['img1'][0]	= (array_key_exists('demo', $temp_data) && $temp_data['demo'][0]) ?  '<img src="'. $temp_data['img1'][0] . '">' :  wp_get_attachment_image($temp_data['img1'][0]);
		}
		if(array_key_exists('img2', $temp_data) && $temp_data['img2'][0]) {
			$temp_data['img2'][0]	= (array_key_exists('demo', $temp_data) && $temp_data['demo'][0]) ?  '<img src="'. $temp_data['img2'][0] . '">' :  wp_get_attachment_image($temp_data['img2'][0]);
		}
		if (array_key_exists('demo', $temp_data) && $temp_data['demo'][0])  $temp_data['feature'][0] =  $temp_data['img1'][0];
		//add link where needed
		foreach ($temp_data as $key => $value) {
			if( array_key_exists($key . '_url', $temp_data)){
				$temp_data[$key][0] = '<a href="' .  $temp_data[$key.'_url'][0] . '">'. $temp_data[$key][0] .'</a>';
			}
		}
		//template json
		$data_path	= $this->base_dir.'/data/';
		$template_json	= (file_exists($data_path.$template.'.json')) ? file_get_contents($data_path.$template.'.json') : file_get_contents($data_path.'default.json') ;
		$template_json = json_decode($template_json);
		$all_elems = '';
		$group_index = 1;
		foreach ($template_json as $key => $value) {
			$key++;
			//if array in array
			if(is_array($value)){
				$group_elems = '';
				//elements
				foreach ($value as $i => $val) {
					//annoying wp notice fix
					if(!array_key_exists($val, $temp_data)) $temp_data[$val][0] = '';
					$group_elem = sprintf('<div class="%1$s-%2$s">%3$s</div>',$this->prefix, $val, $temp_data[$val][0] );
					$group_elems .= $group_elem;
				}
				//wrap elements
				$group = sprintf('<div id ="%1$s-group-%2$s" class="%1$s-group">%3$s</div>', $this->prefix, $group_index, $group_elems);
				$all_elems .= $group;
				$group_index++;
			}else{
				//and again
				if(!array_key_exists($value, $temp_data)) $temp_data[$value][0] = '';
				$elem = sprintf('<div class="%1$s-%2$s">%3$s</div>',$this->prefix, $value, $temp_data[$value][0] );
				$all_elems .= $elem;
			}
		}
		//pack elements
		$single_lud_post = sprintf('<div id="lud-postID-%1$s" class="%2$s-single-post lud-single-post template-%4$s">%3$s</div>', $single_post_id, $this->prefix, $all_elems, $template);
		//print
		echo do_shortcode( $single_lud_post );
	}

	function section_opts() {
		$opts	= array();
		$opts[] = array(
			'key'		=> 'ccname_set',
			'type'		=>  'multi',
			'col'		=> 1,
			'title'		=> __( $this->multiple_up.' Single Lud settings', 'pagelines' ),
			'opts' => array()
		);
		if( $this->multiple !== get_post_type( get_the_ID() ) )
			$opts[0]['opts'][] = array(
				'key'	=>	'single_post_id',
				'type' 			=> 'select',
				'label'	=> __( 'Choose '.$this->single_up, 'pagelines' ),
				'opts'			=> $this->get_single_posts(),
			);
		$opts[0]['opts'][] =
			array(
				'key'	=>	'template_name',
				'type'			=> 'select',
				'label'	=> __( 'Choose Template', 'pagelines' ),
				'opts'			=> $this->get_template_selectvalues(),
			);
		$opts[0]['opts'][] =
			array(
				'key'	=>	'text_font',
				'type' 			=> 'type',
				'label'	=> __( 'Choose '.$this->single_up.' text font', 'pagelines' ),
			);
		return $opts;
	}
	//Custom post type posts list
	function get_single_posts(){
		 global $post;
		 $args = array(
			'posts_per_page'   => 100,
			'orderby'          => 'post_title',
			'order'            => 'ASC',
			'post_type'        => $this->multiple,
		);
		$array 	= array();
		$myposts = get_posts( $args );
		foreach ( $myposts as $post ) : setup_postdata( $post );
			$array[get_the_ID()] 	= array( 'name' => get_the_title() );
		endforeach;
		wp_reset_postdata();
		return $array;
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

	function section_persistent(){
		//add_action( 'template_redirect',array(&$this, 'snav_less') );
		add_filter( 'pl_settings_array', array( &$this, 'get_meta_array' ) );
		add_filter('pless_vars', array(&$this, 'add_less_vars'));
	}

	/* site options metapanel */
	function get_meta_array( $settings ){

		$settings[ $this->id ] = array(
				'name'  => $this->multiple_up,
				//'icon'  => $this->icon,
				'opts'  => $this->sec_site_options()
		);
		return $settings;
	}

	function sec_site_options(){
		$options_array = array(
			array(
				'type' 	=> 	'multi',
				'col'	=> 1,
				'title' => __( 'Background Colors', 'pagelines' ),
				'opts'	=> array(
					array(
						'key'           => $this->prefix.'-templatebg',
						'type'       => 'color',
						'label' => __( 'Container Background', 'pagelines' ),
						'default'	=> pl_hashify(pl_setting('bodybg')),
					),
					array(
						'key'           => $this->prefix.'-singlebg',
						'type'       => 'color',
						'label' => __( 'Single '.$this->single_up.' Background', 'pagelines' ),
						'default'	=> pl_hashify(pl_setting('bodybg')),
					),
					array(
						'key'           => $this->prefix.'-txtcolor',
						'type'          => 'color',
						'label'    => __( 'Content Text Color', 'pagelines' ),
						'default'	=> pl_hashify(pl_setting('text_primary')),
					),
					array(
						'key'           => $this->prefix.'-othertxtcolor',
						'type'       => 'color',
						'label' => __( 'Non Content Text Color', 'pagelines' ),
						'default'	=> pl_hashify(pl_setting('text_primary')),
					),
					array(
						'key'           => $this->prefix.'-linkcolor',
						'type'       => 'color',
						'label' => __( 'Name & Company Link Color', 'pagelines' ),
						'default'	=> pl_hashify(pl_setting('linkcolor')),
					)
				)
			)
		);
		return $options_array;
	}

	function add_less_vars($vars){
		$vars[$this->prefix.'-templatebg'] 	= ( pl_setting($this->prefix.'-templatebg') ) ? pl_hashify( pl_setting( $this->prefix.'-templatebg' ) ) : pl_hashify(pl_setting('bodybg'));
		$vars[$this->prefix.'-singlebg'] 	= ( pl_setting($this->prefix.'-singlebg') ) ? pl_hashify( pl_setting( $this->prefix.'-singlebg' ) ) : pl_hashify(pl_setting('bodybg'));
		$vars[$this->prefix.'-txtcolor'] 	= ( pl_setting($this->prefix.'-txtcolor') ) ? pl_hashify( pl_setting( $this->prefix.'-txtcolor' ) ) : pl_hashify(pl_setting('text_primary'));
		$vars[$this->prefix.'-linkcolor']	= (pl_setting($this->prefix.'-linkcolor')) ? pl_hashify(pl_setting($this->prefix.'-linkcolor')) : pl_hashify(pl_setting('linkcolor'));
		return $vars;
	}
	/*
	//handle less template
	function snav_less(){
		$template 		= ($this->meta['set']['snav_template']) ? $this->meta['set']['snav_template'] : $this->default_template;
		$template_file 	= sprintf('%s/less/%s.less', $this->base_dir, $template);
		pagelines_insert_core_less( $template_file );
	}
	*/
}//EOC