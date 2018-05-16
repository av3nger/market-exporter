<?php
/**
 * Available elements for the YML file.
 *
 * @link       https://github.com/av3nger/market-exporter/
 * @since      1.1.0
 *
 * @package    Market_Exporter
 * @subpackage Market_Exporter/admin
 */

namespace Market_Exporter\Admin;

/**
 * Available elements for the YML file.
 *
 * All the available elements that can be used in the configuration.
 *
 * @package    Market_Exporter
 * @subpackage Market_Exporter/admin
 * @author     Anton Vanyukov <a.vanyukov@testor.ru>
 */
class YML_Elements {

	public static function get_header_elements() {
		$elements = array();

		$elements['name'] = array(
			'type'        => 'text',
			'default'     => get_bloginfo( 'name' ),
			'max_length'  => 20,
			'required'    => true,
			'description' => sprintf(
				'<p>%s</p><p>%s</p>',
				__( 'Короткое название магазина, не более 20 символов. В названии нельзя использовать слова,
					не имеющие отношения к наименованию магазина, например «лучший», «дешевый», указывать номер
					телефона и т. п.', 'market-exporter' ),
				__( 'Название магазина должно совпадать с фактическим названием магазина, которое публикуется
					на сайте. При несоблюдении этого требования наименование Яндекс.Маркет может самостоятельно
					изменить название без уведомления магазина.', 'market-exporter' )
			),
		);

		$elements['company'] = array(
			'type'        => 'text',
			'default'     => '',
			'max_length'  => 0,
			'required'    => true,
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Полное наименование компании, владеющей магазином. Не публикуется, используется для внутренней
				идентификации.', 'market-exporter' )
			),
		);

		$elements['url'] = array(
			'type'        => 'text',
			'default'     => get_site_url(),
			'max_length'  => 0,
			'required'    => false,
			'description' => sprintf(
				'<p>%s</p><p>%s</p>',
				__( 'URL главной страницы магазина. Максимум 50 символов. Допускаются кириллические ссылки.', 'market-exporter' ),
				__( 'Элемент обязателен при размещении по модели «Переход на сайт».', 'market-exporter' )
			),
		);

		$elements['platform'] = array(
			'type'        => 'text',
			'default'     => __( 'WordPress', 'market-exporter' ),
			'max_length'  => 0,
			'required'    => false,
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Система управления контентом, на основе которой работает магазин (CMS).', 'market-exporter' )
			),
		);

		$elements['version'] = array(
			'type'        => 'text',
			'default'     => get_bloginfo( 'version' ),
			'max_length'  => 0,
			'required'    => false,
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Версия CMS.', 'market-exporter' )
			),
		);

		$elements['agency'] = array(
			'type'        => 'text',
			'default'     => '',
			'max_length'  => 0,
			'required'    => false,
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Наименование агентства, которое оказывает техническую поддержку магазину и отвечает за
				работоспособность сайта.', 'market-exporter' )
			),
		);

		$elements['email'] = array(
			'type'        => 'text',
			'default'     => get_bloginfo( 'admin_email' ),
			'max_length'  => 0,
			'required'    => false,
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Контактный адрес разработчиков CMS или агентства, осуществляющего техподдержку.', 'market-exporter' )
			),
		);

		return $elements;
	}

	/**
	 * Print selected element.
	 *
	 * @since 1.1.0
	 *
	 * @param string $element  Element ID.
	 * @param string $type     Type of element. Accepts: 'header'. Default: 'header'.
	 */
	public static function print_element( $element, $value, $type = 'header' ) {
		$elements = call_user_func( array( 'Market_Exporter_Elements', "get_{$type}_elements" ) );
		?>
		<div class="me-list-group-item">
			<div class="me-item-controls">
				<span class="dashicons dashicons-minus"></span>
				<span class="dashicons dashicons-move" aria-hidden="true"></span>
			</div>

			<strong>
				<?php echo "&lt;{$element}&gt;<span contenteditable='true'>{$value}</span>&lt;/{$element}&gt;"; ?>
			</strong>
			<span class="dashicons dashicons-editor-help"></span>
			<div class="me-tooltip-text">
				<?php echo $elements[ $element ]['description']; ?>
			</div>
		</div>
		<?php
	}

}
