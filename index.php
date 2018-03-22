<?php
/**
 * Plugin Name: Normalize
 * Plugin URI: http://www.justbenice.ru/wp-plugin/normalize/
 * Description: Плагин нормализирующий Wordpress.
 * Version: 0.1
 * Author: Igor Kiselev
 * Author URI: http://www.igorkiselev.com/
 * License: A "JustBeNice" license name e.g. GPL2.
 */


// Инициализация параметров для плагина
add_action('admin_init', function () {
	register_setting( 'justbenice-normalize', 'justbenice-enqueue-css-normalize' );
	register_setting( 'justbenice-normalize', 'justbenice-editor-css-normalize' );
	register_setting( 'justbenice-normalize', 'justbenice-disable-emoji' );
	register_setting( 'justbenice-normalize', 'justbenice-disable-adminbar' );
	register_setting( 'justbenice-normalize', 'justbenice-disable-generator' );
	register_setting( 'justbenice-normalize', 'justbenice-disable-rsslinks' );
	register_setting( 'justbenice-normalize', 'justbenice-disable-rellinks' );
	register_setting( 'justbenice-normalize', 'justbenice-enable-navmenus' );
	register_setting( 'justbenice-normalize', 'justbenice-content-the_title' );
	register_setting( 'justbenice-normalize', 'justbenice-header-wp_title' );
	register_setting( 'justbenice-normalize', 'justbenice-functions-html5' );
	register_setting( 'justbenice-normalize', 'justbenice-functions-post-thumbnails' );
	register_setting( 'justbenice-normalize', 'justbenice-functions-ischild' );
	register_setting( 'justbenice-normalize', 'justbenice-functions-bodyclass' );
	register_setting( 'justbenice-normalize', 'justbenice-functions-nav-description' );
	register_setting( 'justbenice-normalize', 'justbenice-functions-escapekey' );
	register_setting( 'justbenice-normalize', 'justbenice-featured-rss' );
	register_setting( 'justbenice-normalize', 'justbenice-opengraph' );
});


// Функции которые зависят от параметров (основная часть изменяющая сайт)

if(get_option( 'justbenice-enqueue-css-normalize' )){
	add_action('wp_enqueue_scripts', function () {
		if(!wp_style_is( 'normalize', $list = 'enqueued' )){
			wp_register_style( 'normalize', plugin_dir_url( __FILE__ ).'stylesheets/normalize.css', array(), '4.1.1n');
			wp_enqueue_style( 'normalize' );
		}
	});
}
if(get_option( 'justbenice-editor-css-normalize' )){
	add_action('after_setup_theme', function () {
		add_editor_style(plugin_dir_url( __FILE__ ).'stylesheets/normalize.css');
	});
}
if(get_option( 'justbenice-disable-emoji' )){
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );	
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_action('widgets_init', function(){
		global $wp_widget_factory;
		remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
	});
}
if(get_option( 'justbenice-disable-adminbar' )){
	add_filter('show_admin_bar', '__return_false');
}
if(get_option( 'justbenice-disable-generator' )){
	remove_action('wp_head', 'wp_generator');
}
if(get_option( 'justbenice-disable-rsslinks' )){
	remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'feed_links', 2);
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
}
if(get_option( 'justbenice-disable-rellinks' )){
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'parent_post_rel_link', 10, 0);
	remove_action('wp_head', 'start_post_rel_link', 10, 0);
	remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
}
if(get_option( 'justbenice-enable-navmenus' )){
	add_action('admin_menu', function () {
		remove_submenu_page( 'themes.php', 'nav-menus.php' );
		add_menu_page(__('Menus'), __('Menus'), 'manage_options', 'nav-menus.php', '', 'dashicons-list-view', 20);
	});
}
if(get_option( 'justbenice-content-the_title' )){
	add_filter('the_title', function ($title) {
		if ($title == '') {return 'Нету заголовка';
		} else {return $title;
		}
	});
}
if(get_option( 'justbenice-header-wp_title' )){
	add_filter('wp_title', function ($title) {
		return $title.esc_attr(get_bloginfo('name'));
	});
}
if(get_option( 'justbenice-featured-rss' )){
	add_filter('the_excerpt_rss', function($content){
		global $post;
		
		if ( has_post_thumbnail( $post->ID ) ){
			$content = '<div>' . get_the_post_thumbnail( $post->ID, 'large', array( 'style' => 'margin-bottom: 1em;' ) ) . '</div>' . $content;
		}
		return $content;
	});
	add_filter('the_content_feed', function($content){
		global $post;
		
		if ( has_post_thumbnail( $post->ID ) ){
			$content = '<div>' . get_the_post_thumbnail( $post->ID, 'large', array( 'style' => 'margin-bottom: 1em;' ) ) . '</div>' . $content;
		}
		return $content;
	});
}
if(get_option( 'justbenice-functions-html5' )){
	add_action('after_setup_theme', function () {
		add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
	});
}
if(get_option( 'justbenice-functions-post-thumbnails' )){
	add_action('after_setup_theme', function () {
		add_theme_support('post-thumbnails');
	});
}
if(get_option( 'justbenice-functions-ischild' )){
	function is_child($slug){
		global $post;
		$child = get_page_by_path($slug);
		if($child){
			if( is_page() && ( $post->post_parent==$child->ID || is_page($child->ID) ) ){
				return true;
			}
		}
		return false; 
	}
}
if(get_option( 'justbenice-functions-bodyclass' )){
	add_filter('body_class', function ($classes) {
	
		global $wpdb, $post;
		
		if (is_page() || is_single()) {
			$classes[] = get_post_type($post->ID).'-'.$post->post_name;
		}
		
		return $classes;
	
	});
}
if(get_option( 'justbenice-functions-nav-description' )){
	add_filter('walker_nav_menu_start_el', function($item_output, $item, $depth, $arg){
		if (strlen($item->description) > 0 ) {
			$item_output .= sprintf('<p class="description">%s</p>', esc_html($item->description));
		}
		return $item_output;
	}, 10, 4);
}
if(get_option( 'justbenice-functions-escapekey' )){
	add_action('wp_footer', function () {
		global $post;
		
		if (is_home() || is_archive() || is_search() || is_404()) {
			$link = '/wp-admin/edit.php';
		}else{
			$link = get_edit_post_link($post->ID, '');
		}
		if (!is_user_logged_in()){
			$link = wp_login_url($link);
		}
		
		echo "<script>document.onkeydown = function(e) {if (e.keyCode == 27) {window.location.href = '".$link."';}};</script>\n";
	});
}

if(get_option( 'justbenice-opengraph' )){
	
    function my_excerpt($text, $excerpt) {
        if ($excerpt) return $excerpt;
        $text = strip_shortcodes( $text );
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = strip_tags($text);
        $excerpt_length = apply_filters('excerpt_length', 55);
        $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
        $words = preg_split("/[\n
         ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
        if ( count($words) > $excerpt_length ) {
                array_pop($words);
                $text = implode(' ', $words);
                $text = $text . $excerpt_more;
        } else {
                $text = implode(' ', $words);
        }

        return apply_filters('wp_trim_excerpt', $text, $excerpt);
    }
	
	
	add_filter('language_attributes', function($og){
		return $og . ' '.'xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
	});

	add_action('wp_head', function(){
		if (is_single()) {
			global $post;
			if(get_the_post_thumbnail($post->ID, 'large')) {
				$thumbnail_id = get_post_thumbnail_id($post->ID);
				$thumbnail_object = get_post($thumbnail_id);
				$image = $thumbnail_object->guid;
			} else {	
				 // default open graph image
				 // $image = '';
			}

			$description = my_excerpt( $post->post_content, $post->post_excerpt );
			$description = strip_tags($description);
			$description = str_replace("\"", "'", $description);

			echo '<meta property="og:title" content="'. get_the_title().'" />'."\n",
					'<meta property="og:type" content="article" />'."\n",
					'<meta property="og:image" content="';
					if (function_exists('wp_get_attachment_thumb_url')) {
						echo wp_get_attachment_thumb_url(get_post_thumbnail_id($post->ID));
					}
			echo '" />'."\n",
					'<meta property="og:url" content="'.get_permalink().'" />'."\n",
					'<meta property="og:description" content="'.$description.'" />'."\n",
					'<meta property="og:site_name" content="'.get_bloginfo('name').'" />'."\n";
		}
	});
}





// Страница настроек плагина

add_action('admin_menu', function () {
	add_options_page( 'Нормализирование', 'Нормализирование', 'manage_options', 'justbenice-normalize', function(){
		if (!current_user_can('manage_options')) {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		// Функции для корректной работы плагина

		function size($url){
			if (file_exists(plugin_dir_path( __FILE__ ).$url)) {
				return round(filesize(plugin_dir_path( __FILE__ ).$url) / 1024, 2).' KB';
			}
		}
		
		?><div class="wrap"><?
			?><h2>Нормализирование сайта</h2><?
			?><form method="post" action="options.php"><?
				settings_fields( 'justbenice-normalize' );
					?><h3>Дополнительные стили и функции</h3><?
					?><table class="form-table"><?
					// justbenice-enqueue-css-normalize
					?><tr><?
						?><th scope="row">Стили</th><?
						?><td><label for="justbenice-enqueue-css-normalize">
							<input
								id="justbenice-enqueue-css-normalize"
								name="justbenice-enqueue-css-normalize"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-enqueue-css-normalize') ); ?> />
							<strong>Normalize.css</strong>, в шапку сайта <small>(+<?php echo size('stylesheets/normalize.css'); ?>)</small> 
						</label>
						<p class="description">Небольшой CSS-файл, который обеспечивает для HTML-элементов лучшую кроссбраузерность в стилях по умолчанию.</p>
					</td><?
					?></tr><?
					// justbenice-editor-css-normalize
					?><tr><?
						?><th scope="row">&nbsp;</th><?
						?><td><label for="justbenice-editor-css-normalize">
							<input
								id="justbenice-editor-css-normalize"
								name="justbenice-editor-css-normalize"
								type="checkbox"
								value="1"
								<?php if(!get_option( 'justbenice-enqueue-css-normalize' )) { ?> disabled<?}?>
								<?php checked( '1', get_option('justbenice-editor-css-normalize') ); ?> />
							<strong>Normalize.css</strong>, в WYSIWYG-редактор
						</label></td><?
					?></tr><?
					// justbenice-functions-ischild
					?><tr><?
						?><th scope="row">Функциии</th><?
						?><td><label for="justbenice-functions-ischild">
							<input
								id="justbenice-functions-ischild"
								name="justbenice-functions-ischild"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-functions-ischild') ); ?> />
							Функция is_child()
							<p class="description">Дополнительная сверка является ли страница, подстраницей кого-то.</p>
						</label></td><?
					?></tr><?
					// justbenice-functions-bodyclass
					?><tr><?
						?><th scope="row">&nbsp;</th><?
						?><td><label for="justbenice-functions-bodyclass">
							<input
								id="justbenice-functions-bodyclass"
								name="justbenice-functions-bodyclass"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-functions-bodyclass') ); ?> />
							Класс в &#60;body&#47;&#62;
							<p class="description">Указывать в классах страницы ее название (slug).</p>
						</label></td><?
					?></tr><?
					
					// justbenice-functions-escapekey
					?><tr><?
						?><th scope="row">&nbsp;</th><?
						?><td><label for="justbenice-functions-escapekey">
							<input
								id="justbenice-functions-escapekey"
								name="justbenice-functions-escapekey"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-functions-escapekey') ); ?> />
							Редактор при нажатии <strong>ESC</strong>
							<p class="description">Открывать редактирование страницы при нажатии ESC.</p>
						</label></td><?
					?></tr><?
					
					
					// justbenice-functions-nav-description
					?><tr><?
						?><th scope="row">&nbsp;</th><?
						?><td><label for="justbenice-functions-nav-description">
							<input
								id="justbenice-functions-nav-description"
								name="justbenice-functions-nav-description"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-functions-nav-description') ); ?> />
							Описание пункта меню
							<p class="description">Показывать в параграфе описание ссылки в wp_nav_menu.</p>
						</label></td><?
					?></tr><?
					
					
					// justbenice-content-the_title
					?><tr><?
						?><th scope="row">Обработка контента</th><?
						?><td><label for="justbenice-content-the_title">
							<input
								id="justbenice-content-the_title"
								name="justbenice-content-the_title"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-content-the_title') ); ?> />
							&laquo;Нет заголовка&raquo;
							<p class="description">Отображать фразу &laquo;Нет заголовка&raquo; в the_title, когда заголовок у поста или страницы пуст.</p>
						</label></td><?
					?></tr><?
					
					
					// justbenice-header-wp_title
					?><tr><?
						?><th scope="row">&nbsp;</th><?
						?><td><label for="justbenice-header-wp_title">
							<input
								id="justbenice-header-wp_title"
								name="justbenice-header-wp_title"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-header-wp_title') ); ?> />
							Название сайта в заголовке
							<p class="description">Отображать название сайта (компании) в заголовке после назнваия страницы (wp_title).</p>
						</label></td><?
					?></tr><?
					?></table><?
					
					
					?><h3>Настройки вордпресса</h3><?
					?><table class="form-table"><?
					
					// justbenice-disable-adminbar
					?><tr><?
						?><th scope="row">Интерфейс вордпресса</th><?
						?><td><label for="justbenice-disable-adminbar">
							<input
								id="justbenice-disable-adminbar"
								name="justbenice-disable-adminbar"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-disable-adminbar') ); ?> />
							Панель администратора
							<p class="description">Скрывать панель администратора на сайте.</p>
						</label></td><?
					?></tr><?
					//justbenice-enable-navmenus
					?><tr><?
						?><th scope="row">&nbsp;</th><?
						?><td><label for="justbenice-enable-navmenus">
							<input
								id="justbenice-enable-navmenus"
								name="justbenice-enable-navmenus"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-enable-navmenus') ); ?> />
							Переместить пункт меню в основное меню
						</label></td><?
					?></tr><?
					// justbenice-disable-emoji
					?><tr><?
						?><th scope="row">Убираем из &#60;head&#47;&#62;</th><?
						?><td><label for="justbenice-disable-emoji">
							<input
								id="justbenice-disable-emoji"
								name="justbenice-disable-emoji"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-disable-emoji') ); ?> />
							Поддержку emoji
							<p class="description">Убирать из &#60;head&#47;&#62; стили и скрипты для обработки emoji на сайте.</p>
						</label></td><?
					?></tr><?
					// justbenice-disable-generator
					?><tr><?
						?><th scope="row">&nbsp;</th><?
						?><td><label for="justbenice-disable-generator">
							<input
								id="justbenice-disable-generator"
								name="justbenice-disable-generator"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-disable-generator') ); ?> />
							Версию вордпресса
							<p class="description">Убирать из &#60;head&#47;&#62; информацию про систему администрирования и версию.</p>
						</label></td><?
					?></tr><?
					// justbenice-disable-rsslinks
					?><tr><?
						?><th scope="row">&nbsp;</th><?
						?><td><label for="justbenice-disable-rsslinks">
							<input
								id="justbenice-disable-rsslinks"
								name="justbenice-disable-rsslinks"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-disable-rsslinks') ); ?> />
							RSS фиды
							<p class="description">Убирать из &#60;head&#47;&#62; ссылки на RSS фиды сайта (будут продажлать работать если просто дописать /feed), а также xml для блог-клиентов.</p>
						</label></td><?
					?></tr><?
					// justbenice-disable-rellinks
					?><tr><?
						?><th scope="row">&nbsp;</th><?
						?><td><label for="justbenice-disable-rellinks">
							<input
								id="justbenice-disable-rellinks"
								name="justbenice-disable-rellinks"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-disable-rellinks') ); ?> />
							REL ссылки
							<p class="description">Убирать из &#60;head&#47;&#62; ссылки на главную страницу, на первую запись, на предыдущую и следующую запись, связь с родительской записью и короткую ссылку к текущей странице.</p>
						</label></td><?
					?></tr><?
					
					// justbenice-featured-rss
					?><tr><?
						?><th scope="row">RSS</th><?
						?><td><label for="justbenice-featured-rss">
							<input
								id="justbenice-featured-rss"
								name="justbenice-featured-rss"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-featured-rss') ); ?> />
							Featured to RSS
							<p class="description">Добавить в начале каждой записи RSS потока изображение поста</p>
						</label></td><?
					?></tr><?
					// justbenice-functions-html5
					?><tr><?
						?><th scope="row">Поддержка для тем</th><?
						?><td><label for="justbenice-functions-html5">
							<input
								id="justbenice-functions-html5"
								name="justbenice-functions-html5"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-functions-html5') ); ?> />
							HTML5 разметка
							<p class="description">Включает поддержку html5 разметки для списка комментариев, формы комментариев, формы поиска, галереи и т.д.</p>
						</label></td><?
					?></tr><?
					// justbenice-functions-post-thumbnails
					?><tr><?
						?><th scope="row">&nbsp;</th><?
						?><td><label for="justbenice-functions-post-thumbnails">
							<input
								id="justbenice-functions-post-thumbnails"
								name="justbenice-functions-post-thumbnails"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-functions-post-thumbnails') ); ?> />
							Миниатюра к посту
							<p class="description">Позволяет устанавливать миниатюру посту.</p>
						</label></td><?
					?></tr><?
					// justbenice-opengraph
					?><tr><?
						?><th scope="row">&nbsp;</th><?
						?><td><label for="justbenice-opengraph">
							<input
								id="justbenice-opengraph"
								name="justbenice-opengraph"
								type="checkbox"
								value="1"
								<?php checked( '1', get_option('justbenice-opengraph') ); ?> />
							Добавляет opengraph поля в шапку
							<p class="description"</p>
						</label></td><?
					?></tr><?
					
					?></table><?
				do_settings_sections("theme-options");
				submit_button();
				?><p>Плагин для упрощения работы от <a href="http://www.justbenice.ru/">Just Be Nice</a></p><?
			?></form><?
		?></div><?
	});
});	

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), function($links){
	return array_merge( $links, array('<a href="' . admin_url( 'options-general.php?page=justbenice-normalize' ) . '">Настройки</a>',) );
});

?>