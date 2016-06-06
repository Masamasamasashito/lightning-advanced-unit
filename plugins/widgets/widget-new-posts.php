<?php

/*-------------------------------------------*/
/*  Side Post list widget
/*-------------------------------------------*/
class WP_Widget_ltg_adv_post_list extends WP_Widget {

	public $taxonomies = array( 'category' );

	function __construct() {

		$widget_name = LIGHTNING_ADVANCED_SHORT_NAME. '_' . __( 'Content Area Posts Widget', LIGHTNING_ADVANCED_TEXTDOMAIN );

		parent::__construct(
			'ltg_adv_post_list',
			$widget_name,
			array( 'description' => __( 'Displays a list of your most recent posts', LIGHTNING_ADVANCED_TEXTDOMAIN ) )
		);
	}

	function widget( $args, $instance ) {
		if ( ! isset( $instance['format'] ) ) { $instance['format'] = 0; }

		echo $args['before_widget'];
		echo '<div class="pt_'.$instance['format'].'">';
		if ( isset( $instance['label'] ) && $instance['label'] ) {
			echo $args['before_title'];
			echo $instance['label'];
			echo $args['after_title'];
		}	

		$count      = ( isset( $instance['count'] ) && $instance['count'] ) ? $instance['count'] : 10;
		$post_type  = ( isset( $instance['post_type'] ) && $instance['post_type'] ) ? $instance['post_type'] : 'post';

		if ( $instance['format'] ) { 
			$this->_taxonomy_init( $post_type );
		}

		$p_args = array(
			'post_type' => $post_type,
			'posts_per_page' => $count,
			'paged' => 1,
		);

		if ( isset( $instance['terms'] ) && $instance['terms'] ) {
			$taxonomies = get_taxonomies( array() );
			$p_args['tax_query'] = array(
				'relation' => 'OR',
			);
			$terms_array = explode( ',', $instance['terms'] );
			foreach ( $taxonomies as $taxonomy ) {
				$p_args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'field' => 'id',
					'terms' => $terms_array,
				);
			}
		}

		$post_loop = new WP_Query( $p_args );

		if ( $post_loop->have_posts() ) :
			if ( ! $instance['format'] ) {
				while ( $post_loop->have_posts() ) : $post_loop->the_post();
					$this->display_pattern_0();
				endwhile;
			} else if ( $instance['format'] == 1 ) {
				while ( $post_loop->have_posts() ) : $post_loop->the_post();
					$this->display_pattern_1();
				endwhile;
			}

		endif;
		echo '</div>';
		echo $args['after_widget'];

		wp_reset_postdata();
		wp_reset_query();

	} // widget($args, $instance)

	/*-------------------------------------------*/
	/*  display_pattern_0
	/*-------------------------------------------*/
	function display_pattern_0() { ?>
		<article class="media">
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if ( has_post_thumbnail()) :?>
			<div class="media-left postList_thumbnail">
				<a href="<?php the_permalink(); ?>">
				<?php
				$attr = array('class'	=> "media-object");
				the_post_thumbnail('thumbnail',$attr); ?>
				</a>
			</div>
			<?php endif; ?>
			<div class="media-body">

			<div class="entry-meta">
			<span class="published entry-meta_items"><?php echo esc_html( get_the_date() ); ?></span>

			<?php global $lightning_theme_options; ?>

			<?php
			// Post update
			$meta_hidden_update = ( isset($lightning_theme_options['postUpdate_hidden']) && $lightning_theme_options['postUpdate_hidden'] ) ? ' entry-meta_hidden' : ''; ?>

			<span class="entry-meta_items entry-meta_updated<?php echo $meta_hidden_update;?>">/ <?php _e('Last updated','lightning'); ?> : <span class="updated"><?php the_modified_date('') ?></span></span>

			<?php
			// Post author
			$meta_hidden_author = ( isset($lightning_theme_options['postAuthor_hidden']) && $lightning_theme_options['postAuthor_hidden'] ) ? ' entry-meta_hidden' : ''; ?>

			<span class="vcard author entry-meta_items entry-meta_items_author<?php echo $meta_hidden_author;?>"><span class="fn"><?php the_author(); ?></span></span>

			<?php
			$taxonomies = get_the_taxonomies();
			if ($taxonomies):
				// get $taxonomy name
				$taxonomy = key( $taxonomies );
				$terms  = get_the_terms( get_the_ID(),$taxonomy );
				$term_url	= esc_url(get_term_link( $terms[0]->term_id,$taxonomy));
				$term_name	= esc_html($terms[0]->name);
				echo '<span class="entry-meta_items entry-meta_items_term"><a href="'.$term_url.'" class="btn btn-xs btn-primary">'.$term_name.'</a></span>';
			endif;
			?>

			</div>
				<h1 class="media-heading entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				<a href="<?php the_permalink(); ?>" class="media-body_excerpt"><?php the_excerpt(); ?></a>
				<!--
				<div><a href="<?php the_permalink(); ?>" class="btn btn-default btn-sm"><?php _e('Read more', 'lightning'); ?></a></div>
				-->   
			</div>
		</div>
		</article><?php
	}


	/*-------------------------------------------*/
	/*  display_pattern_1
	/*-------------------------------------------*/
	function display_pattern_1() {
		global $post;
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header>
			<?php get_template_part('module_loop_post_meta');?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>

			<div class="entry-body">

			<?php 
			$lightning_adv_more_btn_txt = '<span class="btn btn-default btn-block">'. __('Read more', LIGHTNING_ADVANCED_TEXTDOMAIN ). '</span>';
			$more_btn  = apply_filters( 'lightning-adv-more-btn-txt' ,$lightning_adv_more_btn_txt);
			the_content( $more_btn );?>
			</div><!-- [ /.entry-body ] -->

			<div class="entry-footer">
			<?php
			$args = array(
				'before'           => '<nav class="page-link"><dl><dt>Pages :</dt><dd>',
				'after'            => '</dd></dl></nav>',
				'link_before'      => '<span class="page-numbers">',
				'link_after'       => '</span>',
				'echo'             => 1 );
			wp_link_pages( $args ); ?>

			<?php
			/*-------------------------------------------*/
			/*  Category and tax data
			/*-------------------------------------------*/
		    $args = array(
		        'template' => __( '<dl><dt>%s</dt><dd>%l</dd></dl>','lightning' ),
		        'term_template' => '<a href="%1$s">%2$s</a>',
		    );
		    $taxonomies = get_the_taxonomies($post->ID,$args);
		    $taxnomiesHtml = '';
		    if ($taxonomies) {
				foreach ($taxonomies as $key => $value) {
					if ( $key != 'post_tag' ) {
						$taxnomiesHtml .= '<div class="entry-meta-dataList">'.$value.'</div>';
					}
		    	} // foreach
			} // if ($taxonomies)
			$taxnomiesHtml = apply_filters( 'lightning_taxnomiesHtml', $taxnomiesHtml );
			echo $taxnomiesHtml;
			?>

			<?php $tags_list = get_the_tag_list();
			if ( $tags_list ): ?>
			<div class="entry-meta-dataList entry-tag">
			<dl>
			<dt><?php _e('Tags','lightning') ;?></dt>
			<dd class="tagCloud"><?php echo $tags_list; ?></dd>
			</dl>
			</div><!-- [ /.entry-tag ] -->
			<?php endif; ?>
			</div><!-- [ /.entry-footer ] -->

		</article>

	<?php }

	function _taxonomy_init( $post_type ) {
		if ( $post_type == 'post' ) { return; }
		$this->taxonomies = get_object_taxonomies( $post_type );
	}

	function taxonomy_list( $post_id = 0, $before = ' ', $sep = ',', $after = '' ) {
		if ( ! $post_id ) { $post_id = get_the_ID(); }

		$taxo_catelist = array();

		foreach ( $this->taxonomies as $taxonomy ) {
			$terms = get_the_term_list( $post_id, $taxonomy, $before, $sep , $after );
			if ( $terms ) { $taxo_catelist[] = $terms; }
		}

		if ( count( $taxo_catelist ) ) { return join( $taxo_catelist, $sep ); }
		return '';
	}

	function form( $instance ) {
		$defaults = array(
			'count'     => 10,
			'label'     => __( 'Recent Posts', LIGHTNING_ADVANCED_TEXTDOMAIN ),
			'post_type' => 'post',
			'terms'     => '',
			'format'    => '0',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		//タイトル ?>
        <br/>
		<?php echo _e( 'Display Format', LIGHTNING_ADVANCED_TEXTDOMAIN ); ?>:<br/>
		<ul>
		<li><label><input type="radio" name="<?php echo $this->get_field_name( 'format' );  ?>" value="0" <?php if ( $instance['format'] == 0 ) { echo 'checked'; } ?>/><?php echo __( 'Thumbnail', LIGHTNING_ADVANCED_TEXTDOMAIN ) .'/'. __( 'Date', LIGHTNING_ADVANCED_TEXTDOMAIN ) .'/'. __( 'Category', LIGHTNING_ADVANCED_TEXTDOMAIN ) .'/'. __( 'Title', LIGHTNING_ADVANCED_TEXTDOMAIN ).'/'. __( 'Excerpt', LIGHTNING_ADVANCED_TEXTDOMAIN ); ?></label>
		</li>
		<li><label><input type="radio" name="<?php echo $this->get_field_name( 'format' );  ?>" value="1" <?php if ( $instance['format'] == 1 ) { echo 'checked'; } ?>/><?php echo __( 'Thumbnail', LIGHTNING_ADVANCED_TEXTDOMAIN ) .'/'. __( 'Date', LIGHTNING_ADVANCED_TEXTDOMAIN ) .'/'. __( 'Category', LIGHTNING_ADVANCED_TEXTDOMAIN ) .'/'. __( 'Title', LIGHTNING_ADVANCED_TEXTDOMAIN ).'/'. __( 'Content', LIGHTNING_ADVANCED_TEXTDOMAIN ); ?></label>
		</li>
		</ul>
        <br/>
		<label for="<?php echo $this->get_field_id( 'label' );  ?>"><?php _e( 'Title:' ); ?></label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'label' ); ?>-title" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo $instance['label']; ?>" />
        <br/><br />

		<?php //表示件数 ?>
		<label for="<?php echo $this->get_field_id( 'count' );  ?>"><?php _e( 'Display count',LIGHTNING_ADVANCED_TEXTDOMAIN ); ?>:</label><br/>
		<input type="text" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo $instance['count']; ?>" />
        <br /><br />

		<?php //投稿タイプ ?>
		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Slug for the custom type you want to display', LIGHTNING_ADVANCED_TEXTDOMAIN ) ?>:</label><br />
		<input type="text" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>" value="<?php echo esc_attr( $instance['post_type'] ) ?>" />
        <br/><br/>

		<?php // Terms ?>
		<label for="<?php echo $this->get_field_id( 'terms' ); ?>"><?php _e( 'taxonomy ID', LIGHTNING_ADVANCED_TEXTDOMAIN ) ?>:</label><br />
		<input type="text" id="<?php echo $this->get_field_id( 'terms' ); ?>" name="<?php echo $this->get_field_name( 'terms' ); ?>" value="<?php echo esc_attr( $instance['terms'] ) ?>" /><br />
		<?php _e( 'if you need filtering by term, add the term ID separate by ",".', LIGHTNING_ADVANCED_TEXTDOMAIN );
		echo '<br/>';
		_e( 'if empty this area, I will do not filtering.', LIGHTNING_ADVANCED_TEXTDOMAIN );
		echo '<br/><br/>';
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['format']     = $new_instance['format'];
		$instance['count']      = $new_instance['count'];
		$instance['label']      = $new_instance['label'];
		$instance['post_type']  = ! empty( $new_instance['post_type'] ) ? strip_tags( $new_instance['post_type'] ) : 'post';
		$instance['terms']      = preg_replace( '/([^0-9,]+)/', '', $new_instance['terms'] );
		return $instance;
	}
}
add_action( 'widgets_init', create_function( '', 'return register_widget("WP_Widget_ltg_adv_post_list");' ) );